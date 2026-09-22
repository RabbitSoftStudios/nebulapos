<?php
/**
 * AJAX: process_sale_complete.php - Procesamiento Completo de Venta con Firma Electrónica
 * Team MYTS
 */
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 0);

require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/supabase.php';
require_once __DIR__ . '/../../includes/pg_connection.php';
require_once __DIR__ . '/../../includes/signer/signer_local.php';
require_once __DIR__ . '/../../includes/signer/signer_goes.php';

$input = file_get_contents('php://input');
$dteData = json_decode($input, true);

if (!$dteData || !isset($dteData['identificacion']['codigoGeneracion'])) {
    echo json_encode(['success' => false, 'error' => 'DTE inválido']);
    exit;
}

try {
    $codigoGeneracion = $dteData['identificacion']['codigoGeneracion'];
    $tipoDte = $dteData['identificacion']['tipoDte'] ?? '01';

    // =====================================================
    // GENERAR NUMERO DE CONTROL DESDE BD (CORREGIDO)
    // =====================================================
    $pdo = pg_pool();
    $pdo->beginTransaction();

    // 1) Bloquear una fila REAL del tipo de DTE
    $lockStmt = $pdo->prepare("
        SELECT id
        FROM dte_facturas
        WHERE tipo_dte = :tipo
        ORDER BY created_at DESC
        LIMIT 1
        FOR UPDATE
    ");
    $lockStmt->execute([':tipo' => $tipoDte]);

    // 2) Obtener el siguiente correlativo (SIN FOR UPDATE)
    $stmt = $pdo->prepare("
        SELECT
            COALESCE(
                MAX(
                    CAST(
                        RIGHT(numero_control, 15) AS BIGINT
                    )
                ),
                0
            ) + 1 AS next_num
        FROM dte_facturas
        WHERE tipo_dte = :tipo
        AND numero_control ~ '^[A-Z0-9-]+-[0-9]{15}$'
    ");

    $stmt->execute([':tipo' => $tipoDte]);
    $row = $stmt->fetch();

    $secuencial = str_pad($row['next_num'], 15, '0', STR_PAD_LEFT);
    $numeroControl = "DTE-{$tipoDte}-P001M001-{$secuencial}";
    
    // =====================================================
    // GUARDAR JSON FÍSICO
    // =====================================================
    $dir = __DIR__ . '/../../storage/sigs/';
    if (!is_dir($dir)) {
        mkdir($dir, 0777, true);
    }

    $file = "dte_{$codigoGeneracion}.json";
    file_put_contents($dir . $file, json_encode($dteData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

    // =====================================================
    // INSERT EN dte_facturas (SUPABASE)
    // =====================================================
    $db = supabase('dte_facturas');
    $res = $db->insert([
        'codigo_generacion' => $codigoGeneracion,
        'emisor_nit'        => $dteData['emisor']['nit'] ?? null,
        'numero_control'    => $numeroControl,
        'tipo_dte'          => $tipoDte,
        'fecha_emision'     => $dteData['identificacion']['fecEmi'] . ' ' . $dteData['identificacion']['horEmi'],
        'total_pagar'       => $dteData['resumen']['totalPagar'],
        'documento_json'    => json_encode($dteData),
        'estado_firma'      => 'pendiente',
        'origen'            => 'pos_interno'
    ]);

    if (!$res['success']) {
        throw new Exception($res['error'] ?? 'Insert fallido en dte_facturas');
    }

    // Commit del correlativo
    $pdo->commit();

    // =====================================================
    // FIRMA LOCAL
    // =====================================================
    
    sign_document_local(
        [
            'id' => $codigoGeneracion,
            'dte_json' => json_encode($dteData)
        ],
        $codigoGeneracion
    );

    echo json_encode([
        'success' => true,
        'codigoGeneracion' => $codigoGeneracion,
        'numeroControl' => $numeroControl
    ]);

} catch (Exception $e) {

    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }

    error_log("process_sale_complete ERROR: " . $e->getMessage());

    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}
// ========================= FUNCIONES ========================= //