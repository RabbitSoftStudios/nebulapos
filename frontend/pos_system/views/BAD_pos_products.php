<?php
/**
 * Sistema POS - Gestión de Productos
 * ====================================
 * Vista para el manejo de inventario: CRUD de productos.
 * @package    POS System
 * @author     Nebula DET Team
 * @version    2.0.0
 */

// 1. Inclusión de Configuración y Controladores
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../controllers/product_controller.php';

// Verificación de sesión
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$cashier = $_SESSION['cashier'] ?? ['name' => 'Invitado'];
$registerNumber = POS_CAJA_NUMERO;

// Definir BASE_URL si no está definido
if (!defined('BASE_URL')) {
    define('BASE_URL', '/posys/pos_system');
}

// Los datos se cargan desde los controladores incluidos arriba ($products, $categories)
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars(APP_NAME) ?> | Productos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/pos_styles.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/2.0.8/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
</head>
<body class="d-flex flex-column min-vh-100">

    <?php include_once __DIR__ . '/partials/header.php'; ?>
    <?php include_once __DIR__ . '/partials/sidebar.php'; ?>

    <main class="container-fluid mt-4 flex-grow-1">
        <h2 class="mb-4"><i class="fas fa-box me-2"></i> Gestión de Productos</h2>

        <div class="card shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Inventario</h5>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#productModal" id="btn-add-product">
                    <i class="fas fa-plus-circle me-1"></i> Agregar Producto
                </button>
            </div>
            <div class="card-body">
                <input type="text" id="searchProducts"
                       class="form-control mb-3"
                       placeholder="Buscar producto por nombre o código">
                <div class="table-responsive">
                    <table id="productsTable" class="table table-striped table-hover">
                        <thead>
                            <tr>
                              <th data-col="id">ID <i class="fas fa-sort"></i></th>
                              <th data-col="codigo_producto">Código <i class="fas fa-sort"></i></th>
                              <th data-col="nombre_producto">Nombre <i class="fas fa-sort"></i></th>
                              <th data-col="precio_venta">Precio <i class="fas fa-sort"></i></th>
                              <th data-col="stock_actual">Stock <i class="fas fa-sort"></i></th>
                              <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                    <div id="pagination"></div>
                </div>
            </input>
        </div>
    </main>

    <?php if (!isset($_SESSION['membresia']) || (isset($_SESSION['membresia']) && $_SESSION['membresia'] == 'gratis')): ?>
    <!-- Formulario corto para membresía gratis o modo desarrollo (sin sesión) -->
    <div class="modal fade" id="productModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <form id="productForm">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title" id="productModalLabel">Agregar Nuevo Producto</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <input type="hidden" id="product-id" name="id">
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="codigo_producto" class="form-label">Código de Producto *</label>
                                <input type="text" class="form-control" id="codigo_producto" name="codigo_producto" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="codigo_barras" class="form-label">Código de Barras</label>
                                <input type="text" class="form-control" id="codigo_barras" name="codigo_barras">
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="nombre_producto" class="form-label">Nombre del Producto *</label>
                            <input type="text" class="form-control" id="nombre_producto" name="nombre_producto" required>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="precio_venta" class="form-label">Precio de Venta ($)</label>
                                <input type="number" step="0.01" class="form-control" id="precio_venta" name="precio_venta" placeholder="0.00" required>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label for="stock_actual" class="form-label">Stock Inicial</label>
                                <input type="number" step="0.01" class="form-control" id="stock_actual" name="stock_actual" placeholder="0.00">
                                <div class="form-text">Si deja vacío, será 0.</div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="stock_minimo" class="form-label">Stock Mínimo</label>
                                <input type="number" step="1" class="form-control" id="stock_minimo" name="stock_minimo" value="5">
                            </div>
                            <div class="col-md-6 mb-3">
                                <div class="form-check mt-4">
                                    <input class="form-check-input" type="checkbox" id="es_venta_libre" name="es_venta_libre" checked>
                                    <label class="form-check-label" for="es_venta_libre">
                                        Es Venta Libre
                                    </label>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary" id="save-product-btn">Guardar Producto</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php else: ?>
    <!-- Formulario largo para membresía pagada -->
    <!-- ===============================
     MODAL PRODUCTO (INSERT / EDIT)
    ================================ -->
    <div id="productModal" class="modal" style="display:none;">
      <div class="modal-content">
        <h3 id="modalTitle">Nuevo Producto</h3>

        <form id="productForm">
          <input type="hidden" name="action" value="create">
          <input type="hidden" name="id" id="product-id">

          <div class="grid">

            <label>
              Código Producto *
              <input type="text" name="codigo_producto" id="codigo_producto" required maxlength="50">
            </label>

            <label>
              Código Barras
              <input type="text" name="codigo_barras" id="codigo_barras" maxlength="100">
            </label>

            <label>
              Nombre Producto *
              <input type="text" name="nombre_producto" id="nombre_producto" required>
            </label>

            <label>
              Descripción Corta
              <input type="text" name="descripcion_corta" maxlength="100">
            </label>

            <label>
              Descripción Larga
              <textarea name="descripcion_larga"></textarea>
            </label>

            <label>
              Imagen URL
              <input type="url" name="imagen_url">
            </label>

            <label>
              Registro Sanitario
              <input type="text" name="registro_sanitario">
            </label>

            <label>
              Forma Farmacéutica
              <input type="text" name="forma_farmaceutica">
            </label>

            <label>
              Tipo Medicamento
              <input type="text" name="tipo_medicamento">
            </label>

            <label>
              Lote Activo
              <input type="text" name="lote_activo">
            </label>

            <label>
              Fecha Vencimiento
              <input type="date" name="fecha_vencimiento">
            </label>

            <label>
              Precio Costo
              <input type="number" step="0.01" name="precio_costo">
            </label>

            <label>
              Precio Venta
              <input type="number" step="0.01" name="precio_venta" id="precio_venta">
            </label>

            <label>
              Descuento %
              <input type="number" step="0.01" name="descuento_programado">
            </label>

            <label>
              Stock Actual
              <input type="number" step="0.01" name="stock_actual" id="stock_actual">
            </label>

            <label>
              Stock Mínimo
              <input type="number" step="0.01" name="stock_minimo" id="stock_minimo">
            </label>

            <label>
              Unidad Medida
              <select name="unidad_medida">
                <option value="unidad">Unidad</option>
                <option value="caja">Caja</option>
                <option value="frasco">Frasco</option>
                <option value="blister">Blister</option>
              </select>
            </label>

            <label>
              Venta Libre
              <input type="checkbox" name="es_venta_libre" id="es_venta_libre" checked>
            </label>

            <label>
              Controlado
              <input type="checkbox" name="es_controlado">
            </label>

          </div>

          <div class="actions">
            <button type="submit" id="save-product-btn">Guardar</button>
            <button type="button" onclick="closeProductModal()">Cancelar</button>
          </div>
        </form>
      </div>
    </div>
    <?php endif; ?>

    <?php include_once __DIR__ . '/partials/footer.php'; ?>

    <!-- jQuery DEBE cargarse PRIMERO -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    
    <!-- Luego Bootstrap (depende de jQuery) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- DataTables (depende de jQuery) -->
    <script src="https://cdn.datatables.net/2.0.8/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/2.0.8/js/dataTables.bootstrap5.min.js"></script>
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- Scripts personalizados (dependen de jQuery y librerías anteriores) -->
    <script src="<?= BASE_URL ?>/js/pos_api.js"></script>
    <script src="<?= BASE_URL ?>/js/pos_main.js"></script>
    <script src="<?= BASE_URL ?>/js/pos_products.js"></script>
    
    <script>
    const CAN_EDIT_PRODUCTS = <?= (isset($_SESSION['membresia']) && $_SESSION['membresia'] !== 'gratis') ? 'true' : 'false' ?>;
    </script>
    
    <!-- Paginador de productos -->
    <script>
    const API_URL = '<?= BASE_URL ?>/controllers/product_api.php';

    let currentPage = parseInt(localStorage.getItem('products_page')) || 1;
    let orderBy = localStorage.getItem('products_orderBy') || 'id';
    let direction = localStorage.getItem('products_direction') || 'DESC';

    /* =========================
       CARGAR PRODUCTOS
    ========================= */
    function loadProducts(page = 1) {
      currentPage = page;

      localStorage.setItem('products_page', currentPage);
      localStorage.setItem('products_orderBy', orderBy);
      localStorage.setItem('products_direction', direction);

      const url = `${API_URL}?action=list&page=${page}&orderBy=${orderBy}&direction=${direction}`;

      fetch(url)
        .then(r => r.json())
        .then(res => {
          const tbody = document.querySelector('#productsTable tbody');
          tbody.innerHTML = '';

          if (!res.success || res.data.length === 0) {
            tbody.innerHTML = `<tr><td colspan="6" class="text-center">No hay productos</td></tr>`;
            return;
          }

          res.data.forEach(p => {
            const stockClass = (p.stock_actual < (p.stock_minimo || 5))
              ? 'text-danger fw-bold'
              : '';

            const actions = CAN_EDIT_PRODUCTS
              ? `
                <button class="btn btn-sm btn-warning btn-edit"
                  data-id="${p.id}"
                  data-codigo="${p.codigo_producto}"
                  data-barras="${p.codigo_barras || ''}"
                  data-nombre="${p.nombre_producto}"
                  data-precio="${p.precio_venta}"
                  data-stock="${p.stock_actual}"
                  data-stockmin="${p.stock_minimo || 0}"
                  data-venta-libre="${p.es_venta_libre ? 1 : 0}"
                  data-bs-toggle="modal"
                  data-bs-target="#productModal">
                  <i class="fas fa-edit"></i>
                </button>
                <button class="btn btn-sm btn-danger btn-delete" data-id="${p.id}">
                  <i class="fas fa-trash"></i>
                </button>
              `
              : `<span class="text-muted">Solo lectura</span>`;

            tbody.innerHTML += `
              <tr>
                <td>${p.id}</td>
                <td>${p.codigo_producto}</td>
                <td>${p.nombre_producto}</td>
                <td>$${parseFloat(p.precio_venta).toFixed(2)}</td>
                <td class="${stockClass}">${p.stock_actual}</td>
                <td>${actions}</td>
              </tr>
            `;
          });

          renderPagination(res.total);
        });
    }

    /* =========================
       PAGINACIÓN
    ========================= */
    function renderPagination(total) {
      const pages = Math.ceil(total / 10);
      const pag = document.getElementById('pagination');
      pag.innerHTML = '';

      for (let i = 1; i <= pages; i++) {
        const btnClass = i === currentPage ? 'btn-primary' : 'btn-secondary';
        pag.innerHTML += `
          <button class="btn ${btnClass} mx-1" onclick="loadProducts(${i})">${i}</button>
        `;
      }
    }

    /* =========================
       ORDENAMIENTO
    ========================= */
    document.querySelectorAll('#productsTable thead th[data-col]')
      .forEach(th => {
        th.addEventListener('click', () => {
          const col = th.dataset.col;

          if (orderBy === col) {
            direction = direction === 'ASC' ? 'DESC' : 'ASC';
          } else {
            orderBy = col;
            direction = 'ASC';
          }

          loadProducts(1);
        });
      });

    /* =========================
       BÚSQUEDA EN TIEMPO REAL
    ========================= */
    document.getElementById('searchProducts').addEventListener('keyup', function () {
      const term = this.value.toLowerCase();
      document.querySelectorAll('#productsTable tbody tr').forEach(row => {
        row.style.display = row.innerText.toLowerCase().includes(term)
          ? ''
          : 'none';
      });
    });

    /* =========================
       INICIO
    ========================= */
    document.addEventListener('DOMContentLoaded', () => {
      loadProducts();
    });
    </script>
</body>
</html>