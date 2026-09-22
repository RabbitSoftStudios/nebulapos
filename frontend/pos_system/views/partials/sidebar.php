<?php
/**
 * Sistema POS - Parcial de Barra Lateral (Sidebar)
 * ==================================================
 * Menú de navegación principal para las vistas de gestión.
 * * @package    POS System
 * @author     Nebula DET Team
 * @version    2.0.0
 */

$cashierName = $cashier['name'] ?? 'Usuario';
?>

<div class="offcanvas offcanvas-start pos-sidebar bg-dark text-white" tabindex="-1" id="posSidebar" aria-labelledby="posSidebarLabel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="posSidebarLabel">
            <i class="fas fa-cogs me-2"></i> Menú de Gestión
        </h5>
        <button type="button" class="btn-close text-reset bg-white" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body p-0">
        <div class="p-3 mb-3 border-bottom">
            <h6 class="text-info"><?= htmlspecialchars($cashierName) ?></h6>
            <small class="text-white-50">Cajero Activo</small>
        </div>

        <ul class="nav nav-pills flex-column mb-auto">
            <li class="nav-item">
                <a href="/views/pos_dashboard.php" class="nav-link text-white active" aria-current="page">
                    <i class="fas fa-tachometer-alt me-2"></i> Dashboard
                </a>
            </li>
            <li>
                <a href="/views/pos_sale.php" class="nav-link text-white">
                    <i class="fas fa-cash-register me-2"></i> Punto de Venta (POS)
                </a>
            </li>
            <li>
                <a href="/views/pos_products.php" class="nav-link text-white">
                    <i class="fas fa-box me-2"></i> Gestión de Productos
                </a>
            </li>
            <li>
                <a href="/views/pos_customers.php" class="nav-link text-white">
                    <i class="fas fa-users me-2"></i> Clientes
                </a>
            </li>
            <li>
                <a href="/views/pos_reports.php" class="nav-link text-white">
                    <i class="fas fa-chart-line me-2"></i> Reportes
                </a>
            </li>
            <li>
                <a href="/views/pos_settings.php" class="nav-link text-white">
                    <i class="fas fa-cogs me-2"></i> Configuración
                </a>
            </li>
        </ul>
        
        <div class="mt-auto p-3 border-top">
            <a href="/logout.php" class="btn btn-outline-danger w-100">
                <i class="fas fa-sign-out-alt me-2"></i> Cerrar Sesión
            </a>
        </div>
    </div>
</div>