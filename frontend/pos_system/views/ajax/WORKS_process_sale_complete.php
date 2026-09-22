<?php
/**
 * AJAX: process_sale_complete.php - Procesamiento Completo de Venta con Firma Electrónica
 * Flujo: Guardar → Firmar Local → Enviar Gobierno → Actualizar Estado
 */
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 0); // No mostrar errores en JSON

// 1. Cargar dependencias
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/supabase.php';
require_once __DIR__ . '/../../includes/signer/signer_local.php';
require_once __DIR__ . '/../../includes/signer/signer_goes.php';

// 2. Recibir datos
$input = file_get_contents('php://input');
$dteData = json_decode($input, true);

// 3. Validar datos recibidos
if (!$dteData || !isset($dteData['identificacion']['codigoGeneracion'])) {
    echo json_encode([
        'success' => false,
        'error' => 'Datos de DTE inválidos o incompletos'
    ]);
    exit;
}

try {
    $codigoGeneracion = $dteData['identificacion']['codigoGeneracion'];
    $numeroControl = $dteData['identificacion']['numeroControl'];
    
    // 4. Guardar archivo JSON físico para el firmador
    $rutaFolder = __DIR__ . "/../../storage/sigs/";
    if (!is_dir($rutaFolder)) {
        mkdir($rutaFolder, 0777, true);
    }
    
    $nombreArchivo = "dte_" . $codigoGeneracion . ".json";
    $rutaCompleta = $rutaFolder . $nombreArchivo;
    file_put_contents($rutaCompleta, json_encode($dteData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    
    // 5. Guardar en base de datos (estado: pendiente)
    $db = supabase('dte_facturas');
    
    $insertData = [
        'codigo_generacion' => $codigoGeneracion,
        'emisor_nit' => $dteData['emisor']['nit'] ?? null,
        'numero_control' => $numeroControl,
        'tipo_dte' => $dteData['identificacion']['tipoDte'] ?? '01',
        'fecha_emision' => $dteData['identificacion']['fecEmi'] . ' ' . $dteData['identificacion']['horEmi'],
        'total_pagar' => $dteData['resumen']['totalPagar'],
        'documento_json' => json_encode($dteData),
        'estado_firma' => 'pendiente',
        'origen' => 'pos_interno',
        'metadata' => json_encode([
            'metodo_pago' => ($dteData['resumen']['condicionOperacion'] == 1) ? 'Efectivo' : 'Tarjeta',
            'json_local_path' => $nombreArchivo,
            'receptor_nombre' => $dteData['receptor']['nombre'] ?? 'Consumidor Final'
        ])
    ];
    
    $resultInsert = $db->insert($insertData);
    
    if (!$resultInsert['success']) {
        throw new Exception('Error al guardar DTE en base de datos: ' . ($resultInsert['error'] ?? 'Error desconocido'));
    }
    
    // 6. Registrar en pos_ventas para reportes rápidos
    $dbVentas = supabase('pos_ventas');
    $ventaData = [
        'codigo_generacion' => $codigoGeneracion,
        'numero_control' => $numeroControl,
        'cliente_nombre' => $dteData['receptor']['nombre'] ?? 'Consumidor Final',
        'total_pagar' => $dteData['resumen']['totalPagar'],
        'metodo_pago' => ($dteData['resumen']['condicionOperacion'] == 1) ? 'Efectivo' : 'Tarjeta',
        'fecha_emision' => $dteData['identificacion']['fecEmi'],
        'json_path' => $nombreArchivo
    ];
    $dbVentas->insert($ventaData);
    
    // 7. Descontar inventario
    foreach ($dteData['cuerpoDocumento'] as $item) {
        if (isset($item['codigo'])) {
            try {
                $dbProductos = supabase('dte_productos');
                $dbProductos->rpc('descontar_stock', [
                    'p_id' => $item['codigo'],
                    'p_cantidad' => $item['cantidad']
                ]);
            } catch (Exception $e) {
                // Log error pero continuar
                error_log("Error al descontar stock producto {$item['codigo']}: " . $e->getMessage());
            }
        }
    }
    
    // 8. FIRMA LOCAL
    $invoice = [
        'id' => $codigoGeneracion,
        'dte_json' => json_encode($dteData)
    ];
    
    $signedLocal = sign_document_local($invoice, $codigoGeneracion);
    
    // 9. Actualizar estado: firmado_local
    $db->update(
        ['estado_firma' => 'firmado_local'],
        ['codigo_generacion' => $codigoGeneracion]
    );
    
    // 10. ENVIAR AL GOBIERNO
    $govResponse = enviar_firma_gobierno($codigoGeneracion);
    
    // 11. Procesar respuesta del gobierno
    $selloRecibido = null;
    $estadoFinal = 'error_gobierno';
    
    if (isset($govResponse['success']) && $govResponse['success']) {
        $estadoFinal = 'procesado';
        $selloRecibido = $govResponse['response']['selloRecibido'] ?? null;
        
        // Actualizar con sello del gobierno
        $db->update([
            'estado_firma' => $estadoFinal,
            'sello_recibido' => $selloRecibido,
            'respuesta_gobierno' => json_encode($govResponse['response'])
        ], ['codigo_generacion' => $codigoGeneracion]);
    } else {
        // Error al enviar al gobierno
        $db->update([
            'estado_firma' => $estadoFinal,
            'error_gobierno' => $govResponse['error'] ?? 'Error desconocido'
        ], ['codigo_generacion' => $codigoGeneracion]);
    }
    
    // 12. Retornar resultado exitoso
    echo json_encode([
        'success' => true,
        'codigoGeneracion' => $codigoGeneracion,
        'numeroControl' => $numeroControl,
        'selloRecibido' => $selloRecibido,
        'estadoFinal' => $estadoFinal,
        'mensaje' => 'Venta procesada exitosamente'
    ]);
    
} catch (Exception $e) {
    // Registrar error en log
    error_log("Error en process_sale_complete.php: " . $e->getMessage());
    error_log("Stack trace: " . $e->getTraceAsString());
    
    // Retornar error al frontend
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage(),
        'trace' => (defined('DEBUG_MODE') && DEBUG_MODE) ? $e->getTraceAsString() : null
    ]);
}
