<?php
/**
 * Servicio de Firma Local de Documentos Electrónicos (ENCAPSULADO)
 * Team MYTS
 */

function sign_document_local(array $invoice, string $codigoGeneracion, $userId = null): array
{
    // === PROTECCIÓN TOTAL DE SALIDA ===
    ob_start();

    try {

        // ===============================
        // CONFIGURACIÓN
        // ===============================
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

        $dteJson = json_decode($invoice['dte_json'], true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('JSON DTE inválido: ' . json_last_error_msg());
        }

        // ===============================
        // ASEGURAR CODIGO GENERACION
        // ===============================
        $dteJson['identificacion']['codigoGeneracion'] = $codigoGeneracion;

        // ===============================
        // PAYLOAD FIRMA
        // ===============================
        $payload = [
            'nit'         => $config['NIT'],
            'activo'      => true,
            'passwordPri' => $config['PRIVATE_KEY'],
            'dteJson'     => $dteJson
        ];

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

        $signed = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('Respuesta firma no es JSON válido');
        }

        if (empty($signed['body'])) {
            throw new Exception('Firma local sin cuerpo firmado');
        }

        // ===============================
        // VALIDACIÓN FIRMA (ANTES DE MH)
        // ===============================
        if (!is_string($signed['body']) || strlen($signed['body']) < 100) {
            throw new Exception('Firma local inválida o incompleta');
        }

        // ===============================
        // ACTUALIZAR BD (SILENCIADO)
        // ===============================
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
            error_log('[FIRMA_LOCAL_DB] ' . $dbErr->getMessage());
        }

        // ===============================
        // LOG DEV (NO PRODUCCIÓN)
        // ===============================
        if (defined('DEBUG_MODE') && DEBUG_MODE) {
            error_log('[FIRMA_LOCAL_OK] ' . $codigoGeneracion);
            error_log('[FIRMA_LOCAL_BODY_PREVIEW] ' . substr($signed['body'], 0, 120));
        }

        return $signed;

    } catch (Throwable $e) {

        if (defined('DEBUG_MODE') && DEBUG_MODE) {
            error_log('[FIRMA_LOCAL_ERROR] ' . $e->getMessage());
        }

        throw new Exception('Error firma local: ' . $e->getMessage());

    } finally {
        // LIMPIEZA ABSOLUTA
        ob_end_clean();
    }
}
