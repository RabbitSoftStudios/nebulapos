<?php
/**
 * Sistema POS - Dashboard Administrativo
 * ========================================
 * Pantalla principal para administración.
 * * @package    POS System
 * @author     Nebula DET Team
 * @version    2.0.0
 */

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/config/constants.php';
//require_once __DIR__ . '/includes/auth.php';

// Verificar autenticación y redirigir al POS si no es admin
checkAdminAuth(); 

// Obtener estadísticas (simuladas)
$stats = getDashboardStats();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= APP_NAME ?> - Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { background-color: #f8f9fa; }
        .sidebar {
            height: 100vh;
            width: 250px;
            position: fixed;
            top: 0;
            left: 0;
            background-color: #343a40;
            padding-top: 20px;
        }
        .sidebar a {
            padding: 10px 15px;
            text-decoration: none;
            font-size: 18px;
            color: #f8f9fa;
            display: block;
        }
        .sidebar a:hover {
            background-color: #495057;
        }
        .content {
            margin-left: 250px;
            padding: 20px;
        }
        .card-stat {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>
    <div class="sidebar">
        <h3 class="text-white text-center mb-4"><?= APP_NAME ?></h3>
        <a href="dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
        <a href="pos.php"><i class="fas fa-cash-register"></i> POS (Venta)</a>
        <a href="views/pos_products.php"><i class="fas fa-boxes"></i> Productos</a>
        <a href="views/pos_reports.php"><i class="fas fa-chart-line"></i> Reportes</a>
        <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Cerrar Sesión</a>
    </div>

    <div class="content">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1><i class="fas fa-tachometer-alt"></i> Dashboard Administrativo</h1>
            <span class="badge bg-primary fs-6">Usuario: <?= htmlspecialchars($_SESSION['cashier']['name'] ?? 'Admin') ?></span>
        </div>
        
        <div class="row">
            <div class="col-md-3 mb-4">
                <div class="card-stat">
                    <h4>Ventas Hoy</h4>
                    <h1>$<?= number_format($stats['sales_today'], 2) ?></h1>
                    <p><?= $stats['transactions_today'] ?> transacciones</p>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="card-stat" style="background: linear-gradient(135deg, #20c997 0%, #17a2b8 100%);">
                    <h4>Productos Vendidos</h4>
                    <h1><?= $stats['items_sold'] ?></h1>
                    <p>Total de ítems</p>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="card-stat" style="background: linear-gradient(135deg, #ffc107 0%, #fd7e14 100%);">
                    <h4>Stock Bajo</h4>
                    <h1><?= $stats['low_stock_count'] ?></h1>
                    <p>Productos cerca del mínimo</p>
                </div>
            </div>
            <div class="col-md-3 mb-4">
                <div class="card-stat" style="background: linear-gradient(135deg, #dc3545 0%, #f87d93 100%);">
                    <h4>DTE Pendientes</h4>
                    <h1><?= $stats['dte_pending'] ?></h1>
                    <p>Documentos por enviar a DGII</p>
                </div>
            </div>
        </div>
        
        <div class="row mt-4">
            <div class="col-md-8">
                <div class="card">
                    <div class="card-header"><i class="fas fa-chart-area"></i> Ventas de la Semana</div>
                    <div class="card-body">
                        <div style="height: 300px; background-color: #eee; text-align: center; line-height: 300px;">
                            [Gráfico de Ventas Semanales]
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header"><i class="fas fa-bell"></i> Alertas Recientes</div>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item text-danger"><i class="fas fa-exclamation-triangle"></i> Producto X agotado</li>
                        <li class="list-group-item text-warning"><i class="fas fa-box-open"></i> Producto Y en stock mínimo (3)</li>
                        <li class="list-group-item text-info"><i class="fas fa-cloud-upload-alt"></i> Sincronización offline completada</li>
                        <li class="list-group-item text-success"><i class="fas fa-check-circle"></i> Nueva venta registrada (DTE #123)</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php
// Función para verificar autenticación de administrador
function checkAdminAuth() {
    session_start();
    
    // Asumir que 'pos_authenticated' es suficiente, o añadir un check de permisos
    if (!isset($_SESSION['pos_authenticated']) || $_SESSION['pos_authenticated'] !== true) {
        header('Location: login.php');
        exit();
    }
    // Simulación: Redirigir al POS si no tiene un flag de admin (NO IMPLEMENTADO)
}

// Función para obtener estadísticas simuladas
function getDashboardStats() {
    return [
        'sales_today' => 350.50,
        'transactions_today' => 12,
        'items_sold' => 89,
        'low_stock_count' => 7,
        'dte_pending' => 3
    ];
}
?>
