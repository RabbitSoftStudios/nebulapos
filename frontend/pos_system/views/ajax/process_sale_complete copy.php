<?php
/**
 * AJAX: process_sale_complete.php
 * Procesamiento Completo de Venta con Firma Electrónica
 * Team MYTS
 */
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 0);

require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/supabase.php';
require_once __DIR__ . '/../../includes/pg_connection.php';

$input = file_get_contents('php://input');
$dteData = json_decode($input, true);

if (!$dteData || empty($dteData['identificacion']['codigoGeneracion'])) {
    echo json_encode(['success' => false, 'error' => 'DTE inválido']);
    exit;
}

try {
    $codigoGeneracion = $dteData['identificacion']['codigoGeneracion'];
    $tipoDte = $dteData['identificacion']['tipoDte'] ?? '01';
    $correoCliente = $dteData['receptor']['correo'] ?? null;

    // =====================================================
    // GENERAR NUMERO DE CONTROL (CON LOCK)
    // =====================================================
    $pdo = pg_pool();
    $pdo->beginTransaction();

    $lockStmt = $pdo->prepare("
        SELECT id FROM dte_facturas
        WHERE tipo_dte = :tipo
        ORDER BY created_at DESC
        LIMIT 1
        FOR UPDATE
    ");
    $lockStmt->execute([':tipo' => $tipoDte]);

    $stmt = $pdo->prepare("
        SELECT COALESCE(
            MAX(CAST(RIGHT(numero_control, 15) AS BIGINT)), 0
        ) + 1 AS next_num
        FROM dte_facturas
        WHERE tipo_dte = :tipo
        AND numero_control ~ '^[A-Z0-9-]+-[0-9]{15}$'
    ");
    $stmt->execute([':tipo' => $tipoDte]);
    $row = $stmt->fetch();

    $secuencial = str_pad($row['next_num'], 15, '0', STR_PAD_LEFT);
    $numeroControl = "DTE-{$tipoDte}-P001M001-{$secuencial}";
   
    // 🔥 ESTO ES LO QUE FALTABA
    $dteData['identificacion']['numeroControl'] = $numeroControl;
   
    // =====================================================
    // GUARDAR JSON ORIGINAL
    // =====================================================
    $dir = __DIR__ . '/../../storage/sigs/';
    if (!is_dir($dir)) mkdir($dir, 0777, true);

    file_put_contents(
        $dir . "dte_{$codigoGeneracion}.json",
        json_encode($dteData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
    );

    // =====================================================
    // INSERT FACTURA
    // =====================================================
    $db = supabase('dte_facturas');
    $res = $db->insert([
        'codigo_generacion' => $codigoGeneracion,
        'emisor_nit'        => $dteData['emisor']['nit'],
        'numero_control'    => $numeroControl,
        'tipo_dte'          => $tipoDte,
        'fecha_emision'     => $dteData['identificacion']['fecEmi'] . ' ' . $dteData['identificacion']['horEmi'],
        'total_pagar'       => $dteData['resumen']['totalPagar'],
        'documento_json'    => json_encode($dteData),
        'estado_firma'      => 'pendiente',
        'origen'            => 'pos_interno'
    ]);

    if (!$res['success']) {
        throw new Exception($res['error'] ?? 'Insert fallido');
    }

    $pdo->commit();

    // =====================================================
    // FIRMA LOCAL (BASE64)
    // =====================================================
    require_once __DIR__ . '/../../includes/signer/signer_local.php';

    if (empty($dteData['identificacion']['numeroControl'])) {
        throw new Exception(
            'numeroControl no está en el DTE antes de firmar (codigoGeneracion: ' . $codigoGeneracion . ')'
        );
    }

    /* $localSignResult = sign_document_local(
        ['dte_json' => json_encode($dteData)],
        $codigoGeneracion
    );

    if (empty($localSignResult['body'])) {
        throw new Exception('Firma local inválida');
    }

    $firmaBase64 = $localSignResult['body']; */

    $firmaBase64 = sign_document_local(
        ['dte_json' => json_encode($dteData)],
        $codigoGeneracion
    );

    if (!is_string($firmaBase64) || strlen($firmaBase64) < 100) {
        throw new Exception('Firma local inválida');
    }


    // Guardar firma local
    $db->update(
        ['firma_local' => $firmaBase64],
        ['codigo_generacion' => $codigoGeneracion]
    );

    // =====================================================
    // ENVÍO A GOBIERNO (SOLO BASE64)
    // =====================================================
    /* require_once __DIR__ . '/../../includes/goes_signer/signer_goes.php';

    //$goesResult = enviar_firma_gobierno($firmaBase64);
    $goesResult = enviar_firma_gobierno($codigoGeneracion);


    if (empty($goesResult['success']) || $goesResult['success'] !== true) {
        throw new Exception('Error en recepción MH');
    } */

        // =====================================================
// ENVÍO A GOBIERNO (MH)
// SE ENVÍA: codigoGeneracion + documento BASE64
// =====================================================
    require_once __DIR__ . '/../../includes/goes_signer/signer_goes.php';

    /**
     * CONTRATO CORRECTO:
     * enviar_firma_gobierno(
     *     string $codigoGeneracion,
     *     string $documentoFirmadoBase64
     * ): array
     */
    $goesResult = enviar_firma_gobierno(
        $codigoGeneracion,
        $firmaBase64
    );

    // Validación de respuesta MH
    if (
        empty($goesResult['success']) ||
        $goesResult['success'] !== true
    ) {
        throw new Exception(
            'Error en recepción MH para ' . $codigoGeneracion
        );
    }

    // =====================================================
    // UPDATE RESPUESTA MH
    // =====================================================
    /* $db->update([
        'sello_recepcion' => json_encode($goesResult),
        'estado_firma'    => $goesResult['estado'] ?? 'desconocido'
    ], [
        'codigo_generacion' => $codigoGeneracion
    ]); */
    // =====================================================
    // UPDATE RESPUESTA MH (CONFIRMACIÓN ORQUESTADOR)
    // =====================================================
    orchestrator_debug_log(
        $codigoGeneracion,
        'MH_RESPONSE_RECEIVED',
        [
            'estado' => $goesResult['estado'] ?? null,
            'sello'  => $goesResult['sello'] ?? null
        ]
    );

    /**
     * IMPORTANTE:
     * - signer_goes YA guardó respuesta_mh completa
     * - aquí SOLO sincronizamos campos clave
     */
    $db->update([
        'estado_firma'    => $goesResult['estado'] ?? 'desconocido',
        'sello_recepcion' => $goesResult['sello'] ?? null
    ], [
        'codigo_generacion' => $codigoGeneracion
    ]);

    orchestrator_debug_log(
        $codigoGeneracion,
        'DB_SYNC_OK'
    );


    // =====================================================
    // UTILIDADES POST-MH
    // =====================================================
    require_once __DIR__ . '/../../includes/utils/qr_maker.php';
    require_once __DIR__ . '/../../includes/utils/ticket_printer.php';
    require_once __DIR__ . '/../../includes/utils/pdf_generator.php';
    require_once __DIR__ . '/../../includes/utils/mail_sender.php';

    $qr = generar_qr_mh([
        'codigoGeneracion' => $codigoGeneracion,
        'numeroControl'    => $numeroControl,
        'sello'            => $goesResult['sello'] ?? null
    ]);

    $html = generar_ticket_html($dteData, $qr);
    $pdf  = generar_factura_pdf($html, $codigoGeneracion);

    if ($correoCliente) {
        enviar_factura_email($correoCliente, $pdf);
    }

    // =====================================================
    // NOTIFICACIÓN SMS (NO BLOQUEANTE)
    // =====================================================
    try {
        if (
            !empty($dteData['receptor']['telefono']) &&
            !empty($goesResult['estado']) &&
            $goesResult['estado'] === 'PROCESADO'
        ) {
            require_once __DIR__ . '/../../includes/utils/sms_notifier.php';

            $smsOk = enviar_sms_notificacion(
                $dteData['receptor']['telefono'],
                'Su factura electrónica ha sido enviada a su correo. Gracias por su compra.'
            );

            if (!$smsOk) {
                error_log('[SMS] Falló envío SMS para ' . $codigoGeneracion);
            }
        }
    } catch (Throwable $smsErr) {
        error_log('[SMS_ERROR] ' . $smsErr->getMessage());
    }

    // =====================================================
    // RESPUESTA FINAL (ÉXITO)
    // =====================================================
    echo json_encode([
        'success'          => true,
        'codigoGeneracion' => $codigoGeneracion,
        'numeroControl'    => $numeroControl,
        'estadoMH'         => $goesResult['estado'] ?? null,
        'selloRecepcion'   => $goesResult['sello'] ?? null,
        'respuestaMH'      => $goesResult
    ]);

} catch (Exception $e) {

    if (isset($pdo) && $pdo->inTransaction()) {
        $pdo->rollBack();
    }

    error_log('process_sale_complete ERROR: ' . $e->getMessage());

    // =====================================================
    // RESPUESTA FINAL (ERROR)
    // =====================================================
    echo json_encode([
        'success' => false,
        'error'   => $e->getMessage()
    ]);
}