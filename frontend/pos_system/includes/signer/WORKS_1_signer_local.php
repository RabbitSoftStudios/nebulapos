<?php
/**
 * Servicio de Firma Local de Documentos Electrónicos
 * 
 * Esta función se encarga de firmar electrónicamente un documento DTE
 * utilizando un servicio local de firma. Devuelve el documento firmado
 * en formato Base64 listo para ser enviado al gobierno.
 * 
 * Requisitos:
 * - Servicio de firma corriendo en http://localhost:8113/firmardocumento/
 * - Configuración válida en config.php (NIT y clave privada)
 * 
 * @param array $invoice Datos de la factura a firmar
 * @param string $codigoGeneracion Código de generación del DTE
 * @param int $userId ID del usuario que realiza la firma
 * @return array Documento firmado en formato ['status' => 'OK', 'body' => base64]
 * @throws Exception Si ocurre cualquier error en el proceso
 */
function sign_document_local($invoice, $codigoGeneracion, $userId = null) {
    // Cargar configuración del sistema
    $config = require __DIR__ . '/utils/config.php';
    
    // Validar configuración requerida
    if (empty($config['NIT']) || strlen($config['NIT']) != 14 || !is_numeric($config['NIT'])) {
        throw new Exception("NIT inválido en configuración. Debe tener 14 dígitos numéricos");
    }
    
    if (empty($config['PRIVATE_KEY'])) {
        throw new Exception("Clave privada no configurada");
    }

    if (!is_array($invoice) || !isset($invoice['dte_json']) || empty($invoice['dte_json'])) {
        throw new Exception("Datos de factura inválidos o JSON vacío");
    }

    // URL del servicio de firma local
    $sign_url = "http://localhost:8113/firmardocumento/";

    try {
        // Decodificar el JSON del documento
        $dteJson = json_decode($invoice["dte_json"], true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception("Error al decodificar JSON: " . json_last_error_msg());
        }

        // Validar y completar estructura del DTE
        if (!isset($dteJson['identificacion'])) {
            $dteJson['identificacion'] = [];
        }

        // Preparar payload para el servicio de firma
        $payload = [
            "nit" => $config["NIT"],
            "activo" => true,
            "passwordPri" => $config["PRIVATE_KEY"],
            "dteJson" => $dteJson
        ];

        // Validaciones críticas
        if (empty($payload['nit']) || empty($payload['passwordPri'])) {
            throw new Exception("NIT o clave privada no pueden estar vacíos");
        }

        // Usar el código de generación proporcionado o generar uno
        if (empty($codigoGeneracion)) {
            $codigoGeneracion = "DTE-" . ($invoice["id"] ?? uniqid()) . "-" . time();
        }
        
        // Asegurar que el código de generación esté en el JSON
        $payload["dteJson"]["identificacion"]["codigoGeneracion"] = $codigoGeneracion;

        // Configurar y ejecutar solicitud HTTP
        $options = [
            "http" => [
                "header"  => "Content-Type: application/json\r\n",
                "method"  => "POST",
                "content" => json_encode($payload, JSON_UNESCAPED_UNICODE),
                "timeout" => 30  // Tiempo de espera de 30 segundos
            ]
        ];
        
        $context = stream_context_create($options);
        $response = @file_get_contents($sign_url, false, $context);
        
        // Manejar errores de conexión
        if ($response === false) {
            $error = error_get_last();
            throw new Exception("Error en servicio de firma local: " . ($error['message'] ?? 'Error desconocido'));
        }

        // Decodificar respuesta del servicio de firma
        $signed = json_decode($response, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception("Respuesta de firma local no es JSON válido: " . json_last_error_msg());
        }

        // ===================================================================
        // ACTUALIZAR BASE DE DATOS: FIRMA LOCAL
        // ===================================================================
        try {
            require_once __DIR__ . '/utils/pg_connection.php';
            
            // Preparar contenido para guardar (solo el cuerpo firmado)
            $firmaLocalContent = $signed['body'] ?? $response;
            
            // Actualizar la base de datos
            $sql = "UPDATE dte_facturas 
                    SET firma_local = :firma_local, 
                        estado_firma = 'firmado', 
                        updated_at = NOW() 
                    WHERE codigo_generacion = :codigo";
            
            $stmt = $SUPABASE_PDO->prepare($sql);
            $stmt->execute([
                ':firma_local' => $firmaLocalContent,
                ':codigo' => $codigoGeneracion
            ]);
            
            // Verificar si se actualizó
            if ($stmt->rowCount() === 0) {
                error_log("Advertencia: No se encontró factura con código: $codigoGeneracion");
            }
            
        } catch (Exception $e) {
            // Registrar error pero no interrumpir flujo
            error_log("Error al actualizar firma local en BD: " . $e->getMessage());
            
            // Guardar respuesta en archivo como respaldo
            $backupPath = __DIR__ . "/../temp/pendientes/firma_local_$codigoGeneracion.txt";
            file_put_contents($backupPath, $response);
        }

        // ===================================================================
        // CONTINUAR CON EL PROCESO NORMAL
        // ===================================================================
        // Validar estructura de respuesta esperada
        if (!isset($signed['body']) || !is_string($signed['body'])) {
            throw new Exception("La respuesta de firma local no tiene el formato esperado");
        }

        // Guardar documento firmado para referencia
        $filename = __DIR__ . "/../temp/signed_doc.json";
        $folder = dirname($filename);
        if (!file_exists($folder) && !mkdir($folder, 0755, true)) {
            throw new Exception("No se pudo crear directorio para archivo firmado");
        }

        if (file_put_contents($filename, json_encode($signed, JSON_PRETTY_PRINT)) === false) {
            throw new Exception("No se pudo guardar el documento firmado");
        }

        return $signed;

    } catch (Exception $e) {
        throw new Exception("Error critico sign_document_local: " . $e->getMessage());
    }
}