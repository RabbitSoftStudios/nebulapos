<?php
/**
 * Sistema POS - Constantes del Sistema
 * =====================================
 * Este archivo contiene todas las constantes de configuración del sistema POS
 * * @package    POS System
 * @author     Nebula DET Team
 * @version    2.0.0
 * @license    MIT
 */

// ==================== ENTORNO DE LA APLICACIÓN ====================
define('APP_NAME', 'Nebula POS System');
define('APP_VERSION', '2.0.0');
define('APP_ENV', getenv('APP_ENV') ?: 'production');
define('APP_DEBUG', filter_var(getenv('APP_DEBUG'), FILTER_VALIDATE_BOOLEAN));
define('APP_URL', getenv('APP_URL') ?: 'http://localhost:8000');
define('APP_TIMEZONE', 'America/El_Salvador');

// ==================== CONFIGURACIÓN SUPABASE ====================
// define('SUPABASE_URL', getenv('SUPABASE_URL'));
// define('SUPABASE_KEY', getenv('SUPABASE_KEY'));
// define('SUPABASE_JWT_SECRET', getenv('SUPABASE_JWT_SECRET'));

//define('SUPABASE_URL', getenv('SUPABASE_URL') ?: ''); 
define('SUPABASE_URL', env('SUPABASE_URL', '')); 
define('SUPABASE_KEY', env('SUPABASE_KEY', ''));
define('SUPABASE_JWT_SECRET', env('SUPABASE_JWT_SECRET', ''));

// Tablas de Supabase
define('TABLE_PRODUCTS', 'pos_products');
define('TABLE_SALES', 'pos_sales');
define('TABLE_CUSTOMERS', 'pos_customers');
define('TABLE_CATEGORIES', 'pos_categories');
define('TABLE_USERS', 'pos_users');
define('TABLE_DTE_DOCUMENTS', 'dgii_dte_documentos');
define('TABLE_DTE_BODY', 'dgii_dte_cuerpodocumento');
define('TABLE_DTE_APPENDIX', 'dgii_dte_apendice');
define('TABLE_DTE_FACTURAS', 'dte_facturas');
define('TABLE_DTE_LOGS', 'facturacion_logs');

// ==================== CONFIGURACIÓN DTE ====================
define('DTE_ENVIRONMENT', getenv('DTE_ENVIRONMENT') ?: '00');
define('DTE_EMISOR_NIT', getenv('DTE_EMISOR_NIT'));
define('DTE_EMISOR_NRC', getenv('DTE_EMISOR_NRC'));
define('DTE_SIGNATURE_KEY', getenv('DTE_SIGNATURE_KEY'));
define('DTE_SIGNATURE_PASS', getenv('DTE_SIGNATURE_PASS'));

// Tipos de Documento DTE
define('DTE_FACTURA', '01');
define('DTE_CREDITO_FISCAL', '03');
define('DTE_NOTA_CREDITO', '05');
define('DTE_NOTA_DEBITO', '06');
define('DTE_COMPROBANTE_RETENCION', '14');

// ==================== CONFIGURACIÓN POS ====================
define('POS_CAJA_NUMERO', getenv('POS_CAJA_NUMERO') ?: '001');
define('POS_CAJERO_DEFAULT', getenv('POS_CAJERO_DEFAULT') ?: 'admin');
define('POS_TICKET_WIDTH', getenv('POS_TICKET_WIDTH') ?: 80);
define('POS_AUTO_SAVE', filter_var(getenv('POS_AUTO_SAVE'), FILTER_VALIDATE_BOOLEAN));

// Impuestos
define('IVA_RATE', getenv('IVA_RATE') ?: 0.13);
define('RETENTION_RATE', getenv('RETENTION_RATE') ?: 0.01);

// ==================== RUTAS DEL SISTEMA ====================
define('ROOT_PATH', dirname(__DIR__));
define('UPLOAD_PATH', ROOT_PATH . '/uploads/');
define('LOG_PATH', ROOT_PATH . '/logs/');
define('BACKUP_PATH', ROOT_PATH . '/backups/');
define('TEMP_PATH', ROOT_PATH . '/temp/');
define('ASSETS_PATH', ROOT_PATH . '/assets/');

// ==================== SEGURIDAD ====================
define('SESSION_TIMEOUT', getenv('SESSION_TIMEOUT') ?: 3600);
define('MAX_LOGIN_ATTEMPTS', getenv('MAX_LOGIN_ATTEMPTS') ?: 3);
define('ENCRYPTION_KEY', getenv('ENCRYPTION_KEY') ?: 'your-encryption-key-here');

// ==================== CONSTANTES DEL SISTEMA ====================
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
?>
