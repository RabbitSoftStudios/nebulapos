<?php
/**
 * Sistema POS - Gestión de Proveedores
 * PDO local SQLite
 * Team MYTS
 */
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../includes/pg_connection.php';
require_once __DIR__ . '/../includes/auth.php';
if (session_status() === PHP_SESSION_NONE) session_start();
$db = pg_pool();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajax'])) {
    if ($_POST['action'] === 'save') {
        $data = [
            'p_nombre' => $_POST['p_nombre'] ?? null,
            's_nombre' => $_POST['s_nombre'] ?? null,
            'p_apellido' => $_POST['p_apellido'] ?? null,
            's_apellido' => $_POST['s_apellido'] ?? null,
            'dui' => $_POST['dui'] ?? null,
            'nit' => $_POST['nit'] ?? null,
            'nrc' => $_POST['nrc'] ?? null,
            'razon_social' => $_POST['razon_social'] ?? null,
            'nombre_comercial' => $_POST['nombre_comercial'] ?? null,
            'telefono' => $_POST['telefono'] ?? null,
            'email' => $_POST['email'] ?? null,
            'direccion' => $_POST['direccion'] ?? null,
        ];
        if (!empty($_POST['id'])) {
            $data['id'] = (int)$_POST['id'];
            $sql = 'UPDATE mh_proveedor_contribuyente SET p_nombre=:p_nombre,s_nombre=:s_nombre,p_apellido=:p_apellido,s_apellido=:s_apellido,dui=:dui,nit=:nit,nrc=:nrc,razon_social=:razon_social,nombre_comercial=:nombre_comercial,telefono=:telefono,email=:email,direccion=:direccion WHERE id=:id';
        } else {
            $sql = 'INSERT INTO mh_proveedor_contribuyente (p_nombre,s_nombre,p_apellido,s_apellido,dui,nit,nrc,razon_social,nombre_comercial,telefono,email,direccion) VALUES (:p_nombre,:s_nombre,:p_apellido,:s_apellido,:dui,:nit,:nrc,:razon_social,:nombre_comercial,:telefono,:email,:direccion)';
        }
        $stmt = $db->prepare($sql);
        $stmt->execute($data);
        header('Content-Type: application/json');
        echo json_encode(['success' => true]);
        exit;
    }
    if ($_POST['action'] === 'delete') {
        $stmt = $db->prepare('DELETE FROM mh_proveedor_contribuyente WHERE id=:id');
        $stmt->execute(['id' => (int)$_POST['id']]);
        header('Content-Type: application/json');
        echo json_encode(['success' => true]);
        exit;
    }
    if ($_POST['action'] === 'list') {
        $stmt = $db->query("SELECT id, COALESCE(razon_social, nombre_comercial, CONCAT(p_nombre,' ',p_apellido)) AS proveedor, nit, telefono, email FROM mh_proveedor_contribuyente ORDER BY id DESC");
        header('Content-Type: application/json');
        echo json_encode($stmt->fetchAll());
        exit;
    }
}

$providers = $db->query("SELECT id, COALESCE(razon_social, nombre_comercial, CONCAT(p_nombre,' ',p_apellido)) AS proveedor, nit, telefono, email FROM mh_proveedor_contribuyente ORDER BY id DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title><?= htmlspecialchars(APP_NAME) ?> | Proveedores</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
<link rel="stylesheet" href="<?= BASE_URL ?>/css/pos_styles.css">
</head>
<body class="d-flex flex-column min-vh-100">
<?php include __DIR__ . '/partials/header.php'; ?>
<?php include __DIR__ . '/partials/sidebar.php'; ?>
<main class="container-fluid mt-4 flex-grow-1">
<h2><i class="fas fa-truck me-2"></i> Proveedores</h2>
<button class="btn btn-success mb-3" onclick="openModal()"><i class="fas fa-plus"></i> Nuevo Proveedor</button>
<table class="table table-striped"><thead><tr><th>ID</th><th>Proveedor</th><th>NIT</th><th>Teléfono</th><th>Email</th><th>Acciones</th></tr></thead>
<tbody id="providersTable"><?php foreach ($providers as $p): ?><tr><td><?= (int)$p['id'] ?></td><td><?= htmlspecialchars((string)$p['proveedor']) ?></td><td><?= htmlspecialchars((string)$p['nit']) ?></td><td><?= htmlspecialchars((string)$p['telefono']) ?></td><td><?= htmlspecialchars((string)$p['email']) ?></td><td><button class="btn btn-warning btn-sm" onclick='editProvider(<?= json_encode($p) ?>)'><i class="fas fa-edit"></i></button> <button class="btn btn-danger btn-sm" onclick="deleteProvider(<?= (int)$p['id'] ?>)"><i class="fas fa-trash"></i></button></td></tr><?php endforeach; ?></tbody></table>
</main>
<div class="modal fade" id="providerModal"><div class="modal-dialog modal-lg"><div class="modal-content"><form id="providerForm"><div class="modal-header bg-success text-white"><h5 class="modal-title">Proveedor</h5><button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button></div><div class="modal-body"><input type="hidden" name="id" id="id"><div class="row g-3"><input class="form-control" name="razon_social" placeholder="Razón Social"><input class="form-control" name="nombre_comercial" placeholder="Nombre Comercial"><input class="form-control" name="nit" placeholder="NIT"><input class="form-control" name="telefono" placeholder="Teléfono"><input class="form-control" name="email" placeholder="Email"><textarea class="form-control" name="direccion" placeholder="Dirección"></textarea></div></div><div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button><button class="btn btn-success">Guardar</button></div></form></div></div></div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script><script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
const modal=new bootstrap.Modal('#providerModal');
function openModal(){document.getElementById('providerForm').reset();document.getElementById('id').value='';modal.show();}
function editProvider(p){openModal();Object.keys(p).forEach(k=>{const el=document.querySelector(`[name="${k}"]`);if(el)el.value=p[k]??'';});}
document.getElementById('providerForm').onsubmit=e=>{e.preventDefault();const fd=new FormData(e.target);fd.append('ajax','1');fd.append('action','save');fetch('',{method:'POST',body:fd}).then(r=>r.json()).then(x=>{if(!x.success)throw new Error(x.error||'Error');location.reload();}).catch(err=>Swal.fire({icon:'error',title:'Error',text:err.message}));};
function deleteProvider(id){Swal.fire({icon:'warning',title:'¿Eliminar proveedor?',showCancelButton:true}).then(r=>{if(r.isConfirmed)fetch('',{method:'POST',body:new URLSearchParams({ajax:'1',action:'delete',id:String(id)})}).then(r=>r.json()).then(x=>{if(x.success)location.reload();else throw new Error(x.error||'Error');}).catch(err=>Swal.fire({icon:'error',title:'Error',text:err.message}));});}
</script></body></html>
