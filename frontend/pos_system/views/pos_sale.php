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
                                <input type="text" id="rec-nombre" class="form-control" value="Clientes Varios">
                            </div>
                            <div class="col-md-12">
                                <label class="form-label">Correo Electrónico (Envío DTE)</label>
                                <input type="email" id="rec-correo" class="form-control" value="503software@gmail.com">
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
    <script src="ajax/wompi.js"></script>

<script>
/**
 * SISTEMA POS - MÓDULO DE FACTURACIÓN ELECTRÓNICA (DTE)
 * Versión mejorada: v4.0
 * Mejoras implementadas:
 * 1. Búsqueda automática de productos al escanear código de barras
 * 2. Atajos de teclado para funciones frecuentes
 * 3. Sistema de descuentos flexible (individual/total/cupones)
 * 4. Generación DTE según normativa salvadoreña
 */

// ==========================================
// 1. CONFIGURACIÓN Y ESTADO GLOBAL
// ==========================================

<?php
// Generar códigos de control DTE (mantener del código original)
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

/* function generarNumeroControl() {
    // Consultar el último numeroControl de la DB
    // Por simplicidad, usar un contador estático; en producción, query DB solo para dev
    static $lastNumber = 0; // Último usado, incrementar
    $nextNumber = $lastNumber + 1;
    $lastNumber = $nextNumber;
    $numero = str_pad($nextNumber, 15, '0', STR_PAD_LEFT);
    return 'DTE-01-P001M001-' . $numero;
} */

$codigoGeneracion = generarCodigoGeneracion();
//$numeroControl = generarNumeroControl();
?>

const mhControl = {
    codigoGeneracion: "<?= $codigoGeneracion ?>",
    numeroControl: null,  // Se generará en el servidor en process_sale_complete.php
    fechaEmision: "<?= date('Y-m-d') ?>",
    horaEmision: "<?= date('H:i:s') ?>"
};

// Estado global del sistema
let localCart = [];
let cartDiscount = { type: null, amount: 0 };
let currentCustomer = {
    tipoDocumento: null,
    numDocumento: null,
    nrc: null,
    nombre: "Consumidor Final",
    codActividad: "10005",
    descActividad: "Otros",
    direccion: { 
        departamento: "06", 
        municipio: "23", 
        complemento: "San Salvador" 
    },
    telefono: null,
    correo: "damefactura@gmail.com"
};

// ==========================================
// 2. UTILIDADES GENERALES Y ATRIBUTOS
// ==========================================

/**
 * Actualiza el reloj en tiempo real
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
// 3. GESTIÓN DE BÚSQUEDA Y CARRITO
// ==========================================

/**
 * Función principal para procesar códigos de barras
 * Mejora: Búsqueda automática al completar escaneo
 */
async function handleBarcodeInput(barcode) {
    if (!barcode || barcode.trim() === "") return;
    
    try {
        const response = await fetch('ajax/search_product.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ barcode: barcode.trim() })
        });
        
        const result = await response.json();
        
        if (result.success && result.product) {
            addToCart(
                result.product.id,
                result.product.nombre_producto,
                result.product.precio_venta
            );
            // Limpiar input para siguiente escaneo
            const input = document.getElementById('barcode-input');
            if(input) {
                input.value = '';
                input.focus();
            }
            
            // Notificación visual breve
            showToast('success', 'Producto agregado', result.product.nombre_producto);
        } else {
            showToast('error', 'No encontrado', `Código: ${barcode}`);
            const input = document.getElementById('barcode-input');
            if(input) {
                input.value = '';
                input.focus();
            }
        }
    } catch (error) {
        console.error("Error en búsqueda:", error);
        Swal.fire('Error', 'Error de conexión con el servidor', 'error');
    }
}

/**
 * Agrega producto al carrito
 */
function addToCart(id, name, price, discount = null) {
    const existing = localCart.find(item => item.id === id);
    
    if (existing) {
        existing.quantity++;
        if (discount) {
            existing.discount = discount;
        }
    } else {
        localCart.push({ 
            id, 
            name, 
            price, 
            quantity: 1,
            discount: discount || null,
            discountType: discount ? 'item' : null
        });
    }
    
    renderLocalCart();
    const input = document.getElementById('barcode-input');
    if(input) input.focus();
}

/**
 * Renderiza el carrito en la interfaz
 */
