<?php
/**
 * Sistema POS - Gestión de Clientes
 * ====================================
 * Vista para el manejo de la base de datos de clientes.
 * @package    POS System
 * @author     Nebula DET Team
 * @version    2.0.0
 */

// 1. Inclusión de Configuración y Controladores
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../controllers/customer_controller.php';

// Verificación de sesión
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$cashier = $_SESSION['cashier'] ?? ['name' => 'Invitado'];
$registerNumber = POS_CAJA_NUMERO;

// Los datos se cargan desde el controlador customer_controller.php ($customers)
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars(APP_NAME) ?> | Clientes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/pos_styles.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/2.0.8/css/dataTables.bootstrap5.min.css">
</head>
<body class="d-flex flex-column min-vh-100">

    <?php include_once __DIR__ . '/partials/header.php'; ?>
    <?php include_once __DIR__ . '/partials/sidebar.php'; ?>

    <main class="container-fluid mt-4 flex-grow-1">
        <h2 class="mb-4"><i class="fas fa-users me-2"></i> Gestión de Clientes</h2>

        <div class="card shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Base de Datos de Clientes</h5>
                <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#customerModal" id="btn-add-customer">
                    <i class="fas fa-user-plus me-1"></i> Agregar Cliente
                </button>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table id="customersTable" class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Nombres</th>
                                <th>Apellidos</th>
                                <th>DUI / NIT</th>
                                <th>Teléfono</th>
                                <th>Email</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (is_array($customers)): ?>
                                <?php foreach ($customers as $customer): ?>
                                <tr>
                                    <td><?= htmlspecialchars($customer['id']) ?></td>
                                    <td><?= htmlspecialchars($customer['nombres']) ?></td>
                                    <td><?= htmlspecialchars($customer['apellidos']) ?></td>
                                    <td>
                                        <?= htmlspecialchars($customer['dui'] ?? '') ?>
                                        <?= ($customer['dui'] && $customer['nit']) ? '/' : '' ?>
                                        <?= htmlspecialchars($customer['nit'] ?? '') ?>
                                    </td>
                                    <td><?= htmlspecialchars($customer['telefono']) ?></td>
                                    <td><?= htmlspecialchars($customer['email']) ?></td>
                                    <td>
                                        <button class="btn btn-sm btn-warning btn-edit" 
                                            data-id="<?= $customer['id'] ?>" 
                                            data-nombres="<?= htmlspecialchars($customer['nombres']) ?>"
                                            data-apellidos="<?= htmlspecialchars($customer['apellidos']) ?>"
                                            data-dui="<?= htmlspecialchars($customer['dui'] ?? '') ?>"
                                            data-nit="<?= htmlspecialchars($customer['nit'] ?? '') ?>"
                                            data-nrc="<?= htmlspecialchars($customer['nrc'] ?? '') ?>"
                                            data-telefono="<?= htmlspecialchars($customer['telefono'] ?? '') ?>"
                                            data-email="<?= htmlspecialchars($customer['email'] ?? '') ?>"
                                            data-direccion="<?= htmlspecialchars($customer['direccion'] ?? '') ?>"
                                            data-municipio="<?= htmlspecialchars($customer['municipio'] ?? '') ?>"
                                            data-departamento="<?= htmlspecialchars($customer['departamento'] ?? '') ?>"
                                            data-active="<?= htmlspecialchars($customer['active'] ?? '1') ?>"
                                            data-bs-toggle="modal" data-bs-target="#customerModal">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger btn-delete" data-id="<?= $customer['id'] ?>">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center">No se encontraron clientes o error de conexión.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <div class="modal fade" id="customerModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="customerForm">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title" id="customerModalLabel">Agregar Nuevo Cliente</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="customer-id" name="id">
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="nombres" class="form-label">Nombres *</label>
                                <input type="text" class="form-control" id="nombres" name="nombres" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="apellidos" class="form-label">Apellidos *</label>
                                <input type="text" class="form-control" id="apellidos" name="apellidos" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="dui" class="form-label">DUI</label>
                                <input type="text" class="form-control" id="dui" name="dui" placeholder="00000000-0">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="nit" class="form-label">NIT</label>
                                <input type="text" class="form-control" id="nit" name="nit" placeholder="0000-000000-000-0">
                            </div>
                            <div class="col-md-4 mb-3">
                                <label for="nrc" class="form-label">NRC</label>
                                <input type="text" class="form-control" id="nrc" name="nrc">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="telefono" class="form-label">Teléfono</label>
                                <input type="tel" class="form-control" id="telefono" name="telefono">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="email" class="form-label">Correo Electrónico</label>
                                <input type="email" class="form-control" id="email" name="email">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="direccion" class="form-label">Dirección</label>
                            <textarea class="form-control" id="direccion" name="direccion" rows="2"></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="municipio" class="form-label">Municipio</label>
                                <input type="text" class="form-control" id="municipio" name="municipio">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="departamento" class="form-label">Departamento</label>
                                <input type="text" class="form-control" id="departamento" name="departamento">
                            </div>
                        </div>

                        <div class="form-check mb-3">
                            <input class="form-check-input" type="checkbox" id="active" name="active" checked>
                            <label class="form-check-label" for="active">
                                Cliente Activo
                            </label>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-success" id="save-customer-btn">Guardar Cliente</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <?php include_once __DIR__ . '/partials/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/2.0.8/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.bootstrap5.min.js"></script>
    <script src="<?= BASE_URL ?>/js/pos_api.js"></script>
    <script src="<?= BASE_URL ?>/js/pos_main.js"></script>
    <script src="<?= BASE_URL ?>/js/pos_customers.js"></script>
    <script>
        $(document).ready(function() {
            // Inicialización de DataTables
            $('#customersTable').DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/2.0.8/i18n/es-ES.json"
                }
            });
            // La lógica CRUD se maneja en ../public/js/pos_customers.js
        });
    </script>
</body>
</html>