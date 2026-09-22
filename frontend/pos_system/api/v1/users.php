<?php
/**
 * API Endpoint - Usuarios
 * ========================
 * Maneja las peticiones CRUD para usuarios.
 * Soporta multiempresa mediante empresa_uuid.
 */

header('Content-Type: application/json');
require_once __DIR__ . '/../../includes/UsuarioService.php';

// Iniciar sesión para obtener empresa_uuid del usuario logueado
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$method = $_SERVER['REQUEST_METHOD'];
$service = new UsuarioService();

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
    // Obtener empresa_uuid de la sesión
    $empresaUuid = $_SESSION['empresa_uuid'] ?? null;
    
    switch ($method) {
        case 'GET':
            // Obtener usuarios de la empresa logueada
            if (!$empresaUuid) {
                jsonResponse(false, null, 'No hay empresa en sesión', 401);
            }
            
            // Si hay un ID específico en la query
            if (isset($_GET['id'])) {
                $result = $service->getById($_GET['id']);
            } else {
                // Obtener todos los usuarios de la empresa
                $result = $service->getByEmpresa($empresaUuid);
            }
            
            if ($result['success']) {
                jsonResponse(true, $result['data']);
            } else {
                jsonResponse(false, null, $result['error'], 404);
            }
            break;

        case 'POST':
            // Crear nuevo usuario
            $input = json_decode(file_get_contents('php://input'), true);
            if (!$input) {
                jsonResponse(false, null, 'Datos inválidos', 400);
            }
            
            // Asignar empresa_uuid de la sesión
            if (!$empresaUuid) {
                jsonResponse(false, null, 'No hay empresa en sesión', 401);
            }
            $input['empresa_uuid'] = $empresaUuid;
            
            $result = $service->create($input);
            
            if ($result['success']) {
                jsonResponse(true, $result['data'], null, 201);
            } else {
                jsonResponse(false, null, $result['error'], 400);
            }
            break;

        case 'PUT':
        case 'PATCH':
            // Actualizar usuario
            $input = json_decode(file_get_contents('php://input'), true);
            if (!$input || empty($input['id'])) {
                jsonResponse(false, null, 'ID requerido', 400);
            }
            
            $id = $input['id'];
            unset($input['id']);
            
            $result = $service->update($id, $input);
            
            if ($result['success']) {
                jsonResponse(true, $result['data']);
            } else {
                jsonResponse(false, null, $result['error'], 400);
            }
            break;

        case 'DELETE':
            // Eliminar usuario
            $input = json_decode(file_get_contents('php://input'), true);
            if (!$input || empty($input['id'])) {
                jsonResponse(false, null, 'ID requerido', 400);
            }
            
            $result = $service->delete($input['id']);
            
            if ($result['success']) {
                jsonResponse(true, ['message' => 'Usuario eliminado']);
            } else {
                jsonResponse(false, null, $result['error'], 400);
            }
            break;

        default:
            jsonResponse(false, null, 'Método no permitido', 405);
            break;
    }
} catch (Exception $e) {
    jsonResponse(false, null, 'Error servidor: ' . $e->getMessage(), 500);
}
