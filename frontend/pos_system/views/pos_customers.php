<?php
/**
 * Sistema POS - Gestión de Clientes (DEFINITIVO)
 * ==============================================
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
   AJAX HANDLER CLIENTES
================================================== */
if (isset($_POST['ajax']) && $_POST['ajax'] === '1') {
    header('Content-Type: application/json');

    try {
        $action = $_POST['action'] ?? '';

        switch ($action) {

            case 'list':
                // Mostrar campos compatibles y ensamblar nombres/apellidos
                $stmt = $db->query(
                    "SELECT id, p_nombre, s_nombre, p_apellido, s_apellido, dui, nit, telefono, email
                     FROM mh_cliente_consumidor
                     WHERE active = true
                     ORDER BY id DESC"
                );
                $rows = $stmt->fetchAll();
                $out = [];
                foreach ($rows as $r) {
                    $out[] = [
                        'id' => $r['id'],
                        'nombres' => trim(($r['p_nombre'] ?? '') . ' ' . ($r['s_nombre'] ?? '')),
                        'apellidos' => trim(($r['p_apellido'] ?? '') . ' ' . ($r['s_apellido'] ?? '')),
                        'dui' => $r['dui'] ?? null,
                        'nit' => $r['nit'] ?? null,
                        'telefono' => $r['telefono'] ?? null,
                        'email' => $r['email'] ?? null
                    ];
                }
                echo json_encode(['success' => true, 'data' => $out]);
                exit;

            case 'get':
                $stmt = $db->prepare("SELECT * FROM mh_cliente_consumidor WHERE id = :id");
                $stmt->execute([':id' => $_POST['id']]);
                $r = $stmt->fetch();
                if ($r) {
                    $r['nombres'] = trim(($r['p_nombre'] ?? '') . ' ' . ($r['s_nombre'] ?? ''));
                    $r['apellidos'] = trim(($r['p_apellido'] ?? '') . ' ' . ($r['s_apellido'] ?? ''));
                }
                echo json_encode(['success' => true, 'data' => $r]);
                exit;

            case 'create':
                // Mapear nombres/apellidos a p_nombre/s_nombre y p_apellido/s_apellido
                $nombres = explode(' ', trim((string)($_POST['nombres'] ?? '')), 2);
                $apellidos = explode(' ', trim((string)($_POST['apellidos'] ?? '')), 2);

                $stmt = $db->prepare(
                    "INSERT INTO mh_cliente_consumidor
                    (p_nombre, s_nombre, p_apellido, s_apellido, dui, nit, nrc, telefono, email, direccion, municipio, departamento, active)
                    VALUES (:p_nombre,:s_nombre,:p_apellido,:s_apellido,:dui,:nit,:nrc,:telefono,:email,:direccion,:municipio,:departamento,:active)
                    RETURNING id"
                );
                $stmt->execute([
                    ':p_nombre' => $nombres[0] ?? null,
                    ':s_nombre' => $nombres[1] ?? null,
                    ':p_apellido' => $apellidos[0] ?? null,
                    ':s_apellido' => $apellidos[1] ?? null,
                    ':dui' => $_POST['dui'] ?? null,
                    ':nit' => $_POST['nit'] ?? null,
                    ':nrc' => $_POST['nrc'] ?? null,
                    ':telefono' => $_POST['telefono'] ?? null,
                    ':email' => $_POST['email'] ?? null,
                    ':direccion' => $_POST['direccion'] ?? null,
                    ':municipio' => $_POST['municipio'] ?? null,
                    ':departamento' => $_POST['departamento'] ?? null,
                    ':active' => isset($_POST['active']) ? true : true
                ]);
                echo json_encode(['success' => true, 'id' => $stmt->fetchColumn()]);
                exit;

            case 'update':
                $nombres = explode(' ', trim((string)($_POST['nombres'] ?? '')), 2);
                $apellidos = explode(' ', trim((string)($_POST['apellidos'] ?? '')), 2);

                $stmt = $db->prepare(
                    "UPDATE mh_cliente_consumidor SET
                        p_nombre = :p_nombre,
                        s_nombre = :s_nombre,
                        p_apellido = :p_apellido,
                        s_apellido = :s_apellido,
                        dui = :dui,
                        nit = :nit,
                        nrc = :nrc,
                        telefono = :telefono,
                        email = :email,
                        direccion = :direccion,
                        municipio = :municipio,
                        departamento = :departamento,
                        active = :active
                     WHERE id = :id"
                );
                $stmt->execute([
                    ':p_nombre' => $nombres[0] ?? null,
                    ':s_nombre' => $nombres[1] ?? null,
                    ':p_apellido' => $apellidos[0] ?? null,
                    ':s_apellido' => $apellidos[1] ?? null,
                    ':dui' => $_POST['dui'] ?? null,
                    ':nit' => $_POST['nit'] ?? null,
                    ':nrc' => $_POST['nrc'] ?? null,
                    ':telefono' => $_POST['telefono'] ?? null,
                    ':email' => $_POST['email'] ?? null,
                    ':direccion' => $_POST['direccion'] ?? null,
                    ':municipio' => $_POST['municipio'] ?? null,
                    ':departamento' => $_POST['departamento'] ?? null,
                    ':active' => isset($_POST['active']) ? true : false,
                    ':id' => $_POST['id']
                ]);
                echo json_encode(['success' => true]);
                exit;

            case 'delete':
                $stmt = $db->prepare(
                    "UPDATE mh_cliente_consumidor SET active = false WHERE id = :id"
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
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title><?= htmlspecialchars(APP_NAME) ?> | Clientes</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
<link rel="stylesheet" href="<?= BASE_URL ?>/css/pos_styles.css">
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body class="d-flex flex-column min-vh-100">

<?php include __DIR__ . '/partials/header.php'; ?>
<?php include __DIR__ . '/partials/sidebar.php'; ?>

<main class="container-fluid mt-4 flex-grow-1">
<h2 class="mb-4"><i class="fas fa-users me-2"></i> Gestión de Clientes</h2>

<div class="card shadow-sm">
<div class="card-header bg-white d-flex justify-content-between align-items-center">
<h5 class="mb-0">Base de Datos de Clientes</h5>
<button class="btn btn-success" id="btnNewCustomer"><i class="fas fa-user-plus"></i> Agregar Cliente</button>
</div>
<div class="card-body">
<table class="table table-striped" id="customersTable">
<thead>
<tr><th>ID</th><th>Nombres</th><th>Apellidos</th><th>DUI/NIT</th><th>Teléfono</th><th>Email</th><th>Acciones</th></tr>
</thead>
<tbody></tbody>
</table>
</div>
</div>
</main>

<!-- MODAL -->
<div class="modal fade" id="customerModal">
<div class="modal-dialog modal-lg">
<div class="modal-content">
<form id="customerForm">
<div class="modal-header bg-success text-white"><h5>Cliente</h5></div>
<div class="modal-body">
<input type="hidden" name="ajax" value="1">
<input type="hidden" name="action" id="action">
<input type="hidden" name="id" id="id">

<div class="row">
<div class="col-md-6 mb-2"><input class="form-control" name="nombres" placeholder="Nombres" required></div>
<div class="col-md-6 mb-2"><input class="form-control" name="apellidos" placeholder="Apellidos" required></div>
</div>

<div class="row">
<div class="col-md-4 mb-2"><input class="form-control" name="dui" placeholder="DUI"></div>
<div class="col-md-4 mb-2"><input class="form-control" name="nit" placeholder="NIT"></div>
<div class="col-md-4 mb-2"><input class="form-control" name="nrc" placeholder="NRC"></div>
</div>

<div class="row">
<div class="col-md-6 mb-2"><input class="form-control" name="telefono" placeholder="Teléfono"></div>
<div class="col-md-6 mb-2"><input class="form-control" name="email" type="email" placeholder="Email"></div>
</div>

<textarea class="form-control mb-2" name="direccion" placeholder="Dirección"></textarea>

<div class="row">
<div class="col-md-6 mb-2"><input class="form-control" name="municipio" placeholder="Municipio"></div>
<div class="col-md-6 mb-2"><input class="form-control" name="departamento" placeholder="Departamento"></div>
</div>

<label><input type="checkbox" name="active" checked> Cliente Activo</label>
</div>
<div class="modal-footer">
<button type="submit" class="btn btn-success">Guardar</button>
</div>
</form>
</div>
</div>
</div>

<?php include __DIR__ . '/partials/footer.php'; ?>

<script>
function loadCustomers(){
 $.post('',{ajax:1,action:'list'},res=>{
  const tb=$('#customersTable tbody').html('');
  res.data.forEach(c=>{
    tb.append(`<tr>
      <td>${c.id}</td>
      <td>${c.nombres}</td>
      <td>${c.apellidos}</td>
      <td>${c.dui||''} ${c.nit||''}</td>
      <td>${c.telefono||''}</td>
      <td>${c.email||''}</td>
      <td>
        <button class='btn btn-sm btn-warning' onclick='edit(${c.id})'><i class='fas fa-edit'></i></button>
        <button class='btn btn-sm btn-danger' onclick='del(${c.id})'><i class='fas fa-trash'></i></button>
      </td>
    </tr>`);
  });
 },'json');
}

$('#btnNewCustomer').click(()=>{
 $('#customerForm')[0].reset();
 $('#action').val('create');
 new bootstrap.Modal('#customerModal').show();
});

function edit(id){
 $.post('',{ajax:1,action:'get',id},res=>{
   Object.keys(res.data).forEach(k=>$(`[name=${k}]`).val(res.data[k]));
   $('#action').val('update');
   new bootstrap.Modal('#customerModal').show();
 },'json');
}

function del(id){
 Swal.fire({title:'¿Eliminar cliente?',showCancelButton:true}).then(r=>{
  if(r.isConfirmed){
   $.post('',{ajax:1,action:'delete',id},()=>loadCustomers());
  }
 });
}

$('#customerForm').submit(e=>{
 e.preventDefault();
 $.post('',$('#customerForm').serialize(),res=>{
   if(res.success){
     bootstrap.Modal.getInstance(document.getElementById('customerModal')).hide();
     loadCustomers();
   }
 },'json');
});

$(loadCustomers);
</script>
</body>
</html>