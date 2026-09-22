<?php
// ================================
// DASHBOARD ADMINISTRATIVO
// ================================

session_start();

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/config/constants.php';
//require_once __DIR__ . '/includes/auth.php';

// Verificar autenticación
checkAdminAuth();

// Obtener estadísticas
$stats = getDashboardStats();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?= APP_NAME ?> - Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <style>
        body {
            background-color: #f5f6fa;
            overflow-x: hidden;
        }

        /* ===== Sidebar ===== */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: 250px;
            background-color: #212529;
            padding-top: 60px;
            transition: all 0.3s ease;
            z-index: 1000;
        }

        .sidebar a {
            color: #ced4da;
            padding: 12px 20px;
            display: block;
            text-decoration: none;
            font-size: 16px;
        }

        .sidebar a i {
            width: 22px;
        }

        .sidebar a:hover {
            background-color: #343a40;
            color: #fff;
        }

        .sidebar .brand {
            position: fixed;
            top: 0;
            left: 0;
            width: 250px;
            height: 60px;
            background: #111;
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            z-index: 1100;
        }

        /* ===== Content ===== */
        .content {
            margin-left: 250px;
            padding: 25px;
            transition: all 0.3s ease;
        }

        /* ===== Cards ===== */
        .card-stat {
            border-radius: 12px;
            padding: 20px;
            color: #fff;
            box-shadow: 0 6px 14px rgba(0,0,0,0.1);
        }

        /* ===== Topbar ===== */
        .topbar {
            background: #fff;
            padding: 15px 20px;
            border-radius: 12px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.08);
            margin-bottom: 25px;
        }

        /* ===== Responsive ===== */
        @media (max-width: 768px) {
            .sidebar {
                left: -250px;
            }

            .sidebar.show {
                left: 0;
            }

            .content {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>

<!-- ===== Sidebar ===== -->
<div class="sidebar" id="sidebar">
    <div class="brand"><?= APP_NAME ?></div>

    <a href="dashboard.php"><i class="fas fa-gauge-high"></i> Dashboard</a>
    <a href="pos.php"><i class="fas fa-cash-register"></i> POS</a>
    <a href="views/pos_products.php"><i class="fas fa-boxes"></i> Productos</a>
    <a href="views/pos_reports.php"><i class="fas fa-chart-line"></i> Reportes</a>
    <a href="views/pos_customers.php"><i class="fas fa-user"></i> Clientes</a>
    <a href="views/pos_sales.php"><i class="fa-solid fa-circle-dollar-to-slot"></i> Ventas</a>
    <a href="views/pos_settings.php"><i class="fa-solid fa-gears"></i> Configuracion</a>
    <a href="logout.php"><i class="fas fa-right-from-bracket"></i> Cerrar sesión</a>
</div>

<!-- ===== Content ===== -->
<div class="content">

    <!-- Topbar -->
    <div class="topbar d-flex justify-content-between align-items-center">
        <div class="d-flex align-items-center gap-3">
            <button class="btn btn-outline-secondary d-md-none" id="toggleSidebar">
                <i class="fas fa-bars"></i>
            </button>
            <h4 class="mb-0"><i class="fas fa-gauge-high"></i> Dashboard Administrativo</h4>
        </div>

        <span class="badge bg-primary fs-6">
            <?= htmlspecialchars($_SESSION['cashier']['name'] ?? 'Administrador') ?>
        </span>
    </div>

    <!-- Estadísticas -->
    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card-stat" style="background:linear-gradient(135deg,#667eea,#764ba2)">
                <h6>Ventas Hoy</h6>
                <h2>$<?= number_format($stats['sales_today'],2) ?></h2>
                <small><?= $stats['transactions_today'] ?> transacciones</small>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card-stat" style="background:linear-gradient(135deg,#20c997,#17a2b8)">
                <h6>Productos Vendidos</h6>
                <h2><?= $stats['items_sold'] ?></h2>
                <small>Ítems</small>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card-stat" style="background:linear-gradient(135deg,#ffc107,#fd7e14)">
                <h6>Stock Bajo</h6>
                <h2><?= $stats['low_stock_count'] ?></h2>
                <small>Alertas</small>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card-stat" style="background:linear-gradient(135deg,#dc3545,#f87d93)">
                <h6>DTE Pendientes</h6>
                <h2><?= $stats['dte_pending'] ?></h2>
                <small>Por enviar</small>
            </div>
        </div>
    </div>

    <!-- Contenido -->
    <div class="row">
        <div class="col-lg-8 mb-4">
            <div class="card">
                <div class="card-header"><i class="fas fa-chart-area"></i> Ventas Semanales</div>
                <div class="card-body text-center" style="height:300px">
                    [ Gráfico aquí ]
                </div>
            </div>
        </div>

        <div class="col-lg-4 mb-4">
            <div class="card">
                <div class="card-header"><i class="fas fa-bell"></i> Alertas</div>
                <ul class="list-group list-group-flush">
                    <li class="list-group-item text-danger">Producto agotado</li>
                    <li class="list-group-item text-warning">Stock mínimo</li>
                    <li class="list-group-item text-success">Venta registrada</li>
                </ul>
            </div>
        </div>
    </div>

</div>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    document.getElementById('toggleSidebar').addEventListener('click', function () {
        document.getElementById('sidebar').classList.toggle('show');
    });
</script>

</body>
</html>

<?php
// ================================
// FUNCIONES
// ================================

function checkAdminAuth()
{
    if (empty($_SESSION['pos_authenticated'])) {
        header('Location: login.php');
        exit();
    }
}

function getDashboardStats()
{
    return [
        'sales_today'        => 350.50,
        'transactions_today' => 12,
        'items_sold'         => 89,
        'low_stock_count'    => 7,
        'dte_pending'        => 3
    ];
}
