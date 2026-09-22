<?php
/**
 * Sistema POS - Dashboard (Panel de Control)
 * ============================================
 * Vista principal que muestra métricas clave, gráficos y resúmenes.
 * @package    POS System
 * @author     Nebula DET Team
 * @version    2.0.0
 */

// 1. Inclusión de Configuración y Controladores (Asumida)
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../includes/supabase_reports.php'; // Para inicializar Supabase
require_once __DIR__ . '/../includes/init_report.php';
require_once __DIR__ . '/../controllers/auth_controller.php'; // Verifica sesión
require_once __DIR__ . '/../controllers/dashboard_controller.php'; // Controladores de datos del dashboard

// Asumir que auth_controller.php define $cashier y $registerNumber
// Si no están definidos, se usan valores por defecto para no romper los partials
$cashier = $_SESSION['cashier'] ?? ['name' => 'Invitado'];
$registerNumber = $_SESSION['register_number'] ?? '00';

// Simulación de obtención de datos del controlador
// En un sistema real, estas funciones harían llamadas a Supabase a través del cliente inicializado en database.php
//$metrics = fetch_dashboard_metrics($supabase);  REPARAR ESTA LINEA Y LA QUE SIGUE, SE ANIDAN CON 13,14,15 LINE
//$top_products = fetch_top_selling_products($supabase);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars(APP_NAME) ?> | Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="../public/css/pos_styles.css">
</head>
<body class="d-flex flex-column min-vh-100">

    <?php include_once __DIR__ . '/partials/header.php'; ?>
    <?php include_once __DIR__ . '/partials/sidebar.php'; ?>

    <main class="container-fluid mt-4 flex-grow-1">
        <h2 class="mb-4"><i class="fas fa-tachometer-alt me-2"></i> Panel de Control</h2>

        <div class="row g-4 mb-4">
            
            <div class="col-xl-3 col-md-6">
                <div class="card bg-primary text-white shadow-sm h-100">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-8">
                                <div class="fs-5 fw-bold mb-1">Ventas Hoy</div>
                                <div class="h3 mb-0">$<?= number_format($metrics['sales_today'] ?? 0.00, 2) ?></div>
                            </div>
                            <div class="col-4 text-end">
                                <i class="fas fa-money-bill-wave fa-3x opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card bg-success text-white shadow-sm h-100">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-8">
                                <div class="fs-5 fw-bold mb-1">Transacciones</div>
                                <div class="h3 mb-0"><?= number_format($metrics['transactions_today'] ?? 0) ?></div>
                            </div>
                            <div class="col-4 text-end">
                                <i class="fas fa-receipt fa-3x opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card bg-info text-white shadow-sm h-100">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-8">
                                <div class="fs-5 fw-bold mb-1">Total Clientes</div>
                                <div class="h3 mb-0"><?= number_format($metrics['total_customers'] ?? 0) ?></div>
                            </div>
                            <div class="col-4 text-end">
                                <i class="fas fa-users fa-3x opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card bg-warning text-dark shadow-sm h-100">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-8">
                                <div class="fs-5 fw-bold mb-1">Alerta Stock</div>
                                <div class="h3 mb-0"><?= number_format($metrics['low_stock_products'] ?? 0) ?></div>
                            </div>
                            <div class="col-4 text-end">
                                <i class="fas fa-exclamation-triangle fa-3x opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-white fw-bold">Ventas de los Últimos 7 Días</div>
                    <div class="card-body">
                        <canvas id="salesChart" style="max-height: 400px;"></canvas>
                        <div class="text-center text-muted" id="salesChartPlaceholder">
                            <i class="fas fa-chart-line fa-2x"></i>
                            <p>Cargando datos del gráfico de ventas...</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-white fw-bold">Top 5 Productos Vendidos (Hoy)</div>
                    <ul class="list-group list-group-flush">
                        <?php if (!empty($top_products)): ?>
                            <?php foreach ($top_products as $product): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <?= htmlspecialchars($product['name']) ?>
                                    <span class="badge bg-primary rounded-pill"><?= htmlspecialchars($product['quantity']) ?> uds</span>
                                </li>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <li class="list-group-item text-muted text-center">
                                No hay ventas registradas hoy.
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </div>
    </main>

    <?php include_once __DIR__ . '/partials/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
    <script src="../public/js/pos_api.js"></script>
    <script src="../public/js/pos_main.js"></script>
    <script src="../public/js/dashboard_charts.js"></script> 
</body>
</html>