function renderLocalCart() {
    const container = document.getElementById('cart-items-container');
    const totalDisplay = document.getElementById('total-display');
    const subtotalDisplay = document.getElementById('subtotal-display');
    const finishBtn = document.getElementById('finish-sale-btn');
    
    if (!container) return;

    if (localCart.length === 0) {
        container.innerHTML = '<div class="text-center text-muted p-4">Carrito vacío</div>';
        totalDisplay.textContent = '$0.00';
        if(subtotalDisplay) subtotalDisplay.textContent = '$0.00';
        finishBtn.disabled = true;
        return;
    }

    finishBtn.disabled = false;
    let html = '';
    let subtotal = 0;
    let totalDiscount = 0;

    localCart.forEach((item, index) => {
        let lineTotal = item.price * item.quantity;
        let itemDiscount = 0;
        
        // Aplicar descuento individual si existe
        if (item.discount) {
            if (item.discount.type === 'percentage') {
                itemDiscount = lineTotal * (item.discount.amount / 100);
            } else {
                itemDiscount = item.discount.amount;
            }
            lineTotal = Math.max(0, lineTotal - itemDiscount);
        }
        
        subtotal += lineTotal;
        totalDiscount += itemDiscount;
        
        html += `
            <div class="d-flex justify-content-between align-items-center border-bottom py-2">
                <div style="flex: 2;">
                    <div class="fw-bold">${item.name}</div>
                    <small>${item.quantity} x $${parseFloat(item.price).toFixed(2)}</small>
                    ${item.discount ? `<br><small class="text-danger">Desc: ${item.discount.amount}${item.discount.type === 'percentage' ? '%' : '$'}</small>` : ''}
                </div>
                <div class="text-end" style="flex: 1;">
                    <div class="fw-bold">$${lineTotal.toFixed(2)}</div>
                    <div class="btn-group">
                        <button class="btn btn-sm btn-outline-secondary" onclick="updateQuantity(${index}, -1)">
                            <i class="fas fa-minus"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-secondary" onclick="updateQuantity(${index}, 1)">
                            <i class="fas fa-plus"></i>
                        </button>
                        <button class="btn btn-sm text-danger" onclick="removeItem(${index})">
                            <i class="fas fa-trash"></i>
                        </button>
                    </div>
                </div>
            </div>`;
    });

    container.innerHTML = html;
    
    // Aplicar descuento general si existe
    if (cartDiscount.type) {
        let cartDiscountValue = 0;
        if (cartDiscount.type === 'percentage') {
            cartDiscountValue = subtotal * (cartDiscount.amount / 100);
        } else {
            cartDiscountValue = cartDiscount.amount;
        }
        totalDiscount += cartDiscountValue;
        subtotal = Math.max(0, subtotal - cartDiscountValue);
    }

    const finalTotal = subtotal;

    if(subtotalDisplay) {
        subtotalDisplay.textContent = `$${(finalTotal + totalDiscount).toFixed(2)}`;
    }
    
    if(totalDiscount > 0) {
        totalDisplay.innerHTML = `
            <div class="text-end">
                <small class="text-danger d-block">Descuentos: -$${totalDiscount.toFixed(2)}</small>
                <strong class="fs-4">$${finalTotal.toFixed(2)}</strong>
            </div>`;
    } else {
        totalDisplay.innerHTML = `<strong class="fs-4">$${finalTotal.toFixed(2)}</strong>`;
    }
}

/**
 * Actualiza cantidad de un producto
 */
function updateQuantity(index, change) {
    if (localCart[index]) {
        localCart[index].quantity += change;
        if (localCart[index].quantity <= 0) {
            localCart.splice(index, 1);
        }
        renderLocalCart();
    }
}

/**
 * Elimina un producto del carrito
 */
function removeItem(index) {
    localCart.splice(index, 1);
    renderLocalCart();
}

/**
 * Vacía el carrito completo
 */
function clearCart() {
    Swal.fire({
        title: '¿Vaciar carrito?',
        text: 'Se eliminarán todos los productos',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Sí, vaciar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            localCart = [];
            cartDiscount = { type: null, amount: 0 };
            renderLocalCart();
            showToast('info', 'Carrito vaciado', '');
        }
    });
}

// ==========================================
// 4. SISTEMA DE DESCUENTOS FLEXIBLE
// ==========================================

/**
 * Aplica descuento (producto individual, total o cupón)
 */
async function applyDiscount() {
    const { value: discountScope } = await Swal.fire({
        title: 'Tipo de descuento',
        input: 'select',
        inputOptions: {
            'item': 'Producto específico',
            'total': 'Al total de la compra',
            'coupon': 'Cupón de descuento',
            'giftcard': 'Gift Card'
        },
        inputPlaceholder: 'Selecciona tipo',
        showCancelButton: true
    });
    
    if (!discountScope) return;
    
    switch(discountScope) {
        case 'item':
            await applyItemDiscount();
            break;
        case 'total':
            await applyTotalDiscount();
            break;
        case 'coupon':
            await applyCouponDiscount();
            break;
        case 'giftcard':
            await applyGiftCard();
            break;
    }
}

/**
 * Aplica descuento a producto específico
 */
async function applyItemDiscount() {
    if (localCart.length === 0) {
        Swal.fire('Carrito vacío', 'Agrega productos primero', 'warning');
        return;
    }
    
    const { value: productIndex } = await Swal.fire({
        title: 'Selecciona producto',
        input: 'select',
        inputOptions: localCart.reduce((options, item, index) => {
            options[index] = `${item.name} - $${item.price}`;
            return options;
        }, {}),
        showCancelButton: true
    });
    
    if (productIndex === undefined) return;
    
    const { value: discountType } = await Swal.fire({
        title: 'Tipo de descuento',
        input: 'select',
        inputOptions: {
            'percentage': 'Porcentaje (%)',
            'fixed': 'Monto fijo ($)'
        },
        showCancelButton: true
    });
    
    if (!discountType) return;
    
    const { value: amount } = await Swal.fire({
        title: discountType === 'percentage' ? 'Porcentaje de descuento' : 'Monto de descuento',
        input: 'number',
        inputPlaceholder: discountType === 'percentage' ? '0-100' : '0.00',
        showCancelButton: true,
        inputValidator: (value) => {
            const num = parseFloat(value);
            if (discountType === 'percentage' && (num < 0 || num > 100)) {
                return 'El porcentaje debe estar entre 0 y 100';
            }
            if (discountType === 'fixed' && num <= 0) {
                return 'El monto debe ser mayor a 0';
            }
        }
    });
    
    if (amount) {
        localCart[productIndex].discount = {
            type: discountType,
            amount: parseFloat(amount)
        };
        localCart[productIndex].discountType = 'item';
        renderLocalCart();
        showToast('success', 'Descuento aplicado', `a ${localCart[productIndex].name}`);
    }
}

