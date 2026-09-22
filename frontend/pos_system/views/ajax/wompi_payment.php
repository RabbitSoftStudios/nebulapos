<?php
/**
 * Endpoint para gestionar pagos con Wompi
 * ========================================
 * Maneja:
 * 1. Obtención de token de Wompi
 * 2. Creación de enlaces de pago
 * 3. Validación de pagos completados
 * 4. Webhook para notificaciones de Wompi
 */

require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../config/constants.php';
require_once __DIR__ . '/../../config/wompi.php';

header('Content-Type: application/json');

// Obtener método de solicitud
$method = $_SERVER['REQUEST_METHOD'];
$action = isset($_GET['action']) ? sanitize_input($_GET['action']) : null;

try {
    switch ($action) {
        /**
         * Obtener credenciales seguras de Wompi para el cliente
         * Solo expone lo necesario para solicitar pagos
         */
        case 'get-config':
            $wompiConfig = require __DIR__ . '/../../config/wompi.php';
            
            if (!$wompiConfig['enabled']) {
                throw new Exception('Wompi no está configurado', 500);
            }

            $response = [
                'success' => true,
                'clientId' => $wompiConfig['clientId'],
                'apiUrl' => $wompiConfig['api']['apiUrl'],
                'tokenUrl' => $wompiConfig['api']['tokenUrl']
            ];
            echo json_encode($response);
            break;

        /**
         * Procesar solicitud de pago
         * POST: cantidad, email, descripcion, referencia
         */
        case 'create-payment':
            if ($method !== 'POST') {
                throw new Exception('Método no permitido', 405);
            }

            $input = json_decode(file_get_contents('php://input'), true);
            
            // Validar datos requeridos
            if (empty($input['amount']) || !is_numeric($input['amount']) || $input['amount'] <= 0) {
                throw new Exception('Monto inválido', 400);
            }
            
            if (empty($input['email'])) {
                throw new Exception('Email requerido', 400);
            }

            // Datos del pago
            $amount = floatval($input['amount']);
            $email = filter_var($input['email'], FILTER_SANITIZE_EMAIL);
            $description = isset($input['description']) ? sanitize_input($input['description']) : 'Compra POS';
            $reference = isset($input['reference']) ? sanitize_input($input['reference']) : 'REF-' . uniqid();

            // Llamar a API de Wompi para crear enlace de pago
            $paymentLink = createWompiPaymentLink($amount, $email, $description, $reference);

            if (!$paymentLink) {
                throw new Exception('No se pudo crear el enlace de pago', 500);
            }

            echo json_encode([
                'success' => true,
                'paymentLink' => $paymentLink,
                'reference' => $reference,
                'amount' => $amount
            ]);
            break;

        /**
         * Verificar estado de pago
         * POST: reference
         */
        case 'verify-payment':
            if ($method !== 'POST') {
                throw new Exception('Método no permitido', 405);
            }

            $input = json_decode(file_get_contents('php://input'), true);
            
            if (empty($input['reference'])) {
                throw new Exception('Referencia requerida', 400);
            }

            $reference = sanitize_input($input['reference']);
            $paymentStatus = verifyWompiPayment($reference);

            if ($paymentStatus === null) {
                throw new Exception('No se pudo verificar el pago', 500);
            }

            echo json_encode([
                'success' => true,
                'status' => $paymentStatus['status'],
                'amount' => $paymentStatus['amount'] ?? null,
                'transactionId' => $paymentStatus['transactionId'] ?? null
            ]);
            break;

        /**
         * Webhook: Notificación de Wompi (POST)
         * Verificar firma y procesar pago completado
         */
        case 'webhook':
            if ($method !== 'POST') {
                throw new Exception('Método no permitido', 405);
            }

            $payload = file_get_contents('php://input');
            $data = json_decode($payload, true);

            // Verificar firma del webhook (según documentación de Wompi)
            $signature = $_SERVER['HTTP_X_WOMPI_SIGNATURE'] ?? null;
            
            if (!verifyWompiSignature($payload, $signature)) {
                throw new Exception('Firma inválida', 401);
            }

            // Procesar evento
            $event = $data['event'] ?? null;
            
            if ($event === 'transaction.completed') {
                processPaymentCompletion($data['data']);
            }

            echo json_encode(['success' => true, 'received' => true]);
            break;

        default:
            throw new Exception('Acción no definida', 400);
    }

} catch (Exception $e) {
    http_response_code($e->getCode() ?: 500);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}

// ================================================================
// FUNCIONES DE UTILIDAD
// ================================================================

