<?php
/**
 * AJAX: process_sale_complete.php
 * Procesamiento Completo de Venta con Firma Electrónica
 * Team MYTS
 */
// Iniciar buffer de salida para evitar que warnings/notices rompan el JSON
ob_start();

// REGISTRAR SHUTDOWN HANDLER
register_shutdown_function(function() {
    $error = error_get_last();
    if ($error && ($error['type'] === E_ERROR || $error['type'] === E_PARSE || $error['type'] === E_CORE_ERROR || $error['type'] === E_COMPILE_ERROR)) {
        // En caso de error fatal, limpiamos buffer y enviamos JSON de error
        if (ob_get_length()) ob_clean();
        header('Content-Type: application/JSON');
        echo json_encode([
            'success' => false,
            'error' => 'FATAL PHP ERROR: ' . $error['message'] . ' in ' . $error['file'] . ':' . $error['line']
        ]);
    }
});

header('Content-Type: application/JSON'); // User specific casing
error_reporting(E_ALL);
ini_set('display_errors', 0);

require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/supabase.php';
require_once __DIR__ . '/../../includes/pg_connection.php';
require_once __DIR__ . '/../../includes/signer/utils/audit_logger.php';
require_once __DIR__ . '/../../includes/signer/utils/dte_validator_new.php';

$input = file_get_contents('php://input');
$dteData = json_decode($input, true);

// Limpiar buffer si hay errores tempranos
if (!$dteData || empty($dteData['identificacion']['codigoGeneracion'])) {
    ob_clean();
    echo json_encode(['success' => false, 'error' => 'DTE inválido']);
    exit;
}

try {
    $codigoGeneracion = $dteData['identificacion']['codigoGeneracion'];
    $tipoDte = $dteData['identificacion']['tipoDte'] ?? '01';
    $correoCliente = $dteData['receptor']['correo'] ?? null;

    // =====================================================
    // GENERAR NUMERO DE CONTROL PRIMERO (CON LOCK)
    // DEBE HACERSE ANTES DE LA VALIDACIÓN
    // =====================================================
    $pdo = pg_pool();
    
    // Validar conexión antes de iniciar transacción
    if (!is_db_connected($pdo)) {
        throw new Exception('Database connection unavailable. Please try again.');
    }
    
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
   
    // ASIGNAR numeroControl AL JSON ANTES DE VALIDAR
    $dteData['identificacion']['numeroControl'] = $numeroControl;
   
    // =====================================================
    // AHORA SÍ: VALIDAR JSON DTE (CON numeroControl PRESENTE)
    // =====================================================
    $validacion = validar_json_dte_nuevo($dteData);
    if (!$validacion['valid']) {
        $pdo->rollBack();
        ob_clean();
        error_log('DTE VALIDATION FAILED: ' . json_encode($validacion['errors']));
        echo json_encode([
            'success' => false,
            'error' => 'JSON DTE inválido: ' . implode(' | ', array_slice($validacion['errors'], 0, 3)),
            'validation_errors' => $validacion['errors'],
            'model' => 'CONSUMIDOR_FINAL_CON_IVA_INCLUIDO'
        ]);
        exit;
    }
    
    // Log de validación exitosa
    error_log('DTE VALIDATION PASSED for codigoGeneracion: ' . $codigoGeneracion . ' | numeroControl: ' . $numeroControl);
   
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
        'documento_json'    => $dteData,
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

    $firmaBase64 = sign_document_local(
        ['dte_json' => json_encode($dteData)],
        $codigoGeneracion,
        $numeroControl
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
    // ENVÍO A GOBIERNO (MH)
    // SE ENVÍA: codigoGeneracion + documento BASE64
    // =====================================================
    require_once __DIR__ . '/../../includes/signer/signer_goes.php';

    /**
     * CONTRATO CORRECTO:
     * enviar_firma_gobierno(
     *     string $codigoGeneracion,
     *     string $documentoFirmadoBase64,
     *     string $numeroControl = ''
     * ): array
     */
    $goesResult = enviar_firma_gobierno(
        $codigoGeneracion,
        $firmaBase64,
        $numeroControl
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
    // UPDATE RESPUESTA MH (CONFIRMACIÓN ORQUESTADOR)
    // =====================================================
    error_log("MH_RESPONSE_RECEIVED [$codigoGeneracion]: " . json_encode([
        'estado' => $goesResult['estado'] ?? null,
        'sello'  => $goesResult['sello'] ?? null
    ]));

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

    error_log("DB_SYNC_OK [$codigoGeneracion]");


    // =====================================================
    // UTILIDADES POST-MH
    // =====================================================
    require_once __DIR__ . '/../../includes/signer/utils/qr_maker.php';
    require_once __DIR__ . '/../../includes/signer/utils/ticket_printer.php';
    require_once __DIR__ . '/../../includes/signer/utils/pdf_generator.php';
    require_once __DIR__ . '/../../includes/signer/utils/mail_sender.php';

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
            require_once __DIR__ . '/../../includes/signer/utils/sms_notifier.php';

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
    
    // LOG DE AUDITORÍA: Resumen final exitoso
    $codigoGeneracion_var = $dteData['identificacion']['codigoGeneracion'];
    $numeroControl_var = $dteData['identificacion']['numeroControl'];
    audit_log_final_summary($codigoGeneracion_var, $numeroControl_var, 'SUCCESS', [
        'estado_mh' => $goesResult['estado'] ?? null,
        'sello_recibido' => $goesResult['sello'] ?? null,
        'total_pagar' => $dteData['resumen']['totalPagar'],
        'cliente' => $dteData['receptor']['nombre'] ?? 'CONSUMIDOR FINAL'
    ]);
    
    ob_clean(); // Limpiar todo output previo
    echo json_encode([
        'success'          => true,
        'codigoGeneracion' => $codigoGeneracion,
        'numeroControl'    => $numeroControl,
        'estadoMH'         => $goesResult['estado'] ?? null,
        'selloRecepcion'   => $goesResult['sello'] ?? null,
        'respuestaMH'      => $goesResult
    ]);

} catch (Throwable $e) {

    // =====================================================
    // ROLLBACK SEGURO (Validar conexión primero)
    // =====================================================
    if (isset($pdo)) {
        try {
            // Verificar que la conexión esté activa antes de intentar rollback
            if (is_db_connected($pdo) && $pdo->inTransaction()) {
                $pdo->rollBack();
                error_log('Transaction rolled back successfully for error: ' . $e->getMessage());
            } else {
                error_log('Could not rollback: Connection lost or no active transaction');
            }
        } catch (Throwable $rollback_error) {
            // Si el rollback falla, registrar pero no lanzar error
            error_log('WARN: Rollback failed: ' . $rollback_error->getMessage());
        }
    }

    error_log('process_sale_complete ERROR: ' . $e->getMessage());
    
    // LOG DE AUDITORÍA: Resumen final con error
    $codigoGeneracion_error = $dteData['identificacion']['codigoGeneracion'] ?? 'UNKNOWN';
    $numeroControl_error = $dteData['identificacion']['numeroControl'] ?? 'UNKNOWN';
    audit_log_error($codigoGeneracion_error, $numeroControl_error, 'Error durante proceso completo', $e->getMessage(), $e->getFile(), $e->getLine());

    // =====================================================
    // RESPUESTA FINAL (ERROR)
    // =====================================================
    ob_clean(); // Limpiar todo output previo
    echo json_encode([
        'success' => false,
        'error'   => $e->getMessage()
    ]);
}