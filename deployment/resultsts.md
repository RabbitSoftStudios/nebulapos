ec2-user@ip-172-31-1-105 release-source]$ cd /opt/nebulapos/release-source

echo '===== ROUTES ====='
sed -n '1,260p' frontend/pos_system/config/routes.php

echo
echo '===== CONSTANTS ====='
sed -n '1,220p' frontend/pos_system/config/constants.php

echo
echo '===== POS SALE ====='
sed -n '1,260p' frontend/pos_system/views/pos_sale.php

echo
echo '===== SEARCH PRODUCT ====='
sed -n '1,220p' frontend/pos_system/views/ajax/search_product.php

echo
echo '===== SAVE DTE ====='
sed -n '1,280p' frontend/pos_system/views/ajax/save_dte.php

echo
echo '===== PROCESS SALE ====='
sed -n '1,220p' frontend/pos_system/views/pos_dashboard.phpe_complete.php'
===== ROUTES =====
<?php
/**
 * Sistema POS - Rutas de la Aplicación
 * =====================================
 * Define el enrutamiento simple para la aplicación POS.
 * * @package    POS System
 * @author     Nebula DET Team
 * @version    2.0.0
 */

// // Define la ruta base del proyecto (ajustar si es necesario)
// define('BASE_URL', '/');

// // Mapa de rutas
// $routes = [
//     '/' => 'views/pos_sale.php',
//     '/pos' => 'views/pos_sale.php',
//     '/dashboard' => 'views/pos_dashboard.php',
//     '/products' => 'views/pos_products.php',
//     '/customers' => 'views/pos_customers.php',
//     '/reports' => 'views/pos_reports.php',
//     '/settings' => 'views/pos_settings.php',
//     '/login' => 'login.php',
//     '/logout' => 'logout.php',

//     // Rutas de API
//     '/api/v1/products' => 'api/v1/products.php',
//     '/api/v1/sales' => 'api/v1/sales.php',
//     '/api/v1/customers' => 'api/v1/customers.php',
//     // ... otras rutas
// ];

// // Función simple de enrutamiento
// function route($uri, $routes) {
//     $uri = strtok($uri, '?'); // Eliminar query string
//     $uri = rtrim($uri, '/');
//     if (empty($uri)) {
//         $uri = '/';
//     }

//     if (isset($routes[$uri])) {
//         // Incluir el archivo de vista o controlador
//         require_once __DIR__ . '/../' . $routes[$uri];
//     } else {
//         // Ruta no encontrada (404)
//         header("HTTP/1.0 404 Not Found");
//         echo "<h1>404 Not Found</h1>";
//         echo "The page " . htmlspecialchars($uri) . " was not found.";
//     }
// }

// // Obtener la URI
// $uri = $_SERVER['REQUEST_URI'];
// // route($uri, $routes); // Esto se llamaría en index.php


// La constante BASE_URL ahora se define en config/constants.php

// Mapa de rutas
$routes = [
    '/' => 'dashboard.php', // Cambiado de views/pos_sale.php a dashboard.php
    '/pos' => 'views/pos_sale.php',
    '/dashboard' => 'dashboard.php', // Apuntar a la raíz para dashboard.php
    '/products' => 'views/pos_products.php',
    '/customers' => 'views/pos_customers.php',
    '/reports' => 'views/pos_reports.php',
    '/settings' => 'views/pos_settings.php',
    '/login' => 'login.php',
    '/logout' => 'logout.php',

    // Rutas de API
    '/api/v1/products' => 'api/v1/products.php',
    '/api/v1/sales' => 'api/v1/sales.php',
    '/api/v1/customers' => 'api/v1/customers.php',
    '/ajax/process_sale_complete' => 'views/ajax/process_sale_complete.php',
    '/ajax/save_dte' => 'views/ajax/save_dte.php',
    '/ajax/search_product' => 'views/ajax/search_product.php',
    // ... otras rutas
];


// V2.0 Función simple de enrutamiento con control de acceso

function route($uri, $routes) {
    // 1. Limpieza inicial de la URI y query string
    $uri = strtok($uri, '?');

    // ******************************************************
    // ** LA CLAVE: NORMALIZAR LA URI DE ENTRADA **
    // ******************************************************

    // Normalizamos la base y la URI (quitando slashs finales)
    $base_url_clean = rtrim(BASE_URL, '/');
    $uri_clean = rtrim($uri, '/');

    // 2. Eliminación de la BASE_URL del inicio
    if ($base_url_clean !== '/' && str_starts_with($uri_clean, $base_url_clean)) {
        // Obtenemos solo la parte de la ruta de la aplicación.
        // Usamos la URI original ($uri) para mantener cualquier slash que venga después.
        $uri = substr($uri, strlen($base_url_clean));
    }

    // 3. Limpieza final y asignación de la ruta raíz (/)
    $uri = rtrim($uri, '/');

    // Si la URI es vacía (resultado de limpiar la base) o si es solo el nombre de la base,
    // la establecemos como la ruta principal '/'.
    if (empty($uri) || $uri === $base_url_clean) {
        $uri = '/';
    }

    // **********************************************
    // ** LÓGICA DE AUTENTICACIÓN *******************
    // **********************************************
    $public_routes = ['/login', '/logout'];
    $is_authenticated = isset($_SESSION['pos_authenticated']) && $_SESSION['pos_authenticated'] === true;

    if (!$is_authenticated && !in_array($uri, $public_routes)) {
        header('Location: ' . BASE_URL . '/login');
        exit();
    }

    // 4. MANEJO DE RUTAS
    if (isset($routes[$uri])) {
        // Redirección para usuarios autenticados que intentan acceder a login
        if ($is_authenticated && $uri === '/login') {
            $dest = ($_SESSION['cashier']['code'] ?? '') === 'ADMIN' ? '/dashboard' : '/pos';
            header('Location: ' . BASE_URL . $dest);
            exit();
        }

        require_once __DIR__ . '/../' . $routes[$uri];
    } else {
        // Ruta no encontrada (404)
        header("HTTP/1.0 404 Not Found");
        echo "<h1>404 Not Found</h1>";
        echo "The page " . htmlspecialchars($uri) . " was not found.";
    }
}

// V2.0 Función simple de enrutamiento con control de acceso


// V1.0 Función simple de enrutamiento con control de acceso
// function route($uri, $routes) {
//     // 1. Limpieza de URI
//     $uri = strtok($uri, '?'); // Eliminar query string
//     $uri = rtrim($uri, '/');
//     if (empty($uri)) {
//         $uri = '/';
//     }

//     // 2. Definir rutas públicas (que no requieren autenticación)
//     $public_routes = ['/login', '/logout'];

//     // 3. CONTROL DE AUTENTICACIÓN
//     $is_authenticated = isset($_SESSION['pos_authenticated']) && $_SESSION['pos_authenticated'] === true;

//     // Si el usuario NO está autenticado Y la ruta solicitada NO es pública,
//     // lo redirigimos forzosamente a login.
//     if (!$is_authenticated && !in_array($uri, $public_routes)) {
//         // Redirigir al login
//         header('Location: login.php');
//         exit();
//     }

//     // 4. MANEJO DE RUTAS
//     if (isset($routes[$uri])) {
//         // Si el usuario ya está autenticado e intenta acceder al login, redirigir a /pos
//         if ($is_authenticated && $uri === '/login') {
//             header('Location: /pos');
//             exit();
//         }

//         // Incluir el archivo de vista o controlador
//         // Nota: Asumiendo que __DIR__ es config/ y las rutas son relativas a la raíz del proyecto.
//         require_once __DIR__ . '/../' . $routes[$uri];
//     } else {
//         // Ruta no encontrada (404)
//         header("HTTP/1.0 404 Not Found");
//         echo "<h1>404 Not Found</h1>";
//         echo "The page " . htmlspecialchars($uri) . " was not found.";
//     }
// }
?>

===== CONSTANTS =====
<?php
/** NebulaPOS POS - constantes locales */

if (!defined('BASE_URL')) define('BASE_URL', '/pos_system');

if (!function_exists('env')) {
    function env($key, $default = null) {
        $value = $_ENV[$key] ?? $_SERVER[$key] ?? getenv($key);
        return ($value === false || $value === '' || $value === null) ? $default : $value;
    }
}

define('APP_NAME', 'Nebula POS System');
define('APP_VERSION', '7.0.0');
define('APP_ENV', env('APP_ENV', 'production'));
define('APP_DEBUG', filter_var(env('APP_DEBUG', false), FILTER_VALIDATE_BOOLEAN));
define('APP_URL', env('APP_URL', 'https://pos.nebuladet.website'));
define('APP_TIMEZONE', 'America/El_Salvador');
date_default_timezone_set(APP_TIMEZONE);

define('ROOT_PATH', dirname(__DIR__));
define('VIEWS_PATH', ROOT_PATH . '/views');
define('CONTROLLERS_PATH', ROOT_PATH . '/controllers');
define('INCLUDES_PATH', ROOT_PATH . '/includes');
define('LOGS_PATH', ROOT_PATH . '/logs');
define('SCRIPTS_PATH', ROOT_PATH . '/scripts');
define('BACKUP_PATH', ROOT_PATH . '/backups');
define('UPLOAD_PATH', ROOT_PATH . '/uploads/');
define('TEMP_PATH', ROOT_PATH . '/temp/');
define('ASSETS_PATH', ROOT_PATH . '/assets/');

// Persistencia local SQLite.
define('TABLE_PRODUCTS', 'dte_productos');
define('TABLE_SALES', 'pos_ventas');
define('TABLE_CUSTOMERS', 'mh_cliente_consumidor');
define('TABLE_SUPPLIERS', 'mh_proveedor_contribuyente');
define('TABLE_USERS', 'users');
define('TABLE_COMPANIES', 'companies');
define('TABLE_TRANSACTIONS', 'transactions');
define('TABLE_PAYMENTS', 'payments');
define('TABLE_DTE_FACTURAS', 'dte_facturas');

