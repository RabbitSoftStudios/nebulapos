<?php
/**
 * ENVÍO DE DTE FIRMADO AL MINISTERIO DE HACIENDA (GOES)
 * Team MYTS
 *
 * DEBUG FORENSE ACTIVADO
 * --------------------------------------------------
 * - Log único: axelcrashed_debug.log
 * - Máximo 20 entradas (rotativo)
 * - NO output buffering
 * - NO echo / print
 * - NO contaminación de respuesta AJAX
 */
/* ======================================================
 * DEBUG AXELCRASH - ENTRADA signer_goes.php
 * Este log permite saber si el endpoint se ejecuta
 * y qué datos reales está recibiendo desde el frontend
 * ====================================================== */

$debugFile = __DIR__ . '/axelcrashed_debug.log';
$ts = date('Y-m-d H:i:s');

$rawInput = file_get_contents('php://input');

$log  = "\n" . str_repeat('=', 80) . "\n";
$log .= "[{$ts}] ENTRADA signer_goes.php\n";
$log .= str_repeat('=', 80) . "\n";

$log .= "REQUEST_METHOD: " . ($_SERVER['REQUEST_METHOD'] ?? 'N/A') . "\n";
$log .= "CONTENT_TYPE: " . ($_SERVER['CONTENT_TYPE'] ?? 'N/A') . "\n";
$log .= "CONTENT_LENGTH: " . ($_SERVER['CONTENT_LENGTH'] ?? 'N/A') . "\n";

$log .= "\nRAW INPUT (primeros 2000 chars):\n";
$log .= substr($rawInput, 0, 2000) . "\n";

$log .= "\nHEX RAW INPUT (primeros 100 bytes):\n";
$log .= bin2hex(substr($rawInput, 0, 100)) . "\n";

$decoded = json_decode($rawInput, true);
$log .= "\nJSON DECODE RESULT:\n";
$log .= "JSON ERROR: " . json_last_error() . " - " . json_last_error_msg() . "\n";

if (is_array($decoded)) {
    $log .= "TOP LEVEL KEYS: " . implode(', ', array_keys($decoded)) . "\n";

    if (isset($decoded['codigoGeneracion'])) {
        $log .= "codigoGeneracion: " . $decoded['codigoGeneracion'] . "\n";
    } else {
        $log .= "codigoGeneracion: NO EXISTE\n";
    }

    if (isset($decoded['dte_json'])) {
        $log .= "dte_json: EXISTE (len=" . strlen($decoded['dte_json']) . ")\n";
    } else {
        $log .= "dte_json: NO EXISTE\n";
    }
} else {
    $log .= "JSON NO ES ARRAY\n";
}

$log .= str_repeat('=', 80) . "\n";

file_put_contents($debugFile, $log, FILE_APPEND | LOCK_EX);


/**
 * ENVÍO DE DTE FIRMADO AL MINISTERIO DE HACIENDA (GOES)
 * Team MYTS
 */


require_once __DIR__ . '/../utils/config.php';
require_once __DIR__ . '/../utils/pg_connection.php';

require_once __DIR__ . '/token_manager.php';
require_once __DIR__ . '/curl_client.php';
require_once __DIR__ . '/audit_logger.php';
require_once __DIR__ . '/storage_manager.php';

/**
 * Logger forense rotativo (máx 20 entradas)
 */
function axel_debug_log(string $codigoGeneracion, string $step, array $data = []): void
{
    //$logFile = __DIR__ . '/axelcrashed_debug.log';
    $logFile = dirname(__DIR__) . '/signer/axelcrashed_debug.log';


    $entry = [
        'timestamp' => date('Y-m-d H:i:s'),
        'codigoGeneracion' => $codigoGeneracion,
        'step' => $step,
        'data' => $data
    ];

    $logs = [];

    if (file_exists($logFile)) {
        $content = file_get_contents($logFile);
        $logs = json_decode($content, true);
        if (!is_array($logs)) {
            $logs = [];
        }
    }

    // Agregar nuevo log
    $logs[] = $entry;

    // Mantener solo los últimos 20
    if (count($logs) > 20) {
        $logs = array_slice($logs, -20);
    }

    file_put_contents(
        $logFile,
        json_encode($logs, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)
    );
}

