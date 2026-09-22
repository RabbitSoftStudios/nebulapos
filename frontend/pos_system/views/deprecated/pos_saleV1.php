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
            --info-gradient: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
        }
        
        /* Mejoras adicionales */
        .pos-container {
            height: 100vh;
            padding: 15px;
            background: linear-gradient(135deg, #f5f7fa 0%, #e4edf5 100%);
        }
        
        .pos-left-panel {
            background: white;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
            margin-right: 15px;
            display: flex;
            flex-direction: column;
            height: calc(100vh - 30px);
        }
        
        .pos-right-panel {
            background: white;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.08);
            display: flex;
            flex-direction: column;
            height: calc(100vh - 30px);
        }
        
        .pos-sale-header {
            margin-bottom: 25px;
        }
        
        .pos-info-card {
            background: linear-gradient(135deg, #667eea15 0%, #764ba215 100%);
            border: 2px solid #667eea30;
            border-radius: 12px;
            padding: 15px;
            display: flex;
            align-items: center;
            gap: 15px;
            height: 100%;
            transition: all 0.3s ease;
        }
        
        .pos-info-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.1);
        }
        
        .pos-info-card i {
            font-size: 2rem;
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        
        .pos-info-card h4 {
            margin: 0;
            font-size: 1.2rem;
            font-weight: 600;
        }
        
        .pos-info-card small {
            color: #6c757d;
            font-size: 0.8rem;
            display: block;
            margin-bottom: 5px;
        }
        
        .pos-scanner-section {
            background: white;
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 25px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.05);
            border: 2px dashed #e9ecef;
        }
        
        .pos-scanner-input input {
            border: 2px solid #667eea;
            border-right: none;
            font-size: 1.1rem;
        }
        
        .pos-scanner-input .input-group-text {
            border: 2px solid #667eea;
            border-right: none;
            background: var(--primary-gradient);
        }
        
        #manual-add-btn {
            border: 2px solid #28a745;
            background: var(--success-gradient);
            font-weight: 600;
            padding: 0 25px;
        }
        
        .scanner-hint {
            text-align: center;
            margin-top: 10px;
            color: #6c757d;
            font-size: 0.9rem;
        }
        
        .pos-frequent-products {
            flex-grow: 1;
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }
        
        .pos-frequent-products h5 {
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            padding: 10px 0;
            border-bottom: 2px solid #f0f0f0;
            margin-bottom: 20px;
            font-weight: 700;
        }
        
        #frequent-products-grid {
            flex-grow: 1;
            overflow-y: auto;
            padding-right: 5px;
        }
        
        .product-card {
            border: 2px solid #e9ecef;
            border-radius: 12px;
            overflow: hidden;
            transition: all 0.3s ease;
            margin-bottom: 15px;
            height: 100%;
            display: flex;
            flex-direction: column;
        }
        
        .product-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            border-color: #667eea;
        }
        
        .product-image {
            width: 100%;
            height: 120px;
            object-fit: cover;
            border-bottom: 2px solid #f8f9fa;
        }
        
        .product-image-placeholder {
            width: 100%;
            height: 120px;
            background: linear-gradient(135deg, #f0f4ff 0%, #e8f0ff 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            border-bottom: 2px solid #f8f9fa;
        }
        
        .product-image-placeholder i {
            font-size: 2.5rem;
            color: #667eea;
            opacity: 0.6;
        }
        
        .product-info {
            padding: 15px;
            flex-grow: 1;
            display: flex;
            flex-direction: column;
        }
        
        .product-info h6 {
            font-weight: 600;
            margin-bottom: 10px;
            color: #333;
            flex-grow: 1;
        }
        
        .product-price {
            font-size: 1.2rem;
            font-weight: 700;
            color: #28a745;
            margin-bottom: 10px;
        }
        
        .add-to-cart-btn {
            width: 100%;
            background: var(--primary-gradient);
            border: none;
            color: white;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .add-to-cart-btn:hover {
            transform: scale(1.05);
            color: white;
        }
        
        /* Panel derecho mejorado */
        .pos-cart-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 15px;
            border-bottom: 3px solid var(--primary-gradient);
        }
        
        .pos-cart-header h3 {
            margin: 0;
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            font-weight: 700;
        }
        
        #clear-cart-btn {
            background: var(--danger-gradient);
            border: none;
            font-weight: 600;
        }
        
        .pos-cart-items {
            flex-grow: 1;
            overflow-y: auto;
            border: 2px solid #f0f0f0;
            border-radius: 12px;
            padding: 15px;
            margin-bottom: 20px;
            background: #fafbfd;
        }
        
        .cart-item {
            background: white;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 10px;
            border-left: 4px solid #667eea;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        .cart-item-info h6 {
            margin: 0;
            font-weight: 600;
            color: #333;
        }
        
        .cart-item-price {
            font-weight: 700;
            color: #28a745;
        }
        
        .cart-item-quantity {
            display: flex;
            align-items: center;
            gap: 10px;
            background: #f8f9fa;
            padding: 5px 10px;
            border-radius: 20px;
        }
        
        .empty-cart-message {
            text-align: center;
            padding: 40px 20px;
            color: #6c757d;
        }
        
        .empty-cart-message i {
            margin-bottom: 15px;
            opacity: 0.3;
        }
        
        .pos-totals {
            background: linear-gradient(135deg, #667eea05 0%, #764ba205 100%);
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
            border: 2px solid #f0f0f0;
        }
        
        .total-row {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 8px 0;
            border-bottom: 1px solid #eee;
        }
        
        .total-row:last-child {
            border-bottom: none;
        }
        
        .grand-total {
            font-size: 1.3rem;
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-top: 10px;
            padding-top: 15px;
            border-top: 2px solid #eee;
        }
        
        .pos-action-buttons {
            margin-top: auto;
        }
        
        .payment-btn {
            font-size: 1.1rem;
            font-weight: 600;
            padding: 15px;
            border: none;
            transition: all 0.3s ease;
        }
        
        .payment-btn[data-type="cash"] {
            background: var(--success-gradient);
        }
        
        .payment-btn[data-type="card"] {
            background: var(--info-gradient);
        }
        
        .payment-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        #complete-sale-btn {
            background: var(--primary-gradient);
            border: none;
            font-size: 1.2rem;
            font-weight: 700;
            padding: 18px;
            transition: all 0.3s ease;
        }
        
        #complete-sale-btn:not(:disabled):hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(102, 126, 234, 0.3);
        }
        
        #complete-sale-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
        
        .pos-customer-info {
            background: linear-gradient(135deg, #f8f9ff 0%, #f0f5ff 100%);
            border-radius: 12px;
            padding: 20px;
            margin-top: 20px;
            border: 2px solid #e0e7ff;
        }
        
        .customer-details p {
            margin: 5px 0;
            color: #495057;
        }
        
        /* Modales mejorados */
        .modal-content {
            border-radius: 15px;
            border: none;
            box-shadow: 0 10px 40px rgba(0,0,0,0.1);
        }
        
        .modal-header {
            background: var(--primary-gradient);
            color: white;
            border-radius: 15px 15px 0 0;
        }
        
        .modal-header .btn-close {
            filter: invert(1);
        }
        
        /* Scrollbar personalizado */
        ::-webkit-scrollbar {
            width: 8px;
        }
        
        ::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 10px;
        }
        
        ::-webkit-scrollbar-thumb {
            background: #667eea;
            border-radius: 10px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: #764ba2;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .pos-container {
                padding: 10px;
            }
            
            .pos-left-panel,
            .pos-right-panel {
                margin: 0 0 15px 0;
                height: auto;
            }
            
            .product-card {
                margin-bottom: 10px;
            }
        }
    </style>
