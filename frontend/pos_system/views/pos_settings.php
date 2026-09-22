<?php
/**
 * Sistema POS - Configuración del Sistema
 * =========================================
 * Vista para modificar parámetros y configuraciones del POS.
 * @package    POS System
 * @author     Nebula DET Team
 * @version    2.0.0
 */

// 1. Inclusión de Configuración y Controladores (Asumida)
// 1. Inclusión de Configuración y Controladores (Asumida)
require_once __DIR__ . '/../config/constants.php';
// require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../controllers/auth_controller.php';
// require_once __DIR__ . '/../controllers/settings_controller.php';

// Asumir que auth_controller.php define $cashier y $registerNumber
//$cashier = $_SESSION['cashier'] ?? ['name' => 'Invitado'];
// REPARAR LINEA 13,14,15 ESTA Y 18 $registerNumber = $_SESSION['register_number'] ?? '00';

// Solo permitir acceso a usuarios con un rol de administración (Placeholder) DESCOMENTAR PARA QUE FUNCIONE BIEN DESPUES
// if (($_SESSION['cashier']['role'] ?? 'user') !== 'admin') {
//     // Redirigir a dashboard si no es admin
//     header('Location: pos_dashboard.php');
//     exit();
// }

// Simulación de obtención de configuraciones
//$settings = fetch_system_settings($supabase); ESTA LINEA EXTRAE LOS SETTINGS DE LA BD FUNCIONA CON EL BLOQUE DE LA LINEA 21 EN CONJUNTO
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars(APP_NAME) ?> | Configuración</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="../public/css/pos_styles.css">
</head>
<body class="d-flex flex-column min-vh-100">

    <?php include_once __DIR__ . '/partials/header.php'; ?>
    <?php include_once __DIR__ . '/partials/sidebar.php'; ?>

    <main class="container-fluid mt-4 flex-grow-1">
        <h2 class="mb-4"><i class="fas fa-cogs me-2"></i> Configuración del Sistema</h2>

        <ul class="nav nav-tabs" id="settingsTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="general-tab" data-bs-toggle="tab" data-bs-target="#general" type="button" role="tab" aria-controls="general" aria-selected="true">
                    <i class="fas fa-tools me-1"></i> General
                </button>
            </li>
            <!-- <li class="nav-item" role="presentation">
                <button class="nav-link" id="tax-tab" data-bs-toggle="tab" data-bs-target="#tax" type="button" role="tab" aria-controls="tax" aria-selected="false">
                    <i class="fas fa-percent me-1"></i> Impuestos y Moneda
                </button>
            </li> -->
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="users-tab" data-bs-toggle="tab" data-bs-target="#users" type="button" role="tab" aria-controls="users" aria-selected="false">
                    <i class="fas fa-users-cog me-1"></i> Usuarios
                </button>
            </li>
        </ul>

        <div class="tab-content border border-top-0 p-4 bg-white shadow-sm">
            
            <div class="tab-pane fade show active" id="general" role="tabpanel" aria-labelledby="general-tab">
                <h4 class="mb-3">Información de la Empresa</h4>
                <form id="generalSettingsForm">
                    <input type="hidden" id="company_user_id" name="user_id">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="company-nombre" class="form-label">Nombre Comercial</label>
                            <input type="text" class="form-control" id="company-nombre" name="nombre" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="company-razon_social" class="form-label">Razón Social</label>
                            <input type="text" class="form-control" id="company-razon_social" name="razon_social">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="company-nit" class="form-label">NIT</label>
                            <input type="text" class="form-control" id="company-nit" name="nit" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="company-nrc" class="form-label">NRC</label>
                            <input type="text" class="form-control" id="company-nrc" name="nrc">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="company-actividad_economica" class="form-label">Actividad Económica</label>
                        <input type="text" class="form-control" id="company-actividad_economica" name="actividad_economica">
                    </div>
                    <div class="mb-3">
                        <label for="company-direccion" class="form-label">Dirección Fiscal</label>
                        <input type="text" class="form-control" id="company-direccion" name="direccion">
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label for="company-municipio" class="form-label">Municipio</label>
                            <input type="text" class="form-control" id="company-municipio" name="municipio">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="company-departamento" class="form-label">Departamento</label>
                            <input type="text" class="form-control" id="company-departamento" name="departamento">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="company-pais" class="form-label">País</label>
                            <input type="text" class="form-control" id="company-pais" name="pais" value="El Salvador">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> Guardar Información de la Empresa</button>
                </form>
            </div>

            <!-- <div class="tab-pane fade" id="tax" role="tabpanel" aria-labelledby="tax-tab">
                <h4 class="mb-3">Configuración Financiera</h4>
                <form id="financialSettingsForm">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="tax-rate" class="form-label">Tasa de IVA/Impuesto (%)</label>
                            <input type="number" step="0.01" class="form-control" id="tax-rate" name="tax_rate" 
                                   value="<?= htmlspecialchars($settings['tax_rate'] ?? 0.00) ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="currency-symbol" class="form-label">Símbolo de Moneda</label>
                            <input type="text" class="form-control" id="currency-symbol" name="currency_symbol" 
                                   value="<?= htmlspecialchars($settings['currency_symbol'] ?? '$') ?>" required>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> Guardar Configuración Financiera</button>
                </form>
            </div> -->

            <div class="tab-pane fade" id="users" role="tabpanel" aria-labelledby="users-tab">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4>Usuarios y Permisos</h4>
                    <button class="btn btn-success" data-bs-toggle="modal" data-bs-target="#userModal">
                        <i class="fas fa-user-plus me-1"></i> Nuevo Usuario
                    </button>
                </div>
                <div class="table-responsive">
                    <table id="usersTable" class="table table-striped">
                        <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Email</th>
                                <th>Rol</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="usersTableBody">
                            <tr><td colspan="4" class="text-center">Cargando usuarios...</td></tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <div class="modal fade" id="userModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form id="userForm">
                    <input type="hidden" id="user-id" name="id">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title" id="userModalTitle">Nuevo Usuario</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <label for="user-nombre" class="form-label">Nombre Completo <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="user-nombre" name="nombre" required>
                        </div>
                        <div class="mb-3">
                            <label for="user-email" class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="user-email" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="user-password" class="form-label">Contraseña <span class="text-danger" id="password-required">*</span></label>
                            <input type="password" class="form-control" id="user-password" name="password">
                            <small class="text-muted">Dejar en blanco para mantener la contraseña actual (solo al editar)</small>
                        </div>
                        <div class="mb-3">
                            <label for="user-rol" class="form-label">Rol <span class="text-danger">*</span></label>
                            <select class="form-select" id="user-rol" name="rol" required>
                                <option value="">Seleccionar rol...</option>
                                <option value="admin">Administrador</option>
                                <option value="cajero">Cajero</option>
                                <option value="vendedor">Vendedor</option>
                                <option value="supervisor">Supervisor</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="user-telefono" class="form-label">Teléfono</label>
                            <input type="tel" class="form-control" id="user-telefono" name="telefono">
                        </div>
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="user-activo" name="activo" checked>
                                <label class="form-check-label" for="user-activo">
                                    Usuario Activo
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-success"><i class="fas fa-save me-1"></i> Guardar Usuario</button>
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
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="../js/pos_api.js"></script>
    <script src="../js/pos_main.js"></script>
    <script src="../js/pos_settings.js"></script>

    <script>
        $(document).ready(function() {
             // La inicialización de DataTables para usuarios se hará dentro de pos_settings.js
             // después de que se carguen los datos por AJAX.
        });
    </script>
</body>
</html>