// DTE El Salvador.
define('DTE_ENVIRONMENT', env('DTE_ENVIRONMENT', '00'));
define('DTE_EMISOR_NIT', env('DTE_EMISOR_NIT', ''));
define('DTE_EMISOR_NRC', env('DTE_EMISOR_NRC', ''));
define('DTE_SIGNATURE_KEY', env('DTE_SIGNATURE_KEY', ''));
define('DTE_SIGNATURE_PASS', env('DTE_SIGNATURE_PASS', ''));
define('DTE_FACTURA', '01');
define('DTE_CREDITO_FISCAL', '03');
define('DTE_NOTA_CREDITO', '05');
define('DTE_NOTA_DEBITO', '06');
define('DTE_COMPROBANTE_RETENCION', '14');

define('POS_CAJA_NUMERO', env('POS_CAJA_NUMERO', '001'));
define('POS_CAJERO_DEFAULT', env('POS_CAJERO_DEFAULT', 'admin'));
define('POS_TICKET_WIDTH', (int)env('POS_TICKET_WIDTH', 80));
define('POS_AUTO_SAVE', filter_var(env('POS_AUTO_SAVE', false), FILTER_VALIDATE_BOOLEAN));

define('IVA_RATE', (float)env('IVA_RATE', 0.13));
define('RETENTION_RATE', (float)env('RETENTION_RATE', 0.01));
define('DEFAULT_CURRENCY_SYMBOL', env('CURRENCY_SYMBOL', '$'));
define('DEFAULT_TAX_RATE', IVA_RATE);

define('SESSION_TIMEOUT', (int)env('SESSION_TIMEOUT', 3600));
define('MAX_LOGIN_ATTEMPTS', (int)env('MAX_LOGIN_ATTEMPTS', 3));
define('ENCRYPTION_KEY', env('ENCRYPTION_KEY', ''));

define('SALE_PENDING', 'pending');
define('SALE_COMPLETED', 'completed');
define('SALE_CANCELLED', 'cancelled');
define('SALE_REFUNDED', 'refunded');
define('PAYMENT_CASH', 'cash');
define('PAYMENT_CARD', 'card');
define('PAYMENT_TRANSFER', 'transfer');
define('PAYMENT_MIXED', 'mixed');
define('DTE_PENDING', 'pendiente');
define('DTE_SIGNED', 'firmado');
define('DTE_RECEIVED', 'recibido');
define('DTE_ERROR', 'error');

define('MSG_SUCCESS', 'Operación realizada exitosamente');
define('MSG_ERROR', 'Ocurrió un error en la operación');
define('MSG_NO_PRODUCT', 'Producto no encontrado');
define('MSG_SCAN_SUCCESS', 'Producto escaneado exitosamente');
define('MSG_SALE_COMPLETED', 'Venta procesada exitosamente');
define('API_TIMEOUT', (int)env('API_TIMEOUT', 30));
define('ENABLE_OFFLINE_MODE', filter_var(env('ENABLE_OFFLINE_MODE', false), FILTER_VALIDATE_BOOLEAN));
define('HTTP_OK', 200);
define('HTTP_CREATED', 201);
define('HTTP_BAD_REQUEST', 400);
define('HTTP_UNAUTHORIZED', 401);
define('HTTP_FORBIDDEN', 403);
define('HTTP_NOT_FOUND', 404);
define('HTTP_METHOD_NOT_ALLOWED', 405);
define('HTTP_INTERNAL_SERVER_ERROR', 500);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

===== POS SALE =====
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

===== SEARCH PRODUCT =====
<?php
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/supabase.php';
header('Content-Type: application/json');
$input = file_get_contents('php://input');
$data = json_decode($input, true);
if (!isset($data['barcode'])) {
    echo json_encode(['success' => false, 'error' => 'Código de barras requerido']);
    exit;
}
try {
    $db = supabase('dte_productos');
    $result = $db->select('*', ['codigo_barras' => $data['barcode']], 1);

    if ($result['success'] && !empty($result['data'])) {
        echo json_encode([
            'success' => true,
            'product' => $result['data'][0]
        ]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Producto no encontrado']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
===== SAVE DTE =====
<?php
declare(strict_types=1);
header('Content-Type: application/json');
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/supabase.php';
require_once __DIR__ . '/../../includes/pg_connection.php';
checkPOSAuth();

$data=json_decode(file_get_contents('php://input'),true);
if(!is_array($data)||empty($data['identificacion']['codigoGeneracion'])){http_response_code(400);echo json_encode(['success'=>false,'error'=>'No se recibieron datos DTE válidos.']);exit;}
$companyId=(int)($_SESSION['empresa_id']??0);$usuarioId=$_SESSION['user_id']??$_SESSION['usuario_id']??null;
if($companyId<=0){http_response_code(403);echo json_encode(['success'=>false,'error'=>'La sesión no tiene empresa asociada.']);exit;}
try{
 $pdo=pg_pool();$pdo->beginTransaction();$codigo=(string)$data['identificacion']['codigoGeneracion'];$tipo=(string)($data['identificacion']['tipoDte']??'01');$empresaNit=$_SESSION['empresa_nit']??($data['emisor']['nit']??null);$total=(float)($data['resumen']['totalPagar']??0);$items=is_array($data['cuerpoDocumento']??null)?$data['cuerpoDocumento']:[];
 $stmt=$pdo->prepare("SELECT COALESCE(MAX(CAST(RIGHT(numero_control,15) AS INTEGER)),0)+1 FROM dte_facturas WHERE company_id=? AND tipo_dte=?");$stmt->execute([$companyId,$tipo]);$seq=str_pad((string)((int)$stmt->fetchColumn()),15,'0',STR_PAD_LEFT);$numeroControl=$data['identificacion']['numeroControl']??('DTE-'.$tipo.'-P001M001-'.$seq);$data['identificacion']['numeroControl']=$numeroControl;
 $dte=new SupabaseClient('dte_facturas');$dteResult=$dte->insertDTE($data,$usuarioId,$empresaNit);if(!($dteResult['success']??false))throw new RuntimeException($dteResult['error']??'No se pudo guardar el DTE.');$dteId=$dteResult['id']??($dteResult['data'][0]['id']??null);
 $venta=new SupabaseClient('pos_ventas');$ventaResult=$venta->insert(['company_id'=>$companyId,'usuario_id'=>$usuarioId,'codigo_generacion'=>$codigo,'numero_control'=>$numeroControl,'tipo_dte'=>$tipo,'cliente_nombre'=>$data['receptor']['nombre']??'CONSUMIDOR FINAL','cliente_nit'=>$data['receptor']['nit']??'CF','total_pagar'=>$total,'subtotal'=>(float)($data['resumen']['subTotal']??$total),'iva'=>(float)($data['resumen']['totalIva']??0),'metodo_pago'=>((int)($data['resumen']['condicionOperacion']??1)===1?'Efectivo':'Tarjeta'),'fecha_emision'=>$data['identificacion']['fecEmi']??date('Y-m-d'),'created_at'=>date('Y-m-d H:i:s')]);if(!($ventaResult['success']??false))throw new RuntimeException($ventaResult['error']??'No se pudo guardar la venta.');$ventaId=$ventaResult['id']??($ventaResult['data'][0]['id']??null);
 $stockWarnings=[];
 foreach($items as $item){$codigoProducto=trim((string)($item['codigo']??$item['codigoProducto']??''));$cantidad=(float)($item['cantidad']??0);if($codigoProducto===''||$cantidad<=0)continue;$q=$pdo->prepare('SELECT * FROM dte_productos WHERE company_id=? AND (codigo_producto=? OR codigo_barras=? OR sku=?) AND borrado_logico=0 LIMIT 1');$q->execute([$companyId,$codigoProducto,$codigoProducto,$codigoProducto]);$product=$q->fetch();if(!$product){$stockWarnings[]='Producto no encontrado: '.$codigoProducto;continue;}$stockBefore=(float)($product['stock_actual']??0);$stockAfter=$stockBefore-$cantidad;if($stockAfter<0)throw new RuntimeException('Stock insuficiente para producto '.$codigoProducto.'. Disponible: '.$stockBefore.'.');$pdo->prepare('UPDATE dte_productos SET stock_actual=?, actualizado_el=CURRENT_TIMESTAMP WHERE id=? AND company_id=?')->execute([$stockAfter,$product['id'],$companyId]);$pdo->prepare('INSERT INTO inventory_movements (company_id,producto_id,tipo,cantidad,stock_anterior,stock_nuevo,referencia,usuario_id) VALUES (?,?,?,?,?,?,?,?)')->execute([$companyId,$product['id'],'sale',$cantidad,$stockBefore,$stockAfter,$codigo,$usuarioId]);if($ventaId!==null){$d=$pdo->prepare('INSERT INTO detalle_ventas (company_id,venta_id,producto_id,cantidad,precio_unitario,subtotal,iva,total) VALUES (?,?,?,?,?,?,?,?)');$unit=(float)($item['precioUni']??$item['precio']??0);$sub=(float)($item['ventaGravada']??$item['ventaExenta']??($unit*$cantidad));$iva=(float)($item['ivaItem']??0);$d->execute([$companyId,$ventaId,$product['id'],$cantidad,$unit,$sub,$iva,$sub+$iva]);}}
 $hasVentaId=false;foreach($pdo->query('PRAGMA table_info(dte_facturas)')->fetchAll() as $col){if($col['name']==='venta_id'){$hasVentaId=true;break;}}if(!$hasVentaId)$pdo->exec('ALTER TABLE dte_facturas ADD COLUMN venta_id INTEGER');if($ventaId!==null&&$dteId!==null)$pdo->prepare('UPDATE dte_facturas SET venta_id=?, company_id=?, usuario_id=? WHERE id=? AND company_id=?')->execute([$ventaId,$companyId,$usuarioId,$dteId,$companyId]);
 $pdo->commit();$dir=__DIR__.'/../../storage/sigs/';if(!is_dir($dir))mkdir($dir,0770,true);file_put_contents($dir.'dte_'.$codigo.'.json',json_encode($data,JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES));echo json_encode(['success'=>true,'mensaje'=>'Venta, inventario y DTE persistidos correctamente.','codigoGeneracion'=>$codigo,'numeroControl'=>$numeroControl,'dte_id'=>$dteId,'venta_id'=>$ventaId,'stock_warnings'=>$stockWarnings]);
}catch(Throwable $e){if(isset($pdo)&&$pdo->inTransaction())$pdo->rollBack();error_log('[save_dte] '.$e->getMessage());http_response_code(500);echo json_encode(['success'=>false,'error'=>$e->getMessage()]);}

===== PROCESS SALE =====
<?php
/**
 * AJAX: process_sale_complete.php
 * Procesamiento Completo de Venta con Firma Electrónica
 * Team MYTS
 */
ob_start();
register_shutdown_function(function() {
    $error = error_get_last();
    if ($error && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR], true)) {
        if (ob_get_length()) ob_clean();
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'error' => 'FATAL PHP ERROR: ' . $error['message']]);
    }
});
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', '0');

