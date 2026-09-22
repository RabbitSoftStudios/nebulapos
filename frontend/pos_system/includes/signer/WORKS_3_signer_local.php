<?php
/**
 * Archivo: /includes/signer/signer_local.php
 * Servicio de Firma Local de Documentos Electrónicos (ENCAPSULADO)
 *
 * Flujo:
 *  - Normaliza DTE
 *  - Valida contra schema MH
 *  - Firma local
 *  - Retorna SOLO la cadena firmada (base64)
 *
 * Fecha: 2026-01-08
 * Team: MYTS Cloud Computing
 */

function sign_document_local(array $invoice, string $codigoGeneracion, $userId = null): array
{
    // Blindaje absoluto de salida
    ob_start();

    try {

        /* ======================================================
         * 1. CONFIGURACIÓN
         * ====================================================== */
        $config = require __DIR__ . '/utils/config.php';

        if (
            empty($config['NIT']) ||
            strlen($config['NIT']) !== 14 ||
            !ctype_digit($config['NIT'])
        ) {
            throw new Exception('NIT inválido en configuración');
        }

        if (empty($config['PRIVATE_KEY'])) {
            throw new Exception('Clave privada no configurada');
        }

        if (empty($invoice['dte_json'])) {
            throw new Exception('JSON de DTE vacío');
        }

        /* ======================================================
         * 2. DECODIFICAR JSON UNA SOLA VEZ
         * ====================================================== */
        $dteJson = json_decode($invoice['dte_json'], true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('JSON DTE inválido: ' . json_last_error_msg());
        }

        /* ======================================================
         * 3. ASEGURAR CODIGO GENERACION
         * ====================================================== */
        if (!isset($dteJson['identificacion'])) {
            $dteJson['identificacion'] = [];
        }

        $dteJson['identificacion']['codigoGeneracion'] = $codigoGeneracion;

        /* ======================================================
         * 4. NORMALIZAR DTE
         * ====================================================== */
        require_once __DIR__ . '/utils/normalizer.php';
        $dteJson = normalizarDTE($dteJson);

        /* ======================================================
         * 5. VALIDAR CONTRA SCHEMA MH
         * ====================================================== */
        require_once __DIR__ . '/utils/schema_validator.php';
        $schemaPath = __DIR__ . '/schemas/fe-fc-v1.json';
        validarDTEContraSchema($dteJson, $schemaPath);

        /* ======================================================
         * 6. ARMAR PAYLOAD DE FIRMA
         * ====================================================== */
        $payload = [
            'nit'         => $config['NIT'],
            'activo'      => true,
            'passwordPri' => $config['PRIVATE_KEY'],
            'dteJson'     => $dteJson
        ];

        /* ======================================================
         * 7. ENVIAR A SERVICIO DE FIRMA LOCAL
         * ====================================================== */
        $context = stream_context_create([
            'http' => [
                'method'  => 'POST',
                'header'  => "Content-Type: application/json\r\n",
                'content' => json_encode($payload, JSON_UNESCAPED_UNICODE),
                'timeout' => 30
            ]
        ]);

        $response = @file_get_contents(
            'http://localhost:8113/firmardocumento/',
            false,
            $context
        );

        if ($response === false) {
            $err = error_get_last();
            throw new Exception('Error servicio firma local: ' . ($err['message'] ?? 'desconocido'));
        }

        /* ======================================================
         * DTE. VALIDAR RESPUESTA DEL FIRMADOR DEBUGER AXELCRASH
         * ====================================================== */
        // DEBUGGER ROBUSTO - Escribe a archivo dedicado para diagnóstico
        $debugLogFile = __DIR__ . '/debug_axelcrash.log';
        $debugTimestamp = date('Y-m-d H:i:s');
        $debugLog = "\n" . str_repeat('=', 80) . "\n";
        $debugLog .= "[{$debugTimestamp}] INICIO DEBUG RESPUESTA FIRMADOR\n";
        $debugLog .= str_repeat('=', 80) . "\n";
        
        // Info de la respuesta
        $debugLog .= "LONGITUD RESPUESTA: " . strlen($response) . " bytes\n";
        $debugLog .= "TIPO RESPUESTA: " . gettype($response) . "\n";
        $debugLog .= "ES VACIA: " . (empty($response) ? 'SI' : 'NO') . "\n";
        
        // Primeros 500 caracteres
        $debugLog .= "\nPRIMEROS 500 CARACTERES:\n";
        $debugLog .= ">>>" . substr($response, 0, 500) . "<<<\n";
        
        // Caracteres hexadecimales de los primeros 50 bytes (detectar BOM, caracteres ocultos)
        $debugLog .= "\nHEX PRIMEROS 50 BYTES:\n";
        $debugLog .= bin2hex(substr($response, 0, 50)) . "\n";
        
        // Intentar parsear JSON
        $testParse = json_decode($response, true);
        $jsonError = json_last_error();
        $jsonErrorMsg = json_last_error_msg();
        $debugLog .= "\nJSON PARSE TEST:\n";
        $debugLog .= "RESULTADO: " . ($jsonError === JSON_ERROR_NONE ? 'EXITOSO' : 'FALLIDO') . "\n";
        $debugLog .= "ERROR CODE: {$jsonError}\n";
        $debugLog .= "ERROR MSG: {$jsonErrorMsg}\n";
        
        // Si hay datos parseados, mostrar estructura
        if ($testParse !== null) {
            $debugLog .= "KEYS DISPONIBLES: " . implode(', ', array_keys($testParse)) . "\n";
            if (isset($testParse['body'])) {
                $debugLog .= "BODY EXISTE: SI, longitud=" . strlen($testParse['body']) . "\n";
            }
            if (isset($testParse['status'])) {
                $debugLog .= "STATUS: " . $testParse['status'] . "\n";
            }
        }
        
        // Info del request que se envió
        $debugLog .= "\nPAYLOAD ENVIADO (primeros 1000 chars):\n";
        $payloadStr = json_encode($payload, JSON_UNESCAPED_UNICODE);
        $debugLog .= substr($payloadStr, 0, 1000) . "\n";
        
        // Headers de respuesta HTTP si están disponibles
        if (isset($http_response_header)) {
            $debugLog .= "\nHTTP RESPONSE HEADERS:\n";
            foreach ($http_response_header as $header) {
                $debugLog .= "  " . $header . "\n";
            }
        }
        
        $debugLog .= str_repeat('=', 80) . "\n";
        $debugLog .= "[{$debugTimestamp}] FIN DEBUG\n";
        $debugLog .= str_repeat('=', 80) . "\n";
        
        // Escribir al archivo de log
        file_put_contents($debugLogFile, $debugLog, FILE_APPEND | LOCK_EX);
        
        // También escribir al error_log de PHP
        error_log("[AXELCRASH] Debug escrito en: {$debugLogFile}");
        /* ======================================================
         * FIN DEBUGER AXELCRASH
         * ====================================================== */

        /* ======================================================
         * 8. VALIDAR RESPUESTA DEL FIRMADOR
         * ====================================================== */
        $signed = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('Respuesta del firmador no es JSON válido');
        }

        if (
            !isset($signed['body']) ||
            !is_string($signed['body']) ||
            strlen($signed['body']) < 100
        ) {
            throw new Exception('Firma local inválida o incompleta');
        }

        /* ======================================================
         * 9. ACTUALIZAR BD (NO BLOQUEANTE)
         * ====================================================== */
        try {
            require_once __DIR__ . '/utils/pg_connection.php';

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

        } catch (Throwable $dbErr) {
            error_log('[FIRMA_LOCAL_DB_ERROR] ' . $dbErr->getMessage());
        }

        /* ======================================================
         * 10. LOG DEV
         * ====================================================== */
        if (defined('DEBUG_MODE') && DEBUG_MODE) {
            error_log('[FIRMA_LOCAL_OK] ' . $codigoGeneracion);
            error_log('[FIRMA_LOCAL_PREVIEW] ' . substr($signed['body'], 0, 120));
        }

        return $signed;

    } catch (Throwable $e) {

        if (defined('DEBUG_MODE') && DEBUG_MODE) {
            error_log('[FIRMA_LOCAL_ERROR] ' . $e->getMessage());
        }

        throw new Exception('Error firma local: ' . $e->getMessage());

    } finally {
        ob_end_clean();
    }
}