/**
 * Aplica descuento al total de la compra
 */
async function applyTotalDiscount() {
    const { value: discountType } = await Swal.fire({
        title: 'Tipo de descuento',
        input: 'select',
        inputOptions: {
            'percentage': 'Porcentaje (%)',
            'fixed': 'Monto fijo ($)'
        },
        showCancelButton: true
    });
    
    if (!discountType) return;
    
    const { value: amount } = await Swal.fire({
        title: discountType === 'percentage' ? 'Porcentaje de descuento' : 'Monto de descuento',
        input: 'number',
        inputPlaceholder: discountType === 'percentage' ? '0-100' : '0.00',
        showCancelButton: true
    });
    
    if (amount) {
        cartDiscount = {
            type: discountType,
            amount: parseFloat(amount)
        };
        renderLocalCart();
        showToast('success', 'Descuento al total aplicado', '');
    }
}

/**
 * Aplica cupón de descuento
 */
async function applyCouponDiscount() {
    const { value: couponCode } = await Swal.fire({
        title: 'Cupón de descuento',
        input: 'text',
        inputPlaceholder: 'Ingresa el código del cupón',
        showCancelButton: true
    });
    
    if (couponCode) {
        // Validar cupón con el servidor
        try {
            const response = await fetch('ajax/validate_coupon.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ coupon: couponCode })
            });
            
            const result = await response.json();
            
            if (result.success) {
                cartDiscount = {
                    type: result.discount_type || 'percentage',
                    amount: parseFloat(result.amount),
                    coupon: couponCode
                };
                renderLocalCart();
                showToast('success', 'Cupón aplicado', result.message);
            } else {
                Swal.fire('Cupón inválido', result.message || 'El cupón no es válido', 'error');
            }
        } catch (error) {
            Swal.fire('Error', 'No se pudo validar el cupón', 'error');
        }
    }
}

/**
 * Aplica Gift Card
 */
async function applyGiftCard() {
    const { value: giftcardCode } = await Swal.fire({
        title: 'Gift Card',
        input: 'text',
        inputPlaceholder: 'Ingresa el código de la Gift Card',
        showCancelButton: true
    });
    
    if (giftcardCode) {
        try {
            const response = await fetch('ajax/validate_giftcard.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ giftcard: giftcardCode })
            });
            
            const result = await response.json();
            
            if (result.success) {
                // Agregar como producto especial de descuento
                addToCart(
                    'GIFTCARD_' + giftcardCode,
                    'Gift Card - ' + giftcardCode,
                    -parseFloat(result.balance) // Precio negativo para descuento
                );
                showToast('success', 'Gift Card aplicada', `Saldo: $${result.balance}`);
            } else {
                Swal.fire('Gift Card inválida', result.message, 'error');
            }
        } catch (error) {
            Swal.fire('Error', 'No se pudo validar la Gift Card', 'error');
        }
    }
}

// ==========================================
// 5. SISTEMA DE PAGOS (EFECTIVO Y TARJETA)
// ==========================================

/**
 * Abre modal de pago según tipo
 */
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

/**
 * Calcula total del carrito
 */
function calculateTotal() {
    let subtotal = 0;
    let totalDiscount = 0;

    // Calcular subtotal y descuentos por producto
    localCart.forEach(item => {
        let lineTotal = item.price * item.quantity;
        if (item.discount) {
            if (item.discount.type === 'percentage') {
                totalDiscount += lineTotal * (item.discount.amount / 100);
            } else {
                totalDiscount += item.discount.amount;
            }
        }
        subtotal += lineTotal;
    });

    // Aplicar descuento general
    if (cartDiscount.type) {
        if (cartDiscount.type === 'percentage') {
            totalDiscount += subtotal * (cartDiscount.amount / 100);
        } else {
            totalDiscount += cartDiscount.amount;
        }
    }

    return Math.max(0, subtotal - totalDiscount);
}

/**
 * Modal de pago en efectivo
 */
async function showCashPaymentModal(total) {
    const { value: receivedAmount } = await Swal.fire({
        title: 'Pago en Efectivo',
        html: `
            <div class="text-start">
                <p><strong>Total a pagar:</strong> $${total.toFixed(2)}</p>
                <label class="form-label mt-3">Monto recibido:</label>
            </div>
        `,
        input: 'number',
        inputValue: total.toFixed(2),
        inputPlaceholder: '0.00',
        inputAttributes: {
            min: 0,
            step: 0.01,
            autofocus: true
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
            icon: 'success',
            title: 'Cambio calculado',
            html: `
                <div class="text-start">
                    <p><strong>Recibido:</strong> $${receivedAmount.toFixed(2)}</p>
                    <p><strong>Total:</strong> $${total.toFixed(2)}</p>
                    <div class="mt-3 p-3 bg-light rounded">
                        <h4 class="text-success mb-0">Cambio: $${change.toFixed(2)}</h4>
                    </div>
                </div>
            `,
            confirmButtonText: 'Confirmar y Finalizar',
            showCancelButton: true,
            cancelButtonText: 'Ajustar monto'
        }).then((result) => {
            if (result.isConfirmed) {
                processSale('cash');
            }
        });
    }
}

