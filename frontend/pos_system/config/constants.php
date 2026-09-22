<?php
// pos_system/config/constants.php
/**
 * Sistema POS - Constantes del Sistema
 * =====================================
 * Este archivo contiene todas las constantes de configuración del sistema POS.
 * SOLO debe usar funciones nativas de PHP como getenv() y no wrappers como env().
 * @package    POS System
 * @author     Nebula DET Team
 * @version    2.0.0
 * @license    MIT
 */

// 0. Define la ruta base del proyecto de forma dinámica
if (!defined('BASE_URL')) {
    $script_dir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
    // Si estamos en un subdirectorio, rtrim para obtener la raíz del pos_system
    // Nota: Esto asume que el punto de entrada (index.php) está en la raíz de pos_system
    $base = rtrim($script_dir, '/');
    define('BASE_URL', $base);
}

if (!function_exists('env')) {
    /**
     * Obtiene el valor de una variable de entorno de forma segura.
     * Busca en $_ENV, $_SERVER y getenv().
     */
    function env($key, $default = null) {
        $value = $_ENV[$key] ?? $_SERVER[$key] ?? getenv($key);
        return ($value === false || $value === '' || $value === null) ? $default : $value;
    }
}

// =================================================================
// 🚨 NOTA IMPORTANTE PARA EL DESARROLLADOR:
// Para que getenv() lea las variables del archivo .env, 
// debe asegurarse de que la función load_env() (definida en includes/functions.php) 
// se ejecute ANTES de que este archivo sea cargado. 
// Por ejemplo, al inicio de su archivo pos_dashboard.php o index.php.
// =================================================================


// ==================== ENTORNO DE LA APLICACIÓN ====================
define('APP_NAME', 'Nebula POS System');
define('APP_VERSION', '2.0.0');
define('APP_ENV', env('APP_ENV', 'production'));
define('APP_DEBUG', filter_var(env('APP_DEBUG'), FILTER_VALIDATE_BOOLEAN));
define('APP_URL', env('APP_URL', 'http://localhost:8000'));
define('APP_TIMEZONE', 'America/El_Salvador');


// ==================== RUTAS DEL SISTEMA ====================
// Definiciones consolidadas (se asume que ROOT_PATH es el directorio padre de config/)
define('ROOT_PATH', dirname(__DIR__));
define('VIEWS_PATH', ROOT_PATH . '/views');
define('CONTROLLERS_PATH', ROOT_PATH . '/controllers');
define('INCLUDES_PATH', ROOT_PATH . '/includes');
define('LOGS_PATH', ROOT_PATH . '/logs'); // Renombrado de LOG_PATH a LOGS_PATH para consistencia
define('SCRIPTS_PATH', ROOT_PATH . '/scripts');
define('BACKUP_PATH', ROOT_PATH . '/backups');
define('UPLOAD_PATH', ROOT_PATH . '/uploads/');
define('TEMP_PATH', ROOT_PATH . '/temp/');
define('ASSETS_PATH', ROOT_PATH . '/assets/');


// ==================== CONFIGURACIÓN SUPABASE ====================
// CORRECCIÓN: Usando getenv() con fallback
define('SUPABASE_URL', env('SUPABASE_URL', '')); 
define('SUPABASE_KEY', env('SUPABASE_KEY', '')); 
define('SUPABASE_ANON_KEY', SUPABASE_KEY); // Alias para consistencia
define('SUPABASE_SERVICE_ROLE_KEY', env('SUPABASE_SERVICE_ROLE_KEY', '')); // Añadida para uso en scripts/backend
define('SUPABASE_JWT_SECRET', env('SUPABASE_JWT_SECRET', ''));

// Tablas de Supabase
define('TABLE_PRODUCTS', 'dte_productos'); // Changed to dte_productos
define('TABLE_SALES', 'pos_sales');
define('TABLE_CUSTOMERS', 'mh_cliente_consumidor'); // Changed to mh_cliente_consumidor
define('TABLE_CATEGORIES', 'pos_categories');
define('TABLE_USERS', 'pos_users');
define('TABLE_INVENTORY_LOTS', 'pos_inventory_lots'); // Añadida por la arquitectura de vencimiento
define('TABLE_RECIPE_COMPONENTS', 'pos_recipe_components'); // Añadida por la arquitectura de restaurantes

