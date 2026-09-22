<?php
/**
 * Script de Diagnóstico - API de Productos
 * =========================================
 * Prueba directa del endpoint de productos para identificar problemas
 */

error_reporting(E_ALL);
ini_set('display_errors', 1);

echo "<h1>Test de API de Productos</h1>";
echo "<hr>";

// Test 1: Verificar archivos requeridos
echo "<h2>1. Verificación de Archivos</h2>";
$files = [
    'ProductService' => __DIR__ . '/includes/ProductService.php',
    'Supabase' => __DIR__ . '/includes/supabase.php',
    'Config' => __DIR__ . '/includes/config.php'
];

foreach ($files as $name => $path) {
    if (file_exists($path)) {
        echo "✓ $name encontrado<br>";
    } else {
        echo "✗ $name NO encontrado: $path<br>";
    }
}

echo "<hr>";

// Test 2: Cargar ProductService
echo "<h2>2. Carga de ProductService</h2>";
try {
    require_once __DIR__ . '/includes/ProductService.php';
    echo "✓ ProductService cargado correctamente<br>";
    
    $service = new ProductService();
    echo "✓ Instancia de ProductService creada<br>";
} catch (Exception $e) {
    echo "✗ Error: " . $e->getMessage() . "<br>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
    die();
}

echo "<hr>";

// Test 3: Obtener todos los productos
echo "<h2>3. Test GET - Obtener Productos</h2>";
try {
    $result = $service->getAll();
    echo "<strong>Resultado:</strong><br>";
    echo "<pre>" . print_r($result, true) . "</pre>";
    
    if ($result['success']) {
        echo "✓ Productos obtenidos: " . count($result['data']) . " registros<br>";
    } else {
        echo "✗ Error al obtener productos: " . ($result['error'] ?? 'Desconocido') . "<br>";
    }
} catch (Exception $e) {
    echo "✗ Excepción: " . $e->getMessage() . "<br>";
}

echo "<hr>";

// Test 4: Crear un producto de prueba
echo "<h2>4. Test POST - Crear Producto</h2>";
$testProduct = [
    'codigo_producto' => 'TEST-' . time(),
    'codigo_barras' => 'BAR-' . time(),
    'nombre_producto' => 'Producto de Prueba ' . date('H:i:s'),
    'precio_venta' => 10.50,
    'stock_actual' => 100,
    'stock_minimo' => 5,
    'es_venta_libre' => true
];

echo "<strong>Datos a insertar:</strong><br>";
echo "<pre>" . print_r($testProduct, true) . "</pre>";

try {
    $result = $service->create($testProduct);
    echo "<strong>Resultado:</strong><br>";
    echo "<pre>" . print_r($result, true) . "</pre>";
    
    if ($result['success']) {
        echo "✓ Producto creado exitosamente<br>";
        $productId = $result['data'][0]['id'] ?? null;
        
        // Test 5: Obtener el producto recién creado
        if ($productId) {
            echo "<hr>";
            echo "<h2>5. Test GET by ID - Verificar Producto Creado</h2>";
            $getResult = $service->getById($productId);
            echo "<pre>" . print_r($getResult, true) . "</pre>";
            
            if ($getResult['success']) {
                echo "✓ Producto recuperado correctamente<br>";
            }
        }
    } else {
        echo "✗ Error al crear producto: " . ($result['error'] ?? 'Desconocido') . "<br>";
    }
} catch (Exception $e) {
    echo "✗ Excepción: " . $e->getMessage() . "<br>";
    echo "<pre>" . $e->getTraceAsString() . "</pre>";
}

echo "<hr>";

// Test 6: Verificar configuración de Supabase
echo "<h2>6. Configuración de Supabase</h2>";
if (defined('SUPABASE_URL')) {
    echo "✓ SUPABASE_URL: " . substr(SUPABASE_URL, 0, 30) . "...<br>";
} else {
    echo "✗ SUPABASE_URL no definida<br>";
}

if (defined('SUPABASE_KEY')) {
    echo "✓ SUPABASE_KEY: " . substr(SUPABASE_KEY, 0, 20) . "...<br>";
} else {
    echo "✗ SUPABASE_KEY no definida<br>";
}

echo "<hr>";
echo "<h2>Diagnóstico Completo</h2>";
echo "<p>Revise los resultados anteriores para identificar el problema.</p>";
?>
