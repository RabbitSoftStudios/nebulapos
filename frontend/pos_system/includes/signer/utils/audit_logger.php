<?php
/**
 * SISTEMA DE AUDITORÍA CENTRALIZADO PARA FIRMA ELECTRÓNICA
 * Captura el flujo COMPLETO: JSON → Firma Local → Envío MH → Respuesta MH
 * 
 * Formato de log: Separadores claros con codigoGeneracion y numeroControl
 * 
 * Team: MYTS Cloud Computing
 */

define('AUDIT_LOG_FILE', __DIR__ . '/../audit_dte_complete.log');

/**
 * Escribir entrada de auditoría con formato estándar
 * 
 * @param string $codigoGeneracion
 * @param string $numeroControl
 * @param string $step Paso del proceso (INIT, JSON_GENERATED, FIRMA_LOCAL_REQUEST, etc)
 * @param array $data Datos a registrar
 * @param bool $isPrettyJson Si los datos son JSON, formatearlos bonito
 */
function audit_log(
    string $codigoGeneracion, 
    string $numeroControl, 
    string $step, 
    array $data = [], 
    bool $isPrettyJson = false
): void {
    // Evitar escribir simultáneamente desde múltiples procesos
    $file = fopen(AUDIT_LOG_FILE, 'a');
    if (!$file) return;
    
    flock($file, LOCK_EX);
    
    $separator = str_repeat('#', 60);
    $timestamp = date('Y-m-d H:i:s');
    
    // Encabezado estándar
    $header = "\n{$separator}\n";
    $header .= "  CODIGO DE GENERACION: {$codigoGeneracion}\n";
    $header .= "  NUMERO DE CONTROL: {$numeroControl}\n";
    $header .= "  TIMESTAMP: {$timestamp}\n";
    $header .= "  STEP: {$step}\n";
    $header .= "{$separator}\n";
    
    fwrite($file, $header);
    
    // Datos (con formateo especial si es JSON)
    if (!empty($data)) {
        if ($isPrettyJson && isset($data['json_content'])) {
            // Si viene con bandera de JSON, mostrarlo bonito
            fwrite($file, json_encode(
                $data['json_content'], 
                JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES
            ) . "\n");
        } else {
            fwrite($file, json_encode(
                $data, 
                JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE
            ) . "\n");
        }
    }
    
    fwrite($file, "\n");
    
    flock($file, LOCK_UN);
    fclose($file);
}

/**
 * Log de JSON DTE ANTES de firma local
 */
function audit_log_json_generated(
    string $codigoGeneracion,
    string $numeroControl,
    array $dte_json
): void {
    audit_log(
        $codigoGeneracion,
        $numeroControl,
        'JSON_GENERATED',
        ['json_content' => $dte_json],
        true
    );
}

/**
 * Log de respuesta del FIRMADOR LOCAL
 */
function audit_log_firma_local_response(
    string $codigoGeneracion,
    string $numeroControl,
    array $response,
    ?string $firma_base64_preview = null
): void {
    $logData = [
        'success' => $response['success'] ?? false,
        'body_length' => strlen($response['body'] ?? ''),
        'body_preview' => $firma_base64_preview ? substr($firma_base64_preview, 0, 100) . '...' : 'N/A',
        'error' => $response['error'] ?? null,
        'timestamp' => date('Y-m-d H:i:s')
    ];
    
    audit_log(
        $codigoGeneracion,
        $numeroControl,
        'FIRMA_LOCAL_RESPONSE',
        $logData
    );
}

/**
 * Log de PAYLOAD que se envía a Ministerio de Hacienda
 */
function audit_log_mh_payload(
    string $codigoGeneracion,
    string $numeroControl,
    array $payload
): void {
    $logData = [
        'ambiente' => $payload['ambiente'] ?? null,
        'idEnvio' => $payload['idEnvio'] ?? null,
        'version' => $payload['version'] ?? null,
        'tipoDte' => $payload['tipoDte'] ?? null,
        'documento_length' => strlen($payload['documento'] ?? ''),
        'documento_preview' => substr($payload['documento'] ?? '', 0, 100) . '...',
        'codigoGeneracion' => $payload['codigoGeneracion'] ?? null,
        'url_destino' => $payload['url_destino'] ?? 'N/A',
        'timestamp' => date('Y-m-d H:i:s')
    ];
    
    audit_log(
        $codigoGeneracion,
        $numeroControl,
        'MH_PAYLOAD_SEND',
        $logData
    );
}

/**
 * Log de RESPUESTA COMPLETA del Ministerio de Hacienda
 */
function audit_log_mh_response(
    string $codigoGeneracion,
    string $numeroControl,
    array $response,
    int $http_code = 0
): void {
    $logData = [
        'http_code' => $http_code,
        'success' => $response['success'] ?? false,
        'estado' => $response['data']['estado'] ?? null,
        'codigoMsg' => $response['data']['codigoMsg'] ?? null,
        'descripcionMsg' => $response['data']['descripcionMsg'] ?? null,
        'selloRecibido' => $response['data']['selloRecibido'] ?? null,
        'observaciones' => $response['data']['observaciones'] ?? [],
        'fhProcesamiento' => $response['data']['fhProcesamiento'] ?? null,
        'full_response' => $response,
        'timestamp' => date('Y-m-d H:i:s')
    ];
    
    audit_log(
        $codigoGeneracion,
        $numeroControl,
        'MH_RESPONSE_COMPLETE',
        $logData,
        false
    );
}

/**
 * Log de ERRORES durante el proceso
 */
function audit_log_error(
    string $codigoGeneracion,
    string $numeroControl,
    string $error_msg,
    ?string $error_detail = null,
    ?string $file = null,
    ?int $line = null
): void {
    $logData = [
        'error_message' => $error_msg,
        'error_detail' => $error_detail,
        'file' => $file,
        'line' => $line,
        'timestamp' => date('Y-m-d H:i:s')
    ];
    
    audit_log(
        $codigoGeneracion,
        $numeroControl,
        'ERROR_OCCURRED',
        $logData
    );
}

/**
 * Log final: Resumen de todo el proceso
 */
function audit_log_final_summary(
    string $codigoGeneracion,
    string $numeroControl,
    string $final_status,
    array $summary = []
): void {
    $logData = array_merge(
        [
            'final_status' => $final_status, // SUCCESS, FAILED, REJECTED, etc
            'timestamp' => date('Y-m-d H:i:s')
        ],
        $summary
    );
    
    audit_log(
        $codigoGeneracion,
        $numeroControl,
        'PROCESS_FINAL_SUMMARY',
        $logData
    );
}
?>