/**
 * Modal de pago con tarjeta
 * Integración con Wompi para procesar pagos
 */
async function showCardPaymentModal(total) {
    try {
        // Mostrar confirmación inicial
        const { value: confirmPayment } = await Swal.fire({
            title: 'Pago con Tarjeta',
            html: `
                <div class="text-start">
                    <p><strong>Total a pagar:</strong> <span class="text-success fs-5">$${total.toFixed(2)}</span></p>
                    <div class="alert alert-info mt-3">
                        <i class="fas fa-lock me-2"></i>
                        <small>Se abrirá una ventana segura de <strong>Wompi</strong> para procesar el pago</small>
                    </div>
                    <p class="text-muted small mt-3">
                        <strong>¿Desea continuar con el pago?</strong>
                    </p>
                </div>
            `,
            showCancelButton: true,
            confirmButtonText: 'Continuar',
            cancelButtonText: 'Cancelar'
        });

        if (!confirmPayment) {
            return; // Usuario canceló
        }

        // Obtener email del cliente actual
        const email = currentCustomer.correo || 'damefactura@gmail.com';
        const description = `Venta POS - ${currentCustomer.nombre}`;

        // Mostrar progreso de conexión
        Swal.fire({
            title: 'Conectando con Wompi...',
            html: '<div class="spinner-border text-primary" role="status"><span class="visually-hidden">Cargando...</span></div>',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        // Procesar el pago con Wompi
        const paymentResult = await WompiPayment.processPayment({
            amount: total,
            email: email,
            description: description
        });

        // Verificar resultado del pago
        if (paymentResult.success && paymentResult.paymentData.status === 'completed') {
            // Pago exitoso
            await Swal.fire({
                icon: 'success',
                title: '¡Pago Completado!',
                html: `
                    <div class="text-start">
                        <p><strong>Referencia:</strong> ${paymentResult.reference}</p>
                        <p><strong>Monto:</strong> $${total.toFixed(2)}</p>
                        <p><strong>Estado:</strong> <span class="badge bg-success">Aprobado</span></p>
                        <p class="text-muted small mt-3">Finalizando la venta...</p>
                    </div>
                `,
                confirmButtonText: 'Procesar Venta',
                allowOutsideClick: false
            });

            // Procesar la venta con los datos del pago
            processSale('card', {
                reference: paymentResult.reference,
                method: 'Wompi',
                status: 'completed'
            });

        } else {
            // Pago pendiente o cancelado
            const { value: continueProcess } = await Swal.fire({
                icon: 'warning',
                title: 'Estado del Pago',
                html: `
                    <div class="text-start">
                        <p><strong>Referencia:</strong> ${paymentResult.reference}</p>
                        <p><strong>Estado:</strong> <span class="badge bg-warning">Pendiente de confirmación</span></p>
                        <p class="text-muted small mt-3">
                            El pago está siendo procesado. ¿Desea continuar de todas formas?
                        </p>
                    </div>
                `,
                showCancelButton: true,
                confirmButtonText: 'Sí, continuar',
                cancelButtonText: 'Cancelar y reintentar'
            });

            if (continueProcess) {
                // Procesar venta de todas formas
                processSale('card', {
                    reference: paymentResult.reference,
                    method: 'Wompi',
                    status: 'pending'
                });
            } else {
                // Reintentar pago
                showCardPaymentModal(total);
            }
        }

    } catch (error) {
        console.error('Error en pago con tarjeta:', error);

        // Mostrar error
        await Swal.fire({
            icon: 'error',
            title: 'Error en el Pago',
            html: `
                <div class="text-start">
                    <p><strong>Descripción del error:</strong></p>
                    <p class="text-danger small">${error.message}</p>
                    <p class="text-muted small mt-3">
                        Por favor intente nuevamente o use otro método de pago.
                    </p>
                </div>
            `,
            confirmButtonText: 'Entendido'
        });

        // Opción para reintentar
        const { value: retry } = await Swal.fire({
            title: '¿Reintentar?',
            text: 'Presione continuar para intentar nuevamente',
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Reintentar',
            cancelButtonText: 'Cancelar'
        });

        if (retry) {
            showCardPaymentModal(total);
        }
    }
}

// ==========================================
// 6. PROCESAMIENTO DTE Y FINALIZACIÓN DE VENTA
// ==========================================

/**
 * Prepara JSON DTE según normativa salvadoreña
 * IMPORTANTE 1: Los precios en localCart YA INCLUYEN IVA (consumidor final)
 * Estructura: precio con IVA = ventaGravada
 * Cálculo: ventaSinIVA = ventaGravada / 1.13, IVA = ventaGravada - ventaSinIVA
 * IMPORTANTE 2: numeroControl se genera en el SERVIDOR (process_sale_complete.php)
 * IMPORTANTE 3: codigoGeneracion es UUID v4 de 36 caracteres: xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx
 */
function prepareDTEJson(metodoPago, paymentDetails = null) {
    const totalVentaConIVA = calculateTotal();
    
    // Desglosar IVA: si cliente pagó $113, entonces:
    // Valor sin IVA = 113 / 1.13 = 100
    // IVA = 113 - 100 = 13
    const totalVentaSinIVA = parseFloat((totalVentaConIVA / 1.13).toFixed(2));
    const totalIVA = parseFloat((totalVentaConIVA - totalVentaSinIVA).toFixed(2));
    
    // Calcular descuentos por línea
    let totalDescuentos = 0;
    let sumaTributosItems = 0;
    
    const cuerpoDocumento = localCart.map((item, index) => {
        // Precio unitario YA INCLUYE IVA
        const precioConIVA = item.price;
        const cantidad = item.quantity;
        
        // ventaGravada es el total pagado (con IVA incluido)
        let ventaGravadaTotal = precioConIVA * cantidad;
        let montoDescu = 0;
        
        if (item.discount) {
            if (item.discount.type === 'percentage') {
                montoDescu = ventaGravadaTotal * (item.discount.amount / 100);
            } else {
                montoDescu = item.discount.amount;
            }
            ventaGravadaTotal = Math.max(0, ventaGravadaTotal - montoDescu);
            totalDescuentos += montoDescu;
        }
        
        // Desglosar IVA: si el cliente pagó ventaGravadaTotal, entonces:
        // Valor gravado (sin IVA) = ventaGravadaTotal / 1.13
        const ventaSinIVA = parseFloat((ventaGravadaTotal / 1.13).toFixed(2));
        const ivaDelItem = parseFloat((ventaGravadaTotal - ventaSinIVA).toFixed(2));
        
        sumaTributosItems += ivaDelItem;
        
        return {
            "numItem": index + 1,
            "tipoItem": 1,
            "numeroDocumento": null,
            "codTributo": null,
            "descripcion": item.name,
            "cantidad": cantidad,
            "uniMedida": 59,
            "precioUni": precioConIVA,
            "montoDescu": parseFloat(montoDescu.toFixed(2)),
            "ventaNoSuj": 0.0,
            "ventaExenta": 0.0,
            "ventaGravada": parseFloat(ventaGravadaTotal.toFixed(2)),
            "tributos": null,
            "psv": 0.0,
            "noGravado": 0.0,
            "codigo": item.id.toString(),
            "ivaItem": ivaDelItem
        };
    });

    // Añadir descuento general como ítem negativo (si aplica)
    if (cartDiscount.type && cartDiscount.amount > 0) {
        let descuentoGeneral = 0;
        if (cartDiscount.type === 'percentage') {
            descuentoGeneral = totalVentaConIVA * (cartDiscount.amount / 100);
        } else {
            descuentoGeneral = cartDiscount.amount;
        }
        
        cuerpoDocumento.push({
            "numItem": cuerpoDocumento.length + 1,
            "tipoItem": 2, // 2 = Descuento
            "numeroDocumento": null,
            "codTributo": null,
            "descripcion": cartDiscount.coupon ? 
                `Descuento por cupón: ${cartDiscount.coupon}` : 
                "Descuento general",
            "cantidad": 1,
            "uniMedida": 59,
            "precioUni": -descuentoGeneral,
            "montoDescu": 0,
            "ventaNoSuj": 0.0,
            "ventaExenta": 0.0,
            "ventaGravada": parseFloat((-descuentoGeneral).toFixed(2)),
            "tributos": null,
            "psv": 0.0,
            "noGravado": 0.0,
            "codigo": "DESC",
            "ivaItem": 0
        });
        
        totalDescuentos += descuentoGeneral;
    }
    
    // Construir array de tributos
    // Código "59" = IVA (Impuesto al Valor Agregado) según MH El Salvador
    const tributosArray = totalIVA > 0 ? [{
        "codigo": "59",
        "descripcion": "Impuesto al Valor Agregado 13%",
        "valor": totalIVA
    }] : [];

    return {
        "identificacion": {
            "version": 1,
            "ambiente": "00",
            "tipoDte": "01",
            "numeroControl": null,  // SE GENERA EN SERVIDOR: DTE-01-P001M001-000000000000001
            "codigoGeneracion": mhControl.codigoGeneracion,  // UUID v4 36 chars: xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx
            "tipoModelo": 1,
            "tipoOperacion": 1,
            "tipoContingencia": null,
            "motivoContin": null,
            "fecEmi": mhControl.fechaEmision,
            "horEmi": mhControl.horaEmision,
            "tipoMoneda": "USD"
        },
        "documentoRelacionado": null,
        "emisor": {
            "nit": "06150911851010",
            "nrc": "1992934",
            "nombre": "Rodriguez Machuca Jose Alexander",
            "codActividad": "46510",
            "descActividad": "Venta al por mayor de computadoras, equipo periférico y programas informáticos",
            "nombreComercial": null,
            "tipoEstablecimiento": "02",
            "direccion": {
                "departamento": "06",
                "municipio": "23",
                "complemento": "Avsiempre buena 1024, SS"
            },
            "telefono": "21212121",
            "correo": "503software@gmail.com",
            "codEstableMH": "P001",
            "codEstable": "P001",
            "codPuntoVentaMH": "M001",
            "codPuntoVenta": "M001"
        },
        "receptor": currentCustomer,
        "otrosDocumentos": null,
        "ventaTercero": null,
        "cuerpoDocumento": cuerpoDocumento,
        "resumen": {
            "totalNoSuj": 0.0,
            "totalExenta": 0.0,
            // totalGravada = el total que el cliente pagó (con IVA incluido)
            "totalGravada": parseFloat(totalVentaConIVA.toFixed(2)),
            "subTotalVentas": parseFloat(totalVentaConIVA.toFixed(2)),
            "descuNoSuj": 0.0,
            "descuExenta": 0.0,
            "descuGravada": parseFloat(totalDescuentos.toFixed(2)),
            "porcentajeDescuento": 0.0,
            "totalDescu": parseFloat(totalDescuentos.toFixed(2)),
            "tributos": tributosArray,
            "subTotal": parseFloat(totalVentaConIVA.toFixed(2)),
            "ivaRete1": 0.0,
            "reteRenta": 0.0,
            // montoTotalOperacion es el valor gravado (sin IVA)
            "montoTotalOperacion": parseFloat(totalVentaSinIVA.toFixed(2)),
            "totalNoGravado": 0.0,
            // totalPagar es lo que el cliente realmente pagó (con IVA)
            "totalPagar": parseFloat(totalVentaConIVA.toFixed(2)),
            "totalLetras": numeroALetras(totalVentaConIVA),
            "totalIva": totalIVA,
            "saldoFavor": 0.0,
            "condicionOperacion": metodoPago === 'cash' ? 1 : 2,
            "pagos": paymentDetails ? [{
                "codigo": metodoPago === 'cash' ? '01' : '03',
                "montoPago": parseFloat(totalVentaConIVA.toFixed(2)),
                "referencia": paymentDetails.reference || null,
                "plazo": null
            }] : null,
            "numPagoElectronico": null
        },
        "extension": {
            "nombEntrega": null,
            "docuEntrega": null,
            "nombRecibe": null,
            "docuRecibe": null,
            "observaciones": null,
            "placaVehiculo": null
        },
        "apendice": null
    };
}

/**
 * Procesa la venta completa
 */
async function processSale(paymentMethod = 'cash', paymentDetails = null) {
    const dteData = prepareDTEJson(paymentMethod, paymentDetails);
    
    // Validar que haya productos
    if (localCart.length === 0) {
        Swal.fire('Error', 'El carrito está vacío', 'error');
        return;
    }
    
    // Mostrar progreso
    const { value: accept } = await Swal.fire({
        title: 'Confirmar Venta',
        html: `
            <div class="text-start">
                <p><strong>Total:</strong> $${calculateTotal().toFixed(2)}</p>
                <p><strong>Método:</strong> ${paymentMethod === 'cash' ? 'Efectivo' : 'Tarjeta'}</p>
                <p><strong>Cliente:</strong> ${currentCustomer.nombre}</p>
                <p class="text-muted small mt-3">¿Desea continuar con la venta?</p>
            </div>
        `,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Sí, procesar venta',
        cancelButtonText: 'Cancelar'
    });
    
    if (!accept) return;
    
    Swal.fire({
        title: 'Procesando Venta...',
        html: `
            <div class="text-start" id="progress-steps">
                <p class="text-primary">⏳ Generando documento DTE...</p>
                <p class="text-muted">⌛ Enviando a Hacienda...</p>
                <p class="text-muted">⏳ Registrando venta...</p>
            </div>
            <div class="progress mt-3" style="height: 10px;">
                <div class="progress-bar progress-bar-striped progress-bar-animated" style="width: 0%"></div>
            </div>
        `,
        allowOutsideClick: false,
        didOpen: () => {
            Swal.showLoading();
            const progressBar = Swal.getHtmlContainer().querySelector('.progress-bar');
            let progress = 0;
            const interval = setInterval(() => {
                progress += 10;
                progressBar.style.width = `${progress}%`;
                if (progress >= 100) clearInterval(interval);
            }, 300);
        }
    });
    
    try {
        const response = await fetch('ajax/process_sale_complete.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(dteData)
        });
        
        const result = await response.json();
        
        if (result.success) {
            // Mostrar modal de éxito con detalles
            await Swal.fire({
                icon: 'success',
                title: '¡Venta Exitosa!',
                html: `
                    <div class="text-start">
                        <p><strong>Código DTE:</strong> ${result.codigoGeneracion}</p>
                        <p><strong>Número Control:</strong> ${result.numeroControl}</p>
                        <p><strong>Total:</strong> $${calculateTotal().toFixed(2)}</p>
                        <p class="text-muted small mt-3">Se abrirá el ticket de impresión. Ciérralo cuando termines.</p>
                    </div>
                `,
                confirmButtonText: 'Abrir Ticket',
                allowOutsideClick: false,
                allowEscapeKey: false
            });
            
            // ABRIR TICKET EN POPUP
            const printWindow = window.open(
                `print_ticket.php?id=${result.codigoGeneracion}`,
                'PrintWindow',
                'width=800,height=600,menubar=yes,toolbar=yes,location=no,status=no,scrollbars=yes'
            );
            
            // ENVIAR CORREO EN PARALELO (sin bloquear)
            fetch('ajax/send_receipt_email.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({
                    codigoGeneracion: result.codigoGeneracion,
                    numeroControl: result.numeroControl,
                    correoCliente: currentCustomer.correo,
                    totalPagar: calculateTotal()
                })
            }).then(r => r.json()).then(emailResult => {
                console.log('Email result:', emailResult);
            }).catch(err => {
                console.warn('Error enviando correo:', err);
            });
            
            // DETECTAR CIERRE DE VENTANA DE IMPRESIÓN
            if (printWindow) {
                const checkWindowClosed = setInterval(() => {
                    if (printWindow.closed) {
                        clearInterval(checkWindowClosed);
                        
                        // Ventana cerrada - resetear formulario y mostrar confirmación
                        resetFormulario();
                        
                        Swal.fire({
                            icon: 'success',
                            title: '¡Todo Completado!',
                            html: `
                                <div class="text-start">
                                    <p class="mb-2">✅ Venta procesada exitosamente</p>
                                    <p class="mb-2">✅ Ticket impreso</p>
                                    <p class="mb-2">📧 Correo enviado a ${currentCustomer.correo}</p>
                                    <p class="text-success fw-bold mt-3">🎯 Sistema listo para nueva venta</p>
                                </div>
                            `,
                            confirmButtonText: 'Continuar',
                            allowOutsideClick: false,
                            allowEscapeKey: false
                        });
                    }
                }, 500);
                
                // TIMEOUT DE SEGURIDAD: Si ventana no se cierra en 5 minutos, forza el reset
                setTimeout(() => {
                    if (!printWindow.closed) {
                        clearInterval(checkWindowClosed);
                        printWindow.close();
                        
                        resetFormulario();
                        
                        Swal.fire({
                            icon: 'warning',
                            title: 'Tiempo Expirado',
                            html: `
                                <div class="text-start">
                                    <p>La ventana de impresión se cerró automáticamente.</p>
                                    <p class="text-success fw-bold mt-3">✅ Sistema listo para nueva venta</p>
                                </div>
                            `,
                            confirmButtonText: 'Continuar'
                        });
                    }
                }, 5 * 60 * 1000); // 5 minutos
            } else {
                // No se pudo abrir ventana emergente - resetear igual
                resetFormulario();
            }
            
            // Guardar copia del JSON localmente (backup)
            saveLocalBackup(dteData, result.codigoGeneracion);
            
        } else {
            throw new Error(result.error || 'Error desconocido en el servidor');
        }
    } catch (error) {
        Swal.fire({
            icon: 'error',
            title: 'Error en la venta',
            html: `
                <div class="text-start">
                    <p>${error.message}</p>
                    <p class="text-muted small mt-3">Por favor, intente nuevamente o contacte al soporte técnico.</p>
                </div>
            `,
            confirmButtonText: 'Entendido'
        });
    }
}

