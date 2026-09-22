<?php
// pos_system/includes/functions.php
/**
 * Funciones de Utilidad Comunes
 * =============================
 * Colección de funciones de uso general para el sistema POS.
 * @package    POS System
 * @author     Nebula DET Team
 * @version    2.0.0
 */

// Se asume que constants.php ya ha sido cargado
require_once INCLUDES_PATH . '/logger.php'; // Necesario para send_json_error

// ---------------------------------------------------------
// 1. Manejo de Entorno (.env)
// ---------------------------------------------------------

/**
 * Carga las variables de entorno desde un archivo .env.
 * @param string $path La ruta al directorio del archivo .env
 */
function load_env($path = ROOT_PATH) {
    $envFile = $path . '/.env';
    if (!file_exists($envFile)) {
        return;
    }

    $lines = file($envFile, FILE_IGNORE_EMPTY_LINES | FILE_SKIP_EMPTY_LINES);
    foreach ($lines as $line) {
        $line = trim($line);
        if (strpos($line, '#') === 0 || empty($line)) {
            continue;
        }

        list($name, $value) = explode('=', $line, 2);
        $name = trim($name);
        $value = trim($value, " \t\n\r\0\x0B\"'"); // Limpia espacios y comillas

        // Define la variable de entorno si no existe
        if (!array_key_exists($name, $_SERVER) && !array_key_exists($name, $_ENV)) {
            putenv(sprintf('%s=%s', $name, $value));
            $_ENV[$name] = $value;
            $_SERVER[$name] = $value;
        }
    }
}

// Llamar a load_env para asegurar que las variables estén disponibles
load_env();


// ---------------------------------------------------------
// 2. Formato y Utilidades
// ---------------------------------------------------------

/**
 * Formatea un monto como moneda.
 * @param float $amount El monto a formatear.
 * @param string $symbol El símbolo de moneda (por defecto $).
 * @return string
 */
function format_currency(float $amount, string $symbol = DEFAULT_CURRENCY_SYMBOL): string {
    return $symbol . number_format($amount, 2, '.', ',');
}

/**
 * Sanitiza una cadena para prevenir XSS.
 * @param string $data La cadena a sanear.
 * @return string
 */
function sanitize_input(string $data): string {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

// ---------------------------------------------------------
// 3. Utilidades HTTP/JSON
// ---------------------------------------------------------

/**
 * Envía una respuesta JSON y detiene la ejecución.
 * @param mixed $data Los datos a codificar.
 * @param int $status El código de estado HTTP.
 */
function send_json_response($data, int $status = HTTP_OK) {
    http_response_code($status);
    header('Content-Type: application/json');
    echo json_encode(['status' => $status, 'data' => $data]);
    exit();
}

/**
 * Envía una respuesta de error JSON.
 * @param string $message Mensaje de error.
 * @param int $status Código de error HTTP.
 */
function send_json_error(string $message, int $status = HTTP_BAD_REQUEST) {
    http_response_code($status);
    header('Content-Type: application/json');
    Logger::log_error("API Error ({$status}): {$message}");
    echo json_encode([
        'status' => $status, 
        'error' => $message, 
        'timestamp' => date('Y-m-d H:i:s')
    ]);
    exit();
}

/**
 * Obtiene el cuerpo de la solicitud POST como un array PHP.
 * @return array
 */
function get_post_data(): array {
    $content = file_get_contents("php://input");
    $data = json_decode($content, true);
    return is_array($data) ? $data : [];
}