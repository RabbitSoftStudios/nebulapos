<?php
/**
 * Sistema POS - Pantalla Principal de Venta (Versión Auditada v3.1)
 * ===============================================================
 * Integración DTE El Salvador & Supabase Inventory
 */

// 1. CARGA DE DEPENDENCIAS
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../config/routes.php'; 
require_once __DIR__ . '/../includes/supabase.php';
require_once __DIR__ . '/../includes/auth.php';

// 2. LÓGICA DE CONTROL MH (UUID y Correlativo)
// Este archivo debe existir en la ruta especificada para generar $codigoGeneracion y $numeroControl
require_once __DIR__ . '/partials/mh_header_data.php'; 

// 3. VERIFICACIÓN DE SEGURIDAD
checkPOSAuth();

// 4. INICIALIZACIÓN DE VARIABLES DE SESIÓN Y CAJA
$registerNumber = defined('POS_CAJA_NUMERO') ? POS_CAJA_NUMERO : '01';
$cashier = $_SESSION['cashier'] ?? [
    'id' => 1,
    'name' => 'Administrador',
    'code' => defined('POS_CAJERO_DEFAULT') ? POS_CAJERO_DEFAULT : '001'
];

// 5. AUDITORÍA Y CARGA DE PRODUCTOS (SUPABASE)
$frequentProducts = [];
$error_db = null;

if (defined('SUPABASE_URL') && !empty(SUPABASE_URL)) {
    // Definimos el set de columnas necesarias para la venta y el control de inventario
    // Usamos stock_actual para mostrar disponibilidad en tiempo real en la UI
    $columns = 'id, nombre_producto, precio_venta, imagen_url, stock_actual';
    
    // Intentamos conectar a la tabla principal definida en constants o directamente a dte_productos
    $tableName = defined('TABLE_PRODUCTS') ? TABLE_PRODUCTS : 'dte_productos';
    $db = supabase($tableName);

    // PRIMER INTENTO: Productos marcados como frecuentes (Prioridad UI)
    $res = $db->select($columns, ['frecuente' => true], 15);

    if (!$res['success']) {
        $raw_error = (string)$res['error'];
        
        // AUDITORÍA DE ERROR: Si la columna 'frecuente' no existe, hacemos fallback a carga general
        if (stripos($raw_error, 'frecuente') !== false || stripos($raw_error, 'not found') !== false) {
            $res = $db->select($columns, [], 20);
        }
    }

    // VALIDACIÓN FINAL DE DATOS
    if ($res['success']) {
        $frequentProducts = $res['data'];
    } else {
        $error_db = "Error de Auditoría DB: " . ($res['error'] ?? 'Sin respuesta de Supabase');
    }
} else {
    $error_db = "Configuración Crítica: SUPABASE_URL no detectada.";
}

// 6. LIMPIEZA DE DATOS PARA LA VISTA
// Nos aseguramos que $frequentProducts sea siempre un array para evitar errores en el loop HTML
if (!is_array($frequentProducts)) {
    $frequentProducts = [];
}
?>
