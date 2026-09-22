<?php
/**
 * Product API Controller
 * Team MYTS
 */

header('Content-Type: application/json');

require_once __DIR__ . '/../includes/ProductService.php';

$service = new ProductService();
$action = $_POST['action'] ?? $_GET['action'] ?? null;

try {

    switch ($action) {

        case 'list':
            echo json_encode(
                $service->list(
                    (int)($_GET['page'] ?? 1),
                    10
                )
            );
            break;

        case 'get':
            echo json_encode(
                $service->get((int)$_GET['id'])
            );
            break;

        case 'create':
            if (
                empty($_POST['codigo_producto']) ||
                empty($_POST['nombre_producto'])
            ) {
                throw new Exception('Campos obligatorios faltantes');
            }

            echo json_encode(
                $service->create($_POST)
            );
            break;

        case 'update':
            echo json_encode(
                $service->update(
                    (int)$_POST['id'],
                    $_POST
                )
            );
            break;

        case 'delete':
            echo json_encode(
                $service->delete((int)$_POST['id'])
            );
            break;

        default:
            throw new Exception('Acción no válida');
    }

} catch (Throwable $e) {

    http_response_code(400);
    echo json_encode([
        'success' => false,
        'error' => $e->getMessage()
    ]);
}

exit;
