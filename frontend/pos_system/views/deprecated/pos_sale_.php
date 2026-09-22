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
require_once __DIR__ . '/partials/mh_header_data.php'; // Carga de UUID y Control

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
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f7fa;
            overflow: hidden;
            height: 100vh;
        }
        
        /* HEADER SUPERIOR */
        .dashboard-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 10px 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            position: fixed;
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
        
        .dashboard-nav li:hover {
            background-color: rgba(255,255,255,0.1);
        }
        
        .dashboard-nav li.active {
            background-color: rgba(255,255,255,0.2);
            font-weight: 600;
        }
        
        .user-menu {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        /* CONTENEDOR PRINCIPAL */
        .pos-container {
            margin-top: 60px;
            height: calc(100vh - 60px);
            padding: 20px;
            display: flex;
            gap: 20px;
            overflow: hidden;
        }
        
        /* PANEL IZQUIERDO */
        .left-panel {
            flex: 3;
            display: flex;
            flex-direction: column;
            gap: 20px;
            min-width: 0;
        }
        
        /* INFO CARDS SUPERIOR */
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
            width: 50px;
            height: 50px;
            border-radius: 10px;
            background: linear-gradient(135deg, #667eea20 0%, #764ba220 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--primary-color);
            font-size: 1.5rem;
        }
        
        .info-card-content small {
            color: #666;
            font-size: 0.85rem;
            display: block;
            margin-bottom: 5px;
        }
        
        .info-card-content h4 {
            margin: 0;
            font-size: 1.2rem;
            font-weight: 600;
            color: #333;
        }
        
        /* ESCÁNER */
        .scanner-section {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
            border: 1px solid #e0e0e0;
        }
        
        .scanner-label {
            font-weight: 500;
            color: #333;
            margin-bottom: 10px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .scanner-input {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #667eea;
            border-radius: 8px;
            font-size: 1rem;
            outline: none;
            transition: all 0.3s;
        }
        
        .scanner-input:focus {
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.2);
        }
        
        .scanner-hint {
            margin-top: 10px;
            color: #666;
            font-size: 0.85rem;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        /* PRODUCTOS FRECUENTES */
        .products-section {
            flex: 1;
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
            border: 1px solid #e0e0e0;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }
        
        .section-title {
            font-size: 1.2rem;
            font-weight: 600;
            color: #333;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
            padding-bottom: 10px;
            border-bottom: 2px solid #f0f0f0;
        }
        
        .products-grid {
            flex: 1;
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            overflow-y: auto;
            padding-right: 5px;
            min-height: 300px;
        }
        
        .product-card {
            background: white;
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 15px;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            transition: all 0.3s;
            cursor: pointer;
        }
        
        .product-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            border-color: var(--primary-color);
        }
        
        .product-image {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 8px;
            margin-bottom: 10px;
            border: 1px solid #f0f0f0;
        }
        
        .product-image-placeholder {
            width: 80px;
            height: 80px;
            background: linear-gradient(135deg, #f5f7fa 0%, #e4edf5 100%);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 10px;
            color: var(--primary-color);
            font-size: 2rem;
        }
        
        .product-name {
            font-weight: 600;
            color: #333;
            font-size: 0.9rem;
            margin-bottom: 8px;
            line-height: 1.2;
            height: 32px;
            overflow: hidden;
        }
        
        .product-price {
            font-weight: 700;
            color: var(--success-color);
            font-size: 1.1rem;
            margin-bottom: 10px;
        }
        
        .add-to-cart-btn {
            width: 100%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border: none;
            border-radius: 5px;
            padding: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .add-to-cart-btn:hover {
            opacity: 0.9;
        }
        
        /* TECLADO NUMÉRICO */
        .keyboard-section {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
            border: 1px solid #e0e0e0;
        }
        
        .keyboard-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
        }
        
        .keyboard-btn {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 8px;
            padding: 15px;
            font-size: 1.2rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
        }
        
        .keyboard-btn:hover {
            background: #e9ecef;
        }
        
        .keyboard-row {
            display: flex;
            gap: 10px;
            margin-top: 10px;
        }
        
        .keyboard-special {
            flex: 1;
            background: var(--primary-color);
            color: white;
            border: none;
            border-radius: 8px;
            padding: 15px;
            font-weight: 600;
            cursor: pointer;
        }
        
        .add-btn {
            background: var(--success-color);
        }
        
        /* PANEL DERECHO */
        .right-panel {
            flex: 2;
            display: flex;
            flex-direction: column;
            gap: 20px;
            min-width: 400px;
        }
        
        /* CARRITO */
        .cart-section {
            flex: 1;
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
            border: 1px solid #e0e0e0;
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }
        
        .cart-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #f0f0f0;
        }
        
        .cart-header h3 {
            margin: 0;
            font-size: 1.3rem;
            font-weight: 600;
            color: #333;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .clear-cart-btn {
            background: var(--danger-color);
            color: white;
            border: none;
            border-radius: 5px;
            padding: 8px 15px;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        .cart-items {
            flex: 1;
            overflow-y: auto;
            padding-right: 5px;
        }
        
        .empty-cart {
            text-align: center;
            padding: 40px 20px;
            color: #999;
        }
        
        .empty-cart i {
            font-size: 4rem;
            margin-bottom: 15px;
            opacity: 0.3;
        }
        
        .empty-cart p {
            margin: 5px 0;
            font-size: 1.1rem;
        }
        
        .empty-cart small {
            font-size: 0.9rem;
        }
        
        /* TOTALES */
        .totals-section {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
            border: 1px solid #e0e0e0;
        }
        
        .total-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px dashed #e0e0e0;
            color: #666;
        }
        
        .total-row:last-child {
            border-bottom: none;
        }
        
        .grand-total {
            font-size: 1.3rem;
            font-weight: 700;
            color: #333;
            margin-top: 10px;
            padding-top: 10px;
            border-top: 2px solid var(--primary-color);
        }
        
        .total-amount {
            font-weight: 600;
            color: #333;
        }
        
        /* BOTONES DE ACCIÓN */
        .action-buttons {
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        
        .payment-buttons {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
        }
        
        .payment-btn {
            padding: 15px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            font-size: 1rem;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        
        .cash-btn {
            background: linear-gradient(135deg, #42e695 0%, #3bb2b8 100%);
            color: white;
        }
        
        .card-btn {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            color: white;
        }
        
        .other-buttons {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
        }
        
        .other-btn {
            padding: 12px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        
        .customer-btn {
            background: #17a2b8;
            color: white;
        }
        
        .discount-btn {
            background: #ffc107;
            color: #333;
        }
        
        .cancel-btn {
            background: #dc3545;
            color: white;
        }
        
        .complete-btn {
            background: #333;
            color: white;
            padding: 18px;
            font-size: 1.1rem;
            font-weight: 700;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            margin-top: 10px;
        }
        
        .complete-btn:hover {
            background: #000;
        }
        
        /* --- INICIO DE CAMBIOS: ESTILOS MODAL CLIENTE --- */
        .modal-content { border-radius: 15px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.2); }
        .modal-header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 15px 15px 0 0; }
        #customer-data-display { font-size: 0.85rem; padding: 10px; background: #e9ecef; border-radius: 5px; margin-top: 5px; }
        /* --- FIN DE CAMBIOS --- */

        /* SCROLLBAR PERSONALIZADO */
        ::-webkit-scrollbar {
            width: 6px;
        }
        
        ::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 3px;
        }
        
        ::-webkit-scrollbar-thumb {
            background: var(--primary-color);
            border-radius: 3px;
        }
        
        ::-webkit-scrollbar-thumb:hover {
            background: var(--secondary-color);
        }
        
        /* RESPONSIVE PARA PANTALLA 15" */
        @media (max-width: 1366px) {
            .products-grid {
                grid-template-columns: repeat(3, 1fr);
            }
            
            .product-card {
                padding: 12px;
            }
            
            .product-image,
            .product-image-placeholder {
                width: 70px;
                height: 70px;
            }
        }
        /* esta parte deja la pantalla alta*/
        @media (max-width: 1200px) {
            .pos-container {
                flex-direction: column;
                overflow-y: auto;
            }
            
            .left-panel,
            .right-panel {
                min-width: 100%;
            }
            
            .products-grid {
                grid-template-columns: repeat(4, 1fr);
            }
        }
        
        @media (max-width: 768px) {
            .info-cards {
                grid-template-columns: 1fr;
            }
            
            .products-grid {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .keyboard-grid {
                grid-template-columns: repeat(3, 1fr);
            }
            
            .dashboard-nav {
                display: none;
            }
        }
    </style>
</head>
<body>
    <!-- HEADER DASHBOARD -->
    <header class="dashboard-header">
        <div class="container-fluid h-100">
            <div class="row align-items-center h-100">
                <div class="col-md-3">
                    <div class="brand">
                        <i class="fas fa-cash-register"></i>
                        <span>Nebula POS System</span>
                    </div>
                </div>
                <div class="col-md-6">
                    <ul class="dashboard-nav">
                        <li class="active"><i class="fas fa-cash-register"></i> POS</li>
                        <li><i class="fas fa-box"></i> Productos</li>
                        <li><i class="fas fa-chart-bar"></i> Reportes</li>
                        <li><i class="fas fa-users"></i> Clientes</li>
                        <li><i class="fas fa-cog"></i> Configuración</li>
                    </ul>
                </div>
                <div class="col-md-3 text-end">
                    <div class="user-menu">
                        <span>admin</span>
                        <button class="btn btn-light btn-sm">
                            <i class="fas fa-sign-out-alt"></i> Cerrar sesión
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </header>
    
    <!-- CONTENEDOR PRINCIPAL -->
    <div class="pos-container">
        <!-- PANEL IZQUIERDO -->
        <div class="left-panel">
            <!-- INFORMACIÓN DE CAJA -->
            <div class="info-cards">
                <div class="info-card">
                    <div class="info-card-icon">
                        <i class="fas fa-cash-register"></i>
                    </div>
                    <div class="info-card-content">
                        <small>Caja #001</small>
                        <h4>Venta Activa</h4>
                    </div>
                </div>
                
                <div class="info-card">
                    <div class="info-card-icon">
                        <i class="fas fa-user-tie"></i>
                    </div>
                    <div class="info-card-content">
                        <small>Cajero</small>
                        <h4>admin</h4>
                    </div>
                </div>
                
                <div class="info-card">
                    <div class="info-card-icon">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div class="info-card-content">
                        <small>Hora</small>
                        <h4 id="current-time">00:54:54</h4>
                    </div>
                </div>
            </div>
            
            <!-- ESCÁNER -->
            <div class="scanner-section">
                <div class="scanner-label">
                    <i class="fas fa-barcode"></i>
                    <span>Escanear código de barras o ingresar código:</span>
                </div>
                <input type="text" 
                       class="scanner-input" 
                       id="barcode-input"
                       placeholder="Ingrese código del producto..."
                       autofocus>
                <div class="scanner-hint">
                    <i class="fas fa-info-circle"></i>
                    <span>Presiona ENTER para agregar producto rápidamente</span>
                </div>
            </div>
            
            <!-- PRODUCTOS FRECUENTES -->
            <div class="products-section">
                <div class="section-title">
                    <i class="fas fa-fire"></i>
                    <span>Productos Frecuentes</span>
                </div>
                <div class="products-grid" id="frequent-products-grid">
                    <!-- Productos se cargarán aquí -->
                    <div class="product-card">
                        <div class="product-image-placeholder">
                            <i class="fas fa-box"></i>
                        </div>
                        <div class="product-name">Producto Ejemplo 1</div>
                        <div class="product-price">$10.99</div>
                        <button class="add-to-cart-btn">Agregar</button>
                    </div>
                    
                    <div class="product-card">
                        <div class="product-image-placeholder">
                            <i class="fas fa-box"></i>
                        </div>
                        <div class="product-name">Producto Ejemplo 2</div>
                        <div class="product-price">$15.50</div>
                        <button class="add-to-cart-btn">Agregar</button>
                    </div>
                    
                    <div class="product-card">
                        <div class="product-image-placeholder">
                            <i class="fas fa-box"></i>
                        </div>
                        <div class="product-name">Producto Ejemplo 3</div>
                        <div class="product-price">$8.75</div>
                        <button class="add-to-cart-btn">Agregar</button>
                    </div>
                    
                    <div class="product-card">
                        <div class="product-image-placeholder">
                            <i class="fas fa-box"></i>
                        </div>
                        <div class="product-name">Producto Ejemplo 4</div>
                        <div class="product-price">$12.99</div>
                        <button class="add-to-cart-btn">Agregar</button>
                    </div>
                    
                    <div class="product-card">
                        <div class="product-image-placeholder">
                            <i class="fas fa-box"></i>
                        </div>
                        <div class="product-name">Producto Ejemplo 5</div>
                        <div class="product-price">$9.99</div>
                        <button class="add-to-cart-btn">Agregar</button>
                    </div>
                    
                    <div class="product-card">
                        <div class="product-image-placeholder">
                            <i class="fas fa-box"></i>
                        </div>
                        <div class="product-name">Producto Ejemplo 6</div>
                        <div class="product-price">$22.50</div>
                        <button class="add-to-cart-btn">Agregar</button>
                    </div>
                </div>
            </div>
            
            <!-- TECLADO NUMÉRICO -->
            <div class="keyboard-section">
                <div class="keyboard-grid">
                    <button class="keyboard-btn">7</button>
                    <button class="keyboard-btn">8</button>
                    <button class="keyboard-btn">9</button>
                    <button class="keyboard-btn">4</button>
                    <button class="keyboard-btn">5</button>
                    <button class="keyboard-btn">6</button>
                    <button class="keyboard-btn">1</button>
                    <button class="keyboard-btn">2</button>
                    <button class="keyboard-btn">3</button>
                </div>
                <div class="keyboard-row">
                    <button class="keyboard-btn" style="flex: 2;">0</button>
                    <button class="keyboard-special">m</button>
                    <button class="keyboard-special">+</button>
                </div>
                <div class="keyboard-row">
                    <button class="keyboard-special add-btn" style="flex: 1;">AGREGAR (ENTER)</button>
                </div>
            </div>
        </div>
        
        <!-- PANEL DERECHO -->
        <div class="right-panel">
            <!-- CARRITO DE VENTA -->
            <div class="cart-section">
                <div class="cart-header">
                    <h3>
                        <i class="fas fa-shopping-cart"></i>
                        Carrito de Venta
                    </h3>
                    <button class="clear-cart-btn">
                        <i class="fas fa-trash"></i>
                        Vaciar
                    </button>
                </div>
                <div class="cart-items">
                    <div class="empty-cart">
                        <i class="fas fa-shopping-cart"></i>
                        <p>El carrito está vacío</p>
                        <small>Escanee productos para comenzar</small>
                    </div>
                </div>
            </div>
            
            <!-- TOTALES -->
            <div class="totals-section">
                <div class="total-row">
                    <span>Subtotal:</span>
                    <span class="total-amount">$0.00</span>
                </div>
                <div class="total-row">
                    <span>IVA (13%):</span>
                    <span class="total-amount">$0.00</span>
                </div>
                <div class="total-row">
                    <span>Descuento:</span>
                    <span class="total-amount">$0.00</span>
                </div>
                <div class="total-row grand-total">
                    <span>TOTAL A PAGAR:</span>
                    <span class="total-amount">$0.00</span>
                </div>
            </div>
            
            <!-- BOTONES DE ACCIÓN -->
            <div class="action-buttons">
                <div class="payment-buttons">
                    <button class="payment-btn cash-btn">
                        <i class="fas fa-money-bill-wave"></i>
                        Efectivo
                    </button>
                    <button class="payment-btn card-btn">
                        <i class="fas fa-credit-card"></i>
                        Tarjeta
                    </button>
                </div>
                
                <div class="other-buttons">
                    <button class="other-btn customer-btn">
                        <i class="fas fa-user"></i>
                        Cliente
                    </button>
                    <button class="other-btn discount-btn">
                        <i class="fas fa-percent"></i>
                        Descuento
                    </button>
                    <button class="other-btn cancel-btn">
                        <i class="fas fa-times"></i>
                        Cancelar
                    </button>
                </div>
                
                <button class="complete-btn" disabled>
                    <i class="fas fa-check-circle"></i>
                    FINALIZAR VENTA
                </button>
            </div>
        </div>
    </div>
    
    <!-- SCRIPTS -->
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
        // Actualizar hora en tiempo real
        function updateTime() {
            const now = new Date();
            const timeString = now.toLocaleTimeString('es-ES', {
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit',
                hour12: false
            });
            document.getElementById('current-time').textContent = timeString;
        }
        
        // Inicializar
        document.addEventListener('DOMContentLoaded', function() {
            // Actualizar hora cada segundo
            setInterval(updateTime, 1000);
            updateTime();
            
            // Auto-focus en el input del escáner
            document.getElementById('barcode-input').focus();
            
            // Agregar funcionalidad básica a los botones del teclado
            document.querySelectorAll('.keyboard-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const barcodeInput = document.getElementById('barcode-input');
                    barcodeInput.value += this.textContent;
                    barcodeInput.focus();
                });
            });
            
            // Botón agregar del teclado
            document.querySelector('.add-btn').addEventListener('click', function() {
                const barcodeInput = document.getElementById('barcode-input');
                if (barcodeInput.value.trim() !== '') {
                    // Aquí iría la lógica para agregar producto
                    console.log('Agregar producto:', barcodeInput.value);
                    barcodeInput.value = '';
                    barcodeInput.focus();
                }
            });
            
            // Permitir agregar con ENTER
            document.getElementById('barcode-input').addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    document.querySelector('.add-btn').click();
                }
            });

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
