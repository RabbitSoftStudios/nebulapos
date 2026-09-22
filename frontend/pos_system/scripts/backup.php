#!/usr/bin/env php
<?php
// pos_system/scripts/backup.php
/**
 * Script de Copia de Seguridad de Datos
 * =====================================
 * Genera una copia de seguridad JSON de las tablas principales del sistema.
 * @package    POS System
 * @author     Nebula DET Team
 * @version    2.0.0
 */

// Cargar archivos necesarios
require_once __DIR__ . '/../config/constants.php';
require_once INCLUDES_PATH . '/functions.php';
require_once INCLUDES_PATH . '/logger.php';
require_once INCLUDES_PATH . '/database.php';

if (php_sapi_name() !== 'cli') {
    die("Este script debe ejecutarse desde la línea de comandos.\n");
}

echo "========================================================\n";
echo "           GENERADOR DE COPIA DE SEGURIDAD (BACKUP)      \n";
echo "========================================================\n";

$tables_to_backup = [
    'pos_products', 
    'pos_sales', 
    'pos_customers',
    'pos_inventory_lots' // Incluye la tabla de lotes para trazabilidad total
];

$backup_dir = BACKUP_PATH;
$date_prefix = date('Ymd_His');

if (!is_dir($backup_dir)) {
    mkdir($backup_dir, 0777, true);
    Logger::info("Directorio de backup creado: {$backup_dir}");
}

foreach ($tables_to_backup as $table) {
    echo "Respaldando tabla: {$table}... ";
    
    // Usar la Service Role Key para permisos elevados de lectura total
    // Select=* y sin filtros (traer todos)
    $response = SupabaseAPI::request('GET', "{$table}?select=*", [], true); 

    if (!isset($response['error'])) {
        $data_json = json_encode($response, JSON_PRETTY_PRINT);
        $backup_filename = "{$backup_dir}/{$table}_{$date_prefix}.json";
        
        if (file_put_contents($backup_filename, $data_json)) {
            $data_size = format_currency(strlen($data_json) / (1024 * 1024), 'MB');
            echo "[OK] Tamaño: {$data_size}. Guardado en: " . basename($backup_filename) . "\n";
            Logger::info("Backup exitoso para {$table}. Archivo: " . basename($backup_filename));
        } else {
            echo "[ERROR] Fallo al escribir el archivo de backup.\n";
            Logger::log_error("Fallo al escribir el archivo de backup para {$table}.");
        }
    } else {
        echo "[ERROR] Fallo al obtener datos. HTTP: {$response['status']}\n";
        Logger::log_error("Fallo al obtener datos de Supabase para {$table}.");
    }
}

echo "\n========================================================\n";
echo "                  BACKUP FINALIZADO.                     \n";
echo "========================================================\n";