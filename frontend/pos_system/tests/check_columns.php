<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/pg_connection.php';

try {
    $pdo = pg_pool();
    $stmt = $pdo->query("SELECT column_name FROM information_schema.columns WHERE table_name = 'dte_facturas'");
    $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
    echo "Columns in dte_facturas:\n";
    print_r($columns);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
