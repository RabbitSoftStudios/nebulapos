<?php
/**
 * Sistema POS - Generador de Reportes
 * =====================================
 * Vista para seleccionar, generar y visualizar reportes estadísticos.
 * @package    POS System
 * @author     Nebula DET Team
 * @version    2.0.0
 */

// 1. Inclusión de Configuración y Controladores (Asumida)
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../controllers/auth_controller.php';
require_once __DIR__ . '/../includes/supabase_reports.php';
// Asumir que auth_controller.php define $cashier y $registerNumber
//$cashier = $_SESSION['cashier'] ?? ['name' => 'Invitado'];
//$registerNumber = $_SESSION['register_number'] ?? '00';

// Lista de tipos de reportes disponibles
$report_types = [
    'sales_by_date' => 'Ventas por Rango de Fechas',
    'top_products' => 'Productos Más Vendidos',
    'inventory_stock' => 'Reporte de Stock',
    'cashier_performance' => 'Rendimiento de Cajeros'
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars(APP_NAME) ?> | Reportes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="../public/css/pos_styles.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/2.0.8/css/dataTables.bootstrap5.min.css">
</head>
<body class="d-flex flex-column min-vh-100">

    <?php include_once __DIR__ . '/partials/header.php'; ?>
    <?php include_once __DIR__ . '/partials/sidebar.php'; ?>

    <main class="container-fluid mt-4 flex-grow-1">
        <h2 class="mb-4"><i class="fas fa-chart-line me-2"></i> Generación de Reportes</h2>

        <div class="card shadow-sm mb-4">
            <div class="card-header bg-white fw-bold">Parámetros del Reporte</div>
            <div class="card-body">
                <form id="reportForm" class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label for="report-type" class="form-label">Tipo de Reporte</label>
                        <select class="form-select" id="report-type" name="report_type" required>
                            <option value="">Seleccione...</option>
                            <?php foreach ($report_types as $key => $label): ?>
                                <option value="<?= htmlspecialchars($key) ?>"><?= htmlspecialchars($label) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="col-md-3 date-filter">
                        <label for="start-date" class="form-label">Fecha Inicial</label>
                        <input type="date" class="form-control" id="start-date" name="start_date">
                    </div>

                    <div class="col-md-3 date-filter">
                        <label for="end-date" class="form-label">Fecha Final</label>
                        <input type="date" class="form-control" id="end-date" name="end_date">
                    </div>

                    <div class="col-md-2">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-file-alt me-1"></i> Generar
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <div class="card shadow-sm">
            <div class="card-header bg-secondary text-white fw-bold" id="report-title">
                Resultados del Reporte
            </div>
            <div class="card-body">
                <div id="report-results">
                    <div class="alert alert-info text-center">
                        <i class="fas fa-arrow-up me-2"></i> Seleccione un tipo de reporte y haga clic en 'Generar'.
                    </div>
                    </div>
            </div>
        </div>
    </main>

    <?php include_once __DIR__ . '/partials/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.datatables.net/2.0.8/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
    <script src="../public/js/pos_api.js"></script>
    <script src="../public/js/pos_main.js"></script>
    <script src="../public/js/pos_reports.js"></script>
</body>
</html>