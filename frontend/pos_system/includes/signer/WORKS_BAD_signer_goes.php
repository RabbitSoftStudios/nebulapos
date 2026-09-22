<?php
/**
 * ENVÍO DE DTE FIRMADO AL MINISTERIO DE HACIENDA (GOES)
 * Team MYTS
 * Fecha: 2026-01-08
 *
 * Este archivo:
 * - Consume token desde token_manager.php
 * - Envía SOLO la cadena BASE64 firmada
 * - Guarda auditoría
 * - Guarda respuesta MH
 * - Actualiza dte_facturas
 */

require_once __DIR__ . '/../utils/config.php';
require_once __DIR__ . '/../utils/pg_connection.php';

require_once __DIR__ . '/token_manager.php';
require_once __DIR__ . '/curl_client.php';
require_once __DIR__ . '/audit_logger.php';
require_once __DIR__ . '/storage_manager.php';

function enviar_firma_gobierno(string $codigoGeneracion): array
{
    ob_start();

    try {
        $config = require __DIR__ . '/../utils/config.php';

        // ===============================
        // 1. OBTENER TOKEN
        // ===============================
        $token = obtener_token_valido();

        if (empty($token)) {
            throw new Exception('No se pudo obtener token MH');
        }

        $token = str_replace('Bearer ', '', $token);

        // ===============================
        // 2. OBTENER DTE DESDE BD
        // ===============================
        global $SUPABASE_PDO;

        $stmt = $SUPABASE_PDO->prepare("
            SELECT
                codigo_generacion,
                tipo_dte,
                firma_local,
                documento_json
            FROM dte_facturas
            WHERE codigo_generacion = :codigo
            LIMIT 1
        ");

        $stmt->execute([':codigo' => $codigoGeneracion]);
        $dte = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$dte) {
            throw new Exception('DTE no encontrado en base de datos');
        }

        if (empty($dte['firma_local'])) {
            throw new Exception('DTE no tiene firma local');
        }

        $jsonOriginal = json_decode($dte['documento_json'], true);

        if (!$jsonOriginal) {
            throw new Exception('JSON original inválido en BD');
        }

        // ===============================
        // 3. PREPARAR PAYLOAD MH
        // ===============================
        $payload = [
            'ambiente' => $config['AMBIENTE'] ?? '00',
            'idEnvio'  => 1,
            'version'  => (int)$jsonOriginal['identificacion']['version'],
            'tipoDte'  => $dte['tipo_dte'],
            'documento'=> $dte['firma_local'],
            'codigoGeneracion' => $codigoGeneracion,
            'user' => $jsonOriginal['emisor']['nit']
        ];

        // Auditoría
        AuditLogger::log('prepared_payload', $codigoGeneracion, [
            'tipoDte' => $dte['tipo_dte'],
            'ambiente' => $payload['ambiente'],
            'document_length' => strlen($dte['firma_local'])
        ]);

        // ===============================
        // 4. GUARDAR BASE64 EN LOCAL
        // ===============================
        StorageManager::storeBase64($codigoGeneracion, $dte['firma_local']);

        // ===============================
        // 5. ENVÍO A MH
        // ===============================
        $url = ($payload['ambiente'] === '01')
            ? $config['API_SIGNER_PROD_URL']
            : $config['API_SIGNER_TEST_URL'];

        $response = CurlClient::postJson(
            $url,
            $payload,
            [
                "Authorization: Bearer {$token}",
                "Content-Type: application/json",
                "Accept: application/json"
            ]
        );

        // ===============================
        // 6. PROCESAR RESPUESTA
        // ===============================
        if (!$response['success']) {
            throw new Exception('Error comunicación MH: ' . $response['error']);
        }

        $data = $response['data'];

        AuditLogger::log('mh_response', $codigoGeneracion, $data);

        // ===============================
        // 7. ACTUALIZAR BD
        // ===============================
        $stmt = $SUPABASE_PDO->prepare("
            UPDATE dte_facturas
            SET
                estado_firma = :estado,
                sello_recepcion = :sello,
                respuesta_mh = :respuesta,
                updated_at = NOW()
            WHERE codigo_generacion = :codigo
        ");

        $stmt->execute([
            ':estado' => ($data['estado'] ?? '') === 'PROCESADO' ? 'procesado' : 'rechazado',
            ':sello' => $data['selloRecibido'] ?? null,
            ':respuesta' => json_encode($data),
            ':codigo' => $codigoGeneracion
        ]);

        return [
            'success' => true,
            'codigoGeneracion' => $codigoGeneracion,
            'estado' => $data['estado'] ?? null,
            'sello' => $data['selloRecibido'] ?? null,
            'respuesta' => $data
        ];

    } catch (Throwable $e) {

        AuditLogger::log('error', $codigoGeneracion, [
            'message' => $e->getMessage()
        ]);

        throw $e;

    } finally {
        ob_end_clean();
    }
}
