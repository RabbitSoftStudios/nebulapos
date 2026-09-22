<?php
// session_start(); // Ahora se maneja en constants.php

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/config/constants.php';
require_once __DIR__ . '/includes/auth.php';

checkAdminAuth();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title><?= APP_NAME ?> - Dashboard</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">

    <style>
        body { background:#f5f6fa; overflow-x:hidden; }

        .sidebar {
            position:fixed;
            top:0; left:0;
            width:250px; height:100vh;
            background:#212529;
            z-index:1000;
            padding-top:60px;
            transition:.3s;
        }

        .sidebar a {
            color:#ced4da;
            padding:12px 20px;
            display:block;
            text-decoration:none;
        }

        .sidebar a:hover { background:#343a40; color:#fff; }

        .brand {
            position:fixed;
            top:0; left:0;
            width:250px; height:60px;
            background:#111;
            color:#fff;
            display:flex;
            align-items:center;
            justify-content:center;
            font-weight:bold;
            z-index:1100;
        }

        .content {
            margin-left:250px;
            padding:25px;
            transition:.3s;
        }

        @media (max-width:768px) {
            .sidebar { left:-250px; }
            .sidebar.show { left:0; }
            .content { margin-left:0; }
        }
    </style>
</head>
<body>

<!-- Sidebar -->
<div class="sidebar" id="sidebar">
    <div class="brand"><?= APP_NAME ?></div>

    <a href="#" data-view="dashboard_home"><i class="fas fa-gauge-high"></i> Dashboard</a>
    <!-- <a href="#.php" data-view=""><i class="fas fa-cash-register"></i> POS</a> -->
    <a href="#" data-view="pos_products"><i class="fas fa-boxes"></i> Productos</a>
    <a href="#" data-view="pos_reports"><i class="fas fa-chart-line"></i> Reportes</a>
    <a href="#" data-view="pos_customers"><i class="fas fa-user"></i> Clientes</a>
    <a href="#" data-view="pos_settings"><i class="fa-solid fa-gears"></i> Configuracion</a>
    <a href="logout.php"><i class="fas fa-right-from-bracket"></i> Cerrar sesión</a>
</div>

<!-- Content -->
<div class="content">
    <!-- Topbar -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <button class="btn btn-outline-secondary d-md-none" id="toggleSidebar">
            <i class="fas fa-bars"></i>
        </button>
        <div>
            <?php if (($_SESSION['cashier']['code'] ?? '') === 'ADMIN'): ?>
                <span class="badge bg-warning text-dark me-2">
                    <i class="fas fa-code"></i> Developer Mode
                </span>
            <?php endif; ?>
            <span class="badge bg-primary fs-6">
                <i class="fas fa-user-circle"></i> <?= htmlspecialchars($_SESSION['cashier']['name'] ?? 'Administrador') ?>
            </span>
        </div>
    </div>

    <!-- CONTENIDO DINÁMICO -->
    <div id="main-content">
        <!-- Aquí se cargan las vistas -->
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
const BASE_URL = '<?= BASE_URL ?>';
const content = document.getElementById('main-content');
const sidebar = document.getElementById('sidebar');

/* Toggle sidebar móvil */
document.getElementById('toggleSidebar').onclick = () => {
    sidebar.classList.toggle('show');
};

/* Cargar vistas dinámicamente */
function loadView(view, push = true) {
    // Definimos el path de la vista. Todas están en views/.
    // Si la vista ya trae 'pos_', no le agregamos prefijo extra a menos que sea necesario.
    let viewPath = view;
    if (!viewPath.endsWith('.php')) {
        viewPath += '.php';
    }

    // Usamos BASE_URL/views/ delante del nombre del archivo
    const fullUrl = `${BASE_URL}/views/${viewPath}`;
    
    fetch(fullUrl)
        .then(res => {
            if (!res.ok) throw new Error(`Vista [${viewPath}] no encontrada en ${fullUrl}`);
            return res.text();
        })
        .then(html => {
            content.innerHTML = html;
            
            // Re-ejecutar scripts dentro de la vista cargada
            const scripts = content.querySelectorAll('script');
            scripts.forEach(oldScript => {
                const newScript = document.createElement('script');
                Array.from(oldScript.attributes).forEach(attr => newScript.setAttribute(attr.name, attr.value));
                newScript.appendChild(document.createTextNode(oldScript.innerHTML));
                oldScript.parentNode.replaceChild(newScript, oldScript);
            });

            if (push) {
                const newUrl = `${BASE_URL}/dashboard?view=${view}`;
                history.pushState({view}, '', newUrl);
            }
        })
        .catch(err => {
            content.innerHTML = `
                <div class="alert alert-danger">
                    <h5><i class="fas fa-exclamation-triangle"></i> Error al cargar la vista</h5>
                    <p>${err.message}</p>
                    <hr>
                    <small>Verifica que el archivo existe en la carpeta <code>views/</code></small>
                </div>`;
        });
}

/* Sidebar click */
document.querySelectorAll('[data-view]').forEach(link => {
    link.addEventListener('click', e => {
        e.preventDefault();
        loadView(link.dataset.view);
        sidebar.classList.remove('show');
    });
});

/* Historial navegador */
window.onpopstate = e => {
    if (e.state?.view) loadView(e.state.view, false);
};

/* Carga inicial */
const params = new URLSearchParams(window.location.search);
loadView(params.get('view') || 'dashboard_home', false);
</script>

</body>
</html>
<?php
