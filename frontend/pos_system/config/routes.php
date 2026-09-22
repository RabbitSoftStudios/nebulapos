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