/**
 * Crea un enlace de pago en Wompi
 */
function createWompiPaymentLink($amount, $email, $description, $reference) {
    try {
        $wompiConfig = require __DIR__ . '/../../config/wompi.php';
        
        $clientId = $wompiConfig['clientId'];
        $clientSecret = $wompiConfig['clientSecret'];

        // Paso 1: Obtener token de acceso
        $token = getWompiAccessToken($clientId, $clientSecret);
        
        if (!$token) {
            error_log('No se pudo obtener token de Wompi');
            return false;
        }

        // Paso 2: Crear enlace de pago
        $paymentData = [
            'identificadorEnlaceComercio' => $reference,
            'monto' => $amount,
            'nombreProducto' => $description,
            'correoNotificacion' => $email,
            'urlNotificacion' => getBaseUrl() . '/views/ajax/wompi_payment.php?action=webhook'
        ];

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => 'https://api.wompi.sv/EnlacePago',
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => json_encode($paymentData),
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $token
            ],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 15,
            CURLOPT_SSL_VERIFYPEER => false // En producción, verificar certificados
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            error_log('Error de Wompi: ' . $response);
            return false;
        }

        $result = json_decode($response, true);
        
        return $result['urlEnlace'] ?? false;

    } catch (Exception $e) {
        error_log('Error en createWompiPaymentLink: ' . $e->getMessage());
        return false;
    }
}

/**
 * Obtiene token de acceso desde Wompi
 */
function getWompiAccessToken($clientId, $clientSecret) {
    try {
        $tokenData = http_build_query([
            'grant_type' => 'client_credentials',
            'client_id' => $clientId,
            'client_secret' => $clientSecret,
            'audience' => 'wompi_api'
        ]);

        $ch = curl_init();
        curl_setopt_array($ch, [
            CURLOPT_URL => 'https://id.wompi.sv/connect/token',
            CURLOPT_POST => true,
            CURLOPT_POSTFIELDS => $tokenData,
            CURLOPT_HTTPHEADER => [
                'Content-Type: application/x-www-form-urlencoded'
            ],
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_TIMEOUT => 15,
            CURLOPT_SSL_VERIFYPEER => false
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($httpCode !== 200) {
            error_log('Error obteniendo token de Wompi: ' . $response);
            return false;
        }

        $result = json_decode($response, true);
        
        return $result['access_token'] ?? false;

    } catch (Exception $e) {
        error_log('Error en getWompiAccessToken: ' . $e->getMessage());
        return false;
    }
}

/**
 * Verifica el estado de un pago
 */
function verifyWompiPayment($reference) {
    try {
        // TODO: Implementar verificación con API de Wompi
        // Por ahora retornar estado pendiente
        return [
            'status' => 'pending',
            'reference' => $reference
        ];

    } catch (Exception $e) {
        error_log('Error en verifyWompiPayment: ' . $e->getMessage());
        return null;
    }
}

/**
 * Verifica la firma del webhook de Wompi
 */
function verifyWompiSignature($payload, $signature) {
    try {
        $wompiConfig = require __DIR__ . '/../../config/wompi.php';
        $clientSecret = $wompiConfig['clientSecret'];
        
        // Calcular HMAC-SHA256
        $expectedSignature = hash_hmac('sha256', $payload, $clientSecret, false);
        
        return hash_equals($expectedSignature, $signature ?? '');

    } catch (Exception $e) {
        error_log('Error en verifyWompiSignature: ' . $e->getMessage());
        return false;
    }
}

/**
 * Procesa un pago completado
 */
function processPaymentCompletion($paymentData) {
    try {
        $reference = $paymentData['identificadorEnlaceComercio'] ?? null;
        $status = $paymentData['estado'] ?? null;
        $transactionId = $paymentData['idEnlace'] ?? null;

        if (!$reference || $status !== 'completada') {
            return false;
        }

        // Aquí guardar en la base de datos que el pago fue completado
        // y asociarlo con la venta correspondiente
        error_log('Pago completado: ' . json_encode($paymentData));

        return true;

    } catch (Exception $e) {
        error_log('Error en processPaymentCompletion: ' . $e->getMessage());
        return false;
    }
}

/**
 * Obtiene la URL base de la aplicación
 */
function getBaseUrl() {
    $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $path = dirname(dirname($_SERVER['SCRIPT_NAME']));
    return $protocol . '://' . $host . $path;
}

/**
 * Sanitiza entrada de usuario
 */
function sanitize_input($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}
?>
