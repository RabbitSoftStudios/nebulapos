<?php
/**
 * Clase para auditoría del proceso de firma electrónica
 * 
 * Esta clase crea archivos de log detallados en la carpeta 'audit' con prefijo 'audit_'
 * Registra todos los pasos del proceso de firma, incluyendo las solicitudes y respuestas
 * 
 * Características:
 * - Crea automáticamente la carpeta de auditoría si no existe
 * - Genera archivos de log con formato legible para humanos
 * - Registra comandos cURL y equivalentes Python para replicar solicitudes
 * - Captura tiempos de ejecución de cada paso
 * - Guarda respuestas del servidor y errores
 * - Incluye información del sistema para diagnóstico
 */

class SignerAudit {
    // Directorio base (donde se encuentra signer_goes.php)
    private static $baseDir = __DIR__;
    // Nombre de la carpeta de auditoría
    private static $auditDir = 'audit';
    // Archivo de log principal
    private static $logFile;
    // Archivo de debug de curl
    private static $debugFile;
    // Datos de auditoría
    private static $auditData = [];
    // Tiempo de inicio
    private static $startTime;

    /**
     * Inicializa la auditoría
     * - Configura rutas de archivos
     * - Crea carpeta de auditoría si no existe
     * - Inicia temporizador
     */
    public static function startAudit() {
        // Configurar rutas de archivos
        self::setFilePaths();
        
        // Crear directorio de auditoría si no existe
        self::createAuditDir();
        
        // Iniciar temporizador
        self::$startTime = microtime(true);
        
        // Inicializar datos de auditoría
        self::$auditData = [
            'timestamp' => date('Y-m-d H:i:s'),
            'process' => 'firma_electronica',
            'steps' => []  // Aquí se almacenarán los pasos del proceso
        ];
    }

    /**
     * Configura las rutas de los archivos de auditoría
     * - Crea rutas absolutas para los archivos de log
     */
    private static function setFilePaths() {
        $auditDirPath = self::$baseDir . DIRECTORY_SEPARATOR . self::$auditDir;
        self::$logFile = $auditDirPath . DIRECTORY_SEPARATOR . 'audit_signer_audit.log';
        self::$debugFile = $auditDirPath . DIRECTORY_SEPARATOR . 'audit_curl_debug.log';
    }

    /**
     * Crea el directorio de auditoría si no existe
     */
    private static function createAuditDir() {
        $auditDirPath = self::$baseDir . DIRECTORY_SEPARATOR . self::$auditDir;
        
        if (!file_exists($auditDirPath)) {
            mkdir($auditDirPath, 0777, true);
        }
    }

    /**
     * Registra un paso en el proceso de auditoría
     * 
     * @param string $stepName Nombre del paso
     * @param mixed $data Datos relevantes del paso
     */
    public static function logStep($stepName, $data) {
        self::$auditData['steps'][] = [
            'step' => $stepName,
            'time' => microtime(true) - self::$startTime, // Tiempo transcurrido desde inicio
            'data' => $data
        ];
    }

    /**
     * Registra la solicitud cURL
     * 
     * @param string $url URL de destino
     * @param array $headers Encabezados HTTP
     * @param array $payload Datos a enviar
     */
    public static function logCurlRequest($url, $headers, $payload) {
        self::$auditData['curl_request'] = [
            'command' => self::buildCurlCommand($url, $headers, $payload),
            'python_equivalent' => self::getPythonEquivalent($url, $headers, $payload),
            'url' => $url,
            'headers' => $headers,
            'payload' => $payload
        ];
    }

    /**
     * Registra la respuesta del servidor
     * 
     * @param mixed $response Contenido de la respuesta
     * @param int $httpCode Código HTTP de respuesta
     * @param string $error Mensaje de error (si existe)
     */
    public static function logResponse($response, $httpCode, $error) {
        self::$auditData['response'] = [
            'http_code' => $httpCode,
            'content' => $response,
            'error' => $error,
            'response_time' => microtime(true) - self::$startTime
        ];
    }

    /**
     * Guarda los datos de auditoría en el archivo de log
     * - Incluye información del sistema
     * - Adjunta logs de debug de curl si existen
     */
    public static function saveAudit() {
        try {
            // Agregar información del sistema
            self::$auditData['system'] = [
                'php_version' => phpversion(),
                'server' => $_SERVER['SERVER_SOFTWARE'] ?? 'CLI',
                'memory' => memory_get_usage(true),
                'audit_dir' => dirname(self::$logFile) // Para confirmar la ruta
            ];

            // Convertir datos a formato legible
            $logContent = self::formatLogContent();
            
            // Guardar en el archivo de log
            file_put_contents(
                self::$logFile,
                $logContent,
                FILE_APPEND
            );
            
            // Si existe el archivo de debug de curl, adjuntar su contenido
            if (file_exists(self::$debugFile)) {
                $debugContent = file_get_contents(self::$debugFile);
                file_put_contents(
                    self::$logFile,
                    "\n--- CURL DEBUG LOG ---\n" . $debugContent . "\n--- END CURL DEBUG ---\n",
                    FILE_APPEND
                );
            }
        } catch (Exception $e) {
            // Fallback: registrar error en el log de PHP
            error_log("Error al guardar auditoría: " . $e->getMessage());
        }
    }