/**
 * Resetea completamente el formulario de venta
 * Limpia carrito, descuentos, cliente y prepara para nueva venta
 */
function resetFormulario() {
    // 1. Limpiar carrito
    localCart = [];
    cartDiscount = { type: null, amount: 0 };
    
    // 2. Resetear cliente a "Consumidor Final"
    currentCustomer = {
        nombre: "Consumidor Final",
        tipoDocumento: null,
        numDocumento: "000000000000000",
        nrc: null,
        codActividad: "000000",
        descActividad: "Consumidor Final",
        correo: "damefactura@gmail.com"
    };
    
    // 3. Actualizar UI
    renderLocalCart();
    document.getElementById('current-customer-name').textContent = 'Consumidor Final';
    document.getElementById('customer-selected-info').style.display = 'none';
    
    // 4. Limpiar input de código de barras
    const barcodeInput = document.getElementById('barcode-input');
    if (barcodeInput) {
        barcodeInput.value = '';
        barcodeInput.focus();
    }
    
    // 5. Resetear estado de botones
    updateFinishButtonState();
    
    // 6. Mostrar notificación
    showToast('success', 'Sistema Listo', 'Puedes realizar una nueva venta');
}

/**
 * Guarda backup local del DTE
 */
function saveLocalBackup(dteData, codigo) {
    try {
        const backup = {
            timestamp: new Date().toISOString(),
            codigoGeneracion: codigo,
            data: dteData
        };
        
        // Guardar en localStorage
        const backups = JSON.parse(localStorage.getItem('dte_backups') || '[]');
        backups.unshift(backup);
        if (backups.length > 50) backups.pop(); // Mantener solo 50 backups
        localStorage.setItem('dte_backups', JSON.stringify(backups));
    } catch (e) {
        console.warn('No se pudo guardar backup local:', e);
    }
}