// ==================== CONFIGURACIÓN DTE (El Salvador) ====================
define('DTE_ENVIRONMENT', env('DTE_ENVIRONMENT', '00'));
define('DTE_EMISOR_NIT', env('DTE_EMISOR_NIT', ''));
define('DTE_EMISOR_NRC', env('DTE_EMISOR_NRC', ''));
define('DTE_SIGNATURE_KEY', env('DTE_SIGNATURE_KEY', ''));
define('DTE_SIGNATURE_PASS', env('DTE_SIGNATURE_PASS', ''));

// Tablas DTE
define('TABLE_DTE_DOCUMENTS', 'dgii_dte_documentos');
define('TABLE_DTE_BODY', 'dgii_dte_cuerpodocumento');
define('TABLE_DTE_APPENDIX', 'dgii_dte_apendice');
define('TABLE_DTE_FACTURAS', 'dte_facturas');
define('TABLE_DTE_LOGS', 'facturacion_logs');

// Tipos de Documento DTE
define('DTE_FACTURA', '01');
define('DTE_CREDITO_FISCAL', '03');
define('DTE_NOTA_CREDITO', '05');
define('DTE_NOTA_DEBITO', '06');
define('DTE_COMPROBANTE_RETENCION', '14');

// ==================== CONFIGURACIÓN POS (Operación) ====================
define('POS_CAJA_NUMERO', env('POS_CAJA_NUMERO', '001'));
define('POS_CAJERO_DEFAULT', env('POS_CAJERO_DEFAULT', 'admin'));
define('POS_TICKET_WIDTH', env('POS_TICKET_WIDTH', 80));
define('POS_AUTO_SAVE', filter_var(env('POS_AUTO_SAVE'), FILTER_VALIDATE_BOOLEAN));

// Impuestos y Moneda (Consolidado de definiciones anteriores)
define('IVA_RATE', env('IVA_RATE', 0.13));
define('RETENTION_RATE', env('RETENTION_RATE', 0.01));
define('DEFAULT_CURRENCY_SYMBOL', env('CURRENCY_SYMBOL', '$'));
define('DEFAULT_TAX_RATE', (float)IVA_RATE); // Usamos IVA_RATE como tasa por defecto

// ==================== SEGURIDAD ====================
define('SESSION_TIMEOUT', getenv('SESSION_TIMEOUT') ?: 3600);
define('MAX_LOGIN_ATTEMPTS', getenv('MAX_LOGIN_ATTEMPTS') ?: 3);
define('ENCRYPTION_KEY', getenv('ENCRYPTION_KEY') ?: 'your-encryption-key-here'); // Debe ser cambiado en .env

// ==================== CONSTANTES DE ESTADO/TIPO ====================
// Estados de venta
define('SALE_PENDING', 'pending');
define('SALE_COMPLETED', 'completed');
define('SALE_CANCELLED', 'cancelled');
define('SALE_REFUNDED', 'refunded');

// Métodos de pago
define('PAYMENT_CASH', 'cash');
define('PAYMENT_CARD', 'card');
define('PAYMENT_TRANSFER', 'transfer');
define('PAYMENT_MIXED', 'mixed');

// Estados DTE
define('DTE_PENDING', 'pendiente');
define('DTE_SIGNED', 'firmado');
define('DTE_RECEIVED', 'recibido');
define('DTE_ERROR', 'error');

// ==================== MENSAGES DEL SISTEMA ====================
define('MSG_SUCCESS', 'Operación realizada exitosamente');
define('MSG_ERROR', 'Ocurrió un error en la operación');
define('MSG_NO_PRODUCT', 'Producto no encontrado');
define('MSG_SCAN_SUCCESS', 'Producto escaneado exitosamente');
define('MSG_SALE_COMPLETED', 'Venta procesada exitosamente');

// ==================== CONFIGURACIÓN DE API ====================
define('API_TIMEOUT', getenv('API_TIMEOUT') ?: 30);
define('ENABLE_OFFLINE_MODE', filter_var(getenv('ENABLE_OFFLINE_MODE'), FILTER_VALIDATE_BOOLEAN));

// ==================== CÓDIGOS DE RESPUESTA HTTP ====================
define('HTTP_OK', 200);
define('HTTP_CREATED', 201);
define('HTTP_BAD_REQUEST', 400);
define('HTTP_UNAUTHORIZED', 401);
define('HTTP_FORBIDDEN', 403); // Añadido
define('HTTP_NOT_FOUND', 404);
define('HTTP_METHOD_NOT_ALLOWED', 405); // Añadido
define('HTTP_INTERNAL_SERVER_ERROR', 500);

// ==================== INICIAR SESIÓN (Al final del archivo) ====================
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}