<?php
// pos_system/sync_supabase.php
/**
 * Script de Sincronización Programada (Cron/Webhook)
 * ====================================================
 * Ejecuta tareas de sincronización o mantenimiento de datos.
 * @package    POS System
 * @author     Nebula DET Team
 * @version    2.0.0
 */

// Cargar archivos necesarios
require_once __DIR__ . '/config/constants.php';
require_once INCLUDES_PATH . '/functions.php';
require_once INCLUDES_PATH . '/logger.php';
require_once INCLUDES_PATH . '/database.php';

// Determinar el contexto de ejecución
$is_cli = (php_sapi_name() === 'cli');

if (!$is_cli) {
    // Seguridad para Webhooks: requiere una clave secreta
    if (getenv('WEBHOOK_SECRET') !== ($_GET['secret'] ?? '') || empty(getenv('WEBHOOK_SECRET'))) {
        send_json_error("Acceso no autorizado al script de sincronización.", HTTP_UNAUTHORIZED);
    }
    // Prepara el buffer para evitar output inesperado
    ob_start(); 
} else {
    echo "========================================================\n";
    echo "           INICIO DE SINCRONIZACIÓN PROGRAMADA           \n";
    echo "========================================================\n";
}

Logger::info("Iniciando script de sincronización/mantenimiento.");

// ----------------------------------------------------
// TAREA 1: Limpieza de Carritos Abandonados (ejemplo)
// ----------------------------------------------------
Logger::info("Limpiando carritos de compra abandonados...");
$days_to_keep = 7;
// La lógica de eliminación se ejecutaría en Supabase: DELETE FROM pos_carts WHERE updated_at < now() - interval '7 days'
// En PHP se llama a la API o RPC para ejecutar esta limpieza
$response_delete = SupabaseAPI::request('DELETE', "pos_carts?updated_at=lt." . date('Y-m-d H:i:s', strtotime("-{$days_to_keep} days")), [], true);
$deleted_count = $response_delete['count'] ?? 'Desconocido';

Logger::info("Limpieza de carritos completada. Registros afectados: {$deleted_count}.");


// ----------------------------------------------------
// TAREA 2: Notificación de Stock Bajo (ejemplo)
// ----------------------------------------------------
Logger::info("Verificando productos con stock bajo...");
// Llamar a una función RPC en Supabase que devuelva productos con stock < 10
$response_stock = SupabaseAPI::request('GET', 'pos_products?stock=lt.10', [], true);

if (!isset($response_stock['error']) && is_array($response_stock)) {
    $low_stock_count = count($response_stock);
    if ($low_stock_count > 0) {
        Logger::log('WARN', "Alerta: {$low_stock_count} productos con stock bajo.");
        // Aquí se implementaría la lógica de envío de correo/SMS
    }
}
// ----------------------------------------------------

Logger::info("Script de sincronización/mantenimiento finalizado.");

if ($is_cli) {
    echo "\n========================================================\n";
    echo "                  SINCRONIZACIÓN FINALIZADA.             \n";
    echo "========================================================\n";
} else {
    // Si es Webhook, devuelve respuesta JSON y limpia el buffer
    ob_end_clean();
    send_json_response(['message' => 'Sincronización completada con éxito.'], HTTP_OK);
}