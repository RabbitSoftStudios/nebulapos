<?php
/**
 * AJAX: save_dte.php - Procesamiento de Venta e Inventario
 * Auditoría v4.0 - El Salvador DTE
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

$db = supabase();
$codigoGeneracion = $data['identificacion']['codigoGeneracion'];
$nombreArchivo = "dte_" . $codigoGeneracion . ".json";
$rutaFolder = __DIR__ . "/../../storage/sigs/";

try {
    // 2. Guardar archivo físico para el firmador
    if (!is_dir($rutaFolder)) {
        mkdir($rutaFolder, 0777, true);
    }
    file_put_contents($rutaFolder . $nombreArchivo, json_encode($data, JSON_PRETTY_PRINT));

    // 3. Insertar en dte_facturas (Auditoría para Hacienda)
    // Nota: receptor_id se deja nulo o se usa un UUID fijo para "Consumidor Final" si tu DB lo requiere
    $insertFactura = [
        'codigo_generacion' => $codigoGeneracion,
        'emisor_nit'        => $data['emisor']['nit'],
        'numero_control'    => $data['identificacion']['numeroControl'],
        'tipo_dte'          => $data['identificacion']['tipoDte'] ?? '01',
        'fecha_emision'     => $data['identificacion']['fecEmi'] . ' ' . $data['identificacion']['horEmi'],
        'total_pagar'       => $data['resumen']['totalPagar'],
        'documento_json'    => json_encode($data),
        'estado_firma'      => 'pendiente',
        'origen'            => 'interno',
        'metadata'          => json_encode([
            'metodo_pago' => ($data['resumen']['condicionOperacion'] == 1) ? 'Efectivo' : 'Tarjeta',
            'json_local_path' => $nombreArchivo
        ])
    ];
    $db->from('dte_facturas')->insert($insertFactura);

    // 4. Registrar en pos_ventas (Reportes rápidos)
    $ventaData = [
        'codigo_generacion' => $codigoGeneracion,
        'numero_control'    => $data['identificacion']['numeroControl'],
        'cliente_nombre'    => $data['receptor']['nombre'],
        'total_pagar'       => $data['resumen']['totalPagar'],
        'metodo_pago'       => ($data['resumen']['condicionOperacion'] == 1) ? 'Efectivo' : 'Tarjeta',
        'fecha_emision'     => $data['identificacion']['fecEmi'],
        'json_path'         => $nombreArchivo
    ];
    $db->from('pos_ventas')->insert($ventaData);

    // 5. Descuento de Stock por RPC (Stored Procedure en Supabase)
    foreach ($data['cuerpoDocumento'] as $item) {
        if (isset($item['codigo'])) {
            $db->rpc('descontar_stock', [
                'p_id'       => $item['codigo'], 
                'p_cantidad' => $item['cantidad']
            ]);
        }
    }

    echo json_encode([
        'success' => true, 
        'mensaje' => 'Venta procesada, stock actualizado y DTE generado.',
        'codigoGeneracion' => $codigoGeneracion
    ]);

} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}