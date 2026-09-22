<?php
/**
 * Encuentra la configuración REAL que usa ProductService
 */

echo "<h1>Buscando configuración REAL de la base de datos</h1>";

// Método 1: Buscar archivos que contengan 'supabase' o configuración de DB
echo "<h2>1. Archivos de configuración que podrían existir:</h2>";
$configFiles = [
    __DIR__ . '/../.env',
    __DIR__ . '/../config/database.php',
    __DIR__ . '/../config/db.php',
    __DIR__ . '/../application/config/database.php',
    __DIR__ . '/../app/config/database.php',
    __DIR__ . '/../includes/config.php',
    __DIR__ . '/../config.php',
];

foreach ($configFiles as $file) {
    if (file_exists($file)) {
        echo "<p style='color:green'>✅ Encontrado: " . realpath($file) . "</p>";
        
        // Mostrar primeras líneas (sin contraseñas)
        $content = file_get_contents($file);
        $lines = explode("\n", $content);
        echo "<pre>";
        foreach (array_slice($lines, 0, 20) as $line) {
            if (strpos($line, 'DB_') === 0 || strpos($line, 'supabase') !== false) {
                // Ocultar contraseñas
                $safeLine = preg_replace('/PASSWORD\s*=\s*[^\s]+/', 'PASSWORD=*******', $line);
                echo htmlspecialchars($safeLine) . "\n";
            }
        }
        echo "</pre>";
    } else {
        echo "<p style='color:gray'>❌ No existe: " . $file . "</p>";
    }
}

// Método 2: Buscar variables de entorno del sistema
echo "<h2>2. Variables de entorno del sistema:</h2>";
$envVars = [
    'DB_HOST', 'DB_PORT', 'DB_DATABASE', 'DB_USERNAME', 
    'SUPABASE_HOST', 'DATABASE_URL', 'DB_CONNECTION'
];

foreach ($envVars as $var) {
    $value = getenv($var);
    if ($value !== false) {
        echo "<p style='color:green'>✅ $var = " . htmlspecialchars($value) . "</p>";
    } else {
        echo "<p style='color:gray'>❌ $var = NO DEFINIDO</p>";
    }
}

// Método 3: Buscar constantes definidas
echo "<h2>3. Constantes PHP definidas:</h2>";
$constants = get_defined_constants(true);
$dbConstants = [];
foreach ($constants['user'] as $name => $value) {
    if (stripos($name, 'DB_') === 0 || stripos($name, 'DATABASE') !== false) {
        $dbConstants[$name] = $value;
    }
}

if (!empty($dbConstants)) {
    foreach ($dbConstants as $name => $value) {
        echo "<p style='color:green'>✅ $name = " . htmlspecialchars($value) . "</p>";
    }
} else {
    echo "<p style='color:gray'>❌ No hay constantes de base de datos definidas</p>";
}

// Método 4: Probar conexión directa con diferentes configuraciones
echo "<h2>4. Probando configuraciones comunes de Supabase:</h2>";

$configs = [
    [
        'name' => 'Connection Pooler',
        'host' => 'aws-0-us-west-1.pooler.supabase.com',
        'port' => 6543,
        'user' => 'postgres.iyteellzegojaoozwhev',
        'db' => 'postgres'
    ],
    [
        'name' => 'Database Direct',
        'host' => 'db.iyteellzegojaoozwhev.supabase.co',
        'port' => 5432,
        'user' => 'postgres',
        'db' => 'postgres'
    ],
    [
        'name' => 'Project Ref',
        'host' => 'iyteellzegojaoozwhev.supabase.co',
        'port' => 5432,
        'user' => 'postgres',
        'db' => 'postgres'
    ]
];

foreach ($configs as $config) {
    echo "<h3>Probando: {$config['name']}</h3>";
    echo "Host: {$config['host']}:{$config['port']}<br>";
    
    // Probar conexión de red
    $timeout = 3;
    $connection = @fsockopen($config['host'], $config['port'], $errno, $errstr, $timeout);
    
    if ($connection) {
        echo "<p style='color:green'>✅ Puerto accesible</p>";
        fclose($connection);
    } else {
        echo "<p style='color:red'>❌ No accesible: $errstr</p>";
    }
}
?>