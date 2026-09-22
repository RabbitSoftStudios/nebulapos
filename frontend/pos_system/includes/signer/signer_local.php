<?php
/**
 * Archivo: /includes/signer/signer_local.php
 * Servicio de Firma Local de Documentos Electrónicos
 *
 * Flujo:
 *  - Normaliza DTE
 *  - Valida contra schema MH
 *  - Firma local (servicio local)
 *  - Retorna SOLO la cadena BASE64 firmada
 *
 * Team: MYTS Cloud Computing
 */

require_once __DIR__ . '/utils/audit_logger.php';

/**
 * Archivo de log para debugging del firmador local
 */
define('LOCAL_SIGNER_LOG_FILE', __DIR__ . '/signer_local_debug.log');

function local_signer_log(string $msg, array $data = []): void {
    $entry = date('Y-m-d H:i:s') . " | $msg | " . json_encode($data, JSON_UNESCAPED_UNICODE) . PHP_EOL;
    file_put_contents(LOCAL_SIGNER_LOG_FILE, $entry, FILE_APPEND | LOCK_EX);
}

function sign_document_local(array $invoice, string $codigoGeneracion, string $numeroControl = ''): string
{
    // Blindaje total: nada debe salir al cliente
    ob_start();

    try {
        local_signer_log("INIT", ['codigoGeneracion' => $codigoGeneracion, 'numeroControl' => $numeroControl]);

        /* ======================================================
         * 1. CONFIGURACIÓN
         * ====================================================== */
        $config = require __DIR__ . '/utils/config.php';

        if (empty($config['NIT']) || strlen($config['NIT']) !== 14) {
            throw new Exception('NIT inválido en configuración');
        }

        if (empty($config['PRIVATE_KEY'])) {
            throw new Exception('PRIVATE_KEY no configurada');
        }

        if (empty($invoice['dte_json'])) {
            throw new Exception('DTE JSON vacío');
        }

        /* ======================================================
         * 2. DECODIFICAR DTE
         * ====================================================== */
        $dte = json_decode($invoice['dte_json'], true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('JSON DTE inválido: ' . json_last_error_msg());
        }

        // Asegurar código de generación
        $dte['identificacion']['codigoGeneracion'] = $codigoGeneracion;
        
        // LOG: Capturar JSON DTE ANTES de normalizarlo y enviarlo a firma
        if (!empty($numeroControl)) {
            audit_log_json_generated($codigoGeneracion, $numeroControl, $dte);
        }

        /* ======================================================
         * 3. NORMALIZAR Y VALIDAR
         * ====================================================== */
        require_once __DIR__ . '/utils/normalizer.php';
        require_once __DIR__ . '/utils/schema_validator.php';

        // NOTE: Functions validated by user as working correctly.
        $dte = normalizarDTE($dte);
        validarDTEContraSchema($dte, __DIR__ . '/schemas/fe-fc-v1.json');

        local_signer_log("VALIDATION_OK", ['dte_keys' => array_keys($dte)]);

        /* ======================================================
         * 4. PAYLOAD PARA FIRMADOR LOCAL
         * ====================================================== */
        // NOTE: 'activo' => true is legacy, but keeping it for compatibility
        $payload = [
            'nit'         => $config['NIT'],
            'activo'      => true,
            'passwordPri' => $config['PRIVATE_KEY'],
            'dteJson'     => $dte
        ];
        
        // Log payload size for info
        local_signer_log("PAYLOAD_READY", [
            'size' => strlen(json_encode($payload)),
            'nit' => $config['NIT'],
            'dteJson_keys' => array_keys($dte)
        ]);

        /* ======================================================
         * 5. ENVÍO A SERVICIO DE FIRMA LOCAL (REAL)
         * ====================================================== */
        $context = stream_context_create([
            'http' => [
                'method'  => 'POST',
                'header'  => "Content-Type: application/JSON\r\n",
                'content' => json_encode($payload, JSON_UNESCAPED_UNICODE),
                'timeout' => 30
            ]
        ]);

        $response = file_get_contents(
            'http://localhost:8113/firmardocumento/',
            false,
            $context
        );

        if ($response === false) {
            $error = error_get_last();
            local_signer_log("ERROR_HTTP", ['error' => $error]);
            if (!empty($numeroControl)) {
                audit_log_error($codigoGeneracion, $numeroControl, 'Error HTTP del firmador local', json_encode($error));
            }
            throw new Exception('No hubo respuesta del firmador local: ' . ($error['message'] ?? 'Unknown'));
        }

        /* ======================================================
         * 6. VALIDAR RESPUESTA DEL FIRMADOR
         * ====================================================== */
        $signed = json_decode($response, true);

        if (
            json_last_error() !== JSON_ERROR_NONE ||
            empty($signed['body']) ||
            !is_string($signed['body']) ||
            strlen($signed['body']) < 100
        ) {
            local_signer_log("INVALID_RESPONSE", ['response_preview' => substr($response, 0, 200)]);
            if (!empty($numeroControl)) {
                audit_log_error(
                    $codigoGeneracion, 
                    $numeroControl, 
                    'Respuesta inválida del firmador local',
                    substr($response, 0, 500)
                );
            }
            throw new Exception('Firma local inválida o vacía');
        }
        
        local_signer_log("SUCCESS", ['signature_length' => strlen($signed['body'])]);
        
        // LOG: Respuesta del Firmador Local
        if (!empty($numeroControl)) {
            audit_log_firma_local_response(
                $codigoGeneracion,
                $numeroControl,
                [
                    'success' => true,
                    'body' => $signed['body']
                ],
                $signed['body']
            );
        }

        /* ======================================================
         * 7. GUARDAR FIRMA LOCAL EN BD (NO BLOQUEANTE)
         * ====================================================== */
        try {
            // Re-require to ensure connection exists if not already
            require __DIR__ . '/utils/pg_connection.php';
            // Use global if available or logic from pg_connection
            // Ensure connection
            $SUPABASE_PDO = pg_pool();

            if ($SUPABASE_PDO) {
                 $stmt = $SUPABASE_PDO->prepare("
                    UPDATE dte_facturas
                    SET firma_local = :firma,
                        estado_firma = 'firmado_local',
                        updated_at = NOW()
                    WHERE codigo_generacion = :codigo
                ");

                $stmt->execute([
                    ':firma'  => $signed['body'],
                    ':codigo' => $codigoGeneracion
                ]);
            }
        } catch (Throwable $dbErr) {
            local_signer_log("DB_UPDATE_ERROR", ['msg' => $dbErr->getMessage()]);
            // Non-blocking error
        }

        /* ======================================================
         * RETORNO CANÓNICO
         * ====================================================== */
        return $signed['body'];

    } catch (Throwable $e) {
        local_signer_log("FATAL_ERROR", ['msg' => $e->getMessage()]);
        if (!empty($numeroControl)) {
            audit_log_error(
                $codigoGeneracion, 
                $numeroControl, 
                'Error fatal en firma local',
                $e->getMessage(),
                $e->getFile(),
                $e->getLine()
            );
        }
        throw new Exception('Error firma local: ' . $e->getMessage());
    } finally {
        ob_end_clean();
    }
}
