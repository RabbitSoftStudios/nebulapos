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
