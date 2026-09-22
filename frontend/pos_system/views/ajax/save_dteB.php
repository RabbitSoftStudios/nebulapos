<?php
/**
 * AJAX: save_dte.php - Procesamiento de Venta e Inventario
 * Auditoría v4.0 - El Salvador DTE
 * MODIFICADO: Inclusión de receptor_id y multiempresa
 */
header('Content-Type: application/json');

// 1. Carga de dependencias y conexión
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/supabase.php';

$input = file_get_contents('php://input');
$data = json_decode($input, true);

if (!$data) {
    echo json_encode(['success' => false, 'error' => 'No se recibieron datos válidos.']);
    exit;
}

// Iniciar sesión para obtener usuario y empresa
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

try {
    // 2. Guardar archivo físico para el firmador
    $codigoGeneracion = $data['identificacion']['codigoGeneracion'];
    $nombreArchivo = "dte_" . $codigoGeneracion . ".json";
    $rutaFolder = __DIR__ . "/../../storage/sigs/";
    
    if (!is_dir($rutaFolder)) {
        mkdir($rutaFolder, 0777, true);
    }
    
    file_put_contents($rutaFolder . $nombreArchivo, json_encode($data, JSON_PRETTY_PRINT));
    
    // 3. Insertar en dte_facturas con receptor_id
    $supabaseDTE = new SupabaseClient('dte_facturas');
    
    // Obtener usuario y empresa de la sesión (o de donde corresponda)
    $usuarioId = $_SESSION['usuario_id'] ?? null;
    $empresaNit = $_SESSION['empresa_nit'] ?? $data['emisor']['nit'];
    
    // 3. Insertar en dte_facturas con receptor_id
    $supabaseDTE = new SupabaseClient('dte_facturas');

    // Obtener usuario y empresa de la sesión (o de donde corresponda)
    $usuarioId = $_SESSION['usuario_id'] ?? null;
    $empresaNit = $_SESSION['empresa_nit'] ?? $data['emisor']['nit'] ?? '06141234561234';

    // Insertar DTE usando el nuevo método
    $resultDTE = $supabaseDTE->insertDTE($data, $usuarioId, $empresaNit);

    if (!$resultDTE['success']) {
        throw new Exception('Error al guardar DTE en base de datos: ' . ($resultDTE['error'] ?? 'Error desconocido'));
    }
    
    // 4. Registrar en pos_ventas (Reportes rápidos)
    $supabaseVentas = new SupabaseClient('pos_ventas');
    
    $ventaData = [
        'codigo_generacion' => $codigoGeneracion,
        'numero_control'    => $data['identificacion']['numeroControl'],
        'cliente_nombre'    => $data['receptor']['nombre'] ?? 'CONSUMIDOR FINAL',
        'cliente_nit'       => $data['receptor']['nit'] ?? 'CF',
        'total_pagar'       => $data['resumen']['totalPagar'],
        'metodo_pago'       => ($data['resumen']['condicionOperacion'] == 1) ? 'Efectivo' : 'Tarjeta',
        'fecha_emision'     => $data['identificacion']['fecEmi'],
        'usuario_id'        => $usuarioId,
        'empresa_nit'       => $empresaNit,
        'json_path'         => $nombreArchivo,
        'created_at'        => date('c')
    ];
    
    $resultVenta = $supabaseVentas->insert($ventaData);
    
    if (!$resultVenta['success']) {
        error_log('Error al guardar venta POS: ' . ($resultVenta['error'] ?? 'Error desconocido'));
        // No lanzamos excepción porque el DTE ya se guardó
    }
    
    // 5. Descuento de Stock por RPC (Stored Procedure en Supabase)
    $supabaseRPC = new SupabaseClient(); // Sin tabla específica para RPC
    
    foreach ($data['cuerpoDocumento'] as $item) {
        if (isset($item['codigo']) && isset($item['cantidad'])) {
            $resultStock = $supabaseRPC->rpc('descontar_stock', [
                'p_id'       => $item['codigo'], 
                'p_cantidad' => $item['cantidad'],
                'p_usuario_id' => $usuarioId,
                'p_empresa_nit' => $empresaNit
            ]);
            
            if (!$resultStock['success']) {
                error_log('Error al descontar stock para producto ' . $item['codigo'] . ': ' . 
                         ($resultStock['error'] ?? 'Error desconocido'));
            }
        }
    }
    
    echo json_encode([
        'success' => true, 
        'mensaje' => 'Venta procesada, stock actualizado y DTE generado.',
        'codigoGeneracion' => $codigoGeneracion,
        'receptor_id' => $resultDTE['data'][0]['receptor_id'] ?? 'No disponible',
        'dte_id' => $resultDTE['data'][0]['id'] ?? 'No disponible'
    ]);
    
} catch (Exception $e) {
    // Registrar error en log
    error_log('Error en save_dte.php: ' . $e->getMessage() . ' - ' . $e->getTraceAsString());
    
    echo json_encode([
        'success' => false, 
        'error' => $e->getMessage(),
        'error_full' => (defined('ENVIRONMENT') && ENVIRONMENT === 'development') ? 
                       $e->getTraceAsString() : null
    ]);
}