</head>
<body class="pos-body">
    <?php include 'partials/header.php'; ?>
    
    <div class="container-fluid pos-container">
        <div class="row g-0 h-100">
            <!-- Panel Izquierdo - Productos -->
            <div class="col-md-7 pos-left-panel">
                <!-- Cabecera de Información -->
                <div class="pos-sale-header">
                    <div class="row g-3">
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
                    </div>
                </div>
                
                <!-- Escáner -->
                <div class="pos-scanner-section">
                    <div class="input-group pos-scanner-input shadow-sm">
                        <span class="input-group-text">
                            <i class="fas fa-barcode"></i>
                        </span>
                        <input type="text" 
                               class="form-control form-control-lg" 
                               id="barcode-input" 
                               placeholder="📦 Escanear código de barras o ingresar código..."
                               autofocus
                               autocomplete="off">
                        <button class="btn btn-success" id="manual-add-btn">
                            <i class="fas fa-plus me-2"></i>Agregar
                        </button>
                    </div>
                    
                    <div class="scanner-hint mt-2">
                        <small><i class="fas fa-info-circle me-1"></i> Presiona ENTER para agregar producto rápidamente</small>
                    </div>
                </div>
                
                <!-- Productos Frecuentes -->
                <div class="pos-frequent-products">
                    <h5><i class="fas fa-fire me-2"></i> 🔥 Productos Frecuentes</h5>
                    <div class="row g-3" id="frequent-products-grid">
                        <?php if(isset($frequentProducts) && is_array($frequentProducts) && 
                                isset($frequentProducts['success']) && $frequentProducts['success'] === true && 
                                isset($frequentProducts['data']) && is_array($frequentProducts['data'])): ?>
                            
                            <?php if(!empty($frequentProducts['data'])): ?>
                                <?php foreach ($frequentProducts['data'] as $product): ?>
                                    <div class="col-xl-3 col-lg-4 col-md-6">
                                        <div class="product-card" data-product-id="<?= htmlspecialchars((string)($product['id'] ?? '')) ?>">
                                            <?php if (!empty($product['imagen_url'])): ?>
                                                <img src="<?= htmlspecialchars((string)$product['imagen_url']) ?>"
                                                    alt="<?= htmlspecialchars((string)$product['nombre_producto']) ?>"
                                                    class="product-image">
                                            <?php else: ?>
                                                <div class="product-image-placeholder">
                                                    <i class="fas fa-box-open"></i>
                                                </div>
                                            <?php endif; ?>
                                            <div class="product-info">
                                                <h6><?= htmlspecialchars((string)$product['nombre_producto']) ?></h6>
                                                <p class="product-price">$<?=
                                                    isset($product['precio_venta']) ?
                                                    number_format((float)$product['precio_venta'], 2) :
                                                    '0.00'
                                                ?></p>
                                                <button class="btn btn-sm add-to-cart-btn">
                                                    <i class="fas fa-cart-plus me-1"></i> Agregar
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="col-12">
                                    <div class="alert alert-info text-center py-4">
                                        <i class="fas fa-info-circle fa-2x mb-3"></i>
                                        <h5>No hay productos frecuentes</h5>
                                        <p class="mb-0">Agrega productos para que aparezcan aquí</p>
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php else: ?>
                            <div class="col-12">
                                <div class="alert alert-warning text-center py-4">
                                    <i class="fas fa-exclamation-triangle fa-2x mb-3"></i>
                                    <h5>Error al cargar productos</h5>
                                    <p class="mb-0">Intenta recargar la página</p>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <?php include 'partials/keyboard.php'; ?>
            </div>
            
            <!-- Panel Derecho - Carrito -->
            <div class="col-md-5 pos-right-panel">
                <!-- Encabezado del Carrito -->
                <div class="pos-cart-header">
                    <h3><i class="fas fa-shopping-cart me-2"></i> 🛒 Carrito de Venta</h3>
                    <button class="btn btn-danger" id="clear-cart-btn">
                        <i class="fas fa-trash me-1"></i> Vaciar
                    </button>
                </div>
                
                <!-- Items del Carrito -->
                <div class="pos-cart-items" id="cart-items-container">
                    <div class="empty-cart-message">
                        <i class="fas fa-shopping-basket fa-4x"></i>
                        <h5 class="mt-3">Carrito Vacío</h5>
                        <p class="text-muted">Escanea productos o selecciona de la lista para comenzar</p>
                    </div>
                </div>
                
                <!-- Totales -->
                <div class="pos-totals">
                    <div class="total-row">
                        <span>Subtotal:</span>
                        <span id="subtotal-amount" class="fw-bold">$0.00</span>
                    </div>
                    <div class="total-row">
                        <span>IVA (<?= (IVA_RATE * 100) ?>%):</span>
                        <span id="tax-amount" class="fw-bold">$0.00</span>
                    </div>
                    <div class="total-row">
                        <span>Descuento:</span>
                        <span id="discount-amount" class="fw-bold text-danger">$0.00</span>
                    </div>
                    <div class="total-row grand-total">
                        <span><strong>TOTAL A PAGAR:</strong></span>
                        <span id="total-amount" class="display-6 fw-bold">$0.00</span>
                    </div>
                </div>
                
                <!-- Botones de Acción -->
                <div class="pos-action-buttons">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <button class="btn btn-lg btn-success w-100 payment-btn shadow-sm" data-type="cash">
                                <i class="fas fa-money-bill-wave me-2"></i> 💵 Efectivo
                            </button>
                        </div>
                        <div class="col-md-6">
                            <button class="btn btn-lg btn-primary w-100 payment-btn shadow-sm" data-type="card">
                                <i class="fas fa-credit-card me-2"></i> 💳 Tarjeta
                            </button>
                        </div>
                        
                        <div class="col-md-4">
                            <button class="btn btn-lg btn-info w-100 shadow-sm" id="customer-btn">
                                <i class="fas fa-user me-1"></i> Cliente
                            </button>
                        </div>
                        <div class="col-md-4">
                            <button class="btn btn-lg btn-warning w-100 shadow-sm" id="discount-btn">
                                <i class="fas fa-percent me-1"></i> Descuento
                            </button>
                        </div>
                        <div class="col-md-4">
                            <button class="btn btn-lg btn-danger w-100 shadow-sm" id="cancel-sale-btn">
                                <i class="fas fa-times me-1"></i> Cancelar
                            </button>
                        </div>
                        
                        <div class="col-12 mt-3">
                            <button class="btn btn-lg btn-dark w-100 py-3 shadow-lg" id="complete-sale-btn" disabled>
                                <i class="fas fa-check-circle me-2"></i> ✅ FINALIZAR VENTA
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- Información del Cliente -->
                <div class="pos-customer-info" id="customer-info" style="display: none;">
                    <h6><i class="fas fa-user-tag me-2"></i> 👤 Información del Cliente</h6>
                    <div class="customer-details mt-2">
                        <p><strong>Nombre:</strong> <span id="customer-name" class="text-primary">Consumidor Final</span></p>
                        <p><strong>Documento:</strong> <span id="customer-document" class="text-muted">N/A</span></p>
                        <p><strong>NRC:</strong> <span id="customer-nrc" class="text-muted">N/A</span></p>
                    </div>
                    <button class="btn btn-outline-primary w-100 mt-2" id="change-customer-btn">
                        <i class="fas fa-exchange-alt me-1"></i> Cambiar Cliente
                    </button>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Modales (mantienen estructura original con mejoras visuales) -->
    <div class="modal fade" id="customerModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="fas fa-users me-2"></i> Seleccionar Cliente</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="input-group mb-4">
                        <input type="text" class="form-control form-control-lg" 
                               id="search-customer" 
                               placeholder="🔍 Buscar por NIT, DUI o nombre...">
                        <button class="btn btn-primary" id="search-customer-btn">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                    
                    <div class="customer-list" id="customer-list"></div>
                    
                    <div class="mt-4">
                        <button class="btn btn-outline-primary w-100 py-3" id="new-customer-btn">
                            <i class="fas fa-user-plus me-2"></i> Nuevo Cliente
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
                    <h5 class="modal-title"><i class="fas fa-money-check-alt me-2"></i> 💰 Procesar Pago</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="payment-summary text-center mb-4">
                        <h3 class="text-muted">Total a Pagar</h3>
                        <h1 id="payment-total" class="display-4 fw-bold text-primary">$0.00</h1>
                        <div class="payment-method mt-4" id="payment-method"></div>
                    </div>
                    
                    <div class="cash-payment" id="cash-payment-section" style="display: none;">
                        <div class="mb-3">
                            <label for="cash-received" class="form-label fw-bold">
                                <i class="fas fa-money-bill-wave me-1"></i> Efectivo Recibido:
                            </label>
                            <input type="number" class="form-control form-control-lg text-center" 
                                   id="cash-received" step="0.01" min="0" placeholder="0.00">
                        </div>
                        <div class="change-display alert alert-success text-center">
                            <h4 class="mb-0">
                                <i class="fas fa-exchange-alt me-2"></i>
                                Cambio: <span id="change-amount" class="fw-bold">$0.00</span>
                            </h4>
                        </div>
                    </div>
                    
                    <div class="card-payment" id="card-payment-section" style="display: none;">
                        <div class="mb-3">
                            <label for="card-number" class="form-label">Número de Tarjeta:</label>
                            <input type="text" class="form-control" id="card-number" 
                                   placeholder="**** **** **** ****">
                        </div>
                        <div class="row g-3">
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
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i> Cancelar
                    </button>
                    <button type="button" class="btn btn-success px-5" id="confirm-payment-btn">
                        <i class="fas fa-check me-1"></i> Confirmar Pago
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