require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/auth.php';
require_once __DIR__ . '/../../includes/supabase.php';
require_once __DIR__ . '/../../includes/pg_connection.php';
require_once __DIR__ . '/../../includes/signer/utils/audit_logger.php';
require_once __DIR__ . '/../../includes/signer/utils/dte_validator_new.php';

checkPOSAuth();
$companyId = (int)($_SESSION['empresa_id'] ?? 0);
if ($companyId <= 0) {
    ob_clean();
    echo json_encode(['success' => false, 'error' => 'Sesión sin empresa asociada.']);
    exit;
}

$dteData = json_decode(file_get_contents('php://input'), true);
if (!$dteData || empty($dteData['identificacion']['codigoGeneracion'])) {
    ob_clean();
    echo json_encode(['success' => false, 'error' => 'DTE inválido']);
    exit;
}

try {
    $codigoGeneracion = $dteData['identificacion']['codigoGeneracion'];
    $tipoDte = $dteData['identificacion']['tipoDte'] ?? '01';
    $correoCliente = $dteData['receptor']['correo'] ?? null;
    $pdo = pg_pool();

    if (!is_db_connected($pdo)) throw new Exception('Database connection unavailable. Please try again.');
    $pdo->beginTransaction();

    // La secuencia de control es independiente por empresa y tipo de DTE.
    $stmt = $pdo->prepare("SELECT COALESCE(MAX(CAST(RIGHT(numero_control, 15) AS INTEGER)), 0) + 1 AS next_num FROM dte_facturas WHERE company_id = :company AND tipo_dte = :tipo AND numero_control IS NOT NULL AND LENGTH(numero_control) >= 15");
    $stmt->execute([':company' => $companyId, ':tipo' => $tipoDte]);
    $row = $stmt->fetch();
    $secuencial = str_pad((string)($row['next_num'] ?? 1), 15, '0', STR_PAD_LEFT);
    $numeroControl = "DTE-{$tipoDte}-P001M001-{$secuencial}";
    $dteData['identificacion']['numeroControl'] = $numeroControl;

    $validacion = validar_json_dte_nuevo($dteData);
    if (!$validacion['valid']) {
        $pdo->rollBack();
        ob_clean();
        echo json_encode(['success' => false, 'error' => 'JSON DTE inválido: ' . implode(' | ', array_slice($validacion['errors'], 0, 3)), 'validation_errors' => $validacion['errors'], 'model' => 'CONSUMIDOR_FINAL_CON_IVA_INCLUIDO']);
        exit;
    }

    error_log('DTE VALIDATION PASSED for codigoGeneracion: ' . $codigoGeneracion . ' | numeroControl: ' . $numeroControl . ' | company_id: ' . $companyId);
    $dir = __DIR__ . '/../../storage/sigs/';
    if (!is_dir($dir)) mkdir($dir, 0770, true);
    file_put_contents($dir . "dte_{$codigoGeneracion}.json", json_encode($dteData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

    $db = supabase('dte_facturas');
    $res = $db->insert([
        'company_id' => $companyId,
        'usuario_id' => (int)($_SESSION['user_id'] ?? 0) ?: null,
        'codigo_generacion' => $codigoGeneracion,
        'emisor_nit' => $dteData['emisor']['nit'] ?? null,
        'numero_control' => $numeroControl,
        'tipo_dte' => $tipoDte,
        'fecha_emision' => ($dteData['identificacion']['fecEmi'] ?? date('Y-m-d')) . ' ' . ($dteData['identificacion']['horEmi'] ?? date('H:i:s')),
        'total_pagar' => $dteData['resumen']['totalPagar'] ?? 0,
        'documento_json' => $dteData,
        'estado_firma' => 'pendiente',
        'origen' => 'pos_interno'
    ]);
    if (!$res['success']) throw new Exception($res['error'] ?? 'Insert fallido');
    $pdo->commit();

    require_once __DIR__ . '/../../includes/signer/signer_local.php';
    $firmaBase64 = sign_document_local(['dte_json' => json_encode($dteData)], $codigoGeneracion, $numeroControl);
    if (!is_string($firmaBase64) || strlen($firmaBase64) < 100) throw new Exception('Firma local inválida');

    $db->update(['firma_local' => $firmaBase64], ['codigo_generacion' => $codigoGeneracion]);

    require_once __DIR__ . '/../../includes/signer/signer_goes.php';
    $goesResult = enviar_firma_gobierno($codigoGeneracion, $firmaBase64, $numeroControl);
    if (empty($goesResult['success']) || $goesResult['success'] !== true) throw new Exception('Error en recepción MH para ' . $codigoGeneracion);

    $db->update([
        'estado_firma' => $goesResult['estado'] ?? 'desconocido',
        'sello_recepcion' => $goesResult['sello'] ?? null
    ], ['codigo_generacion' => $codigoGeneracion]);

    require_once __DIR__ . '/../../includes/signer/utils/qr_maker.php';
    require_once __DIR__ . '/../../includes/signer/utils/ticket_printer.php';
    require_once __DIR__ . '/../../includes/signer/utils/pdf_generator.php';
    require_once __DIR__ . '/../../includes/signer/utils/mail_sender.php';

    $qr = generar_qr_mh(['codigoGeneracion' => $codigoGeneracion, 'numeroControl' => $numeroControl, 'sello' => $goesResult['sello'] ?? null]);
    $html = generar_ticket_html($dteData, $qr);
    $pdf = generar_factura_pdf($html, $codigoGeneracion);
    if ($correoCliente) enviar_factura_email($correoCliente, $pdf);

    try {
        if (!empty($dteData['receptor']['telefono']) && ($goesResult['estado'] ?? null) === 'PROCESADO') {
            require_once __DIR__ . '/../../includes/signer/utils/sms_notifier.php';
            enviar_sms_notificacion($dteData['receptor']['telefono'], 'Su factura electrónica ha sido enviada a su correo. Gracias por su compra.');
        }
    } catch (Throwable $smsErr) {
        error_log('[SMS_ERROR] ' . $smsErr->getMessage());
    }

    audit_log_final_summary($codigoGeneracion, $numeroControl, 'SUCCESS', [
        'company_id' => $companyId,
        'estado_mh' => $goesResult['estado'] ?? null,
        'sello_recibido' => $goesResult['sello'] ?? null,
        'total_pagar' => $dteData['resumen']['totalPagar'] ?? 0,
        'cliente' => $dteData['receptor']['nombre'] ?? 'CONSUMIDOR FINAL'
    ]);

    ob_clean();
    echo json_encode(['success' => true, 'codigoGeneracion' => $codigoGeneracion, 'numeroControl' => $numeroControl, 'estadoMH' => $goesResult['estado'] ?? null, 'selloRecepcion' => $goesResult['sello'] ?? null, 'respuestaMH' => $goesResult]);
} catch (Throwable $e) {
    if (isset($pdo)) {
        try { if (is_db_connected($pdo) && $pdo->inTransaction()) $pdo->rollBack(); } catch (Throwable $ignored) {}
    }
    error_log('process_sale_complete ERROR: ' . $e->getMessage());
    $codigo = $dteData['identificacion']['codigoGeneracion'] ?? 'UNKNOWN';
    $control = $dteData['identificacion']['numeroControl'] ?? 'UNKNOWN';
    audit_log_error($codigo, $control, 'Error durante proceso completo', $e->getMessage(), $e->getFile(), $e->getLine());
    ob_clean();
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

===== DASHBOARD =====
<?php
/**
 * Sistema POS - Dashboard (Panel de Control)
 * ============================================
 * Vista principal que muestra métricas clave, gráficos y resúmenes.
 * @package    POS System
 * @author     Nebula DET Team
 * @version    2.0.0
 */

// 1. Inclusión de Configuración y Controladores (Asumida)
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../includes/supabase_reports.php'; // Para inicializar Supabase
require_once __DIR__ . '/../includes/init_report.php';
require_once __DIR__ . '/../controllers/auth_controller.php'; // Verifica sesión
require_once __DIR__ . '/../controllers/dashboard_controller.php'; // Controladores de datos del dashboard

// Asumir que auth_controller.php define $cashier y $registerNumber
// Si no están definidos, se usan valores por defecto para no romper los partials
$cashier = $_SESSION['cashier'] ?? ['name' => 'Invitado'];
$registerNumber = $_SESSION['register_number'] ?? '00';

// Simulación de obtención de datos del controlador
// En un sistema real, estas funciones harían llamadas a Supabase a través del cliente inicializado en database.php
//$metrics = fetch_dashboard_metrics($supabase);  REPARAR ESTA LINEA Y LA QUE SIGUE, SE ANIDAN CON 13,14,15 LINE
//$top_products = fetch_top_selling_products($supabase);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars(APP_NAME) ?> | Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
    <link rel="stylesheet" href="../public/css/pos_styles.css">
</head>
<body class="d-flex flex-column min-vh-100">

    <?php include_once __DIR__ . '/partials/header.php'; ?>
    <?php include_once __DIR__ . '/partials/sidebar.php'; ?>

    <main class="container-fluid mt-4 flex-grow-1">
        <h2 class="mb-4"><i class="fas fa-tachometer-alt me-2"></i> Panel de Control</h2>

        <div class="row g-4 mb-4">

            <div class="col-xl-3 col-md-6">
                <div class="card bg-primary text-white shadow-sm h-100">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-8">
                                <div class="fs-5 fw-bold mb-1">Ventas Hoy</div>
                                <div class="h3 mb-0">$<?= number_format($metrics['sales_today'] ?? 0.00, 2) ?></div>
                            </div>
                            <div class="col-4 text-end">
                                <i class="fas fa-money-bill-wave fa-3x opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card bg-success text-white shadow-sm h-100">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-8">
                                <div class="fs-5 fw-bold mb-1">Transacciones</div>
                                <div class="h3 mb-0"><?= number_format($metrics['transactions_today'] ?? 0) ?></div>
                            </div>
                            <div class="col-4 text-end">
                                <i class="fas fa-receipt fa-3x opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card bg-info text-white shadow-sm h-100">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-8">
                                <div class="fs-5 fw-bold mb-1">Total Clientes</div>
                                <div class="h3 mb-0"><?= number_format($metrics['total_customers'] ?? 0) ?></div>
                            </div>
                            <div class="col-4 text-end">
                                <i class="fas fa-users fa-3x opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-xl-3 col-md-6">
                <div class="card bg-warning text-dark shadow-sm h-100">
                    <div class="card-body">
                        <div class="row align-items-center">
                            <div class="col-8">
                                <div class="fs-5 fw-bold mb-1">Alerta Stock</div>
                                <div class="h3 mb-0"><?= number_format($metrics['low_stock_products'] ?? 0) ?></div>
                            </div>
                            <div class="col-4 text-end">
                                <i class="fas fa-exclamation-triangle fa-3x opacity-50"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-8">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-white fw-bold">Ventas de los Últimos 7 Días</div>
                    <div class="card-body">
                        <canvas id="salesChart" style="max-height: 400px;"></canvas>
                        <div class="text-center text-muted" id="salesChartPlaceholder">
                            <i class="fas fa-chart-line fa-2x"></i>
                            <p>Cargando datos del gráfico de ventas...</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card shadow-sm h-100">
                    <div class="card-header bg-white fw-bold">Top 5 Productos Vendidos (Hoy)</div>
                    <ul class="list-group list-group-flush">
                        <?php if (!empty($top_products)): ?>
                            <?php foreach ($top_products as $product): ?>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    <?= htmlspecialchars($product['name']) ?>
                                    <span class="badge bg-primary rounded-pill"><?= htmlspecialchars($product['quantity']) ?> uds</span>
                                </li>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <li class="list-group-item text-muted text-center">
                                No hay ventas registradas hoy.
                            </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
        </div>
    </main>

    <?php include_once __DIR__ . '/partials/footer.php'; ?>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
    <script src="../public/js/pos_api.js"></script>
    <script src="../public/js/pos_main.js"></script>
    <script src="../public/js/dashboard_charts.js"></script>
</body>
</html>[ec2-user@ip-172-31-1-105 release-source]$


$ echo '===== JS API CALLS ====='

grep -RniE \
'fetch\(|XMLHttpRequest|ajax|url:|\.php|search_product|save_dte|process_sale_complete|wompi|pos_customers|pos_suppliers' \
frontend/pos_system/js \
frontend/pos_system/views \
--include='*.js' \
--include='*.php' \
--exclude-dir=documentacion \
--exclude-dir=test \
--exclude-dir=OLDS \
--exclude-dir=deprecated \
2>/dev/null | head -400
===== JS API CALLS =====
frontend/pos_system/js/pos_api.js:47:            const response = await fetch(url, config);
frontend/pos_system/js/pos_api.js:64:        // Asumiendo que products.php maneja el parámetro 'barcode' en GET
frontend/pos_system/js/pos_api.js:65:        return this.request('products.php?barcode=' + encodeURIComponent(barcode), 'GET', null, false);
frontend/pos_system/js/pos_api.js:72:        // Asumiendo que products.php maneja el parámetro 'search' en GET
frontend/pos_system/js/pos_api.js:73:        return this.request('products.php?search=' + encodeURIComponent(searchTerm), 'GET', null, false);
frontend/pos_system/js/pos_api.js:80:        // Asumiendo que sales.php recibe un POST con los datos de la venta
frontend/pos_system/js/pos_api.js:81:        return this.request('sales.php', 'POST', saleData);
frontend/pos_system/js/pos_api.js:92:        // return this.request('products.php?id=' + productId, 'PATCH', { action: 'decrement_stock', quantity: quantity });
frontend/pos_system/js/pos_api.js:101:            const response = await fetch('../templates/ticket.php', {
frontend/pos_system/js/pos_cart.js:113:// Instanciación global para que pos_sale.php lo reconozca
frontend/pos_system/js/pos_customers.js:2:    const API_URL = (typeof BASE_URL !== 'undefined' ? BASE_URL : '..') + '/api/v1/customers.php';
frontend/pos_system/js/pos_customers.js:68:        $.ajax({
frontend/pos_system/js/pos_customers.js:69:            url: url,
frontend/pos_system/js/pos_customers.js:105:        $.ajax({
frontend/pos_system/js/pos_customers.js:106:            url: API_URL + '?id=' + id,
frontend/pos_system/js/pos_main.js:491:        window.open('../views/pos_reports.php', '_blank');
frontend/pos_system/js/pos_main.js:497:    // Estas variables globales deberían venir del PHP (pos_sale.php)
frontend/pos_system/js/pos_products.js:2:    const API_URL = (typeof BASE_URL !== 'undefined' ? BASE_URL : '..') + '/api/v1/products.php';
frontend/pos_system/js/pos_products.js:5:    console.log('📡 API URL:', API_URL);
frontend/pos_system/js/pos_products.js:64:        $.ajax({
frontend/pos_system/js/pos_products.js:65:            url: API_URL + (isEdit ? '?id=' + productId : ''),
frontend/pos_system/js/pos_products.js:94:                console.error('❌ AJAX Error:', {
frontend/pos_system/js/pos_products.js:148:                $.ajax({
frontend/pos_system/js/pos_products.js:149:                    url: API_URL + '?id=' + id,
frontend/pos_system/js/pos_products.js:175:                        console.error('❌ Delete AJAX Error:', xhr);
frontend/pos_system/js/pos_settings.js:9:    const API_SETTINGS_URL = (typeof BASE_URL !== 'undefined' ? BASE_URL : '..') + '/api/v1/settings.php';
frontend/pos_system/js/pos_settings.js:10:    const API_USERS_URL = (typeof BASE_URL !== 'undefined' ? BASE_URL : '..') + '/api/v1/users.php';
frontend/pos_system/js/pos_settings.js:22:        $.ajax({
frontend/pos_system/js/pos_settings.js:23:            url: API_SETTINGS_URL,
frontend/pos_system/js/pos_settings.js:102:                $.ajax({
frontend/pos_system/js/pos_settings.js:103:                    url: API_SETTINGS_URL,
frontend/pos_system/js/pos_settings.js:157:        $.ajax({
frontend/pos_system/js/pos_settings.js:158:            url: API_USERS_URL,
frontend/pos_system/js/pos_settings.js:207:                    url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json'
frontend/pos_system/js/pos_settings.js:216:                    url: '//cdn.datatables.net/plug-ins/1.13.7/i18n/es-ES.json'
frontend/pos_system/js/pos_settings.js:241:        $.ajax({
frontend/pos_system/js/pos_settings.js:242:            url: API_USERS_URL + '?id=' + userId,
frontend/pos_system/js/pos_settings.js:296:                $.ajax({
frontend/pos_system/js/pos_settings.js:297:                    url: API_USERS_URL,
frontend/pos_system/js/pos_settings.js:361:        $.ajax({
frontend/pos_system/js/pos_settings.js:362:            url: API_USERS_URL,
frontend/pos_system/js/pos_ticket.js:45:        // En una aplicación real, se haría una llamada API a /templates/ticket.php
frontend/pos_system/js/pos_ticket.js:104:                // Incluir los estilos del ticket.php
frontend/pos_system/js/pos_ticket.js:105:                printWindow.document.write('<style>@media print { @page { margin: 0; size: 80mm auto; } } body { font-family: "Courier New", monospace; font-size: 12px; width: 80mm; margin: 0 auto; padding: 5mm; } /* ... otros estilos de ticket.php */</style>');
frontend/pos_system/views/BAD_pos_customers.php:12:require_once __DIR__ . '/../includes/config.php';
frontend/pos_system/views/BAD_pos_customers.php:13:require_once __DIR__ . '/../config/constants.php';
frontend/pos_system/views/BAD_pos_customers.php:14:require_once __DIR__ . '/../includes/auth.php';
frontend/pos_system/views/BAD_pos_customers.php:15:require_once __DIR__ . '/../controllers/customer_controller.php';
frontend/pos_system/views/BAD_pos_customers.php:25:// Los datos se cargan desde el controlador customer_controller.php ($customers)
frontend/pos_system/views/BAD_pos_customers.php:34:    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
frontend/pos_system/views/BAD_pos_customers.php:40:    <?php include_once __DIR__ . '/partials/header.php'; ?>
frontend/pos_system/views/BAD_pos_customers.php:41:    <?php include_once __DIR__ . '/partials/sidebar.php'; ?>
frontend/pos_system/views/BAD_pos_customers.php:197:    <?php include_once __DIR__ . '/partials/footer.php'; ?>
frontend/pos_system/views/BAD_pos_customers.php:205:    <script src="<?= BASE_URL ?>/js/pos_customers.js"></script>
frontend/pos_system/views/BAD_pos_customers.php:214:            // La lógica CRUD se maneja en ../public/js/pos_customers.js
frontend/pos_system/views/BAD_pos_products.php:12:require_once __DIR__ . '/../includes/config.php';
frontend/pos_system/views/BAD_pos_products.php:13:require_once __DIR__ . '/../config/constants.php';
frontend/pos_system/views/BAD_pos_products.php:14:require_once __DIR__ . '/../includes/auth.php';
frontend/pos_system/views/BAD_pos_products.php:15:require_once __DIR__ . '/../controllers/product_controller.php';
frontend/pos_system/views/BAD_pos_products.php:39:    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
frontend/pos_system/views/BAD_pos_products.php:46:    <?php include_once __DIR__ . '/partials/header.php'; ?>
frontend/pos_system/views/BAD_pos_products.php:47:    <?php include_once __DIR__ . '/partials/sidebar.php'; ?>
frontend/pos_system/views/BAD_pos_products.php:274:    <?php include_once __DIR__ . '/partials/footer.php'; ?>
frontend/pos_system/views/BAD_pos_products.php:300:    const API_URL = '<?= BASE_URL ?>/controllers/product_api.php';
frontend/pos_system/views/BAD_pos_products.php:318:      fetch(url)
frontend/pos_system/views/BADpos_reports.php:12:require_once __DIR__ . '/../config/constants.php';
frontend/pos_system/views/BADpos_reports.php:13:require_once __DIR__ . '/../controllers/auth_controller.php';
frontend/pos_system/views/BADpos_reports.php:14:require_once __DIR__ . '/../includes/supabase_reports.php';
frontend/pos_system/views/BADpos_reports.php:15:// Asumir que auth_controller.php define $cashier y $registerNumber
frontend/pos_system/views/BADpos_reports.php:34:    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
frontend/pos_system/views/BADpos_reports.php:40:    <?php include_once __DIR__ . '/partials/header.php'; ?>
frontend/pos_system/views/BADpos_reports.php:41:    <?php include_once __DIR__ . '/partials/sidebar.php'; ?>
frontend/pos_system/views/BADpos_reports.php:93:    <?php include_once __DIR__ . '/partials/footer.php'; ?>
frontend/pos_system/views/ajax/WORKS_1_process_sale_complete.php:3: * AJAX: process_sale_complete.php - Procesamiento Completo de Venta con Firma Electrónica
frontend/pos_system/views/ajax/WORKS_1_process_sale_complete.php:10:require_once __DIR__ . '/../../includes/config.php';
frontend/pos_system/views/ajax/WORKS_1_process_sale_complete.php:11:require_once __DIR__ . '/../../includes/supabase.php';
frontend/pos_system/views/ajax/WORKS_1_process_sale_complete.php:12:require_once __DIR__ . '/../../includes/pg_connection.php';
frontend/pos_system/views/ajax/WORKS_1_process_sale_complete.php:13:require_once __DIR__ . '/../../includes/signer/signer_local.php';
frontend/pos_system/views/ajax/WORKS_1_process_sale_complete.php:14:require_once __DIR__ . '/../../includes/signer/signer_goes.php';
frontend/pos_system/views/ajax/WORKS_1_process_sale_complete.php:62:    $row = $stmt->fetch();
frontend/pos_system/views/ajax/WORKS_1_process_sale_complete.php:125:    error_log("process_sale_complete ERROR: " . $e->getMessage());
frontend/pos_system/views/ajax/WORKS_process_sale_complete.php:3: * AJAX: process_sale_complete.php - Procesamiento Completo de Venta con Firma Electrónica
frontend/pos_system/views/ajax/WORKS_process_sale_complete.php:11:require_once __DIR__ . '/../../includes/config.php';
frontend/pos_system/views/ajax/WORKS_process_sale_complete.php:12:require_once __DIR__ . '/../../includes/supabase.php';
frontend/pos_system/views/ajax/WORKS_process_sale_complete.php:13:require_once __DIR__ . '/../../includes/signer/signer_local.php';
frontend/pos_system/views/ajax/WORKS_process_sale_complete.php:14:require_once __DIR__ . '/../../includes/signer/signer_goes.php';
frontend/pos_system/views/ajax/WORKS_process_sale_complete.php:149:    error_log("Error en process_sale_complete.php: " . $e->getMessage());
frontend/pos_system/views/ajax/pos_sale.js:103:        const response = await fetch('ajax/search_product.php', { // Fetch product data
frontend/pos_system/views/ajax/pos_sale.js:451:            const response = await fetch('ajax/validate_coupon.php', {
frontend/pos_system/views/ajax/pos_sale.js:489:            const response = await fetch('ajax/validate_giftcard.php', {
frontend/pos_system/views/ajax/pos_sale.js:845:        const response = await fetch('ajax/process_sale_complete.php', {
frontend/pos_system/views/ajax/pos_sale.js:869:            window.open(`print_ticket.php?id=${result.codigoGeneracion}`, '_blank');
frontend/pos_system/views/ajax/pos_saleBAD3126.js:97:        const response = await fetch('ajax/search_product.php', {
frontend/pos_system/views/ajax/pos_saleBAD3126.js:445:            const response = await fetch('ajax/validate_coupon.php', {
frontend/pos_system/views/ajax/pos_saleBAD3126.js:483:            const response = await fetch('ajax/validate_giftcard.php', {
frontend/pos_system/views/ajax/pos_saleBAD3126.js:839:        const response = await fetch('ajax/process_sale_complete.php', {
frontend/pos_system/views/ajax/pos_saleBAD3126.js:863:            window.open(`print_ticket.php?id=${result.codigoGeneracion}`, '_blank');
frontend/pos_system/views/ajax/process_sale_complete copy.php:3: * AJAX: process_sale_complete.php
frontend/pos_system/views/ajax/process_sale_complete copy.php:11:require_once __DIR__ . '/../../includes/config.php';
frontend/pos_system/views/ajax/process_sale_complete copy.php:12:require_once __DIR__ . '/../../includes/supabase.php';
frontend/pos_system/views/ajax/process_sale_complete copy.php:13:require_once __DIR__ . '/../../includes/pg_connection.php';
frontend/pos_system/views/ajax/process_sale_complete copy.php:52:    $row = $stmt->fetch();
frontend/pos_system/views/ajax/process_sale_complete copy.php:96:    require_once __DIR__ . '/../../includes/signer/signer_local.php';
frontend/pos_system/views/ajax/process_sale_complete copy.php:134:    /* require_once __DIR__ . '/../../includes/goes_signer/signer_goes.php';
frontend/pos_system/views/ajax/process_sale_complete copy.php:148:    require_once __DIR__ . '/../../includes/goes_signer/signer_goes.php';
frontend/pos_system/views/ajax/process_sale_complete copy.php:214:    require_once __DIR__ . '/../../includes/utils/qr_maker.php';
frontend/pos_system/views/ajax/process_sale_complete copy.php:215:    require_once __DIR__ . '/../../includes/utils/ticket_printer.php';
frontend/pos_system/views/ajax/process_sale_complete copy.php:216:    require_once __DIR__ . '/../../includes/utils/pdf_generator.php';
frontend/pos_system/views/ajax/process_sale_complete copy.php:217:    require_once __DIR__ . '/../../includes/utils/mail_sender.php';
frontend/pos_system/views/ajax/process_sale_complete copy.php:241:            require_once __DIR__ . '/../../includes/utils/sms_notifier.php';
frontend/pos_system/views/ajax/process_sale_complete copy.php:274:    error_log('process_sale_complete ERROR: ' . $e->getMessage());
frontend/pos_system/views/ajax/process_sale_complete.php:3: * AJAX: process_sale_complete.php
frontend/pos_system/views/ajax/process_sale_complete.php:20:require_once __DIR__ . '/../../includes/config.php';
frontend/pos_system/views/ajax/process_sale_complete.php:21:require_once __DIR__ . '/../../includes/auth.php';
frontend/pos_system/views/ajax/process_sale_complete.php:22:require_once __DIR__ . '/../../includes/supabase.php';
frontend/pos_system/views/ajax/process_sale_complete.php:23:require_once __DIR__ . '/../../includes/pg_connection.php';
frontend/pos_system/views/ajax/process_sale_complete.php:24:require_once __DIR__ . '/../../includes/signer/utils/audit_logger.php';
frontend/pos_system/views/ajax/process_sale_complete.php:25:require_once __DIR__ . '/../../includes/signer/utils/dte_validator_new.php';
frontend/pos_system/views/ajax/process_sale_complete.php:54:    $row = $stmt->fetch();
frontend/pos_system/views/ajax/process_sale_complete.php:89:    require_once __DIR__ . '/../../includes/signer/signer_local.php';
frontend/pos_system/views/ajax/process_sale_complete.php:95:    require_once __DIR__ . '/../../includes/signer/signer_goes.php';
frontend/pos_system/views/ajax/process_sale_complete.php:104:    require_once __DIR__ . '/../../includes/signer/utils/qr_maker.php';
frontend/pos_system/views/ajax/process_sale_complete.php:105:    require_once __DIR__ . '/../../includes/signer/utils/ticket_printer.php';
frontend/pos_system/views/ajax/process_sale_complete.php:106:    require_once __DIR__ . '/../../includes/signer/utils/pdf_generator.php';
frontend/pos_system/views/ajax/process_sale_complete.php:107:    require_once __DIR__ . '/../../includes/signer/utils/mail_sender.php';
frontend/pos_system/views/ajax/process_sale_complete.php:116:            require_once __DIR__ . '/../../includes/signer/utils/sms_notifier.php';
frontend/pos_system/views/ajax/process_sale_complete.php:137:    error_log('process_sale_complete ERROR: ' . $e->getMessage());
frontend/pos_system/views/ajax/products.js:3:const API_URL = '/controllers/product_api.php';
frontend/pos_system/views/ajax/products.js:31:  fetch(API_URL, {
frontend/pos_system/views/ajax/products.js:61:    fetch(API_URL, {
frontend/pos_system/views/ajax/save_dte.php:4:require_once __DIR__ . '/../../includes/config.php';
frontend/pos_system/views/ajax/save_dte.php:5:require_once __DIR__ . '/../../includes/auth.php';
frontend/pos_system/views/ajax/save_dte.php:6:require_once __DIR__ . '/../../includes/supabase.php';
frontend/pos_system/views/ajax/save_dte.php:7:require_once __DIR__ . '/../../includes/pg_connection.php';
frontend/pos_system/views/ajax/save_dte.php:20: foreach($items as $item){$codigoProducto=trim((string)($item['codigo']??$item['codigoProducto']??''));$cantidad=(float)($item['cantidad']??0);if($codigoProducto===''||$cantidad<=0)continue;$q=$pdo->prepare('SELECT * FROM dte_productos WHERE company_id=? AND (codigo_producto=? OR codigo_barras=? OR sku=?) AND borrado_logico=0 LIMIT 1');$q->execute([$companyId,$codigoProducto,$codigoProducto,$codigoProducto]);$product=$q->fetch();if(!$product){$stockWarnings[]='Producto no encontrado: '.$codigoProducto;continue;}$stockBefore=(float)($product['stock_actual']??0);$stockAfter=$stockBefore-$cantidad;if($stockAfter<0)throw new RuntimeException('Stock insuficiente para producto '.$codigoProducto.'. Disponible: '.$stockBefore.'.');$pdo->prepare('UPDATE dte_productos SET stock_actual=?, actualizado_el=CURRENT_TIMESTAMP WHERE id=? AND company_id=?')->execute([$stockAfter,$product['id'],$companyId]);$pdo->prepare('INSERT INTO inventory_movements (company_id,producto_id,tipo,cantidad,stock_anterior,stock_nuevo,referencia,usuario_id) VALUES (?,?,?,?,?,?,?,?)')->execute([$companyId,$product['id'],'sale',$cantidad,$stockBefore,$stockAfter,$codigo,$usuarioId]);if($ventaId!==null){$d=$pdo->prepare('INSERT INTO detalle_ventas (company_id,venta_id,producto_id,cantidad,precio_unitario,subtotal,iva,total) VALUES (?,?,?,?,?,?,?,?)');$unit=(float)($item['precioUni']??$item['precio']??0);$sub=(float)($item['ventaGravada']??$item['ventaExenta']??($unit*$cantidad));$iva=(float)($item['ivaItem']??0);$d->execute([$companyId,$ventaId,$product['id'],$cantidad,$unit,$sub,$iva,$sub+$iva]);}}
frontend/pos_system/views/ajax/save_dte.php:23:}catch(Throwable $e){if(isset($pdo)&&$pdo->inTransaction())$pdo->rollBack();error_log('[save_dte] '.$e->getMessage());http_response_code(500);echo json_encode(['success'=>false,'error'=>$e->getMessage()]);}
frontend/pos_system/views/ajax/save_dteB.php:3: * AJAX: save_dte.php - Procesamiento de Venta e Inventario
frontend/pos_system/views/ajax/save_dteB.php:10:require_once __DIR__ . '/../../includes/config.php';
frontend/pos_system/views/ajax/save_dteB.php:11:require_once __DIR__ . '/../../includes/supabase.php';
frontend/pos_system/views/ajax/save_dteB.php:112:    error_log('Error en save_dte.php: ' . $e->getMessage() . ' - ' . $e->getTraceAsString());
frontend/pos_system/views/ajax/save_dte_NOUUID.php:3: * AJAX: save_dte.php - Procesamiento de Venta e Inventario
frontend/pos_system/views/ajax/save_dte_NOUUID.php:9:require_once __DIR__ . '/../../includes/config.php';
frontend/pos_system/views/ajax/save_dte_NOUUID.php:10:require_once __DIR__ . '/../../includes/supabase.php';
frontend/pos_system/views/ajax/search_product.php:2:require_once __DIR__ . '/../../includes/config.php';
frontend/pos_system/views/ajax/search_product.php:3:require_once __DIR__ . '/../../includes/supabase.php';
frontend/pos_system/views/ajax/send_receipt_email.php:3: * AJAX: send_receipt_email.php
frontend/pos_system/views/ajax/send_receipt_email.php:31:    require_once __DIR__ . '/../../includes/config.php';
frontend/pos_system/views/ajax/wompi.js:2: * Wompi Payment Module
frontend/pos_system/views/ajax/wompi.js:4: * Integración con API de Wompi para procesamiento de pagos con tarjeta
frontend/pos_system/views/ajax/wompi.js:7: * const payment = await WompiPayment.initializePayment({
frontend/pos_system/views/ajax/wompi.js:14:const WompiPayment = (() => {
frontend/pos_system/views/ajax/wompi.js:20:     * Inicializar configuración de Wompi
frontend/pos_system/views/ajax/wompi.js:24:            const response = await fetch('ajax/wompi_payment.php?action=get-config');
frontend/pos_system/views/ajax/wompi.js:28:                throw new Error('No se pudo cargar la configuración de Wompi');
frontend/pos_system/views/ajax/wompi.js:32:            console.log('✓ Wompi configurado correctamente');
frontend/pos_system/views/ajax/wompi.js:36:            console.error('Error inicializando Wompi:', error);
frontend/pos_system/views/ajax/wompi.js:56:            const response = await fetch('ajax/wompi_payment.php?action=create-payment', {
frontend/pos_system/views/ajax/wompi.js:87:            // Crear ventana modal/popup para Wompi
frontend/pos_system/views/ajax/wompi.js:95:                'WompiPayment',
frontend/pos_system/views/ajax/wompi.js:141:            const response = await fetch('ajax/wompi_payment.php?action=verify-payment', {
frontend/pos_system/views/ajax/wompi.js:183:                        throw new Error('No se pudo inicializar Wompi');
frontend/pos_system/views/ajax/wompi_payment.php:3: * Endpoint para gestionar pagos con Wompi
frontend/pos_system/views/ajax/wompi_payment.php:6: * 1. Obtención de token de Wompi
frontend/pos_system/views/ajax/wompi_payment.php:9: * 4. Webhook para notificaciones de Wompi
frontend/pos_system/views/ajax/wompi_payment.php:12:require_once __DIR__ . '/../../includes/config.php';
frontend/pos_system/views/ajax/wompi_payment.php:13:require_once __DIR__ . '/../../config/constants.php';
frontend/pos_system/views/ajax/wompi_payment.php:14:require_once __DIR__ . '/../../config/wompi.php';
frontend/pos_system/views/ajax/wompi_payment.php:25:         * Obtener credenciales seguras de Wompi para el cliente
frontend/pos_system/views/ajax/wompi_payment.php:29:            $wompiConfig = require __DIR__ . '/../../config/wompi.php';
frontend/pos_system/views/ajax/wompi_payment.php:31:            if (!$wompiConfig['enabled']) {
frontend/pos_system/views/ajax/wompi_payment.php:32:                throw new Exception('Wompi no está configurado', 500);
frontend/pos_system/views/ajax/wompi_payment.php:37:                'clientId' => $wompiConfig['clientId'],
frontend/pos_system/views/ajax/wompi_payment.php:38:                'apiUrl' => $wompiConfig['api']['apiUrl'],
frontend/pos_system/views/ajax/wompi_payment.php:39:                'tokenUrl' => $wompiConfig['api']['tokenUrl']
frontend/pos_system/views/ajax/wompi_payment.php:70:            // Llamar a API de Wompi para crear enlace de pago
frontend/pos_system/views/ajax/wompi_payment.php:71:            $paymentLink = createWompiPaymentLink($amount, $email, $description, $reference);
frontend/pos_system/views/ajax/wompi_payment.php:101:            $paymentStatus = verifyWompiPayment($reference);
frontend/pos_system/views/ajax/wompi_payment.php:116:         * Webhook: Notificación de Wompi (POST)
frontend/pos_system/views/ajax/wompi_payment.php:127:            // Verificar firma del webhook (según documentación de Wompi)
frontend/pos_system/views/ajax/wompi_payment.php:128:            $signature = $_SERVER['HTTP_X_WOMPI_SIGNATURE'] ?? null;
frontend/pos_system/views/ajax/wompi_payment.php:130:            if (!verifyWompiSignature($payload, $signature)) {
frontend/pos_system/views/ajax/wompi_payment.php:161: * Crea un enlace de pago en Wompi
frontend/pos_system/views/ajax/wompi_payment.php:163:function createWompiPaymentLink($amount, $email, $description, $reference) {
frontend/pos_system/views/ajax/wompi_payment.php:165:        $wompiConfig = require __DIR__ . '/../../config/wompi.php';
frontend/pos_system/views/ajax/wompi_payment.php:167:        $clientId = $wompiConfig['clientId'];
frontend/pos_system/views/ajax/wompi_payment.php:168:        $clientSecret = $wompiConfig['clientSecret'];
frontend/pos_system/views/ajax/wompi_payment.php:171:        $token = getWompiAccessToken($clientId, $clientSecret);
frontend/pos_system/views/ajax/wompi_payment.php:174:            error_log('No se pudo obtener token de Wompi');
frontend/pos_system/views/ajax/wompi_payment.php:184:            'urlNotificacion' => getBaseUrl() . '/views/ajax/wompi_payment.php?action=webhook'
frontend/pos_system/views/ajax/wompi_payment.php:189:            CURLOPT_URL => 'https://api.wompi.sv/EnlacePago',
frontend/pos_system/views/ajax/wompi_payment.php:206:            error_log('Error de Wompi: ' . $response);
frontend/pos_system/views/ajax/wompi_payment.php:215:        error_log('Error en createWompiPaymentLink: ' . $e->getMessage());
frontend/pos_system/views/ajax/wompi_payment.php:221: * Obtiene token de acceso desde Wompi
frontend/pos_system/views/ajax/wompi_payment.php:223:function getWompiAccessToken($clientId, $clientSecret) {
frontend/pos_system/views/ajax/wompi_payment.php:229:            'audience' => 'wompi_api'
frontend/pos_system/views/ajax/wompi_payment.php:234:            CURLOPT_URL => 'https://id.wompi.sv/connect/token',
frontend/pos_system/views/ajax/wompi_payment.php:250:            error_log('Error obteniendo token de Wompi: ' . $response);
frontend/pos_system/views/ajax/wompi_payment.php:259:        error_log('Error en getWompiAccessToken: ' . $e->getMessage());
frontend/pos_system/views/ajax/wompi_payment.php:267:function verifyWompiPayment($reference) {
frontend/pos_system/views/ajax/wompi_payment.php:269:        // TODO: Implementar verificación con API de Wompi
frontend/pos_system/views/ajax/wompi_payment.php:277:        error_log('Error en verifyWompiPayment: ' . $e->getMessage());
frontend/pos_system/views/ajax/wompi_payment.php:283: * Verifica la firma del webhook de Wompi
frontend/pos_system/views/ajax/wompi_payment.php:285:function verifyWompiSignature($payload, $signature) {
frontend/pos_system/views/ajax/wompi_payment.php:287:        $wompiConfig = require __DIR__ . '/../../config/wompi.php';
frontend/pos_system/views/ajax/wompi_payment.php:288:        $clientSecret = $wompiConfig['clientSecret'];
frontend/pos_system/views/ajax/wompi_payment.php:296:        error_log('Error en verifyWompiSignature: ' . $e->getMessage());
frontend/pos_system/views/partials/footer.php:11:// Se utiliza APP_NAME y APP_VERSION de constants.php
frontend/pos_system/views/partials/header.php:12:// en la vista principal (pos_sale.php o dashboard.php) antes de incluir este partial.
frontend/pos_system/views/partials/header.php:19:            <a class="navbar-brand d-flex align-items-center" href="/dashboard.php">
frontend/pos_system/views/partials/header.php:38:                <a href="/dashboard.php" class="btn btn-outline-info me-2" title="Dashboard">
frontend/pos_system/views/partials/header.php:46:                <a href="/logout.php" class="btn btn-danger" title="Cerrar Sesión">
frontend/pos_system/views/partials/sidebar.php:29:                <a href="/views/pos_dashboard.php" class="nav-link text-white active" aria-current="page">
frontend/pos_system/views/partials/sidebar.php:34:                <a href="/views/pos_sale.php" class="nav-link text-white">
frontend/pos_system/views/partials/sidebar.php:39:                <a href="/views/pos_products.php" class="nav-link text-white">
frontend/pos_system/views/partials/sidebar.php:44:                <a href="/views/pos_customers.php" class="nav-link text-white">
frontend/pos_system/views/partials/sidebar.php:49:                <a href="/views/pos_reports.php" class="nav-link text-white">
frontend/pos_system/views/partials/sidebar.php:54:                <a href="/views/pos_settings.php" class="nav-link text-white">
frontend/pos_system/views/partials/sidebar.php:61:            <a href="/logout.php" class="btn btn-outline-danger w-100">
frontend/pos_system/views/pos_customers copy.php:10:require_once __DIR__ . '/../includes/config.php';
frontend/pos_system/views/pos_customers copy.php:11:require_once __DIR__ . '/../config/constants.php';
frontend/pos_system/views/pos_customers copy.php:12:require_once __DIR__ . '/../includes/auth.php';
frontend/pos_system/views/pos_customers copy.php:13:require_once __DIR__ . '/../includes/pg_connection.php';
frontend/pos_system/views/pos_customers copy.php:26:   AJAX HANDLER CLIENTES
frontend/pos_system/views/pos_customers copy.php:28:if (isset($_POST['ajax']) && $_POST['ajax'] === '1') {
frontend/pos_system/views/pos_customers copy.php:39:                     FROM pos_customers
frontend/pos_system/views/pos_customers copy.php:47:                $stmt = $db->prepare("SELECT * FROM pos_customers WHERE id = :id");
frontend/pos_system/views/pos_customers copy.php:49:                echo json_encode(['success' => true, 'data' => $stmt->fetch()]);
frontend/pos_system/views/pos_customers copy.php:54:                    "INSERT INTO pos_customers
frontend/pos_system/views/pos_customers copy.php:77:                    "UPDATE pos_customers SET
frontend/pos_system/views/pos_customers copy.php:110:                    "UPDATE pos_customers SET borrado_logico = true WHERE id = :id"
frontend/pos_system/views/pos_customers copy.php:133:<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
frontend/pos_system/views/pos_customers copy.php:141:<?php include __DIR__ . '/partials/header.php'; ?>
frontend/pos_system/views/pos_customers copy.php:142:<?php include __DIR__ . '/partials/sidebar.php'; ?>
frontend/pos_system/views/pos_customers copy.php:170:<input type="hidden" name="ajax" value="1">
frontend/pos_system/views/pos_customers copy.php:207:<?php include __DIR__ . '/partials/footer.php'; ?>
frontend/pos_system/views/pos_customers copy.php:211: $.post('',{ajax:1,action:'list'},res=>{
frontend/pos_system/views/pos_customers copy.php:237: $.post('',{ajax:1,action:'get',id},res=>{
frontend/pos_system/views/pos_customers copy.php:247:   $.post('',{ajax:1,action:'delete',id},()=>loadCustomers());
frontend/pos_system/views/pos_customers.php:2:declare(strict_types=1);require_once __DIR__.'/../config/constants.php';require_once __DIR__.'/../includes/auth.php';require_once __DIR__.'/../includes/pg_connection.php';checkPOSAuth();$db=pg_pool();$companyId=(int)($_SESSION['empresa_id']??0);
frontend/pos_system/views/pos_customers.php:3:if(isset($_POST['ajax'])&&$_POST['ajax']==='1'){header('Content-Type: application/json');try{$action=$_POST['action']??'';
frontend/pos_system/views/pos_customers.php:5:if($action==='get'){$s=$db->prepare('SELECT * FROM mh_cliente_consumidor WHERE id=? AND company_id=? LIMIT 1');$s->execute([(int)$_POST['id'],$companyId]);$r=$s->fetch();if($r){$r['nombres']=trim(($r['p_nombre']??'').' '.($r['s_nombre']??''));$r['apellidos']=trim(($r['p_apellido']??'').' '.($r['s_apellido']??''));}echo json_encode(['success'=>true,'data'=>$r]);exit;}
frontend/pos_system/views/pos_customers.php:10:?><!DOCTYPE html><html lang="es"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0"><title><?=htmlspecialchars(APP_NAME)?> | Clientes</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"><link rel="stylesheet" href="<?=BASE_URL?>/css/pos_styles.css"><script src="https://code.jquery.com/jquery-3.7.1.min.js"></script><script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script><script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script></head><body class="d-flex flex-column min-vh-100"><?php include __DIR__.'/partials/header.php';?><?php include __DIR__.'/partials/sidebar.php';?><main class="container-fluid mt-4 flex-grow-1"><h2 class="mb-4"><i class="fas fa-users me-2"></i> Gestión de Clientes</h2><div class="card shadow-sm"><div class="card-header bg-white d-flex justify-content-between align-items-center"><h5 class="mb-0">Base de Datos de Clientes</h5><button class="btn btn-success" id="btnNewCustomer"><i class="fas fa-user-plus"></i> Agregar Cliente</button></div><div class="card-body"><table class="table table-striped" id="customersTable"><thead><tr><th>ID</th><th>Nombres</th><th>Apellidos</th><th>DUI/NIT</th><th>Teléfono</th><th>Email</th><th>Acciones</th></tr></thead><tbody></tbody></table></div></div></main><div class="modal fade" id="customerModal"><div class="modal-dialog modal-lg"><div class="modal-content"><form id="customerForm"><div class="modal-header bg-success text-white"><h5>Cliente</h5></div><div class="modal-body"><input type="hidden" name="ajax" value="1"><input type="hidden" name="action" id="action"><input type="hidden" name="id" id="id"><div class="row"><div class="col-md-6 mb-2"><input class="form-control" name="nombres" placeholder="Nombres" required></div><div class="col-md-6 mb-2"><input class="form-control" name="apellidos" placeholder="Apellidos" required></div></div><div class="row"><div class="col-md-4 mb-2"><input class="form-control" name="dui" placeholder="DUI"></div><div class="col-md-4 mb-2"><input class="form-control" name="nit" placeholder="NIT"></div><div class="col-md-4 mb-2"><input class="form-control" name="nrc" placeholder="NRC"></div></div><div class="row"><div class="col-md-6 mb-2"><input class="form-control" name="telefono" placeholder="Teléfono"></div><div class="col-md-6 mb-2"><input class="form-control" name="email" type="email" placeholder="Email"></div></div><textarea class="form-control mb-2" name="direccion" placeholder="Dirección"></textarea><div class="row"><div class="col-md-6 mb-2"><input class="form-control" name="municipio" placeholder="Municipio"></div><div class="col-md-6 mb-2"><input class="form-control" name="departamento" placeholder="Departamento"></div></div><label><input type="checkbox" name="active" checked> Cliente Activo</label></div><div class="modal-footer"><button type="submit" class="btn btn-success">Guardar</button></div></form></div></div></div><script>function loadCustomers(){$.post('',{ajax:1,action:'list'},res=>{const tb=$('#customersTable tbody').html('');res.data.forEach(c=>tb.append(`<tr><td>${c.id}</td><td>${c.nombres}</td><td>${c.apellidos}</td><td>${c.dui||''} ${c.nit||''}</td><td>${c.telefono||''}</td><td>${c.email||''}</td><td><button class='btn btn-sm btn-warning' onclick='edit(${c.id})'><i class='fas fa-edit'></i></button> <button class='btn btn-sm btn-danger' onclick='del(${c.id})'><i class='fas fa-trash'></i></button></td></tr>`);},'json');}$('#btnNewCustomer').click(()=>{$('#customerForm')[0].reset();$('#action').val('create');new bootstrap.Modal('#customerModal').show();});function edit(id){$.post('',{ajax:1,action:'get',id},res=>{Object.keys(res.data).forEach(k=>$(`[name=${k}]`).val(res.data[k]));$('#action').val('update');new bootstrap.Modal('#customerModal').show();},'json');}function del(id){Swal.fire({title:'¿Eliminar cliente?',showCancelButton:true}).then(r=>{if(r.isConfirmed)$.post('',{ajax:1,action:'delete',id},()=>loadCustomers());});}$('#customerForm').submit(e=>{e.preventDefault();$.post('',$('#customerForm').serialize(),res=>{if(res.success){bootstrap.Modal.getInstance(document.getElementById('customerModal')).hide();loadCustomers();}},'json');});$(loadCustomers);</script></body></html>
frontend/pos_system/views/pos_dashboard.php:12:require_once __DIR__ . '/../config/constants.php';
frontend/pos_system/views/pos_dashboard.php:13:require_once __DIR__ . '/../includes/supabase_reports.php'; // Para inicializar Supabase
frontend/pos_system/views/pos_dashboard.php:14:require_once __DIR__ . '/../includes/init_report.php';
frontend/pos_system/views/pos_dashboard.php:15:require_once __DIR__ . '/../controllers/auth_controller.php'; // Verifica sesión
frontend/pos_system/views/pos_dashboard.php:16:require_once __DIR__ . '/../controllers/dashboard_controller.php'; // Controladores de datos del dashboard
frontend/pos_system/views/pos_dashboard.php:18:// Asumir que auth_controller.php define $cashier y $registerNumber
frontend/pos_system/views/pos_dashboard.php:24:// En un sistema real, estas funciones harían llamadas a Supabase a través del cliente inicializado en database.php
frontend/pos_system/views/pos_dashboard.php:35:    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
frontend/pos_system/views/pos_dashboard.php:40:    <?php include_once __DIR__ . '/partials/header.php'; ?>
frontend/pos_system/views/pos_dashboard.php:41:    <?php include_once __DIR__ . '/partials/sidebar.php'; ?>
frontend/pos_system/views/pos_dashboard.php:149:    <?php include_once __DIR__ . '/partials/footer.php'; ?>
frontend/pos_system/views/pos_products.php:10:require_once __DIR__ . '/../includes/config.php';
frontend/pos_system/views/pos_products.php:11:require_once __DIR__ . '/../config/constants.php';
frontend/pos_system/views/pos_products.php:12:require_once __DIR__ . '/../includes/auth.php';
frontend/pos_system/views/pos_products.php:13:require_once __DIR__ . '/../includes/pg_connection.php';
frontend/pos_system/views/pos_products.php:26:   AJAX HANDLER
frontend/pos_system/views/pos_products.php:28:if (isset($_POST['ajax']) && $_POST['ajax'] === '1') {
frontend/pos_system/views/pos_products.php:66:                echo json_encode(['success' => true, 'data' => $stmt->fetch()]);
frontend/pos_system/views/pos_products.php:161:<input type="hidden" name="ajax" value="1">
frontend/pos_system/views/pos_products.php:185:  $.post('', { ajax: 1, action: 'list', page }, res => {
frontend/pos_system/views/pos_products.php:220:  $.post('', { ajax:1, action:'get', id }, res => {
frontend/pos_system/views/pos_products.php:232:      $.post('', { ajax:1, action:'delete', id }, () => loadProducts());
frontend/pos_system/views/pos_reports.php:9:require_once __DIR__ . '/../config/constants.php';
frontend/pos_system/views/pos_reports.php:10:require_once __DIR__ . '/../includes/pg_connection.php';
frontend/pos_system/views/pos_reports.php:11:require_once __DIR__ . '/../includes/auth.php';
frontend/pos_system/views/pos_reports.php:95:<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
frontend/pos_system/views/pos_reports.php:101:<?php include __DIR__ . '/partials/header.php'; ?>
frontend/pos_system/views/pos_reports.php:102:<?php include __DIR__ . '/partials/sidebar.php'; ?>
frontend/pos_system/views/pos_reports.php:170:<?php include __DIR__ . '/partials/footer.php'; ?>
frontend/pos_system/views/pos_sale.php:12:require_once __DIR__ . '/../includes/config.php';
frontend/pos_system/views/pos_sale.php:13:require_once __DIR__ . '/../config/constants.php';
frontend/pos_system/views/pos_sale.php:14:require_once __DIR__ . '/../config/routes.php';
frontend/pos_system/views/pos_sale.php:15:require_once __DIR__ . '/../includes/supabase.php';
frontend/pos_system/views/pos_sale.php:16:require_once __DIR__ . '/../includes/auth.php';
frontend/pos_system/views/pos_sale.php:105:    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
frontend/pos_system/views/pos_sale.php:502:    <script src="ajax/wompi.js"></script>
frontend/pos_system/views/pos_sale.php:548:    numeroControl: null,  // Se generará en el servidor en process_sale_complete.php
frontend/pos_system/views/pos_sale.php:610:        const response = await fetch('ajax/search_product.php', {
frontend/pos_system/views/pos_sale.php:958:            const response = await fetch('ajax/validate_coupon.php', {
frontend/pos_system/views/pos_sale.php:996:            const response = await fetch('ajax/validate_giftcard.php', {
frontend/pos_system/views/pos_sale.php:1136: * Integración con Wompi para procesar pagos
frontend/pos_system/views/pos_sale.php:1148:                        <small>Se abrirá una ventana segura de <strong>Wompi</strong> para procesar el pago</small>
frontend/pos_system/views/pos_sale.php:1170:            title: 'Conectando con Wompi...',
frontend/pos_system/views/pos_sale.php:1178:        // Procesar el pago con Wompi
frontend/pos_system/views/pos_sale.php:1179:        const paymentResult = await WompiPayment.processPayment({
frontend/pos_system/views/pos_sale.php:1206:                method: 'Wompi',
frontend/pos_system/views/pos_sale.php:1233:                    method: 'Wompi',
frontend/pos_system/views/pos_sale.php:1286: * IMPORTANTE 2: numeroControl se genera en el SERVIDOR (process_sale_complete.php)
frontend/pos_system/views/pos_sale.php:1532:        const response = await fetch('ajax/process_sale_complete.php', {
frontend/pos_system/views/pos_sale.php:1560:                `print_ticket.php?id=${result.codigoGeneracion}`,
frontend/pos_system/views/pos_sale.php:1566:            fetch('ajax/send_receipt_email.php', {
frontend/pos_system/views/pos_sale.php:1957://         header('Location: ' . BASE_URL . '/login.php');
frontend/pos_system/views/pos_settings.php:13:require_once __DIR__ . '/../config/constants.php';
frontend/pos_system/views/pos_settings.php:14:// require_once __DIR__ . '/../config/database.php';
frontend/pos_system/views/pos_settings.php:15:require_once __DIR__ . '/../controllers/auth_controller.php';
frontend/pos_system/views/pos_settings.php:16:// require_once __DIR__ . '/../controllers/settings_controller.php';
frontend/pos_system/views/pos_settings.php:18:// Asumir que auth_controller.php define $cashier y $registerNumber
frontend/pos_system/views/pos_settings.php:25://     header('Location: pos_dashboard.php');
frontend/pos_system/views/pos_settings.php:39:    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
frontend/pos_system/views/pos_settings.php:45:    <?php include_once __DIR__ . '/partials/header.php'; ?>
frontend/pos_system/views/pos_settings.php:46:    <?php include_once __DIR__ . '/partials/sidebar.php'; ?>
frontend/pos_system/views/pos_settings.php:221:    <?php include_once __DIR__ . '/partials/footer.php'; ?>
frontend/pos_system/views/pos_settings.php:235:             // después de que se carguen los datos por AJAX.
frontend/pos_system/views/pos_suppliers.php:2:declare(strict_types=1);require_once __DIR__.'/../config/constants.php';require_once __DIR__.'/../includes/pg_connection.php';require_once __DIR__.'/../includes/auth.php';checkPOSAuth();$db=pg_pool();$companyId=(int)($_SESSION['empresa_id']??0);
frontend/pos_system/views/pos_suppliers.php:3:if($_SERVER['REQUEST_METHOD']==='POST'&&isset($_POST['ajax'])){$action=$_POST['action']??'';
frontend/pos_system/views/pos_suppliers.php:8:?><!DOCTYPE html><html lang="es"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"><title><?=htmlspecialchars(APP_NAME)?> | Proveedores</title><link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet"><link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"><link rel="stylesheet" href="<?=BASE_URL?>/css/pos_styles.css"></head><body class="d-flex flex-column min-vh-100"><?php include __DIR__.'/partials/header.php';?><?php include __DIR__.'/partials/sidebar.php';?><main class="container-fluid mt-4 flex-grow-1"><h2><i class="fas fa-truck me-2"></i> Proveedores</h2><button class="btn btn-success mb-3" onclick="openModal()"><i class="fas fa-plus"></i> Nuevo Proveedor</button><table class="table table-striped"><thead><tr><th>ID</th><th>Proveedor</th><th>NIT</th><th>Teléfono</th><th>Email</th><th>Acciones</th></tr></thead><tbody id="providersTable"><?php foreach($providers as $p):?><tr><td><?= (int)$p['id']?></td><td><?=htmlspecialchars((string)$p['proveedor'])?></td><td><?=htmlspecialchars((string)$p['nit'])?></td><td><?=htmlspecialchars((string)$p['telefono'])?></td><td><?=htmlspecialchars((string)$p['email'])?></td><td><button class="btn btn-warning btn-sm" onclick='editProvider(<?=json_encode($p)?>)'><i class="fas fa-edit"></i></button> <button class="btn btn-danger btn-sm" onclick="deleteProvider(<?= (int)$p['id']?>)"><i class="fas fa-trash"></i></button></td></tr><?php endforeach;?></tbody></table></main><div class="modal fade" id="providerModal"><div class="modal-dialog modal-lg"><div class="modal-content"><form id="providerForm"><div class="modal-header bg-success text-white"><h5 class="modal-title">Proveedor</h5><button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button></div><div class="modal-body"><input type="hidden" name="id" id="id"><div class="row g-3"><input class="form-control" name="razon_social" placeholder="Razón Social"><input class="form-control" name="nombre_comercial" placeholder="Nombre Comercial"><input class="form-control" name="nit" placeholder="NIT"><input class="form-control" name="telefono" placeholder="Teléfono"><input class="form-control" name="email" placeholder="Email"><textarea class="form-control" name="direccion" placeholder="Dirección"></textarea></div></div><div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button><button class="btn btn-success">Guardar</button></div></form></div></div></div><script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script><script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script><script>const modal=new bootstrap.Modal('#providerModal');function openModal(){document.getElementById('providerForm').reset();document.getElementById('id').value='';modal.show();}function editProvider(p){openModal();Object.keys(p).forEach(k=>{const el=document.querySelector(`[name="${k}"]`);if(el)el.value=p[k]??'';});}document.getElementById('providerForm').onsubmit=e=>{e.preventDefault();const fd=new FormData(e.target);fd.append('ajax','1');fd.append('action','save');fetch('',{method:'POST',body:fd}).then(r=>r.json()).then(x=>{if(!x.success)throw new Error(x.error||'Error');location.reload();}).catch(err=>Swal.fire({icon:'error',title:'Error',text:err.message}));};function deleteProvider(id){Swal.fire({icon:'warning',title:'¿Eliminar proveedor?',showCancelButton:true}).then(r=>{if(r.isConfirmed)fetch('',{method:'POST',body:new URLSearchParams({ajax:'1',action:'delete',id:String(id)})}).then(r=>r.json()).then(x=>{if(x.success)location.reload();else throw new Error(x.error||'Error');}).catch(err=>Swal.fire({icon:'error',title:'Error',text:err.message}));});}</script></body></html>
frontend/pos_system/views/print_ticket.php:8:require_once __DIR__ . '/../includes/signer/utils/ticket_printer.php';
frontend/pos_system/views/print_ticket.php:41:    // For now, let's assume it should exist since process_sale_complete generates it.
[ec2-user@ip-172-31-1-105 release-source]$




