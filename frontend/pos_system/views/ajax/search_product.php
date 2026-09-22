<?php
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/supabase.php';
header('Content-Type: application/json');
$input = file_get_contents('php://input');
$data = json_decode($input, true);
if (!isset($data['barcode'])) {
    echo json_encode(['success' => false, 'error' => 'Código de barras requerido']);
    exit;
}
try {
    $db = supabase('dte_productos');
    $result = $db->select('*', ['codigo_barras' => $data['barcode']], 1);
    
    if ($result['success'] && !empty($result['data'])) {
        echo json_encode([
            'success' => true,
            'product' => $result['data'][0]
        ]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Producto no encontrado']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}