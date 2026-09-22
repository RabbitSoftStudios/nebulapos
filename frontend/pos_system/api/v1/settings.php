<?php
/**
 * API Endpoint - Configuración (Empresa)
 * =====================================
 * Maneja las peticiones CRUD para la información de la empresa.
 */

header('Content-Type: application/json');
require_once __DIR__ . '/../../includes/EmpresaService.php';

$method = $_SERVER['REQUEST_METHOD'];
$service = new EmpresaService();

// Helper para enviar respuesta JSON
function jsonResponse($success, $data = null, $error = null, $code = 200) {
    http_response_code($code);
    echo json_encode([
        'success' => $success,
        'data' => $data,
        'error' => $error
    ]);
    exit;
}

try {
    switch ($method) {
        case 'GET':
            // Por ahora, traemos la primera empresa (generalmente solo hay una)
            // En el futuro se podría filtrar por user_id de la sesión
            $result = $service->get();
            if ($result['success']) jsonResponse(true, $result['data']);
            else jsonResponse(false, null, $result['error'], 404);
            break;

        case 'POST':
        case 'PUT':
        case 'PATCH':
            $input = json_decode(file_get_contents('php://input'), true);
            if (!$input) jsonResponse(false, null, 'Datos inválidos', 400);

            // Si es POST y no hay ID, EmpresaService.insert se ejecutará.
            // Si hay ID o filtros, EmpresaService.update se ejecutará.
            $result = $service->update($input);
            
            if ($result['success']) jsonResponse(true, $result['data']);
            else jsonResponse(false, null, $result['error'], 500);
            break;

        default:
            jsonResponse(false, null, 'Método no permitido', 405);
            break;
    }
} catch (Exception $e) {
    jsonResponse(false, null, 'Error servidor: ' . $e->getMessage(), 500);
}
