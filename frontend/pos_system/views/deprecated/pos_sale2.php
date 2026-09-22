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
if (defined('SUPABASE_URL') && !empty(SUPABASE_URL)) {
    // Inicializar la conexión a la tabla de productos (dte_productos)
    $db = supabase(TABLE_PRODUCTS);

    // Optimización: Seleccionar solo columnas necesarias para la UI
    $columns = 'id, nombre_producto, precio_venta, imagen_url';

    // Ejecutar la consulta intentando filtrar por campo 'frecuente'
    $frequentProducts = $db->select($columns, ['frecuente' => true], 10);

    // Manejo de Resultados y Fallbacks
    if (!isset($frequentProducts['success']) || $frequentProducts['success'] !== true) {
        $error_message = 'Error en la conexión o consulta de la base de datos.';
        
        if (isset($frequentProducts['error'])) {
            $raw_error = (string)$frequentProducts['error'];

            // Caso 1: La tabla no existe
            if (stripos($raw_error, 'relation') !== false && stripos($raw_error, 'does not exist') !== false) {
                $error_message = "Error: La tabla '" . $db->getTableName() . "' no existe. Verifique la configuración.";
            } 
            // Caso 2: La columna 'frecuente' no existe (fallback a traer los primeros 10)
            elseif (stripos($raw_error, 'column') !== false && (stripos($raw_error, 'frecuente') !== false || stripos($raw_error, 'not found') !== false)) {
                $frequentProducts = $db->select($columns, [], 10);
                if ($frequentProducts['success']) {
                    $frequentProducts['message'] = 'fallback_no_frecuente_column';
                }
            } else {
                $error_message = "Error de Consulta: " . htmlspecialchars($raw_error);
            }
        }

        // Si después del posible fallback seguimos sin éxito
        if (!isset($frequentProducts['success']) || $frequentProducts['success'] !== true) {
            $frequentProducts = [
                'success' => false,
                'error' => $error_message
            ];
        }
    }

    // Asegurar estructura de 'data' en éxito
    if (isset($frequentProducts['success']) && $frequentProducts['success'] === true && !isset($frequentProducts['data'])) {
        $frequentProducts['data'] = [];
    }

} else {
    $frequentProducts = [
        'success' => false,
        'error' => 'Error de Configuración: SUPABASE_URL no definida en el archivo .env'
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
    
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/pos_styles.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/pos_themes.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/css/pos_animations.css">
    
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --success-gradient: linear-gradient(135deg, #42e695 0%, #3bb2b8 100%);
            --danger-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            --warning-gradient: linear-gradient(135deg, #f6d365 0%, #fda085 100%);
        }
    </style>
</head>
<body class="pos-body">
    <?php include 'partials/header.php'; ?>
    
    <div class="container-fluid pos-container">
        <div class="row g-0 h-100">
            <div class="col-md-7 pos-left-panel">
                <div class="pos-sale-header">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="pos-info-card">
                                <i class="fas fa-cash-register"></i>
                                <div>
                                    <small>Caja #<?= $registerNumber ?></small>
                                    <h4>Venta Activa</h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="pos-info-card">
                                <i class="fas fa-user-tie"></i>
                                <div>
                                    <small>Cajero</small>
                                    <h4><?= htmlspecialchars($cashier['name']) ?></h4>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="pos-info-card">
                                <i class="fas fa-clock"></i>
                                <div>
                                    <small>Hora</small>
                                    <h4 id="current-time"><?= date('H:i:s') ?></h4>
                                </div>
                            </div>
                        </div>

                        <!-- <div class="col-md-4">
                            <div class="pos-info-card">
                                <i class="fas fa-clock"></i>
                                <div>
                                    <small>Hora</small>
                                    <h4 id="current-time"><?= date('H:i:s') ?></h4>
                                </div>
                            </div>
                        </div>  -->
                    </div>
                </div>
                
                <div class="pos-scanner-section">
                    <div class="input-group pos-scanner-input">
                        <span class="input-group-text bg-primary text-white">
                            <i class="fas fa-barcode"></i>
                        </span>
                        <input type="text" 
                               class="form-control form-control-lg" 
                               id="barcode-input" 
                               placeholder="Escanear código de barras o ingresar código..."
                               autofocus>
                        <button class="btn btn-success" id="manual-add-btn">
                            <i class="fas fa-plus"></i> Agregar
                        </button>
                    </div>
                    
                    <div class="scanner-hint">
                        <small><i class="fas fa-info-circle"></i> Presione ENTER para agregar producto</small>
                    </div>
                </div>
                <div class="pos-frequent-products">
                    <h5><i class="fas fa-fire"></i> Productos Frecuentes</h5>
                    <div class="row" id="frequent-products-grid">
                        <?php
                        // 1. Verificación de la Respuesta Exitosa:
                        // Se valida que $frequentProducts exista, sea un array, tenga 'success' y sea true,
                        // y que 'data' exista y sea un array (permitiendo que esté vacío en este punto).
                        if (isset($frequentProducts) && is_array($frequentProducts) &&
                            isset($frequentProducts['success']) &&
                            $frequentProducts['success'] === true &&
                            isset($frequentProducts['data']) && // <-- Añadimos la verificación de que 'data' exista
                            is_array($frequentProducts['data'])): // <-- Ya NO se requiere !empty($frequentProducts['data']) aquí
                        ?>
                            <?php
                            // 2. Verificación de Productos Vacíos (Dentro del Caso de Éxito):
                            // Si la respuesta es exitosa pero la data está vacía, mostramos el mensaje informativo.
                            if (!empty($frequentProducts['data'])):
                            ?>
                                <?php foreach ($frequentProducts['data'] as $product): ?>
                                    <div class="col-md-3 col-sm-6">
                                        <div class="product-card" data-product-id="<?= htmlspecialchars((string)($product['id'] ?? '')) ?>">
                                            <?php if (!empty($product['imagen_url'])): ?>
                                                <img src="<?= htmlspecialchars((string)$product['imagen_url']) ?>"
                                                    alt="<?= htmlspecialchars((string)$product['nombre_producto']) ?>"
                                                    class="product-image">
                                            <?php else: ?>
                                                <div class="product-image-placeholder">
                                                    <i class="fas fa-box"></i>
                                                </div>
                                            <?php endif; ?>
                                            <div class="product-info">
                                                <h6><?= htmlspecialchars((string)$product['nombre_producto']) ?></h6>
                                                <p class="product-price">$<?=
                                                    isset($product['precio_venta']) ?
                                                    number_format((float)$product['precio_venta'], 2) :
                                                    '0.00'
                                                ?></p>
                                                <button class="btn btn-sm btn-outline-primary add-to-cart-btn">
                                                    <i class="fas fa-cart-plus"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="col-12">
                                    <div class="alert alert-info">
                                        No hay productos frecuentes configurados.
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php else: ?>
                            <div class="col-12">
                                <div class="alert alert-danger">
                                    <?php
                                    // Determinación del mensaje de error
                                    $errorMessage = 'Error desconocido de la consulta o formato inválido.';
                                    if (!isset($frequentProducts)) {
                                        $errorMessage = "Error: La variable de productos frecuentes no está definida.";
                                    } elseif (isset($frequentProducts['error'])) {
                                        $errorMessage = "Error: " . htmlspecialchars($frequentProducts['error']);
                                    } elseif (isset($frequentProducts['success']) && $frequentProducts['success'] === false) {
                                        $errorMessage = "Error: La consulta falló (success=false).";
                                    }

                                    echo $errorMessage;
                                    ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <?php include 'partials/keyboard.php'; ?>
            </div>
            
            <div class="col-md-5 pos-right-panel">
                <div class="pos-cart-header">
                    <h3><i class="fas fa-shopping-cart"></i> Carrito de Venta</h3>
                    <button class="btn btn-danger btn-sm" id="clear-cart-btn">
                        <i class="fas fa-trash"></i> Vaciar
                    </button>
                </div>
                
                <div class="pos-cart-items" id="cart-items-container">
                    <div class="empty-cart-message">
                        <i class="fas fa-shopping-cart fa-3x text-muted"></i>
                        <p>El carrito está vacío</p>
                        <small>Escanee productos para comenzar</small>
                    </div>
                </div>
                
                <div class="pos-totals">
                    <div class="total-row">
                        <span>Subtotal:</span>
                        <span id="subtotal-amount">$0.00</span>
                    </div>
                    <div class="total-row">
                        <span>IVA (<?= (IVA_RATE * 100) ?>%):</span>
                        <span id="tax-amount">$0.00</span>
                    </div>
                    <div class="total-row">
                        <span>Descuento:</span>
                        <span id="discount-amount">$0.00</span>
                    </div>
                    <div class="total-row grand-total">
                        <span><strong>TOTAL:</strong></span>
                        <span id="total-amount"><strong>$0.00</strong></span>
                    </div>
                </div>
                
                <div class="pos-action-buttons">
                    <div class="row g-2">
                        <div class="col-md-6">
                            <button class="btn btn-lg btn-success w-100 payment-btn" data-type="cash">
                                <i class="fas fa-money-bill-wave"></i> Efectivo
                            </button>
                        </div>
                        <div class="col-md-6">
                            <button class="btn btn-lg btn-primary w-100 payment-btn" data-type="card">
                                <i class="fas fa-credit-card"></i> Tarjeta
                            </button>
                        </div>
                        <div class="col-md-4">
                            <button class="btn btn-lg btn-info w-100" id="customer-btn">
                                <i class="fas fa-user"></i> Cliente
                            </button>
                        </div>
                        <div class="col-md-4">
                            <button class="btn btn-lg btn-warning w-100" id="discount-btn">
                                <i class="fas fa-percent"></i> Descuento
                            </button>
                        </div>
                        <div class="col-md-4">
                            <button class="btn btn-lg btn-danger w-100" id="cancel-sale-btn">
                                <i class="fas fa-times"></i> Cancelar
                            </button>
                        </div>
                        <div class="col-md-12 mt-2">
                            <button class="btn btn-lg btn-dark w-100" id="complete-sale-btn" disabled>
                                <i class="fas fa-check-circle"></i> FINALIZAR VENTA
                            </button>
                        </div>
                    </div>
                </div>
                
                <div class="pos-customer-info" id="customer-info" style="display: none;">
                    <h6><i class="fas fa-user-tag"></i> Información del Cliente</h6>
                    <div class="customer-details">
                        <p><strong>Nombre:</strong> <span id="customer-name">Consumidor Final</span></p>
                        <p><strong>Documento:</strong> <span id="customer-document">N/A</span></p>
                        <p><strong>NRC:</strong> <span id="customer-nrc">N/A</span></p>
                    </div>
                    <button class="btn btn-sm btn-outline-secondary w-100" id="change-customer-btn">
                        <i class="fas fa-exchange-alt"></i> Cambiar Cliente
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <div class="modal fade" id="customerModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-users"></i> Seleccionar Cliente</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="input-group mb-3">
                        <input type="text" class="form-control" id="search-customer" 
                               placeholder="Buscar por NIT, DUI o nombre...">
                        <button class="btn btn-primary" id="search-customer-btn">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                    
                    <div class="customer-list" id="customer-list">
                        </div>
                    
                    <div class="mt-3">
                        <button class="btn btn-outline-primary w-100" id="new-customer-btn">
                            <i class="fas fa-user-plus"></i> Nuevo Cliente
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="modal fade" id="paymentModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-money-check-alt"></i> Procesar Pago</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="payment-summary">
                        <h4 class="text-center">Total a Pagar: <span id="payment-total">$0.00</span></h4>
                        <div class="payment-method" id="payment-method">
                            </div>
                    </div>
                    
                    <div class="cash-payment" id="cash-payment-section" style="display: none;">
                        <div class="mb-3">
                            <label for="cash-received">Efectivo Recibido:</label>
                            <input type="number" class="form-control form-control-lg" 
                                   id="cash-received" step="0.01" min="0">
                        </div>
                        <div class="change-display">
                            <h5>Cambio: <span id="change-amount" class="text-success">$0.00</span></h5>
                        </div>
                    </div>
                    
                    <div class="card-payment" id="card-payment-section" style="display: none;">
                        <div class="mb-3">
                            <label for="card-number">Número de Tarjeta:</label>
                            <input type="text" class="form-control" id="card-number" 
                                   placeholder="**** **** **** ****">
                        </div>
                        <div class="row">
                            <div class="col-md-6">
                                <label for="card-expiry">Expiración:</label>
                                <input type="text" class="form-control" id="card-expiry" 
                                       placeholder="MM/YY">
                            </div>
                            <div class="col-md-6">
                                <label for="card-cvv">CVV:</label>
                                <input type="text" class="form-control" id="card-cvv" 
                                       placeholder="***">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-success" id="confirm-payment-btn">
                        <i class="fas fa-check"></i> Confirmar Pago
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script src="<?= BASE_URL ?>/js/pos_main.js"></script>
    <script src="<?= BASE_URL ?>/js/pos_scanner.js"></script>
    <script src="<?= BASE_URL ?>/js/pos_cart.js"></script>
    <script src="<?= BASE_URL ?>/js/pos_payment.js"></script>
    <script src="<?= BASE_URL ?>/js/pos_api.js"></script>
    <script src="<?= BASE_URL ?>/js/pos_ui.js"></script>
    <script src="<?= BASE_URL ?>/js/pos_shortcuts.js"></script>
    <script src="<?= BASE_URL ?>/js/clock.js"></script>
    
    <script>
        // Inicializar sistema POS
        document.addEventListener('DOMContentLoaded', function() {
            // Inicializar carrito
            window.posCart.init();
            
            // Inicializar escáner
            window.posScanner.init();
            
            // Inicializar atajos de teclado
            window.posShortcuts.init();
            
            // Actualizar hora cada segundo
            setInterval(() => {
                const now = new Date();
                document.getElementById('current-time').textContent = 
                    now.toLocaleTimeString('es-SV');
            }, 1000);
            
            // Configurar eventos
            setupEventListeners();
        });
        
        function setupEventListeners() {
            // Botón de agregar manual
            document.getElementById('manual-add-btn').addEventListener('click', function() {
                const barcode = document.getElementById('barcode-input').value;
                if (barcode.trim()) {
                    window.posScanner.processBarcode(barcode.trim());
                }
            });
            
            // Botones de pago
            document.querySelectorAll('.payment-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const paymentType = this.getAttribute('data-type');
                    window.posPayment.showPaymentModal(paymentType);
                });
            });
            
            // Botón de cliente
            document.getElementById('customer-btn').addEventListener('click', function() {
                window.posUI.showCustomerModal();
            });
            
            // Botón de finalizar venta
            document.getElementById('complete-sale-btn').addEventListener('click', function() {
                window.posPayment.completeSale();
            });
        }
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
