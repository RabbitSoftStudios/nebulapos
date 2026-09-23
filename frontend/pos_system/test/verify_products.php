<?php
// verify_products.php
/**
 * Script de Verificación de Productos
 * Ajustado para cargar el entorno de forma robusta.
 */

// 1. Localizar y cargar el autoloader y entorno
$rootPath = __DIR__;
while (!file_exists($rootPath . '/vendor/autoload.php') && $rootPath !== dirname($rootPath)) {
    $rootPath = dirname($rootPath);
}

if (file_exists($rootPath . '/vendor/autoload.php')) {
    require_once $rootPath . '/vendor/autoload.php';
}

if (file_exists(__DIR__ . '/.env')) {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
    $dotenv->safeLoad();
}

// 2. Cargar Dependencias del Sistema
require_once __DIR__ . '/config/constants.php';
require_once __DIR__ . '/includes/ProductService.php';

echo "<h1>Verificación de ProductService (dte_productos)</h1>";

$service = new ProductService();
$testCode = 'TEST-' . uniqid();

echo "<h2>1. Crear Producto</h2>";
$newProd = [
    'codigo_producto' => $testCode,
    'codigo_barras' => $testCode, // Mismo para test
    'nombre_producto' => 'Producto Prueba ' . $testCode,
    'precio_venta' => 10.50,
    'stock_actual' => 100,
    'stock_minimo' => 5
];

$create = $service->create($newProd);

if ($create['success']) {
    $id = $create['data'][0]['id'] ?? null;
    if ($id) {
        echo "<p style='color:green'>✅ Creado ID: $id - " . $newProd['nombre_producto'] . "</p>";
    } else {
        echo "<p style='color:orange'>⚠️ Creado pero no se retornó ID (Check table dte_productos)</p>";
    }
} else {
    echo "<p style='color:red'>❌ Error Crear: " . $create['error'] . "</p>";
    $id = null;
}

if ($id) {
    echo "<h2>2. Leer (GetById)</h2>";
    $read = $service->getById($id);
    if ($read['success']) {
        echo "<p style='color:green'>✅ Leído: " . $read['data']['nombre_producto'] . "</p>";
    } else {
        echo "<p style='color:red'>❌ Error Leer: " . $read['error'] . "</p>";
    }

    echo "<h2>3. Actualizar</h2>";
    $update = $service->update($id, ['nombre_producto' => 'Producto Editado', 'precio_venta' => 15.00]);
    if ($update['success']) {
        echo "<p style='color:green'>✅ Actualizado Correctamente</p>";
    } else {
        echo "<p style='color:red'>❌ Error Actualizar: " . $update['error'] . "</p>";
    }

    echo "<h2>4. Eliminar (Soft Delete)</h2>";
    $del = $service->delete($id);
    if ($del['success']) {
        echo "<p style='color:green'>✅ Eliminado (Lógico)</p>";
    } else {
        echo "<p style='color:red'>❌ Error Eliminar: " . $del['error'] . "</p>";
    }
}
?>
