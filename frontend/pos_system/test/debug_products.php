<?php
// debug_products.php
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

require_once __DIR__ . '/config/constants.php';
require_once __DIR__ . '/includes/supabase.php';

$db = supabase(TABLE_PRODUCTS);
$result = $db->select('*', [], 1);

echo "Resultado de dte_productos:\n";
if ($result['success']) {
    print_r($result['data']);
} else {
    echo "Error: " . ($result['error'] ?? 'Desconocido');
}
?>
