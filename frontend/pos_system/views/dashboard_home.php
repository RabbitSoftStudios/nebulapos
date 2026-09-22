<?php
$stats = [
    'sales_today' => 350.50,
    'transactions_today' => 12,
    'items_sold' => 89,
    'low_stock_count' => 7,
    'dte_pending' => 3
];
?>

<div class="row">
    <div class="col-md-3 mb-4">
        <div class="p-4 text-white rounded" style="background:#667eea">
            <h6>Ventas Hoy</h6>
            <h2>$<?= number_format($stats['sales_today'],2) ?></h2>
        </div>
    </div>

    <div class="col-md-3 mb-4">
        <div class="p-4 text-white rounded" style="background:#20c997">
            <h6>Productos Vendidos</h6>
            <h2><?= $stats['items_sold'] ?></h2>
        </div>
    </div>

    <div class="col-md-3 mb-4">
        <div class="p-4 text-white rounded" style="background:#ffc107">
            <h6>Stock Bajo</h6>
            <h2><?= $stats['low_stock_count'] ?></h2>
        </div>
    </div>

    <div class="col-md-3 mb-4">
        <div class="p-4 text-white rounded" style="background:#dc3545">
            <h6>DTE Pendientes</h6>
            <h2><?= $stats['dte_pending'] ?></h2>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header"><i class="fas fa-chart-area"></i> Ventas Semanales</div>
    <div class="card-body text-center" style="height:300px">
        [ Chart.js aquí ]
    </div>
</div>
