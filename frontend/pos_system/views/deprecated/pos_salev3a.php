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
    <title>Nebula POS System</title>
    
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
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f8f9fa;
            height: 100vh;
            width: 100vw;
            overflow: hidden;
            position: fixed;
        }
        
        /* SCROLL GLOBAL INVISIBLE */
        .global-scroll-container {
            height: 100vh;
            width: 100vw;
            overflow-y: auto;
            overflow-x: hidden;
            scrollbar-width: none; /* Firefox */
            -ms-overflow-style: none; /* IE/Edge */
        }
        
        .global-scroll-container::-webkit-scrollbar {
            display: none; /* Chrome/Safari/Opera */
        }
        
        /* HEADER SUPERIOR FIJO */
        .dashboard-header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 0;
            position: sticky;
            top: 0;
            left: 0;
            right: 0;
            z-index: 1000;
            height: 60px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .dashboard-header .container-fluid {
            height: 100%;
        }
        
        .dashboard-header .row {
            height: 100%;
        }
        
        .brand {
            font-size: 1.5rem;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 10px;
            height: 100%;
        }
        
        .brand i {
            font-size: 1.8rem;
        }
        
        .nav-menu {
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100%;
            gap: 20px;
            list-style: none;
            margin: 0;
            padding: 0;
        }
        
        .nav-item {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 15px;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.3s;
            color: rgba(255,255,255,0.9);
            font-weight: 500;
        }
        
        .nav-item:hover {
            background-color: rgba(255,255,255,0.15);
        }
        
        .nav-item.active {
            background-color: rgba(255,255,255,0.2);
            font-weight: 600;
            color: white;
        }
        
        .user-info {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 15px;
            height: 100%;
        }
        
        .user-name {
            font-weight: 500;
        }
        
        .logout-btn {
            background: rgba(255,255,255,0.2);
            border: 1px solid rgba(255,255,255,0.3);
            color: white;
            padding: 5px 15px;
            border-radius: 4px;
            font-weight: 500;
            transition: all 0.3s;
        }
        
        .logout-btn:hover {
            background: rgba(255,255,255,0.3);
        }
        
        /* CONTENIDO PRINCIPAL - CON ALTURA FLEXIBLE */
        .main-content {
            min-height: calc(100vh - 60px);
            padding: 20px;
            background: #f8f9fa;
        }
        
        /* LAYOUT DE DOS COLUMNAS PRINCIPALES */
        .pos-layout {
            display: flex;
            gap: 20px;
            min-height: calc(100vh - 100px);
        }
        
        /* COLUMNA IZQUIERDA - PRODUCTOS + TECLADO */
        .left-column {
            flex: 3;
            display: flex;
            gap: 20px;
            min-width: 0;
        }
        
        /* SUB-COLUMNAS DENTRO DE LA IZQUIERDA */
        .products-column {
            flex: 2;
            display: flex;
            flex-direction: column;
            gap: 20px;
        }
        
        .keyboard-column {
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 20px;
            min-width: 300px;
        }
        
        /* INFORMACIÓN DE CAJA */
        .info-cards {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 15px;
            margin-bottom: 10px;
        }
        
        .info-card {
            background: white;
            border-radius: 10px;
            padding: 15px;
            display: flex;
            align-items: center;
            gap: 15px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.05);
            border: 1px solid #e0e0e0;
        }
        
        .info-icon {
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
        
        .info-content h5 {
            font-size: 1.2rem;
            font-weight: 700;
            color: #333;
            margin: 0;
        }
        
        .info-content small {
            color: #666;
            font-size: 0.85rem;
            display: block;
            margin-bottom: 5px;
        }
        
        /* ESCÁNER */
        .scanner-container {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.05);
            border: 1px solid #e0e0e0;
            margin-bottom: 10px;
        }
        
        .scanner-title {
            font-weight: 600;
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
            margin-bottom: 8px;
            outline: none;
            transition: all 0.3s;
        }
        
        .scanner-input:focus {
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.2);
        }
        
        .scanner-hint {
            color: #666;
            font-size: 0.85rem;
            display: flex;
            align-items: center;
            gap: 5px;
        }
        
        /* PRODUCTOS FRECUENTES - CON SCROLL AUTOMÁTICO */
        .frequent-products-container {
            flex: 1;
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.05);
            border: 1px solid #e0e0e0;
            display: flex;
            flex-direction: column;
            overflow: hidden;
            min-height: 400px;
        }
        
        .section-title {
            font-size: 1.2rem;
            font-weight: 700;
            color: #333;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #f0f0f0;
            display: flex;
            align-items: center;
            gap: 10px;
            flex-shrink: 0;
        }
        
        .products-grid {
            flex: 1;
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
            gap: 15px;
            overflow-y: auto;
            padding-right: 5px;
            padding-bottom: 5px;
        }
        
        .product-card {
            background: white;
            border: 2px solid #e9ecef;
            border-radius: 10px;
            padding: 15px;
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
            transition: all 0.3s;
            cursor: pointer;
            height: 180px;
        }
        
        .product-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            border-color: #667eea;
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
            line-height: 1.3;
            height: 38px;
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
            border-radius: 6px;
            padding: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            font-size: 0.9rem;
        }
        
        .add-to-cart-btn:hover {
            opacity: 0.9;
            transform: scale(1.02);
        }
        
        /* TECLADO NUMÉRICO - ESTILO ORIGINAL */
        .keyboard-container {
            flex: 1;
            background: #dc3545;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
            display: flex;
            flex-direction: column;
            gap: 10px;
            min-height: 400px;
        }
        
        .keyboard-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
            flex: 1;
        }
        
        .keyboard-btn {
            background: rgba(255, 255, 255, 0.9);
            border: none;
            border-radius: 8px;
            font-size: 1.5rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s;
            color: #333;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .keyboard-btn:hover {
            background: white;
            transform: scale(1.05);
        }
        
        .keyboard-row {
            display: flex;
            gap: 10px;
            margin-top: 10px;
        }
        
        .keyboard-special {
            flex: 1;
            background: #333;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 1.2rem;
            font-weight: 600;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .keyboard-add {
            background: linear-gradient(135deg, #42e695 0%, #3bb2b8 100%);
            color: white;
            border: none;
            border-radius: 8px;
            padding: 15px;
            font-size: 1.1rem;
            font-weight: 700;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }
        
        /* COLUMNA DERECHA - CARRITO Y BOTONES */
        .right-column {
            flex: 2;
            display: flex;
            flex-direction: column;
            gap: 20px;
            min-width: 450px;
        }
        
        /* CARRITO DE VENTA */
        .cart-container {
            flex: 1;
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.05);
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
            padding-bottom: 15px;
            border-bottom: 2px solid #f0f0f0;
            flex-shrink: 0;
        }
        
        .cart-title {
            font-size: 1.4rem;
            font-weight: 700;
            color: #333;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        
        .clear-cart-btn {
            background: var(--danger-color);
            color: white;
            border: none;
            border-radius: 6px;
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
            padding: 10px;
            background: #fafbfd;
            border-radius: 8px;
            border: 1px solid #e0e0e0;
            margin-bottom: 15px;
        }
        
        .empty-cart {
            text-align: center;
            padding: 60px 20px;
            color: #666;
        }
        
        .empty-cart i {
            font-size: 4rem;
            margin-bottom: 20px;
            opacity: 0.3;
        }
        
        .empty-cart h5 {
            font-size: 1.3rem;
            margin-bottom: 10px;
            color: #333;
        }
        
        .empty-cart p {
            color: #666;
            margin: 0;
        }
        
        .cart-item {
            background: white;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 10px;
            border-left: 4px solid #667eea;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        /* TOTALES */
        .totals-container {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.05);
            border: 1px solid #e0e0e0;
        }
        
        .total-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px dashed #e0e0e0;
            color: #666;
        }
        
        .grand-total {
            font-size: 1.4rem;
            font-weight: 700;
            color: #333;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 2px solid var(--primary-color);
        }
        
        /* BOTONES DE ACCIÓN */
        .action-buttons {
            background: white;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 3px 10px rgba(0,0,0,0.05);
            border: 1px solid #e0e0e0;
        }
        
        .payment-buttons {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 10px;
            margin-bottom: 15px;
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
        
        .action-row {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 10px;
            margin-bottom: 15px;
        }
        
        .action-btn {
            padding: 15px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
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
        
        .complete-sale-btn {
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
            width: 100%;
        }
        
        .complete-sale-btn:hover:not(:disabled) {
            background: #000;
            transform: scale(1.02);
        }
        
        .complete-sale-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }
        
        /* SCROLLBAR INVISIBLE PARA PRODUCTOS */
        .products-grid::-webkit-scrollbar {
            width: 6px;
        }
        
        .products-grid::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 4px;
        }
        
        .products-grid::-webkit-scrollbar-thumb {
            background: #ccc;
            border-radius: 4px;
            opacity: 0.5;
        }
        
        .products-grid::-webkit-scrollbar-thumb:hover {
            background: #aaa;
        }
        
        .cart-items::-webkit-scrollbar {
            width: 6px;
        }
        
        .cart-items::-webkit-scrollbar-track {
            background: #f1f1f1;
            border-radius: 4px;
        }
        
        .cart-items::-webkit-scrollbar-thumb {
            background: #ccc;
            border-radius: 4px;
            opacity: 0.5;
        }
        
        /* RESPONSIVE */
        @media (max-width: 1200px) {
            .pos-layout {
                flex-direction: column;
            }
            
            .left-column {
                flex-direction: column;
            }
            
            .products-column,
            .keyboard-column {
                min-width: 100%;
            }
            
            .keyboard-container {
                min-height: 300px;
            }
            
            .right-column {
                min-width: 100%;
            }
            
            .products-grid {
                grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
            }
        }
        
        @media (max-width: 768px) {
            .main-content {
                padding: 15px;
            }
            
            .info-cards {
                grid-template-columns: 1fr;
            }
            
            .nav-menu {
                display: none;
            }
            
            .products-grid {
                grid-template-columns: repeat(auto-fill, minmax(140px, 1fr));
            }
        }
    </style>
</head>
<body>
    <!-- CONTENEDOR CON SCROLL GLOBAL INVISIBLE -->
    <div class="global-scroll-container">
        <!-- HEADER SUPERIOR -->
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
                        <ul class="nav-menu">
                            <li class="nav-item active">
                                <i class="fas fa-cash-register"></i>
                                <span>POS</span>
                            </li>
                            <li class="nav-item">
                                <i class="fas fa-box"></i>
                                <span>Productos</span>
                            </li>
                            <li class="nav-item">
                                <i class="fas fa-chart-bar"></i>
                                <span>Reportes</span>
                            </li>
                            <li class="nav-item">
                                <i class="fas fa-users"></i>
                                <span>Clientes</span>
                            </li>
                            <li class="nav-item">
                                <i class="fas fa-cog"></i>
                                <span>Configuración</span>
                            </li>
                        </ul>
                    </div>
                    <div class="col-md-3">
                        <div class="user-info">
                            <span class="user-name">admin</span>
                            <button class="logout-btn">
                                <i class="fas fa-sign-out-alt"></i> Cerrar sesión
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </header>
        
        <!-- CONTENIDO PRINCIPAL -->
        <div class="main-content">
            <!-- LAYOUT DE DOS COLUMNAS PRINCIPALES -->
            <div class="pos-layout">
                <!-- COLUMNA IZQUIERDA - DIVIDIDA EN 2 SUBCOLUMNAS -->
                <div class="left-column">
                    <!-- SUBCOLUMNA IZQUIERDA - PRODUCTOS FRECUENTES -->
                    <div class="products-column">
                        <!-- INFORMACIÓN DE CAJA -->
                        <div class="info-cards">
                            <div class="info-card">
                                <div class="info-icon">
                                    <i class="fas fa-cash-register"></i>
                                </div>
                                <div class="info-content">
                                    <small>Caja #001</small>
                                    <h5>Venta Activa</h5>
                                </div>
                            </div>
                            
                            <div class="info-card">
                                <div class="info-icon">
                                    <i class="fas fa-user-tie"></i>
                                </div>
                                <div class="info-content">
                                    <small>Cajero</small>
                                    <h5>admin</h5>
                                </div>
                            </div>
                            
                            <div class="info-card">
                                <div class="info-icon">
                                    <i class="fas fa-clock"></i>
                                </div>
                                <div class="info-content">
                                    <small>Hora</small>
                                    <h5 id="current-time">00:54:38</h5>
                                </div>
                            </div>
                        </div>
                        
                        <!-- ESCÁNER -->
                        <div class="scanner-container">
                            <div class="scanner-title">
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
                        
                        <!-- PRODUCTOS FRECUENTES CON SCROLL -->
                        <div class="frequent-products-container">
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
                                    <button class="add-to-cart-btn">
                                        <i class="fas fa-cart-plus"></i> Agregar
                                    </button>
                                </div>
                                
                                <div class="product-card">
                                    <div class="product-image-placeholder">
                                        <i class="fas fa-box"></i>
                                    </div>
                                    <div class="product-name">Producto Ejemplo 2</div>
                                    <div class="product-price">$15.50</div>
                                    <button class="add-to-cart-btn">
                                        <i class="fas fa-cart-plus"></i> Agregar
                                    </button>
                                </div>
                                
                                <div class="product-card">
                                    <div class="product-image-placeholder">
                                        <i class="fas fa-box"></i>
                                    </div>
                                    <div class="product-name">Producto Ejemplo 3</div>
                                    <div class="product-price">$8.75</div>
                                    <button class="add-to-cart-btn">
                                        <i class="fas fa-cart-plus"></i> Agregar
                                    </button>
                                </div>
                                
                                <div class="product-card">
                                    <div class="product-image-placeholder">
                                        <i class="fas fa-box"></i>
                                    </div>
                                    <div class="product-name">Producto Ejemplo 4</div>
                                    <div class="product-price">$12.99</div>
                                    <button class="add-to-cart-btn">
                                        <i class="fas fa-cart-plus"></i> Agregar
                                    </button>
                                </div>
                                
                                <div class="product-card">
                                    <div class="product-image-placeholder">
                                        <i class="fas fa-box"></i>
                                    </div>
                                    <div class="product-name">Producto Ejemplo 5</div>
                                    <div class="product-price">$9.99</div>
                                    <button class="add-to-cart-btn">
                                        <i class="fas fa-cart-plus"></i> Agregar
                                    </button>
                                </div>
                                
                                <div class="product-card">
                                    <div class="product-image-placeholder">
                                        <i class="fas fa-box"></i>
                                    </div>
                                    <div class="product-name">Producto Ejemplo 6</div>
                                    <div class="product-price">$22.50</div>
                                    <button class="add-to-cart-btn">
                                        <i class="fas fa-cart-plus"></i> Agregar
                                    </button>
                                </div>
                                
                                <div class="product-card">
                                    <div class="product-image-placeholder">
                                        <i class="fas fa-box"></i>
                                    </div>
                                    <div class="product-name">Producto Ejemplo 7</div>
                                    <div class="product-price">$18.75</div>
                                    <button class="add-to-cart-btn">
                                        <i class="fas fa-cart-plus"></i> Agregar
                                    </button>
                                </div>
                                
                                <div class="product-card">
                                    <div class="product-image-placeholder">
                                        <i class="fas fa-box"></i>
                                    </div>
                                    <div class="product-name">Producto Ejemplo 8</div>
                                    <div class="product-price">$14.25</div>
                                    <button class="add-to-cart-btn">
                                        <i class="fas fa-cart-plus"></i> Agregar
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- SUBCOLUMNA DERECHA - TECLADO NUMÉRICO -->
                    <div class="keyboard-column">
                        <div class="keyboard-container">
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
                            <button class="keyboard-add">
                                <i class="fas fa-plus-circle"></i>
                                AGREGAR (ENTER)
                            </button>
                        </div>
                    </div>
                </div>
                
                <!-- COLUMNA DERECHA - CARRITO Y BOTONES -->
                <div class="right-column">
                    <!-- CARRITO DE VENTA -->
                    <div class="cart-container">
                        <div class="cart-header">
                            <div class="cart-title">
                                <i class="fas fa-shopping-cart"></i>
                                <span>Carrito de Venta</span>
                            </div>
                            <button class="clear-cart-btn">
                                <i class="fas fa-trash"></i>
                                <span>Vaciar</span>
                            </button>
                        </div>
                        
                        <div class="cart-items" id="cart-items">
                            <div class="empty-cart">
                                <i class="fas fa-shopping-cart"></i>
                                <h5>El carrito está vacío</h5>
                                <p>Escanee productos para comenzar</p>
                            </div>
                            <!-- Los items del carrito se agregarán aquí dinámicamente -->
                        </div>
                    </div>
                    
                    <!-- TOTALES -->
                    <div class="totals-container">
                        <div class="total-row">
                            <span>Subtotal:</span>
                            <span id="subtotal-amount">$0.00</span>
                        </div>
                        <div class="total-row">
                            <span>IVA (13%):</span>
                            <span id="tax-amount">$0.00</span>
                        </div>
                        <div class="total-row">
                            <span>Descuento:</span>
                            <span id="discount-amount">$0.00</span>
                        </div>
                        <div class="total-row grand-total">
                            <span>TOTAL A PAGAR:</span>
                            <span id="total-amount">$0.00</span>
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
                        
                        <div class="action-row">
                            <button class="action-btn customer-btn">
                                <i class="fas fa-user"></i>
                                Cliente
                            </button>
                            <button class="action-btn discount-btn">
                                <i class="fas fa-percent"></i>
                                Descuento
                            </button>
                            <button class="action-btn cancel-btn">
                                <i class="fas fa-times"></i>
                                Cancelar
                            </button>
                        </div>
                        
                        <button class="complete-sale-btn" id="complete-sale-btn" disabled>
                            <i class="fas fa-check-circle"></i>
                            FINALIZAR VENTA
                        </button>
                    </div>
                </div>
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
            const hours = now.getHours().toString().padStart(2, '0');
            const minutes = now.getMinutes().toString().padStart(2, '0');
            const seconds = now.getSeconds().toString().padStart(2, '0');
            document.getElementById('current-time').textContent = `${hours}:${minutes}:${seconds}`;
        }
        
        // Variables para el carrito
        let cartItems = [];
        let subtotal = 0;
        let taxRate = 0.13;
        let discount = 0;
        
        // Actualizar totales
        function updateTotals() {
            const tax = subtotal * taxRate;
            const total = subtotal + tax - discount;
            
            document.getElementById('subtotal-amount').textContent = `$${subtotal.toFixed(2)}`;
            document.getElementById('tax-amount').textContent = `$${tax.toFixed(2)}`;
            document.getElementById('discount-amount').textContent = `$${discount.toFixed(2)}`;
            document.getElementById('total-amount').textContent = `$${total.toFixed(2)}`;
            
            // Habilitar botón de finalizar si hay items
            const completeBtn = document.getElementById('complete-sale-btn');
            completeBtn.disabled = cartItems.length === 0;
        }
        
        // Agregar producto al carrito
        function addToCart(productName, price) {
            // Buscar si el producto ya está en el carrito
            const existingItem = cartItems.find(item => item.name === productName);
            
            if (existingItem) {
                existingItem.quantity++;
                existingItem.total = existingItem.quantity * existingItem.price;
            } else {
                cartItems.push({
                    name: productName,
                    price: price,
                    quantity: 1,
                    total: price
                });
            }
            
            // Recalcular subtotal
            subtotal = cartItems.reduce((sum, item) => sum + item.total, 0);
            
            // Actualizar vista del carrito
            updateCartView();
            updateTotals();
            
            // Mostrar notificación
            Swal.fire({
                icon: 'success',
                title: 'Producto agregado',
                text: `${productName} se ha añadido al carrito`,
                timer: 1500,
                showConfirmButton: false
            });
        }
        
        // Actualizar vista del carrito
        function updateCartView() {
            const cartContainer = document.getElementById('cart-items');
            
            if (cartItems.length === 0) {
                cartContainer.innerHTML = `
                    <div class="empty-cart">
                        <i class="fas fa-shopping-cart"></i>
                        <h5>El carrito está vacío</h5>
                        <p>Escanee productos para comenzar</p>
                    </div>
                `;
                return;
            }
            
            let html = '';
            cartItems.forEach((item, index) => {
                html += `
                    <div class="cart-item" data-index="${index}">
                        <div class="item-info">
                            <h6 style="margin-bottom: 5px; font-weight: 600;">${item.name}</h6>
                            <p style="margin: 0; color: #666; font-size: 0.9rem;">Precio: $${item.price.toFixed(2)}</p>
                        </div>
                        <div class="item-controls">
                            <div style="display: flex; align-items: center; gap: 10px;">
                                <button class="btn btn-sm btn-outline-secondary" style="min-width: 30px;" onclick="decreaseQuantity(${index})">-</button>
                                <span style="font-weight: 600; min-width: 30px; text-align: center;">${item.quantity}</span>
                                <button class="btn btn-sm btn-outline-secondary" style="min-width: 30px;" onclick="increaseQuantity(${index})">+</button>
                            </div>
                            <p style="margin: 8px 0 0 0; font-weight: 700; color: #28a745;">$${item.total.toFixed(2)}</p>
                            <button class="btn btn-sm btn-danger" style="margin-top: 5px; padding: 3px 8px;" onclick="removeItem(${index})">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                `;
            });
            
            cartContainer.innerHTML = html;
        }
        
        // Funciones globales para manipular el carrito
        window.increaseQuantity = function(index) {
            cartItems[index].quantity++;
            cartItems[index].total = cartItems[index].quantity * cartItems[index].price;
            subtotal = cartItems.reduce((sum, item) => sum + item.total, 0);
            updateCartView();
            updateTotals();
        };
        
        window.decreaseQuantity = function(index) {
            if (cartItems[index].quantity > 1) {
                cartItems[index].quantity--;
                cartItems[index].total = cartItems[index].quantity * cartItems[index].price;
            } else {
                cartItems.splice(index, 1);
            }
            subtotal = cartItems.reduce((sum, item) => sum + item.total, 0);
            updateCartView();
            updateTotals();
        };
        
        window.removeItem = function(index) {
            cartItems.splice(index, 1);
            subtotal = cartItems.reduce((sum, item) => sum + item.total, 0);
            updateCartView();
            updateTotals();
        };
        
        // Vaciar carrito
        document.querySelector('.clear-cart-btn').addEventListener('click', function() {
            Swal.fire({
                title: '¿Vaciar carrito?',
                text: "Esta acción eliminará todos los productos del carrito",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Sí, vaciar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    cartItems = [];
                    subtotal = 0;
                    discount = 0;
                    updateCartView();
                    updateTotals();
                    Swal.fire(
                        'Carrito vacío',
                        'Todos los productos han sido eliminados',
                        'success'
                    );
                }
            });
        });
        
        // Inicializar
        document.addEventListener('DOMContentLoaded', function() {
            // Actualizar hora cada segundo
            setInterval(updateTime, 1000);
            updateTime();
            
            // Auto-focus en el input del escáner
            document.getElementById('barcode-input').focus();
            
            // Agregar funcionalidad a los botones del teclado
            document.querySelectorAll('.keyboard-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const barcodeInput = document.getElementById('barcode-input');
                    barcodeInput.value += this.textContent;
                    barcodeInput.focus();
                });
            });
            
            // Botón AGREGAR del teclado
            document.querySelector('.keyboard-add').addEventListener('click', function() {
                const barcodeInput = document.getElementById('barcode-input');
                if (barcodeInput.value.trim() !== '') {
                    // Simular agregar producto
                    addToCart('Producto Escaneado', parseFloat((Math.random() * 50 + 1).toFixed(2)));
                    barcodeInput.value = '';
                    barcodeInput.focus();
                }
            });
            
            // Permitir agregar con ENTER
            document.getElementById('barcode-input').addEventListener('keypress', function(e) {
                if (e.key === 'Enter') {
                    document.querySelector('.keyboard-add').click();
                }
            });
            
            // Agregar productos de ejemplo a productos frecuentes
            document.querySelectorAll('.add-to-cart-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const productCard = this.closest('.product-card');
                    const productName = productCard.querySelector('.product-name').textContent;
                    const productPrice = parseFloat(productCard.querySelector('.product-price').textContent.replace('$', ''));
                    addToCart(productName, productPrice);
                });
            });
            
            // Botones de pago
            document.querySelectorAll('.payment-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const paymentType = this.classList.contains('cash-btn') ? 'efectivo' : 'tarjeta';
                    Swal.fire({
                        title: `Pago en ${paymentType}`,
                        text: `Total a pagar: $${document.getElementById('total-amount').textContent.replace('$', '')}`,
                        icon: 'info',
                        showCancelButton: true,
                        confirmButtonText: 'Confirmar pago',
                        cancelButtonText: 'Cancelar'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            Swal.fire(
                                'Pago realizado',
                                'La venta ha sido procesada exitosamente',
                                'success'
                            );
                            // Resetear carrito después de pago exitoso
                            cartItems = [];
                            subtotal = 0;
                            discount = 0;
                            updateCartView();
                            updateTotals();
                        }
                    });
                });
            });
            
            // Botón finalizar venta
            document.getElementById('complete-sale-btn').addEventListener('click', function() {
                if (!this.disabled) {
                    Swal.fire({
                        title: 'Seleccionar método de pago',
                        text: '¿Cómo desea realizar el pago?',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'Efectivo',
                        cancelButtonText: 'Tarjeta',
                        showDenyButton: true,
                        denyButtonText: 'Cancelar'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Pago en efectivo
                            document.querySelector('.cash-btn').click();
                        } else if (result.dismiss === Swal.DismissReason.cancel) {
                            // Pago con tarjeta
                            document.querySelector('.card-btn').click();
                        }
                    });
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