    /**
     * Formatea el contenido del log para que sea legible
     * 
     * @return string Contenido formateado del log
     */
    private static function formatLogContent() {
        $log = "===== AUDITORÍA DE FIRMA ELECTRÓNICA =====\n";
        $log .= "Fecha: " . self::$auditData['timestamp'] . "\n";
        $log .= "Proceso: " . self::$auditData['process'] . "\n\n";
        
        // Registrar pasos del proceso
        if (!empty(self::$auditData['steps'])) {
            $log .= "--- PASOS DEL PROCESO ---\n";
            foreach (self::$auditData['steps'] as $step) {
                $log .= sprintf(
                    "[%.4fs] %s:\n%s\n\n",
                    $step['time'],
                    $step['step'],
                    print_r($step['data'], true)
                );
            }
        }
        
        // Registrar detalles de la solicitud cURL
        if (isset(self::$auditData['curl_request'])) {
            $log .= "--- SOLICITUD CURL ---\n";
            $log .= "URL: " . self::$auditData['curl_request']['url'] . "\n\n";
            
            $log .= "COMANDO CURL:\n";
            $log .= self::$auditData['curl_request']['command'] . "\n\n";
            
            $log .= "EQUIVALENTE PYTHON:\n";
            $log .= self::$auditData['curl_request']['python_equivalent'] . "\n\n";
            
            $log .= "HEADERS:\n";
            foreach (self::$auditData['curl_request']['headers'] as $header) {
                $log .= $header . "\n";
            }
            $log .= "\n";
            
            $log .= "DATOS ENVIADOS (PAYLOAD):\n";
            $log .= print_r(self::$auditData['curl_request']['payload'], true) . "\n\n";
        }
        
        // Registrar respuesta del servidor
        if (isset(self::$auditData['response'])) {
            $log .= "--- RESPUESTA DEL SERVIDOR ---\n";
            $log .= "Código HTTP: " . self::$auditData['response']['http_code'] . "\n";
            $log .= "Error: " . (self::$auditData['response']['error'] ?: 'Ninguno') . "\n";
            $log .= "Tiempo de respuesta: " . self::$auditData['response']['response_time'] . " segundos\n";
            $log .= "Contenido:\n";
            $log .= self::$auditData['response']['content'] . "\n\n";
        }
        
        // Registrar información del sistema
        if (isset(self::$auditData['system'])) {
            $log .= "--- INFORMACIÓN DEL SISTEMA ---\n";
            $log .= "PHP Version: " . self::$auditData['system']['php_version'] . "\n";
            $log .= "Server: " . self::$auditData['system']['server'] . "\n";
            $log .= "Memoria usada: " . number_format(self::$auditData['system']['memory'] / 1024) . " KB\n";
            $log .= "Directorio de auditoría: " . self::$auditData['system']['audit_dir'] . "\n";
        }
        
        $log .= "===== FIN DE AUDITORÍA =====\n\n";
        
        return $log;
    }

    /**
     * Construye el comando cURL equivalente
     * 
     * @param string $url URL de destino
     * @param array $headers Encabezados HTTP
     * @param array $payload Datos a enviar
     * @return string Comando cURL completo
     */
    private static function buildCurlCommand($url, $headers, $payload) {
        $headerStrings = [];
        foreach ($headers as $header) {
            $headerStrings[] = "-H '" . addslashes($header) . "'";
        }
        
        $data = json_encode($payload, JSON_UNESCAPED_SLASHES);
        return sprintf(
            "curl -X POST '%s' %s -d '%s' --insecure",
            $url,
            implode(' ', $headerStrings),
            addslashes($data)
        );
    }

    /**
     * Genera código Python equivalente para la solicitud
     * 
     * @param string $url URL de destino
     * @param array $headers Encabezados HTTP
     * @param array $payload Datos a enviar
     * @return string Código Python equivalente
     */
    private static function getPythonEquivalent($url, $headers, $payload) {
        $pyHeaders = [];
        foreach ($headers as $header) {
            $parts = explode(':', $header, 2);
            if (count($parts) === 2) {
                $key = trim($parts[0]);
                $value = trim($parts[1]);
                $pyHeaders[$key] = $value;
            }
        }

        // Manejo especial para el campo 'documento'
        $payloadForDisplay = $payload;
        if (isset($payloadForDisplay['documento']) && is_array($payloadForDisplay['documento'])) {
            $payloadForDisplay['documento'] = '[DOCUMENTO_FIRMADO]';
        }

        $pythonCode = "# Python equivalent request\n";
        $pythonCode .= "import requests\n\n";
        $pythonCode .= "url = \"{$url}\"\n\n";
        
        $pythonCode .= "headers = {\n";
        foreach ($pyHeaders as $key => $value) {
            $pythonCode .= "    \"{$key}\": \"{$value}\",\n";
        }
        $pythonCode .= "}\n\n";
        
        $pythonCode .= "payload = {\n";
        foreach ($payloadForDisplay as $key => $value) {
            if ($key === 'documento') {
                $pythonCode .= "    \"documento\": \"[DOCUMENTO_FIRMADO]\",\n";
            } else {
                $pythonCode .= "    \"{$key}\": " . var_export($value, true) . ",\n";
            }
        }
        $pythonCode = rtrim($pythonCode, ",\n") . "\n}\n\n";
        
        $pythonCode .= "response = requests.post(\n";
        $pythonCode .= "    url,\n";
        $pythonCode .= "    json=payload,\n";
        $pythonCode .= "    headers=headers,\n";
        $pythonCode .= "    verify=False  # Equivalente a --insecure\n";
        $pythonCode .= ")\n\n";
        
        $pythonCode .= "print(f\"Status Code: {response.status_code}\")\n";
        $pythonCode .= "print(f\"Response: {response.text}\")\n";
        
        return $pythonCode;
    }
}