<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/pg_connection.php';

try {
    $pdo = pg_pool();
    $stmt = $pdo->query("PRAGMA table_info(dte_facturas)");
    $columns = $stmt->fetchAll(PDO::FETCH_COLUMN, 1);
    echo "Columns in dte_facturas:\n";
    print_r($columns);
} catch (PDOException $e) {
    fwrite(STDERR, "Error: " . $e->getMessage() . PHP_EOL);
    exit(1);
}
