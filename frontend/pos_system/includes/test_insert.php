<?php
require_once __DIR__ . '/pg_connection.php';

try {
    $db = PgConnection::get();

    $stmt = $db->prepare("
        INSERT INTO dte_productos
        (codigo_producto, nombre_producto, precio_venta, stock_actual)
        VALUES (:codigo, :nombre, :precio, :stock)
        RETURNING id, codigo_producto, nombre_producto
    ");

    $stmt->execute([
        'codigo' => 'TEST-' . rand(1000,9999),
        'nombre' => 'Producto de Prueba',
        'precio' => 1.50,
        'stock'  => 10
    ]);

    $product = $stmt->fetch();
    echo "✅ INSERT OK\n";
    print_r($product);

} catch (Throwable $e) {
    echo "❌ ERROR INSERT:\n";
    echo $e->getMessage();
}
