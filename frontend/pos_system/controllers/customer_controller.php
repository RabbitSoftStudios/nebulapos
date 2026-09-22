<?php
/**
 * Archivo: customer_controller.php
 * Ubicación: /controllers/customer_controller.php
 * Fecha: 2026-01-07
 * Team MYTS
 */

/* =========================
   HEADERS CORS
========================= */
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PATCH, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");
header("Content-Type: application/json");

/* =========================
   PRE-FLIGHT (OPTIONS)
========================= */
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

/* =========================
   DEPENDENCIAS
========================= */
require_once __DIR__ . '/../includes/ProductService.php';

$productService = new ProductService();

/* =========================
   ACCIÓN
========================= */
$action = $_POST['action'] ?? $_GET['action'] ?? null;

$response = [
    'success' => false,
    'message' => 'Acción no válida'
];

/* =========================
   ROUTER
========================= */
switch ($action) {

    case 'list':
        $response = $productService->all();
        break;

    case 'get':
        if (!isset($_GET['id'])) {
            $response['message'] = 'ID requerido';
            break;
        }
        $response = $productService->get((int) $_GET['id']);
        break;

    case 'create':
        unset($_POST['action']);
        $response = $productService->create($_POST);
        break;

    case 'update':
        if (!isset($_POST['id'])) {
            $response['message'] = 'ID requerido';
            break;
        }
        $id = (int) $_POST['id'];
        unset($_POST['id'], $_POST['action']);
        $response = $productService->update($id, $_POST);
        break;

    case 'delete':
        if (!isset($_POST['id'])) {
            $response['message'] = 'ID requerido';
            break;
        }
        $response = $productService->delete((int) $_POST['id']);
        break;
}

/* =========================
   RESPUESTA
========================= */
echo json_encode($response);
exit;
