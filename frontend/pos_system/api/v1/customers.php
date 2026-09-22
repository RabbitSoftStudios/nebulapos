<?php
/**
 * API Endpoint - Clientes
 * =======================
 * Maneja las peticiones CRUD para la entidad de Clientes (dte_clientes).
 */

// 1. Configuración e Inclusiones
header('Content-Type: application/json');
require_once __DIR__ . '/../../includes/CustomerService.php';

// 2. Manejo de Método HTTP
$method = $_SERVER['REQUEST_METHOD'];
$service = new CustomerService();

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
            // Obtener ID si existe (ej. customers.php?id=1)
            $id = $_GET['id'] ?? null;
            
            if ($id) {
                // Obtener un solo cliente
                $result = $service->getById($id);
                if ($result['success']) {
                    jsonResponse(true, $result['data']);
                } else {
                    jsonResponse(false, null, $result['error'], 404);
                }
            } else {
                // Obtener todos
                $result = $service->getAll();
                if ($result['success']) {
                    jsonResponse(true, $result['data']);
                } else {
                    jsonResponse(false, null, $result['error'], 500);
                }
            }
            break;

        case 'POST':
            // Crear nuevo cliente
            // Leer cuerpo JSON
            $input = json_decode(file_get_contents('php://input'), true);
            
            if (!$input) {
                jsonResponse(false, null, 'Datos inválidos o vacíos', 400);
            }
            
            $result = $service->create($input);
            
            if ($result['success']) {
                jsonResponse(true, $result['data'], null, 201);
            } else {
                jsonResponse(false, null, $result['error'], 500);
            }
            break;

        case 'PUT':
            // Actualizar cliente (se prefiere PUT para update completo, o PATCH para parcial, 
            // pero usaremos PUT para simplificar o leeremos 'id' del body/query)
            
            $input = json_decode(file_get_contents('php://input'), true);
            $id = $_GET['id'] ?? $input['id'] ?? null;
            
            if (!$id) {
                jsonResponse(false, null, 'ID de cliente es requerido', 400);
            }
            
            if (!$input) {
                jsonResponse(false, null, 'Datos inválidos o vacíos', 400);
            }
            
            $result = $service->update($id, $input);
            
            if ($result['success']) {
                jsonResponse(true, $result['data']);
            } else {
                jsonResponse(false, null, $result['error'], 500);
            }
            break;

        case 'DELETE':
            // Eliminar cliente
            $id = $_GET['id'] ?? null;
            if (!$id) {
                jsonResponse(false, null, 'ID de cliente es requerido', 400);
            }
            
            $result = $service->delete($id);
            
            if ($result['success']) {
                jsonResponse(true, null, null, 200);
            } else {
                jsonResponse(false, null, $result['error'], 500);
            }
            break;
            
        default:
            jsonResponse(false, null, 'Método no permitido', 405);
            break;
    }
} catch (Exception $e) {
    jsonResponse(false, null, 'Error interno del servidor: ' . $e->getMessage(), 500);
}
