<?php
/**
 * Sistema POS - Generador de Reportes
 * =====================================
 * PDO Pooled Edition
 * Team MYTS
 */

require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../includes/pg_connection.php';
require_once __DIR__ . '/../includes/auth.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$db = pg_pool();

$reportTypes = [
    'sales_by_date' => 'Ventas por Rango de Fechas',
    'top_products' => 'Productos Más Vendidos',
    'inventory_stock' => 'Stock de Inventario'
];

$reportData = [];
$reportTitle = null;

/* ===============================
   PROCESAMIENTO DEL FORMULARIO
================================ */
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $type = $_POST['report_type'] ?? null;
    $start = $_POST['start_date'] ?? null;
    $end   = $_POST['end_date'] ?? null;

    switch ($type) {

        /* -----------------------------
           VENTAS POR FECHA
        ------------------------------*/
        case 'sales_by_date':
            $stmt = $db->prepare("
                SELECT DATE(fecha) as fecha, SUM(total) as total
                FROM ventas
                WHERE fecha BETWEEN :start AND :end
                GROUP BY DATE(fecha)
                ORDER BY fecha
            ");
            $stmt->execute([
                ':start' => $start,
                ':end'   => $end
            ]);
            $reportData = $stmt->fetchAll();
            $reportTitle = "Ventas del {$start} al {$end}";
            break;

        /* -----------------------------
           PRODUCTOS MÁS VENDIDOS
        ------------------------------*/
        case 'top_products':
            $stmt = $db->query("
                SELECT p.nombre_producto, SUM(dv.cantidad) as total_vendido
                FROM detalle_ventas dv
                JOIN dte_productos p ON p.id = dv.producto_id
                GROUP BY p.nombre_producto
                ORDER BY total_vendido DESC
                LIMIT 10
            ");
            $reportData = $stmt->fetchAll();
            $reportTitle = "Top 10 Productos Más Vendidos";
            break;

        /* -----------------------------
           INVENTARIO
        ------------------------------*/
        case 'inventory_stock':
            $stmt = $db->query("
                SELECT codigo_producto, nombre_producto, stock_actual, stock_minimo
                FROM dte_productos
                WHERE borrado_logico = false
                ORDER BY nombre_producto
            ");
            $reportData = $stmt->fetchAll();
            $reportTitle = "Reporte de Inventario";
            break;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title><?= APP_NAME ?> | Reportes</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
<link rel="stylesheet" href="<?= BASE_URL ?>/css/pos_styles.css">
</head>

<body class="d-flex flex-column min-vh-100">

<?php include __DIR__ . '/partials/header.php'; ?>
<?php include __DIR__ . '/partials/sidebar.php'; ?>

<main class="container-fluid mt-4 flex-grow-1">
<h2 class="mb-4"><i class="fas fa-chart-line me-2"></i> Reportes</h2>

<form method="POST" class="row g-3 mb-4">
    <div class="col-md-4">
        <label class="form-label">Tipo de Reporte</label>
        <select name="report_type" class="form-select" required>
            <option value="">Seleccione...</option>
            <?php foreach ($reportTypes as $k => $v): ?>
                <option value="<?= $k ?>"><?= $v ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="col-md-3">
        <label class="form-label">Fecha Inicio</label>
        <input type="date" name="start_date" class="form-control">
    </div>

    <div class="col-md-3">
        <label class="form-label">Fecha Fin</label>
        <input type="date" name="end_date" class="form-control">
    </div>

    <div class="col-md-2 d-flex align-items-end">
        <button class="btn btn-primary w-100">
            <i class="fas fa-file-alt"></i> Generar
        </button>
    </div>
</form>

<div class="card shadow-sm">
<div class="card-header bg-secondary text-white">
    <?= $reportTitle ?? 'Resultados' ?>
</div>
<div class="card-body">

<?php if ($reportData): ?>
<table class="table table-striped">
<thead>
<tr>
<?php foreach (array_keys($reportData[0]) as $col): ?>
    <th><?= htmlspecialchars(ucwords(str_replace('_',' ', $col))) ?></th>
<?php endforeach; ?>
</tr>
</thead>
<tbody>
<?php foreach ($reportData as $row): ?>
<tr>
<?php foreach ($row as $val): ?>
    <td><?= htmlspecialchars($val) ?></td>
<?php endforeach; ?>
</tr>
<?php endforeach; ?>
</tbody>
</table>
<?php else: ?>
<div class="alert alert-info text-center">
    Seleccione un reporte y genere resultados.
</div>
<?php endif; ?>

</div>
</div>
</main>

<?php include __DIR__ . '/partials/footer.php'; ?>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>