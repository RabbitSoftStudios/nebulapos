<?php
/* // init.php
$envFile = __DIR__ . '/../.env';

if (!file_exists($envFile)) {
    throw new RuntimeException('.env file not found at: ' . $envFile);
}

// Leer el archivo .env
$lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
foreach ($lines as $line) {
    // Saltar comentarios
    if (strpos(trim($line), '#') === 0) {
        continue;
    }
    
    // Separar clave y valor
    if (strpos($line, '=') !== false) {
        list($key, $value) = explode('=', $line, 2);
        $key = trim($key);
        $value = trim($value);
        
        // Establecer como variable de entorno
        putenv("$key=$value");
        $_ENV[$key] = $value;
    }
}

// Verificar las variables necesarias
$required_vars = ['SUPABASE_URL', 'SUPABASE_KEY'];
foreach ($required_vars as $var) {
    if (empty($_ENV[$var]) && empty(getenv($var))) {
        throw new RuntimeException("Variable $var is missing in .env file");
    }
} */

// Cargar Dotenv solo una vez
if (file_exists(__DIR__ . '/../.env')) {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/..');
    $dotenv->load();
    
    // Definir constantes solo si no existen
    if (!defined('SUPABASE_URL')) {
        define('SUPABASE_URL', $_ENV['SUPABASE_URL'] ?? '');
    }
    if (!defined('SUPABASE_KEY')) {
        define('SUPABASE_KEY', $_ENV['SUPABASE_KEY'] ?? '');
    }
    
    // Verificar que las constantes tienen valores
    if (empty(SUPABASE_URL) || empty(SUPABASE_KEY)) {
        throw new RuntimeException('Supabase configuration is missing in .env file');
    }
}