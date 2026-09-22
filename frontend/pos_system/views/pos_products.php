<?php
/**
 * Sistema POS - Gestión de Productos (DEFINITIVO PDO)
 * ==================================================
 * Arquitectura: PHP puro + PostgreSQL PDO pooled
 * Team MYTS
 * Fecha: 2026-01-07
 */

require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/pg_connection.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!defined('BASE_URL')) {
    define('BASE_URL', '/posys/pos_system');
}

$db = pg_pool();

/* ==================================================
   AJAX HANDLER
================================================== */
if (isset($_POST['ajax']) && $_POST['ajax'] === '1') {
    header('Content-Type: application/json');

    try {
        $action = $_POST['action'] ?? '';

        switch ($action) {

            case 'list':
                $page = max(1, (int)($_POST['page'] ?? 1));
                $limit = 10;
                $offset = ($page - 1) * $limit;

                $total = (int)$db
                    ->query("SELECT COUNT(*) FROM dte_productos WHERE borrado_logico = false")
                    ->fetchColumn();

                $stmt = $db->prepare(
                    "SELECT id, codigo_producto, nombre_producto, precio_venta, stock_actual, stock_minimo
                     FROM dte_productos
                     WHERE borrado_logico = false
                     ORDER BY id DESC
                     LIMIT :limit OFFSET :offset"
                );
                $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
                $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
                $stmt->execute();

                echo json_encode([
                    'success' => true,
                    'data' => $stmt->fetchAll(),
                    'total' => $total
                ]);
                exit;

            case 'get':
                $stmt = $db->prepare("SELECT * FROM dte_productos WHERE id = :id");
                $stmt->execute([':id' => $_POST['id']]);
                echo json_encode(['success' => true, 'data' => $stmt->fetch()]);
                exit;

            case 'create':
                $stmt = $db->prepare(
                    "INSERT INTO dte_productos
                    (codigo_producto, nombre_producto, precio_venta, stock_actual, stock_minimo, es_venta_libre)
                    VALUES (:codigo, :nombre, :precio, :stock, :stock_min, :venta_libre)
                    RETURNING id"
                );
                $stmt->execute([
                    ':codigo' => $_POST['codigo_producto'],
                    ':nombre' => $_POST['nombre_producto'],
                    ':precio' => $_POST['precio_venta'] ?? 0,
                    ':stock' => $_POST['stock_actual'] ?? 0,
                    ':stock_min' => $_POST['stock_minimo'] ?? 0,
                    ':venta_libre' => isset($_POST['es_venta_libre'])
                ]);

                echo json_encode(['success' => true, 'id' => $stmt->fetchColumn()]);
                exit;

            case 'update':
                $stmt = $db->prepare(
                    "UPDATE dte_productos SET
                        codigo_producto = :codigo,
                        nombre_producto = :nombre,
                        precio_venta = :precio,
                        stock_actual = :stock,
                        stock_minimo = :stock_min,
                        es_venta_libre = :venta_libre,
                        actualizado_el = now()
                     WHERE id = :id"
                );
                $stmt->execute([
                    ':codigo' => $_POST['codigo_producto'],
                    ':nombre' => $_POST['nombre_producto'],
                    ':precio' => $_POST['precio_venta'],
                    ':stock' => $_POST['stock_actual'],
                    ':stock_min' => $_POST['stock_minimo'],
                    ':venta_libre' => isset($_POST['es_venta_libre']),
                    ':id' => $_POST['id']
                ]);

                echo json_encode(['success' => true]);
                exit;

            case 'delete':
                $stmt = $db->prepare(
                    "UPDATE dte_productos SET borrado_logico = true WHERE id = :id"
                );
                $stmt->execute([':id' => $_POST['id']]);

                echo json_encode(['success' => true]);
                exit;
        }

        echo json_encode(['success' => false, 'error' => 'Acción no válida']);
        exit;

    } catch (Throwable $e) {
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Productos</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="container py-4">

<h3>Gestión de Productos</h3>
<button class="btn btn-primary mb-3" id="btnNew">Nuevo Producto</button>

<table class="table table-striped" id="productsTable">
<thead>
<tr><th>ID</th><th>Código</th><th>Nombre</th><th>Precio</th><th>Stock</th><th>Acciones</th></tr>
</thead>
<tbody></tbody>
</table>
<div id="pagination"></div>

<div class="modal fade" id="productModal" tabindex="-1">
<div class="modal-dialog">
<div class="modal-content">
<form id="productForm">
<div class="modal-header"><h5 id="modalTitle">Producto</h5></div>
<div class="modal-body">
<input type="hidden" name="ajax" value="1">
<input type="hidden" name="action" id="action">
<input type="hidden" name="id" id="id">

<input class="form-control mb-2" name="codigo_producto" placeholder="Código" required>
<input class="form-control mb-2" name="nombre_producto" placeholder="Nombre" required>
<input class="form-control mb-2" name="precio_venta" type="number" step="0.01" placeholder="Precio">
<input class="form-control mb-2" name="stock_actual" type="number" placeholder="Stock">
<input class="form-control mb-2" name="stock_minimo" type="number" placeholder="Stock mínimo">
<label><input type="checkbox" name="es_venta_libre" checked> Venta libre</label>
</div>
<div class="modal-footer">
<button type="submit" class="btn btn-success">Guardar</button>
</div>
</form>
</div>
</div>
</div>

<script>
let currentPage = 1;

function loadProducts(page = 1) {
  currentPage = page;
  $.post('', { ajax: 1, action: 'list', page }, res => {
    const tbody = $('#productsTable tbody').html('');
    res.data.forEach(p => {
      tbody.append(`<tr>
        <td>${p.id}</td>
        <td>${p.codigo_producto}</td>
        <td>${p.nombre_producto}</td>
        <td>$${parseFloat(p.precio_venta).toFixed(2)}</td>
        <td>${p.stock_actual}</td>
        <td>
          <button class="btn btn-sm btn-warning" onclick="editProduct(${p.id})">Editar</button>
          <button class="btn btn-sm btn-danger" onclick="deleteProduct(${p.id})">Eliminar</button>
        </td>
      </tr>`);
    });
    renderPagination(res.total);
  }, 'json');
}

function renderPagination(total) {
  const pages = Math.ceil(total / 10);
  let html = '';
  for (let i = 1; i <= pages; i++) {
    html += `<button class="btn ${i===currentPage?'btn-primary':'btn-secondary'} mx-1" onclick="loadProducts(${i})">${i}</button>`;
  }
  $('#pagination').html(html);
}

$('#btnNew').click(() => {
  $('#productForm')[0].reset();
  $('#action').val('create');
  new bootstrap.Modal('#productModal').show();
});

function editProduct(id) {
  $.post('', { ajax:1, action:'get', id }, res => {
    Object.keys(res.data).forEach(k => {
      $(`[name=${k}]`).val(res.data[k]);
    });
    $('#action').val('update');
    new bootstrap.Modal('#productModal').show();
  }, 'json');
}

function deleteProduct(id) {
  Swal.fire({ title:'¿Eliminar?', showCancelButton:true }).then(r => {
    if (r.isConfirmed) {
      $.post('', { ajax:1, action:'delete', id }, () => loadProducts());
    }
  });
}

$('#productForm').submit(e => {
  e.preventDefault();
  $.post('', $('#productForm').serialize(), res => {
    if (res.success) {
      bootstrap.Modal.getInstance(document.getElementById('productModal')).hide();
      loadProducts(currentPage);
    }
  }, 'json');
});

$(loadProducts);
</script>
</body>
</html>