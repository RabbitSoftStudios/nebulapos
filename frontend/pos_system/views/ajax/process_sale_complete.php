<?php
/**
 * AJAX: process_sale_complete.php
 * Procesamiento Completo de Venta con Firma Electrónica
 * Team MYTS
 */
ob_start();
register_shutdown_function(function() {
    $error = error_get_last();
    if ($error && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR], true)) {
        if (ob_get_length()) ob_clean();
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'error' => 'FATAL PHP ERROR: ' . $error['message']]);
    }
});
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', '0');

require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/supabase.php';
require_once __DIR__ . '/../../includes/pg_connection.php';
require_once __DIR__ . '/../../includes/signer/utils/audit_logger.php';
require_once __DIR__ . '/../../includes/signer/utils/dte_validator_new.php';

checkPOSAuth();
$companyId = (int)($_SESSION['empresa_id'] ?? 0);
if ($companyId <= 0) {
    ob_clean();
    echo json_encode(['success' => false, 'error' => 'Sesión sin empresa asociada.']);
    exit;
}

$dteData = json_decode(file_get_contents('php://input'), true);
if (!$dteData || empty($dteData['identificacion']['codigoGeneracion'])) {
    ob_clean();
    echo json_encode(['success' => false, 'error' => 'DTE inválido']);
    exit;
}