function enviar_firma_gobierno(string $codigoGeneracion): array
{
    try {
        axel_debug_log($codigoGeneracion, 'START', [
            'message' => 'Inicio de enviar_firma_gobierno'
        ]);

        $config = require __DIR__ . '/../utils/config.php';

        // ======================================================
        // 1. TOKEN
        // ======================================================
        $token = obtener_token_valido();

        axel_debug_log($codigoGeneracion, 'TOKEN_OBTAINED', [
            'token_length' => strlen((string)$token)
        ]);

        if (empty($token)) {
            throw new Exception('No se pudo obtener token MH');
        }

        $token = str_replace('Bearer ', '', $token);

        // ======================================================
        // 2. DTE BD
        // ======================================================
        global $SUPABASE_PDO;

        $stmt = $SUPABASE_PDO->prepare("
            SELECT codigo_generacion, tipo_dte, firma_local, documento_json
            FROM dte_facturas
            WHERE codigo_generacion = :codigo
            LIMIT 1
        ");
        $stmt->execute([':codigo' => $codigoGeneracion]);
        $dte = $stmt->fetch(PDO::FETCH_ASSOC);

        axel_debug_log($codigoGeneracion, 'DB_FETCH', [
            'found' => (bool)$dte
        ]);

        if (!$dte) {
            throw new Exception('DTE no encontrado en BD');
        }

        if (empty($dte['firma_local'])) {
            throw new Exception('DTE sin firma local');
        }

        $jsonOriginal = json_decode($dte['documento_json'], true);
        if (!$jsonOriginal) {
            throw new Exception('JSON original inválido');
        }

        // ======================================================
        // 3. PAYLOAD MH
        // ======================================================
        $payload = [
            'ambiente' => $config['AMBIENTE'] ?? '00',
            'idEnvio' => 1,
            'version' => (int)$jsonOriginal['identificacion']['version'],
            'tipoDte' => $dte['tipo_dte'],
            'documento' => $dte['firma_local'],
            'codigoGeneracion' => $codigoGeneracion,
            'user' => $jsonOriginal['emisor']['nit']
        ];

        axel_debug_log($codigoGeneracion, 'PAYLOAD_READY', [
            'ambiente' => $payload['ambiente'],
            'tipoDte' => $payload['tipoDte'],
            'firma_length' => strlen($dte['firma_local'])
        ]);

        // ======================================================
        // 4. STORAGE LOCAL
        // ======================================================
        StorageManager::storeBase64($codigoGeneracion, $dte['firma_local']);

        axel_debug_log($codigoGeneracion, 'BASE64_STORED');

        // ======================================================
        // 5. ENVÍO MH
        // ======================================================
        $url = ($payload['ambiente'] === '01')
            ? $config['API_SIGNER_PROD_URL']
            : $config['API_SIGNER_TEST_URL'];

        axel_debug_log($codigoGeneracion, 'SEND_MH', [
            'url' => $url
        ]);

        $response = CurlClient::postJson(
            $url,
            $payload,
            [
                "Authorization: Bearer {$token}",
                "Content-Type: application/json",
                "Accept: application/json"
            ]
        );

        axel_debug_log($codigoGeneracion, 'MH_RESPONSE_RAW', [
            'success' => $response['success'] ?? false,
            'http_error' => $response['error'] ?? null
        ]);

        if (!$response['success']) {
            throw new Exception('Error comunicación MH: ' . ($response['error'] ?? 'desconocido'));
        }

        $data = $response['data'];

        axel_debug_log($codigoGeneracion, 'MH_RESPONSE_OK', [
            'estado' => $data['estado'] ?? null,
            'has_sello' => isset($data['selloRecibido'])
        ]);

        // ======================================================
        // 6. UPDATE BD
        // ======================================================
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
            ':respuesta' => json_encode($data, JSON_UNESCAPED_UNICODE),
            ':codigo' => $codigoGeneracion
        ]);

        axel_debug_log($codigoGeneracion, 'DB_UPDATED');

        return [
            'success' => true,
            'codigoGeneracion' => $codigoGeneracion,
            'estado' => $data['estado'] ?? null,
            'sello' => $data['selloRecibido'] ?? null,
            'respuesta' => $data
        ];

    } catch (Throwable $e) {

        axel_debug_log($codigoGeneracion, 'ERROR', [
            'message' => $e->getMessage(),
            'trace' => $e->getTraceAsString()
        ]);

        throw $e;
    }
}
