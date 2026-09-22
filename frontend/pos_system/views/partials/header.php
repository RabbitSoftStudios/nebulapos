<?php
/**
 * Sistema POS - Parcial de Cabecera (Header)
 * ===========================================
 * Barra de navegación superior para el sistema POS.
 * * @package    POS System
 * @author     Nebula DET Team
 * @version    2.0.0
 */

// Se asume que las variables $cashier y $registerNumber están definidas 
// en la vista principal (pos_sale.php o dashboard.php) antes de incluir este partial.
$cashierName = $cashier['name'] ?? 'Cajero Desconocido';
$registerNumber = $registerNumber ?? '000';
?>
<header class="pos-header">
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-sm">
        <div class="container-fluid">
            <a class="navbar-brand d-flex align-items-center" href="/dashboard.php">
                <i class="fas fa-cash-register me-2"></i>
                <?= htmlspecialchars(APP_NAME) ?>
            </a>
            
            <button class="btn btn-outline-light d-lg-none me-2" type="button" data-bs-toggle="offcanvas" data-bs-target="#posSidebar" aria-controls="posSidebar">
                <i class="fas fa-bars"></i>
            </button>

            <ul class="navbar-nav mx-auto d-none d-lg-flex">
                <li class="nav-item me-4">
                    <span class="nav-link text-white-50"><i class="fas fa-user-tie me-1"></i> Cajero: <?= htmlspecialchars($cashierName) ?></span>
                </li>
                <li class="nav-item">
                    <span class="nav-link text-white-50"><i class="fas fa-box-open me-1"></i> Caja #<?= htmlspecialchars($registerNumber) ?></span>
                </li>
            </ul>

            <div class="d-flex align-items-center">
                <a href="/dashboard.php" class="btn btn-outline-info me-2" title="Dashboard">
                    <i class="fas fa-home"></i>
                </a>
                
                <span class="badge rounded-pill bg-success me-2" id="pos-connection-status">
                    <i class="fas fa-globe"></i> Online
                </span>

                <a href="/logout.php" class="btn btn-danger" title="Cerrar Sesión">
                    <i class="fas fa-sign-out-alt"></i>
                </a>
            </div>
        </div>
    </nav>
</header>