try {
    $codigoGeneracion = $dteData['identificacion']['codigoGeneracion'];
    $tipoDte = $dteData['identificacion']['tipoDte'] ?? '01';
    $correoCliente = $dteData['receptor']['correo'] ?? null;
    $pdo = pg_pool();

    if (!is_db_connected($pdo)) throw new Exception('Database connection unavailable. Please try again.');
    $pdo->beginTransaction();

    // La secuencia de control es independiente por empresa y tipo de DTE.
    $stmt = $pdo->prepare("SELECT COALESCE(MAX(CAST(RIGHT(numero_control, 15) AS INTEGER)), 0) + 1 AS next_num FROM dte_facturas WHERE company_id = :company AND tipo_dte = :tipo AND numero_control IS NOT NULL AND LENGTH(numero_control) >= 15");
    $stmt->execute([':company' => $companyId, ':tipo' => $tipoDte]);
    $row = $stmt->fetch();
    $secuencial = str_pad((string)($row['next_num'] ?? 1), 15, '0', STR_PAD_LEFT);
    $numeroControl = "DTE-{$tipoDte}-P001M001-{$secuencial}";
    $dteData['identificacion']['numeroControl'] = $numeroControl;

    $validacion = validar_json_dte_nuevo($dteData);
    if (!$validacion['valid']) {
        $pdo->rollBack();
        ob_clean();
        echo json_encode(['success' => false, 'error' => 'JSON DTE inválido: ' . implode(' | ', array_slice($validacion['errors'], 0, 3)), 'validation_errors' => $validacion['errors'], 'model' => 'CONSUMIDOR_FINAL_CON_IVA_INCLUIDO']);
        exit;
    }

    error_log('DTE VALIDATION PASSED for codigoGeneracion: ' . $codigoGeneracion . ' | numeroControl: ' . $numeroControl . ' | company_id: ' . $companyId);
    $dir = __DIR__ . '/../../storage/sigs/';
    if (!is_dir($dir)) mkdir($dir, 0770, true);
    file_put_contents($dir . "dte_{$codigoGeneracion}.json", json_encode($dteData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

    $db = supabase('dte_facturas');
    $res = $db->insert([
        'company_id' => $companyId,
        'usuario_id' => (int)($_SESSION['user_id'] ?? 0) ?: null,
        'codigo_generacion' => $codigoGeneracion,
        'emisor_nit' => $dteData['emisor']['nit'] ?? null,
        'numero_control' => $numeroControl,
        'tipo_dte' => $tipoDte,
        'fecha_emision' => ($dteData['identificacion']['fecEmi'] ?? date('Y-m-d')) . ' ' . ($dteData['identificacion']['horEmi'] ?? date('H:i:s')),
        'total_pagar' => $dteData['resumen']['totalPagar'] ?? 0,
        'documento_json' => $dteData,
        'estado_firma' => 'pendiente',
        'origen' => 'pos_interno'
    ]);
    if (!$res['success']) throw new Exception($res['error'] ?? 'Insert fallido');
    $pdo->commit();

    require_once __DIR__ . '/../../includes/signer/signer_local.php';
    $firmaBase64 = sign_document_local(['dte_json' => json_encode($dteData)], $codigoGeneracion, $numeroControl);
    if (!is_string($firmaBase64) || strlen($firmaBase64) < 100) throw new Exception('Firma local inválida');

    $db->update(['firma_local' => $firmaBase64], ['codigo_generacion' => $codigoGeneracion]);

    require_once __DIR__ . '/../../includes/signer/signer_goes.php';
    $goesResult = enviar_firma_gobierno($codigoGeneracion, $firmaBase64, $numeroControl);
    if (empty($goesResult['success']) || $goesResult['success'] !== true) throw new Exception('Error en recepción MH para ' . $codigoGeneracion);

    $db->update([
        'estado_firma' => $goesResult['estado'] ?? 'desconocido',
        'sello_recepcion' => $goesResult['sello'] ?? null
    ], ['codigo_generacion' => $codigoGeneracion]);

    require_once __DIR__ . '/../../includes/signer/utils/qr_maker.php';
    require_once __DIR__ . '/../../includes/signer/utils/ticket_printer.php';
    require_once __DIR__ . '/../../includes/signer/utils/pdf_generator.php';
    require_once __DIR__ . '/../../includes/signer/utils/mail_sender.php';

    $qr = generar_qr_mh(['codigoGeneracion' => $codigoGeneracion, 'numeroControl' => $numeroControl, 'sello' => $goesResult['sello'] ?? null]);
    $html = generar_ticket_html($dteData, $qr);
    $pdf = generar_factura_pdf($html, $codigoGeneracion);
    if ($correoCliente) enviar_factura_email($correoCliente, $pdf);

    try {
        if (!empty($dteData['receptor']['telefono']) && ($goesResult['estado'] ?? null) === 'PROCESADO') {
            require_once __DIR__ . '/../../includes/signer/utils/sms_notifier.php';
            enviar_sms_notificacion($dteData['receptor']['telefono'], 'Su factura electrónica ha sido enviada a su correo. Gracias por su compra.');
        }
    } catch (Throwable $smsErr) {
        error_log('[SMS_ERROR] ' . $smsErr->getMessage());
    }

    audit_log_final_summary($codigoGeneracion, $numeroControl, 'SUCCESS', [
        'company_id' => $companyId,
        'estado_mh' => $goesResult['estado'] ?? null,
        'sello_recibido' => $goesResult['sello'] ?? null,
        'total_pagar' => $dteData['resumen']['totalPagar'] ?? 0,
        'cliente' => $dteData['receptor']['nombre'] ?? 'CONSUMIDOR FINAL'
    ]);

    ob_clean();
    echo json_encode(['success' => true, 'codigoGeneracion' => $codigoGeneracion, 'numeroControl' => $numeroControl, 'estadoMH' => $goesResult['estado'] ?? null, 'selloRecepcion' => $goesResult['sello'] ?? null, 'respuestaMH' => $goesResult]);
} catch (Throwable $e) {
    if (isset($pdo)) {
        try { if (is_db_connected($pdo) && $pdo->inTransaction()) $pdo->rollBack(); } catch (Throwable $ignored) {}
    }
    error_log('process_sale_complete ERROR: ' . $e->getMessage());
    $codigo = $dteData['identificacion']['codigoGeneracion'] ?? 'UNKNOWN';
    $control = $dteData['identificacion']['numeroControl'] ?? 'UNKNOWN';
    audit_log_error($codigo, $control, 'Error durante proceso completo', $e->getMessage(), $e->getFile(), $e->getLine());
    ob_clean();
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
