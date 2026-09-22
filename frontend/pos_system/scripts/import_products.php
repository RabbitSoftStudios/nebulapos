#!/usr/bin/env php
<?php
// pos_system/scripts/import_products.php
/**
 * Script de Importación de Productos desde CSV
 * =============================================
 * Procesa un archivo CSV para la importación masiva de productos a Supabase.
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
    die("ERROR: Este script debe ejecutarse desde la línea de comandos.\n");
}

if (!isset($argv[1])) {
    die("ERROR: Debe proporcionar la ruta al archivo CSV.\nUso: php scripts/import_products.php <ruta/al/archivo.csv>\n");
}

$csv_file_path = $argv[1];

if (!file_exists($csv_file_path)) {
    die("ERROR: El archivo '{$csv_file_path}' no existe.\n");
}

echo "========================================================\n";
echo "          IMPORTADOR DE PRODUCTOS DESDE CSV              \n";
echo "========================================================\n";
echo "Archivo a procesar: {$csv_file_path}\n";

$file = fopen($csv_file_path, 'r');
if (!$file) {
    die("ERROR: No se pudo abrir el archivo CSV.\n");
}

// Encabezados requeridos (alineados con la arquitectura propuesta)
$expected_headers = ['name', 'sku', 'price', 'category_id', 'product_type', 'stock', 'batch_number', 'expiration_date'];
$headers = fgetcsv($file, 1000, ',');

if (count(array_intersect($expected_headers, $headers)) !== count($expected_headers)) {
    fclose($file);
    die("ERROR: El CSV no contiene todos los encabezados requeridos. Revise: " . implode(', ', $expected_headers) . "\n");
}

$imported_count = 0;
$error_count = 0;
$products_to_insert = [];

// 1. Lectura del CSV y pre-procesamiento
while (($row = fgetcsv($file, 1000, ',')) !== false) {
    $product_data = array_combine($headers, $row);
    
    // Preparar datos para pos_products
    $product_entry = [
        'name' => sanitize_input($product_data['name']),
        'sku' => sanitize_input($product_data['sku']),
        'price' => (float)$product_data['price'],
        'category_id' => (int)$product_data['category_id'],
        'product_type' => sanitize_input($product_data['product_type'])
    ];
    
    $products_to_insert[] = $product_entry;
}
fclose($file);

// 2. Inserción Masiva de Productos (usando Supabase Upsert)
echo "\nIniciando inserción masiva de " . count($products_to_insert) . " productos...\n";

// La inserción masiva de productos y el manejo de lotes requiere una función RPC o un 
// proceso de dos pasos, pero para el script, simulamos una inserción simple.

// --- SIMULACIÓN DE INSERCIÓN API (Se asume que la lógica de lotes se hará en un paso siguiente) ---
$response = SupabaseAPI::request('POST', 'pos_products?on_conflict=sku', $products_to_insert, true); 

if (!isset($response['error']) && is_array($response)) {
    $imported_count = count($response);
    echo "   [OK] Productos insertados/actualizados: {$imported_count}.\n";
} else {
    $error_count = count($products_to_insert); // Asumimos que todo el batch falló
    echo "   [ERROR] Fallo en la inserción masiva. HTTP: " . ($response['status'] ?? 'N/A') . "\n";
    Logger::log_error("Fallo masivo de importación: " . ($response['error'] ?? 'Desconocido'));
}
// -------------------------------------------------------------------------------------------------

echo "\n========================================================\n";
echo "  IMPORTACIÓN FINALIZADA.  \n";
echo "========================================================\n";
echo "Productos procesados: " . count($products_to_insert) . "\n";
echo "Productos insertados/actualizados: {$imported_count}\n";

if ($error_count > 0) {
    echo "Revise el log de errores para detalles.\n";
}