// ==========================================
// 7. GESTIÓN DE CLIENTES
// ==========================================

/**
 * Abre modal para seleccionar/crear cliente
 */
function openCustomerModal() {
    const modal = new bootstrap.Modal(document.getElementById('customerModal'));
    
    // Prellenar formulario si hay datos
    if (currentCustomer.nombre !== "Consumidor Final") {
        document.getElementById('rec-nombre').value = currentCustomer.nombre;
        document.getElementById('rec-correo').value = currentCustomer.correo;
    }
    
    modal.show();
}

/**
 * Guarda datos del cliente
 */
function saveCustomerData() {
    const nombre = document.getElementById('rec-nombre').value.trim();
    const correo = document.getElementById('rec-correo').value.trim();
    
    if (!nombre) {
        Swal.fire('Error', 'El nombre es obligatorio', 'error');
        return;
    }
    
    currentCustomer.nombre = nombre.toUpperCase();
    currentCustomer.correo = correo || "damefactura@gmail.com";
    
    // Actualizar interfaz
    document.getElementById('current-customer-name').textContent = currentCustomer.nombre;
    document.getElementById('customer-selected-info').style.display = 'block';
    
    // Cerrar modal
    bootstrap.Modal.getInstance(document.getElementById('customerModal')).hide();
    
    showToast('success', 'Cliente asignado', currentCustomer.nombre);
}

