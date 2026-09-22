<?php
/**
 * API Endpoint - Productos
 * ========================
 * Maneja las peticiones CRUD para la entidad de Productos (dte_productos).
 */

// CORS Headers - Permitir peticiones desde el mismo dominio
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, PATCH, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
header('Content-Type: application/json; charset=UTF-8');

// Manejar preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once __DIR__ . '/../../includes/ProductService.php';

// Configurar logging de errores
$logDir = __DIR__ . '/../../logs';
if (!file_exists($logDir)) {
    @mkdir($logDir, 0755, true);
}
$logFile = $logDir . '/api_errors.log';

// Función de logging
function logError($message, $context = []) {
    global $logFile;
    $timestamp = date('Y-m-d H:i:s');
    $contextStr = !empty($context) ? json_encode($context, JSON_PRETTY_PRINT) : '';
    $logMessage = "[$timestamp] $message\n";
    if ($contextStr) {
        $logMessage .= "Context: $contextStr\n";
    }
    $logMessage .= str_repeat('-', 80) . "\n";
    @file_put_contents($logFile, $logMessage, FILE_APPEND);
}

// Log de la petición entrante
logError('API Request', [
    'method' => $_SERVER['REQUEST_METHOD'],
    'uri' => $_SERVER['REQUEST_URI'] ?? '',
    'query' => $_GET,
    'input' => file_get_contents('php://input')
]);

$method = $_SERVER['REQUEST_METHOD'];
$service = new ProductService();

// Helper para enviar respuesta JSON
function jsonResponse($success, $data = null, $error = null, $code = 200) {
    global $logFile;
    
    $response = [
        'success' => $success,
        'data' => $data,
        'error' => $error
    ];
    
    // Log de la respuesta
    logError('API Response', [
        'code' => $code,
        'response' => $response
    ]);
    
    http_response_code($code);
    echo json_encode($response);
    exit;
}

try {
    switch ($method) {
        case 'GET':
            $action = $_GET['action'] ?? null;
            $id = $_GET['id'] ?? null;
            $barcode = $_GET['barcode'] ?? null;
            $page = (int)($_GET['page'] ?? 1);

            switch ($action) {
                case 'list':
                    echo json_encode(
                        $service->list(
                            (int)($_GET['page'] ?? 1),
                            10,
                            $_GET['orderBy'] ?? 'id',
                            $_GET['direction'] ?? 'DESC'
                        )
                    );
                    break;
                default:
                    if ($id) {
                        $result = $service->getById($id);
                        if ($result['success']) jsonResponse(true, $result['data']);
                        else jsonResponse(false, null, $result['error'], 404);
                    } elseif ($barcode) {
                        $result = $service->getByBarcode($barcode);
                        if ($result['success']) jsonResponse(true, $result['data']);
                        else jsonResponse(false, null, $result['error'], 404);
                    } else {
                        // Obtener todos (sin paginación)
                        $result = $service->list(1, 1000);
                        if ($result['success']) jsonResponse(true, $result['data']);
                        else jsonResponse(false, null, 'Error al obtener productos', 500);
                    }
                    break;
            }
            break;

        case 'POST':
            $input = json_decode(file_get_contents('php://input'), true);
            if (!$input) jsonResponse(false, null, 'Datos inválidos', 400);

            $result = $service->create($input);
            if ($result['success']) jsonResponse(true, $result['data'], null, 201);
            else jsonResponse(false, null, $result['error'], 500);
            break;

        case 'PUT':
            $input = json_decode(file_get_contents('php://input'), true);
            $id = $_GET['id'] ?? $input['id'] ?? null;

            if (!$id) jsonResponse(false, null, 'ID requerido', 400);
            if (!$input) jsonResponse(false, null, 'Datos inválidos', 400);

            $result = $service->update($id, $input);
            if ($result['success']) jsonResponse(true, $result['data']);
            else jsonResponse(false, null, $result['error'], 500);
            break;

        case 'DELETE':
            $id = $_GET['id'] ?? null;
            if (!$id) jsonResponse(false, null, 'ID requerido', 400);

            $result = $service->delete($id);
            if ($result['success']) jsonResponse(true, null, null, 200);
            else jsonResponse(false, null, $result['error'], 500);
            break;

        default:
            jsonResponse(false, null, 'Método no permitido', 405);
            break;
    }
} catch (Exception $e) {
    jsonResponse(false, null, 'Error servidor: ' . $e->getMessage(), 500);
}
