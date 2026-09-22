<?php
/**
 * Sistema POS - Pantalla Principal de Venta
 * ===========================================
 * Interfaz principal del punto de venta con escáner de código de barras
 * @package    POS System
 * @author     Nebula DET Team
 * @version    3.0.0
 */

// Incluir dependencias
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../config/routes.php'; 
require_once __DIR__ . '/../includes/supabase.php';
require_once __DIR__ . '/../includes/auth.php';

// Verificar autenticación
checkPOSAuth();

// Inicializar variables esenciales
$registerNumber = POS_CAJA_NUMERO;
$cashier = $_SESSION['cashier'] ?? [
    'id' => 1,
    'name' => 'Administrador',
    'code' => POS_CAJERO_DEFAULT
];
$frequentProducts = []; 

// LÓGICA DE DATOS EXTERNOS (DEPENDIENTE DE SUPABASE)
$frequentProducts = ['success' => false, 'data' => []];

if (defined('SUPABASE_URL') && !empty(SUPABASE_URL)) {
    try {
        // Inicializar la conexión a la tabla de productos (dte_productos)
        $db = supabase('dte_productos');

        // Optimización: Seleccionar solo columnas necesarias para la UI
        $columns = 'id, nombre_producto, precio_venta, imagen_url, stock_actual';

        // Ejecutar la consulta intentando filtrar por campo 'frecuente'
        $result = $db->select($columns, ['frecuente' => true], 10);

        // Manejo de Resultados y Fallbacks
        if (!isset($result['success']) || $result['success'] !== true) {
            $error_message = 'Error en la conexión o consulta de la base de datos.';
            
            if (isset($result['error'])) {
                $raw_error = (string)$result['error'];

                // Caso 1: La tabla no existe
                if (stripos($raw_error, 'relation') !== false && stripos($raw_error, 'does not exist') !== false) {
                    $error_message = "Error: La tabla 'dte_productos' no existe. Verifique la configuración.";
                } 
                // Caso 2: La columna 'frecuente' no existe (fallback a traer los primeros 10)
                elseif (stripos($raw_error, 'column') !== false && (stripos($raw_error, 'frecuente') !== false || stripos($raw_error, 'not found') !== false)) {
                    $result = $db->select($columns, [], 10);
                    if (isset($result['success']) && $result['success']) {
                        $frequentProducts = $result;
                    }
                } else {
                    $error_message = "Error de Consulta: " . htmlspecialchars($raw_error);
                }
            }

            // Si después del posible fallback seguimos sin éxito
            if (!isset($frequentProducts['success']) || $frequentProducts['success'] !== true) {
                $frequentProducts = [
                    'success' => false,
                    'error' => $error_message,
                    'data' => []
                ];
            }
        } else {
            $frequentProducts = $result;
        }

        // Asegurar estructura de 'data' en éxito
        if (isset($frequentProducts['success']) && $frequentProducts['success'] === true && !isset($frequentProducts['data'])) {
            $frequentProducts['data'] = [];
        }
    } catch (Exception $e) {
        $frequentProducts = [
            'success' => false,
            'error' => 'Error al conectar con la base de datos: ' . $e->getMessage(),
            'data' => []
        ];
    }
} else {
    $frequentProducts = [
        'success' => false,
        'error' => 'Error de Configuración: SUPABASE_URL no definida en el archivo .env',
        'data' => []
    ];
}
?>
<!DOCTYPE html>
<html lang="es" data-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title><?= APP_NAME ?> - Punto de Venta</title>
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    
    <style>
        :root {
            --primary-color: #667eea;
            --secondary-color: #764ba2;
            --success-color: #28a745;
            --info-color: #17a2b8;
            --warning-color: #ffc107;
            --danger-color: #dc3545;
            --light-color: #f8f9fa;
            --dark-color: #343a40;
        }
        
        /* SCROLL INVISIBLE PERO FUNCIONAL */
        * {
            scrollbar-width: none; /* Firefox */
            -ms-overflow-style: none;  /* IE/Edge */
        }
        *::-webkit-scrollbar {
            display: none; /* Chrome, Safari, Opera */
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f7fa;
            min-height: 100vh; /* Permite que crezca */
            overflow-y: auto; /* Habilita scroll vertical */
            margin: 0;
        }
        
        .dashboard-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 10px 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            position: sticky; /* Se mantiene arriba al hacer scroll */
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            height: 60px;
        }
        
        .dashboard-header .brand {
            font-size: 1.5rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .dashboard-nav {
            display: flex;
            gap: 20px;
            list-style: none;
            margin: 0;
            padding: 0;
        }
        
        .dashboard-nav li {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 8px 15px;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .dashboard-nav li.active {
            background-color: rgba(255,255,255,0.2);
            font-weight: 600;
        }

        /* --- INICIO DE CAMBIOS: ESTILOS MODAL CLIENTE --- */
        .modal-content { border-radius: 15px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.2); }
        .modal-header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 15px 15px 0 0; }
        #customer-selected-info { font-size: 0.85rem; padding: 10px; background: #f8f9fa; border-radius: 8px; border-left: 4px solid var(--primary-color); }
        /* --- FIN DE CAMBIOS --- */

        /* CONTENEDOR PRINCIPAL FLEXIBLE */
        .pos-container {
            padding: 20px;
            display: flex;
            gap: 20px;
            min-height: calc(100vh - 60px); /* Altura mínima de la pantalla */
        }
        
        .left-panel {
            flex: 3;
            display: flex;
            flex-direction: column;
            gap: 20px;
            min-width: 0;
        }
        
        .right-panel {
            flex: 2;
            display: flex;
            flex-direction: column;
            gap: 20px;
            min-width: 400px;
        }

        /* INFO CARDS */
        .info-cards {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
        }
        
        .info-card {
            background: white;
            border-radius: 10px;
            padding: 15px;
            display: flex;
            align-items: center;
            gap: 15px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
            border: 1px solid #e0e0e0;
        }
        
        .info-card-icon {
            width: 50px; height: 50px; border-radius: 10px;
            background: linear-gradient(135deg, #667eea20 0%, #764ba220 100%);
            display: flex; align-items: center; justify-content: center;
            color: var(--primary-color); font-size: 1.5rem;
        }

        /* ESCÁNER Y SECCIONES */
        .scanner-section, .products-section, .keyboard-section, .cart-section, .totals-section {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
            border: 1px solid #e0e0e0;
        }

        .scanner-input {
            width: 100%; padding: 12px 15px; border: 2px solid #667eea;
            border-radius: 8px; font-size: 1rem; outline: none;
        }

        /* PRODUCTOS CON ALTA PRESENTACIÓN */
        .products-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            margin-top: 15px;
        }
        
        .product-card {
            background: white; border: 1px solid #e0e0e0; border-radius: 8px;
            padding: 15px; display: flex; flex-direction: column; align-items: center;
            text-align: center; transition: all 0.3s; cursor: pointer;
        }
        
        .product-card:hover { transform: translateY(-2px); border-color: var(--primary-color); }

        .product-image-placeholder {
            width: 80px; height: 80px; background: #f5f7fa; border-radius: 8px;
            display: flex; align-items: center; justify-content: center;
            margin-bottom: 10px; color: var(--primary-color); font-size: 2rem;
        }

        .product-name { font-weight: 600; font-size: 0.9rem; height: 32px; overflow: hidden; }
        .product-price { font-weight: 700; color: var(--success-color); font-size: 1.1rem; margin-bottom: 10px; }
        .add-to-cart-btn {
            width: 100%; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white; border: none; border-radius: 5px; padding: 8px;
        }

        /* BOTONES DE ACCIÓN (RESTAURADOS) */
        .payment-buttons { display: grid; grid-template-columns: 1fr 1fr; gap: 10px; }
        .payment-btn { padding: 15px; border-radius: 8px; border: none; color: white; font-weight: 600; display: flex; align-items: center; justify-content: center; gap: 10px; }
        .cash-btn { background: linear-gradient(135deg, #42e695 0%, #3bb2b8 100%); }
        .card-btn { background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%); }

        .other-buttons { display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px; margin-top: 10px; }
        .other-btn { padding: 12px; border: none; border-radius: 8px; font-weight: 600; color: white; display: flex; align-items: center; justify-content: center; gap: 8px; }
        .customer-btn { background: #17a2b8; }
        .discount-btn { background: #ffc107; color: #333; }
        .cancel-btn { background: #dc3545; }

        .complete-btn {
            width: 100%; background: #333; color: white; padding: 18px; margin-top: 10px;
            font-size: 1.1rem; font-weight: 700; border: none; border-radius: 8px;
        }

        /* RESPONSIVE */
        @media (max-width: 1200px) {
            .pos-container { flex-direction: column; }
            .right-panel { min-width: 100%; }
            .products-grid { grid-template-columns: repeat(4, 1fr); }
        }
        
        @media (max-width: 768px) {
            .info-cards { grid-template-columns: 1fr; }
            .products-grid { grid-template-columns: repeat(2, 1fr); }
            .dashboard-nav { display: none; }
        }
    </style>
</head>
<body>
    <header class="dashboard-header">
        <div class="container-fluid h-100">
            <div class="row align-items-center h-100">
                <div class="col-md-3 col-6">
                    <div class="brand">
                        <i class="fas fa-cash-register"></i>
                        <span>Nebula POS</span>
                    </div>
                </div>
                <div class="col-md-6 d-none d-md-block">
                    <ul class="dashboard-nav">
                        <li class="active"><i class="fas fa-cash-register"></i> POS</li>
                        <li><i class="fas fa-box"></i> Productos</li>
                        <li><i class="fas fa-chart-bar"></i> Reportes</li>
                        <li><i class="fas fa-users"></i> Clientes</li>
                    </ul>
                </div>
                <div class="col-md-3 col-6 text-end">
                    <button class="btn btn-light btn-sm"><i class="fas fa-sign-out-alt"></i></button>
                </div>
            </div>
        </div>
    </header>
    
    <div class="pos-container">
        <div class="left-panel">
            <div class="info-cards">
                <div class="info-card">
                    <div class="info-card-icon"><i class="fas fa-cash-register"></i></div>
                    <div class="info-card-content">
                        <small>Caja #<?= htmlspecialchars($registerNumber) ?></small>
                        <h4>Activa</h4>
                    </div>
                </div>
                <div class="info-card">
                    <div class="info-card-icon"><i class="fas fa-user-tie"></i></div>
                    <div class="info-card-content">
                        <small>Cajero</small>
                        <h4><?= htmlspecialchars($cashier['name']) ?></h4>
                    </div>
                </div>
                <div class="info-card">
                    <div class="info-card-icon"><i class="fas fa-clock"></i></div>
                    <div class="info-card-content">
                        <small>Hora</small>
                        <h4 id="current-time">00:00:00</h4>
                    </div>
                </div>
            </div>
            
            <div class="scanner-section">
                <input type="text" class="scanner-input" id="barcode-input" placeholder="Escanear código..." autofocus 
                       onkeydown="if(event.key==='Enter') { handleBarcodeInput(this.value); this.value=''; }">
            </div>
            
            <div class="products-section">
                <div class="section-title mb-3"><strong><i class="fas fa-fire"></i> Productos Frecuentes</strong></div>
                <div class="products-grid">
                    <?php if (isset($frequentProducts['success']) && $frequentProducts['success'] && !empty($frequentProducts['data'])): ?>
                        <?php foreach($frequentProducts['data'] as $prod): ?>
                            <div class="product-card" onclick="addToCart(<?= isset($prod['id']) ? intval($prod['id']) : 0 ?>, '<?= isset($prod['nombre_producto']) ? addslashes($prod['nombre_producto']) : 'Producto' ?>', <?= isset($prod['precio_venta']) ? floatval($prod['precio_venta']) : 0 ?>)">
                                <div class="product-image-placeholder">
                                    <?php if(!empty($prod['imagen_url'])): ?>
                                        <img src="<?= htmlspecialchars($prod['imagen_url']) ?>" style="width:100%; height:100%; object-fit:cover; border-radius:8px;">
                                    <?php else: ?>
                                        <i class="fas fa-box"></i>
                                    <?php endif; ?>
                                </div>
                                <div class="product-name"><?= isset($prod['nombre_producto']) ? htmlspecialchars($prod['nombre_producto']) : 'Sin nombre' ?></div>
                                <div class="product-price">$<?= isset($prod['precio_venta']) ? number_format(floatval($prod['precio_venta']), 2) : '0.00' ?></div>
                                <button class="add-to-cart-btn">Agregar</button>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="col-12 text-center text-muted p-4">
                            <?= isset($frequentProducts['error']) ? htmlspecialchars($frequentProducts['error']) : 'No hay productos disponibles.' ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="keyboard-section">
                <div class="keyboard-grid" style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 10px;">
                    <button class="keyboard-btn btn btn-outline-secondary py-3 fw-bold" onclick="document.getElementById('barcode-input').value += '7'">7</button>
                    <button class="keyboard-btn btn btn-outline-secondary py-3 fw-bold" onclick="document.getElementById('barcode-input').value += '8'">8</button>
                    <button class="keyboard-btn btn btn-outline-secondary py-3 fw-bold" onclick="document.getElementById('barcode-input').value += '9'">9</button>
                    <button class="keyboard-btn btn btn-outline-secondary py-3 fw-bold" onclick="document.getElementById('barcode-input').value += '4'">4</button>
                    <button class="keyboard-btn btn btn-outline-secondary py-3 fw-bold" onclick="document.getElementById('barcode-input').value += '5'">5</button>
                    <button class="keyboard-btn btn btn-outline-secondary py-3 fw-bold" onclick="document.getElementById('barcode-input').value += '6'">6</button>
                    <button class="keyboard-btn btn btn-outline-secondary py-3 fw-bold" onclick="document.getElementById('barcode-input').value += '1'">1</button>
                    <button class="keyboard-btn btn btn-outline-secondary py-3 fw-bold" onclick="document.getElementById('barcode-input').value += '2'">2</button>
                    <button class="keyboard-btn btn btn-outline-secondary py-3 fw-bold" onclick="document.getElementById('barcode-input').value += '3'">3</button>
                    <button class="keyboard-btn btn btn-outline-secondary py-3 fw-bold" onclick="document.getElementById('barcode-input').value += '0'">0</button>
                    <button class="keyboard-btn btn btn-outline-secondary py-3 fw-bold" onclick="document.getElementById('barcode-input').value += '00'">00</button>
                    <button class="keyboard-btn btn btn-outline-secondary py-3 fw-bold" id="clear-btn" onclick="document.getElementById('barcode-input').value = ''">C</button>
                </div>
                <div class="mt-3">
                    <button class="btn btn-success w-100 py-3 fw-bold add-btn" id="manual-add-btn" 
                            onclick="handleBarcodeInput(document.getElementById('barcode-input').value); document.getElementById('barcode-input').value='';">
                        <i class="fas fa-plus-circle me-2"></i> AGREGAR (ENTER)
                    </button>
                </div>
            </div>
        </div>
        
        <div class="right-panel">
            <div class="cart-section">
                <div class="d-flex justify-content-between border-bottom pb-2 mb-2">
                    <h3 class="fs-5 m-0"><i class="fas fa-shopping-cart"></i> Carrito</h3>
                    <button class="btn btn-danger btn-sm" onclick="clearCart()">Vaciar</button>
                </div>
                <div class="cart-items" id="cart-items-container">
                    <div class="text-center text-muted p-4">Carrito vacío</div>
                </div>
                <div id="customer-selected-info" class="mt-2" style="display:none;">
                    <small class="text-muted"><i class="fas fa-user-tag"></i> Receptor:</small>
                    <div id="current-customer-name" class="fw-bold text-primary">Consumidor Final</div>
                </div>
            </div>
            
            <div class="totals-section">
                <div class="d-flex justify-content-between">
                    <span>Subtotal:</span>
                    <strong id="subtotal-display">$0.00</strong>
                </div>
                <div class="d-flex justify-content-between border-top mt-2 pt-2 fs-4">
                    <strong>TOTAL:</strong>
                    <strong id="total-display">$0.00</strong>
                </div>
            </div>
            
            <div class="action-buttons">
                <div class="payment-buttons">
                    <button class="payment-btn cash-btn" onclick="openPaymentModal('cash')">
                        <i class="fas fa-money-bill-wave"></i> Efectivo
                    </button>
                    <button class="payment-btn card-btn" onclick="openPaymentModal('card')">
                        <i class="fas fa-credit-card"></i> Tarjeta
                    </button>
                </div>
                
                <div class="other-buttons">
                    <button class="other-btn customer-btn" onclick="openCustomerModal()">
                        <i class="fas fa-user"></i> Cliente
                    </button>
                    <button class="other-btn discount-btn" onclick="applyDiscount()">
                        <i class="fas fa-percent"></i> Desc.
                    </button>
                    <button class="other-btn cancel-btn" onclick="clearCart()">
                        <i class="fas fa-times"></i> Cancelar
                    </button>
                </div>
                
                <button class="complete-btn" id="finish-sale-btn" disabled onclick="processSale()">
                    FINALIZAR VENTA
                </button>
            </div>
        </div>
    </div>

    <div class="modal fade" id="customerModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-user-tag me-2"></i> Datos del Receptor (DTE)</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="customer-form">
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label">Nombre o Razón Social</label>
                                <input type="text" id="rec-nombre" class="form-control" placeholder="Nombre completo">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Correo Electrónico (Envío DTE)</label>
                                <input type="email" id="rec-correo" class="form-control" placeholder="cliente@correo.com">
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cerrar</button>
                    <button type="button" class="btn btn-primary" onclick="saveCustomerData()">Confirmar Receptor</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
/**
 * SISTEMA POS - MÓDULO DE FACTURACIÓN ELECTRÓNICA (DTE)
 * Auditoría: v3.2 - Integración completa de eventos y esquema MH El Salvador
 */

// ==========================================
// 1. ESTADO GLOBAL Y CONFIGURACIÓN
// ==========================================
<?php
// Generar códigos de control DTE
function generarCodigoGeneracion() {
    return strtoupper(sprintf(
        '%s-%s-%s-%s-%s',
        bin2hex(random_bytes(4)),
        bin2hex(random_bytes(2)),
        bin2hex(random_bytes(2)),
        bin2hex(random_bytes(2)),
        bin2hex(random_bytes(6))
    ));
}

function generarNumeroControl() {
    return 'DTE-01-' . strtoupper(substr(bin2hex(random_bytes(8)), 0, 15));
}

$codigoGeneracion = generarCodigoGeneracion();
$numeroControl = generarNumeroControl();
?>
const mhControl = {
    codigoGeneracion: "<?= $codigoGeneracion ?>",
    numeroControl: "<?= $numeroControl ?>",
    fechaEmision: "<?= date('Y-m-d') ?>",
    horaEmision: "<?= date('H:i:s') ?>"
};

let receptorData = {
    nombre: "Consumidor Final",
    correo: "damefactura@gmail.com",
    tipoDocumento: "13", // DUI
    numDocumento: "00000000-0",
    direccion: { 
        departamento: "06", 
        municipio: "23", 
        complemento: "San Salvador" 
    }
};

// ==========================================
// 2. UTILIDADES DE INTERFAZ
// ==========================================

/**
 * Actualiza el reloj en tiempo real (Auditado)
 */
function updateClock() {
    const timeDisplay = document.getElementById('current-time');
    if (timeDisplay) {
        const now = new Date();
        timeDisplay.textContent = now.toLocaleTimeString('es-SV', {
            hour: '2-digit', minute: '2-digit', second: '2-digit', hour12: false
        });
    }
}

/**
 * Convierte montos a formato de texto legal para el DTE
 */
function numeroALetras(total) {
    const format = new Intl.NumberFormat('en-US', { minimumFractionDigits: 2 });
    const partes = format.format(total).split('.');
    return `SON ${partes[0]} ${partes[1]}/100 DOLARES`;
}

// ==========================================
// 3. LÓGICA CORE DEL CARRITO (Auditado para integrarse con el HTML)
// ==========================================

// Variable temporal para el carrito si window.posCart no está listo
let localCart = [];

function addToCart(id, name, price) {
    // Si existe el módulo externo lo usamos, si no, usamos lógica local de emergencia
    if (window.posCart && typeof window.posCart.addItem === 'function') {
        window.posCart.addItem({ id, name, price, quantity: 1 });
    } else {
        const existing = localCart.find(item => item.id === id);
        if (existing) {
            existing.quantity++;
        } else {
            localCart.push({ id, name, price, quantity: 1 });
        }
        renderLocalCart();
    }
    document.getElementById('barcode-input').focus();
}

function renderLocalCart() {
    const container = document.getElementById('cart-items-container');
    const totalDisplay = document.getElementById('total-display');
    const finishBtn = document.getElementById('finish-sale-btn');
    
    if (!container) return;

    if (localCart.length === 0) {
        container.innerHTML = '<div class="text-center text-muted p-4">Carrito vacío</div>';
        totalDisplay.textContent = '$0.00';
        finishBtn.disabled = true;
        return;
    }

    finishBtn.disabled = false;
    let html = '';
    let total = 0;

    localCart.forEach((item, index) => {
        const subtotal = item.price * item.quantity;
        total += subtotal;
        html += `
            <div class="d-flex justify-content-between align-items-center border-bottom py-2">
                <div>
                    <div class="fw-bold">${item.name}</div>
                    <small>${item.quantity} x $${item.price.toFixed(2)}</small>
                </div>
                <div class="text-end">
                    <div class="fw-bold">$${subtotal.toFixed(2)}</div>
                    <button class="btn btn-sm text-danger" onclick="removeItem(${index})"><i class="fas fa-trash"></i></button>
                </div>
            </div>`;
    });

    container.innerHTML = html;
    totalDisplay.textContent = `$${total.toFixed(2)}`;
}

function removeItem(index) {
    localCart.splice(index, 1);
    renderLocalCart();
}

function clearCart() {
    localCart = [];
    renderLocalCart();
    Swal.fire('Carrito vaciado', '', 'info');
}

// ==========================================
// 4. PROCESAMIENTO DTE (HACIENDA)
// ==========================================

function prepareDTEJson(metodoPago) {
    const items = (window.posCart && window.posCart.items) ? window.posCart.items : localCart;
    const totalVenta = items.reduce((acc, i) => acc + (i.price * i.quantity), 0);
    const totalIva = parseFloat((totalVenta - (totalVenta / 1.13)).toFixed(2));

    return {
        "identificacion": {
            "version": 1, "ambiente": "00", "tipoDte": "01",
            "numeroControl": mhControl.numeroControl,
            "codigoGeneracion": mhControl.codigoGeneracion,
            "tipoModelo": 1, "tipoOperacion": 1,
            "fecEmi": mhControl.fechaEmision,
            "horEmi": mhControl.horaEmision,
            "tipoMoneda": "USD"
        },
        "emisor": {
            "nit": "06150911851010", "nrc": "1992934",
            "nombre": "Rodriguez Machuca Jose Alexander",
            "codActividad": "46510",
            "direccion": { "departamento": "06", "municipio": "23", "complemento": "San Salvador" }
        },
        "receptor": receptorData,
        "cuerpoDocumento": items.map((item, index) => ({
            "numItem": index + 1,
            "tipoItem": 1,
            "descripcion": item.name,
            "cantidad": item.quantity,
            "uniMedida": 59,
            "precioUni": item.price,
            "montoDescu": 0.0,
            "ventaGravada": parseFloat((item.price * item.quantity).toFixed(2)),
            "codigo": item.id.toString(),
            "ivaItem": parseFloat(((item.price * item.quantity) - ((item.price * item.quantity) / 1.13)).toFixed(2))
        })),
        "resumen": {
            "totalGravada": parseFloat(totalVenta.toFixed(2)),
            "totalPagar": parseFloat(totalVenta.toFixed(2)),
            "totalLetras": numeroALetras(totalVenta),
            "totalIva": totalIva,
            "condicionOperacion": metodoPago === 'cash' ? 1 : 2
        }
    };
}

async function handleBarcodeInput(barcode) {
    if (!barcode) return;
    
    try {
        const response = await fetch('ajax/search_product.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ barcode })
        });
        
        const result = await response.json();
        
        if (result.success && result.product) {
            addToCart(
                result.product.id,
                result.product.nombre_producto,
                result.product.precio_venta
            );
        } else {
            Swal.fire('Producto no encontrado', '', 'warning');
        }
    } catch (error) {
        Swal.fire('Error', 'No se pudo buscar el producto', 'error');
    }
}

async function processSale() {
    const dteData = prepareDTEJson('cash');
    
    Swal.fire({
        title: 'Procesando Venta...',
        html: `
            <div id="progress-steps">
                <p>⏳ Guardando DTE...</p>
            </div>
        `,
        allowOutsideClick: false,
        didOpen: () => { Swal.showLoading(); }
    });
    
    try {
        const response = await fetch('ajax/process_sale_complete.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(dteData)
        });
        
        const result = await response.json();
        
        if (result.success) {
            await Swal.fire({
                icon: 'success',
                title: '¡Venta Exitosa!',
                html: `
                    <p>Código: ${result.codigoGeneracion}</p>
                    <p>Sello: ${result.selloRecibido || 'Pendiente'}</p>
                `
            });
            
            // Abrir ticket
            window.open(`print_ticket.php?id=${result.codigoGeneracion}`, '_blank');
            
            // Reiniciar
            location.reload();
        } else {
            throw new Error(result.error || 'Error desconocido');
        }
    } catch (error) {
        Swal.fire('Error', error.message, 'error');
    }
}

async function applyDiscount() {
    const { value: discountType } = await Swal.fire({
        title: 'Aplicar Descuento',
        input: 'select',
        inputOptions: {
            'percentage': 'Porcentaje (%)',
            'fixed': 'Monto Fijo ($)'
        },
        inputPlaceholder: 'Selecciona tipo de descuento',
        showCancelButton: true
    });
    
    if (!discountType) return;
    
    const { value: amount } = await Swal.fire({
        title: 'Ingresa el monto',
        input: 'number',
        inputPlaceholder: discountType === 'percentage' ? '0-100' : '0.00',
        showCancelButton: true
    });
    
    if (amount) {
        applyDiscountToCart(discountType, parseFloat(amount));
    }
}

function openPaymentModal(type) {
    const total = calculateTotal();
    
    if (total <= 0) {
        Swal.fire('Carrito vacío', 'Agrega productos antes de procesar el pago', 'warning');
        return;
    }
    
    if (type === 'cash') {
        showCashPaymentModal(total);
    } else {
        showCardPaymentModal(total);
    }
}

// Helper function to calculate cart total
function calculateTotal() {
    const items = (window.posCart && window.posCart.items) ? window.posCart.items : localCart;
    return items.reduce((acc, item) => acc + (item.price * item.quantity), 0);
}

// Apply discount to cart
let cartDiscount = { type: null, amount: 0 };

function applyDiscountToCart(type, amount) {
    cartDiscount = { type, amount };
    
    const subtotal = calculateTotal();
    let discountValue = 0;
    
    if (type === 'percentage') {
        if (amount > 100) amount = 100;
        discountValue = subtotal * (amount / 100);
    } else {
        discountValue = amount;
        if (discountValue > subtotal) discountValue = subtotal;
    }
    
    const finalTotal = subtotal - discountValue;
    
    // Update display
    const totalDisplay = document.getElementById('total-display');
    const subtotalDisplay = document.getElementById('subtotal-display');
    
    if (subtotalDisplay) {
        subtotalDisplay.textContent = `$${subtotal.toFixed(2)}`;
    }
    
    if (totalDisplay) {
        totalDisplay.innerHTML = `
            <div>
                <small class="text-muted">Descuento: -$${discountValue.toFixed(2)}</small><br>
                $${finalTotal.toFixed(2)}
            </div>
        `;
    }
    
    Swal.fire({
        icon: 'success',
        title: 'Descuento aplicado',
        text: `Se aplicó un descuento de $${discountValue.toFixed(2)}`,
        timer: 2000
    });
}

// Cash payment modal
async function showCashPaymentModal(total) {
    const { value: receivedAmount } = await Swal.fire({
        title: 'Pago en Efectivo',
        html: `
            <div class="text-start">
                <p><strong>Total a pagar:</strong> $${total.toFixed(2)}</p>
                <label class="form-label">Monto recibido:</label>
            </div>
        `,
        input: 'number',
        inputPlaceholder: '0.00',
        inputAttributes: {
            min: 0,
            step: 0.01
        },
        showCancelButton: true,
        confirmButtonText: 'Procesar Venta',
        cancelButtonText: 'Cancelar',
        preConfirm: (value) => {
            const amount = parseFloat(value);
            if (isNaN(amount) || amount < total) {
                Swal.showValidationMessage('El monto debe ser mayor o igual al total');
                return false;
            }
            return amount;
        }
    });
    
    if (receivedAmount) {
        const change = receivedAmount - total;
        
        await Swal.fire({
            icon: 'info',
            title: 'Cambio',
            html: `
                <p>Recibido: $${receivedAmount.toFixed(2)}</p>
                <p>Total: $${total.toFixed(2)}</p>
                <h3 class="text-success">Cambio: $${change.toFixed(2)}</h3>
            `,
            confirmButtonText: 'Continuar'
        });
        
        processSale();
    }
}

// Card payment modal
async function showCardPaymentModal(total) {
    const { value: formValues } = await Swal.fire({
        title: 'Pago con Tarjeta',
        html: `
            <div class="text-start">
                <p><strong>Total a pagar:</strong> $${total.toFixed(2)}</p>
                <div class="mb-3">
                    <label class="form-label">Últimos 4 dígitos:</label>
                    <input id="card-digits" class="swal2-input" placeholder="1234" maxlength="4">
                </div>
                <div class="mb-3">
                    <label class="form-label">Número de autorización:</label>
                    <input id="auth-number" class="swal2-input" placeholder="AUTH123456">
                </div>
            </div>
        `,
        showCancelButton: true,
        confirmButtonText: 'Procesar Venta',
        cancelButtonText: 'Cancelar',
        preConfirm: () => {
            const digits = document.getElementById('card-digits').value;
            const authNumber = document.getElementById('auth-number').value;
            
            if (!digits || digits.length !== 4) {
                Swal.showValidationMessage('Ingresa los 4 dígitos de la tarjeta');
                return false;
            }
            
            if (!authNumber) {
                Swal.showValidationMessage('Ingresa el número de autorización');
                return false;
            }
            
            return { digits, authNumber };
        }
    });
    
    if (formValues) {
        processSale();
    }
}

// ==========================================
// 5. GESTIÓN DE CLIENTES Y EVENTOS
// ==========================================

function openCustomerModal() {
    const modal = new bootstrap.Modal(document.getElementById('customerModal'));
    modal.show();
}

function saveCustomerData() {
    const nombre = document.getElementById('rec-nombre').value;
    const correo = document.getElementById('rec-correo').value;
    
    if (nombre) receptorData.nombre = nombre.toUpperCase();
    if (correo) receptorData.correo = correo;

    document.getElementById('current-customer-name').textContent = receptorData.nombre;
    document.getElementById('customer-selected-info').style.display = 'block';
    
    bootstrap.Modal.getInstance(document.getElementById('customerModal')).hide();
}

function handleBarcodeInput(value) {
    if (!value) return;
    // Si tienes el posScanner lo usamos, si no, simulamos búsqueda
    console.log("Escaneando:", value);
    // addToCart(99, "Producto Escaneado", 1.00); // Ejemplo
}

document.addEventListener('DOMContentLoaded', function() {
    // Iniciar Reloj
    setInterval(updateClock, 1000);
    updateClock();

    // Foco inicial
    const input = document.getElementById('barcode-input');
    if (input) input.focus();

    // Delegación de eventos para teclado numérico (Auditado)
    document.querySelectorAll('.keyboard-btn').forEach(btn => {
        btn.onclick = function() {
            if (this.id === 'clear-btn') {
                input.value = '';
            } else {
                input.value += this.textContent.trim();
            }
            input.focus();
        };
    });

    // Soporte para tecla Enter en el input
    input.addEventListener('keypress', (e) => {
        if (e.key === 'Enter') {
            handleBarcodeInput(input.value);
            input.value = '';
        }
    });
});
</script>
</body>
</html>
<?php
// Función para verificar autenticación POS
// function checkPOSAuth() {
//     session_start();
    
//     if (!isset($_SESSION['pos_authenticated']) || $_SESSION['pos_authenticated'] !== true) {
//         header('Location: ' . BASE_URL . '/login.php');
//         exit();
//     }
// }
?>