// ==========================================
// 8. ATRIBUTOS DE TECLADO
// ==========================================

/**
 * Configura atajos de teclado globales
 */
function setupKeyboardShortcuts() {
    document.addEventListener('keydown', function(e) {
        // Prevenir comportamiento por defecto de teclas F
        if (e.key.startsWith('F') && e.key.length <= 3) {
            e.preventDefault();
        }
        
        // Atajos principales
        switch(e.key) {
            case 'F1':
                openCustomerModal();
                break;
                
            case 'F7':
                if(!document.getElementById('finish-sale-btn').disabled) {
                    const total = calculateTotal();
                    if (total > 0) {
                        openPaymentModal('cash');
                    }
                }
                break;
                
            case 'F8':
                if(localCart.length > 0) {
                    applyDiscount();
                }
                break;
                
            case 'F9':
                if(!document.getElementById('finish-sale-btn').disabled) {
                    openPaymentModal('cash');
                }
                break;
                
            case 'F10':
                if(!document.getElementById('finish-sale-btn').disabled) {
                    openPaymentModal('card');
                }
                break;
                
            case 'Escape':
                if(localCart.length > 0) {
                    clearCart();
                }
                break;
                
            case 'Enter':
                // Evitar que Enter en campos de formulario active atajos
                if (e.target.tagName !== 'INPUT' || e.target.id === 'barcode-input') {
                    e.preventDefault();
                    const input = document.getElementById('barcode-input');
                    if (input && document.activeElement === input) {
                        handleBarcodeInput(input.value);
                    }
                }
                break;
                
            case '+':
                // Aumentar cantidad del último producto
                if (localCart.length > 0) {
                    updateQuantity(localCart.length - 1, 1);
                }
                break;
                
            case '-':
                // Disminuir cantidad del último producto
                if (localCart.length > 0) {
                    updateQuantity(localCart.length - 1, -1);
                }
                break;
        }
    });
}

