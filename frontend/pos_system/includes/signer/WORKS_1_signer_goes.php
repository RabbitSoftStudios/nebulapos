<?php
/**
 * Servicio de Envío de Documentos al Gobierno
 * 
 * Esta función se encarga de enviar documentos firmados al servicio
 * de facturación electrónica del gobierno. Incluye sistema completo
 * de auditoría y manejo de errores.
 * 
 * Requisitos:
 * - Token de autenticación válido
 * - Documento firmado en formato Base64
 * - Configuración válida en config.php
 * 
 * @param string $codigoGeneracion Código de generación del DTE
 * @return array Resultado del envío con estructura detallada
 * @throws Exception Si ocurre cualquier error en el proceso
 */

// Versión mejorada con auditoría completa
require_once __DIR__ . '/token_manager.php';
require_once __DIR__ . '/signer_audit.php';

// Iniciar proceso de auditoría
SignerAudit::startAudit();

function enviar_firma_gobierno($codigoGeneracion = null) {
    try {
        // Cargar configuración del sistema
        $config = require __DIR__ . '/utils/config.php';
        
        // 1. Obtener token de autenticación válido
        $token = obtener_token_valido();
        
        // Validar token
        if (empty($token)) {
            throw new Exception("No se pudo obtener un token válido");
        }
        
        // Limpiar prefijo "Bearer " si existe
        if (strpos($token, 'Bearer ') === 0) {
            $token = substr($token, 7);
        }

        // 2. Leer documento original
        $originalDocPath = __DIR__ . '/../temp/uploads/dte_master.json';
        if (!file_exists($originalDocPath)) {
            throw new Exception("Archivo original no encontrado en: $originalDocPath");
        }
        
        $originalContent = file_get_contents($originalDocPath);
        $originalJson = json_decode($originalContent, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception("El documento original no es un JSON válido: " . json_last_error_msg());
        }

        // Obtener metadatos esenciales del documento
        $codigoGeneracion = $codigoGeneracion ?? $originalJson['identificacion']['codigoGeneracion'] ?? null;
        $tipoDte = $originalJson['identificacion']['tipoDte'] ?? null;
        $version = $originalJson['identificacion']['version'] ?? null;
        $emisorNit = $originalJson['emisor']['nit'] ?? null;
        $ambiente = $config["AMBIENTE"] ?? "00";
        
        // Validar campos esenciales
        if (empty($codigoGeneracion)) {
            throw new Exception("Falta 'codigoGeneracion' en el documento");
        }
        
        if (empty($tipoDte)) {
            throw new Exception("Falta 'tipoDte' en el documento");
        }
        
        if (empty($version)) {
            throw new Exception("Falta 'version' en el documento");
        }
        
        if (empty($emisorNit)) {
            throw new Exception("No se pudo obtener el NIT del emisor");
        }

        // 3. Leer documento firmado
        $signedDocPath = __DIR__ . '/../temp/signed_doc.json';
        if (!file_exists($signedDocPath)) {
            throw new Exception("Documento firmado no encontrado en: $signedDocPath");
        }

        $signedContent = file_get_contents($signedDocPath);
        $signedData = json_decode($signedContent, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception("El documento firmado no es un JSON válido: " . json_last_error_msg());
        }

        // Extraer el documento firmado en Base64
        $jsonFirmado = $signedData['body'] ?? null;
        
        // Validar formato del documento firmado
        if (empty($jsonFirmado)) {
            throw new Exception("El documento firmado no contiene el campo 'body' requerido");
        }

        if (!is_string($jsonFirmado) || empty(trim($jsonFirmado))) {
            throw new Exception("El campo 'body' del documento firmado no contiene datos válidos");
        }

        // 4. Preparar datos para enviar al gobierno
        $data = [
            "ambiente" => $ambiente, // "00" para pruebas, "01" para producción
            "idEnvio" => 1, // Correlativo de envío
            "version" => (int)$version, // Versión del formato DTE
            "tipoDte" => $tipoDte, // Tipo de documento (01 = Factura)
            "documento" => $jsonFirmado, // Documento firmado en Base64
            "codigoGeneracion" => $codigoGeneracion, // Código único del documento
            "user" => $emisorNit // NIT del emisor (requerido por el gobierno)
        ];

        // Validar que todos los campos requeridos existen
        $requiredFields = ['ambiente', 'version', 'tipoDte', 'documento', 'codigoGeneracion', 'user'];
        foreach ($requiredFields as $field) {
            if (!isset($data[$field]) || empty($data[$field])) {
                throw new Exception("Campo requerido faltante o vacío: $field");
            }
        }

        // Registrar paso en auditoría
        SignerAudit::logStep('prepared_data', [
            'codigoGeneracion' => $codigoGeneracion,
            'tipoDte' => $tipoDte,
            'version' => $version,
            'ambiente' => $ambiente,
            'emisorNit' => $emisorNit,
            'documento_length' => strlen($jsonFirmado)
        ]);

        // Configurar headers de la solicitud
        $headers = [
            "Authorization: Bearer $token",
            "Content-Type: application/json",
            "User-Agent: Orion-API/1.0",
            "Accept: application/json"
        ];
        
        // 5. Configurar y ejecutar cURL
        $ch = curl_init();
        
        // Crear directorio de auditoría si no existe
        $auditDir = __DIR__ . '/../audit';
        if (!is_dir($auditDir)) {
            if (!mkdir($auditDir, 0755, true)) {
                throw new Exception("No se pudo crear directorio de auditoría: $auditDir");
            }
        }
        
        // Configurar archivo de debug para cURL
        $curlDebugPath = $auditDir . '/curl_debug_' . date('Ymd_His') . '.log';
        $curlDebugHandle = fopen($curlDebugPath, 'w+');
        
        // Configurar opciones de cURL
        $curlOptions = [
            CURLOPT_URL => $config["API_SIGNER_TEST_URL"],
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($data, JSON_UNESCAPED_UNICODE),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTPHEADER => $headers,
            CURLOPT_SSL_VERIFYPEER => false,
            CURLOPT_TIMEOUT => 60,
            CURLOPT_CONNECTTIMEOUT => 30,
            CURLOPT_VERBOSE => true,
            CURLOPT_STDERR => $curlDebugHandle,
            CURLOPT_FAILONERROR => false,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_MAXREDIRS => 10
        ];
        
        // Aplicar configuraciones y ejecutar
        curl_setopt_array($ch, $curlOptions);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $error = curl_error($ch);
        
        // Obtener información adicional de la respuesta
        $contentType = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
        $totalTime = curl_getinfo($ch, CURLINFO_TOTAL_TIME);
        
        // Cerrar recursos
        curl_close($ch);
        fclose($curlDebugHandle);

        // 6. Procesar respuesta del gobierno
        $responseData = json_decode($response, true);
        $isValidJson = (json_last_error() === JSON_ERROR_NONE);
        
        // Registrar respuesta en auditoría
        SignerAudit::logStep('received_response', [
            'http_code' => $httpCode,
            'content_type' => $contentType,
            'total_time' => $totalTime,
            'response_length' => strlen($response),
            'has_valid_json' => $isValidJson
        ]);
        
        // Guardar respuesta completa
        $receiptPath = __DIR__ . '/../temp/receipt_seal_' . $codigoGeneracion . '.json';
        file_put_contents($receiptPath, json_encode([
            'timestamp' => date('Y-m-d H:i:s'),
            'http_code' => $httpCode,
            'response' => $responseData ?: $response,
            'error' => $error,
            'request_data' => $data
        ], JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

        // 7. Manejar errores HTTP
        if ($httpCode != 200) {
            $errorMessage = "Error en la comunicación con el gobierno. Código HTTP: $httpCode";
            
            if ($isValidJson && isset($responseData['mensaje'])) {
                $errorMessage .= " - Mensaje: " . $responseData['mensaje'];
            } elseif (!empty($response)) {
                $errorMessage .= " - Respuesta: " . substr($response, 0, 200);
            }
            
            throw new Exception($errorMessage);
        }

        // 8. Validar respuesta del gobierno
        if (!$isValidJson) {
            throw new Exception("La respuesta del gobierno no es un JSON válido: " . substr($response, 0, 500));
        }

        if (!isset($responseData['estado'])) {
            throw new Exception("La respuesta del gobierno no incluye estado del procesamiento");
        }

        // 9. Preparar resultado estructurado
        $result = [
            'success' => $httpCode == 200 && ($responseData['estado'] ?? '') == 'PROCESADO',
            'http_code' => $httpCode,
            'response' => $responseData,
            'error' => $error,
            'codigoGeneracion' => $codigoGeneracion,
            'timestamp' => date('Y-m-d H:i:s')
        ];

        return $result;

    } catch (Exception $e) {
        // Registrar error en auditoría
        SignerAudit::logStep('error', [
            'message' => $e->getMessage(),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => $e->getTraceAsString()
        ]);
        
        // Re-lanzar la excepción para manejo externo
        throw new Exception("Error en enviar_firma_gobierno: " . $e->getMessage(), 0, $e);
        
    } finally {
        // Guardar auditoría al final del proceso
        SignerAudit::saveAudit();
    }
}