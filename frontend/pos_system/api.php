<?php
/**
 * Sistema POS - Redirección de API
 * =================================
 * Punto de entrada para todas las solicitudes a /api/v1/
 * * @package    POS System
 * @author     Nebula DET Team
 * @version    2.0.0
 */

// 1. Cargar configuración
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/config/constants.php';
require_once __DIR__ . '/config/routes.php';

// 2. Obtener la URI de la API
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

// 3. Simular enrutamiento de API (solo rutas /api/v1/*)
$base_api = '/api/v1/';
$pos = strpos($uri, $base_api);

if ($pos !== false) {
    // Obtener el endpoint eliminando todo lo anterior y incluyendo el prefijo
    $endpoint = substr($uri, $pos + strlen($base_api));
    $file_path = 'api/v1/' . $endpoint . '.php'; 
    
    // Verificar si existe un archivo de API para el endpoint
    if (file_exists(__DIR__ . '/' . $file_path)) {
        require_once __DIR__ . '/' . $file_path;
        exit();
    }
}

// Si la URI no coincide con una ruta de API
header("HTTP/1.0 404 Not Found");
echo json_encode(['success' => false, 'error' => 'API Endpoint Not Found']);
exit();
?>