// ==========================================
// 9. UTILIDADES ADICIONALES
// ==========================================

/**
 * Muestra notificación toast
 */
function showToast(type, title, message) {
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 3000,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer);
            toast.addEventListener('mouseleave', Swal.resumeTimer);
        }
    });
    
    Toast.fire({
        icon: type,
        title: `<strong>${title}</strong>${message ? `<br><small>${message}</small>` : ''}`
    });
}

/**
 * Inicializa teclado en pantalla
 */
function setupOnScreenKeyboard() {
    const input = document.getElementById('barcode-input');
    if (!input) return;
    
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
}

// ==========================================
// 10. INICIALIZACIÓN DEL SISTEMA
// ==========================================

document.addEventListener('DOMContentLoaded', function() {
    // 1. Iniciar reloj
    setInterval(updateClock, 1000);
    updateClock();
    
    // 2. Configurar foco inicial
    const input = document.getElementById('barcode-input');
    if (input) {
        input.focus();
        
        // Escaner automático: detecta cuando se termina de escribir (pausa de 100ms)
        let scannerTimeout;
        input.addEventListener('input', function(e) {
            clearTimeout(scannerTimeout);
            scannerTimeout = setTimeout(() => {
                if (this.value.length >= 3) { // Mínimo 3 caracteres para búsqueda
                    handleBarcodeInput(this.value);
                }
            }, 100);
        });
        
        // También soporte para Enter explícito
        input.addEventListener('keypress', (e) => {
            if (e.key === 'Enter') {
                e.preventDefault();
                handleBarcodeInput(input.value);
            }
        });
    }
    
    // 3. Configurar atajos de teclado
    setupKeyboardShortcuts();
    
    // 4. Configurar teclado en pantalla
    setupOnScreenKeyboard();
    
    // 5. Inicializar carrito
    renderLocalCart();
    
    // 6. Mostrar ayuda de atajos al inicio
    setTimeout(() => {
        console.log(`
            ===========================================
            ATRIBUTOS DE TECLADO DISPONIBLES:
            ===========================================
            F1 .......... Abrir ventana de cliente
            F7 .......... Pagar con efectivo (si hay productos)
            F8 .......... Aplicar descuento
            F9 .......... Pagar con efectivo (alternativo)
            F10 ......... Pagar con tarjeta
            ESC ......... Cancelar venta/vaciar carrito
            ENTER ....... Procesar código de barras
            + ........... Aumentar cantidad último producto
            - ........... Disminuir cantidad último producto
            ===========================================
        `);
    }, 1000);
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
