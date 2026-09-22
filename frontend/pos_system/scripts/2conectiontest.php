<?php
/**
 * Diagnóstico de conexión a Supabase
 */

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

echo "<h1>Diagnóstico de Conexión a Supabase</h1>";

// Configuración
$host = $_ENV['DB_HOST'] ?? 'iyteellzegojaoozwhev.supabase.co';
$port = $_ENV['DB_PORT'] ?? 5432;

echo "<h2>1. Resolución DNS</h2>";
echo "Host: <strong>$host</strong><br>";

// Intentar resolver el hostname
$ip = gethostbyname($host);
if ($ip === $host) {
    echo "<span style='color:red'>❌ No se pudo resolver el hostname</span><br>";
    echo "Posible problema DNS. Intentando con ping...<br>";
    
    // Intentar ping (si está permitido)
    $result = shell_exec("ping -c 3 $host 2>&1");
    echo "<pre>$result</pre>";
} else {
    echo "<span style='color:green'>✅ Host resuelto a: $ip</span><br>";
}

echo "<h2>2. Conexión de red</h2>";
// Probar si el puerto está accesible
$timeout = 5;
$connection = @fsockopen($host, $port, $errno, $errstr, $timeout);

if ($connection) {
    echo "<span style='color:green'>✅ Puerto $port accesible</span><br>";
    fclose($connection);
} else {
    echo "<span style='color:red'>❌ No se puede conectar a $host:$port</span><br>";
    echo "Error: $errstr (Código: $errno)<br>";
}

echo "<h2>3. Configuración actual</h2>";
echo "<pre>";
echo "DB_HOST: " . ($_ENV['DB_HOST'] ?? 'NO DEFINIDO') . "\n";
echo "DB_PORT: " . ($_ENV['DB_PORT'] ?? 'NO DEFINIDO') . "\n";
echo "DB_DATABASE: " . ($_ENV['DB_DATABASE'] ?? 'NO DEFINIDO') . "\n";
echo "DB_USERNAME: " . ($_ENV['DB_USERNAME'] ?? 'NO DEFINIDO') . "\n";
echo "DB_SSLMODE: " . ($_ENV['DB_SSLMODE'] ?? 'NO DEFINIDO') . "\n";
echo "</pre>";

echo "<h2>4. Verificar configuración de Supabase</h2>";
echo "<p>Ve a tu panel de Supabase: <a href='https://supabase.com/dashboard' target='_blank'>https://supabase.com/dashboard</a></p>";
echo "<p>1. Selecciona tu proyecto</p>";
echo "<p>2. Ve a Settings → Database</p>";
echo "<p>3. Copia los valores de:</p>";
echo "<ul>";
echo "<li>Host (ej: db.iyteellzegojaoozwhev.supabase.co)</li>";
echo "<li>Port (ej: 5432)</li>";
echo "<li>Database (ej: postgres)</li>";
echo "<li>Username (ej: postgres)</li>";
echo "</ul>";

echo "<h2>5. Probar conexión directa con PDO</h2>";
try {
    $dsn = "pgsql:host=$host;port=$port;dbname=postgres;sslmode=require";
    $pdo = new PDO($dsn, $_ENV['DB_USERNAME'] ?? 'postgres', $_ENV['DB_PASSWORD'] ?? '');
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Ejecutar consulta simple
    $stmt = $pdo->query("SELECT version()");
    $version = $stmt->fetchColumn();
    
    echo "<span style='color:green'>✅ Conexión exitosa</span><br>";
    echo "PostgreSQL Version: $version<br>";
    
    // Verificar tablas
    $stmt = $pdo->query("SELECT table_name FROM information_schema.tables WHERE table_schema = 'public'");
    $tables = $stmt->fetchAll(PDO::FETCH_COLUMN);
    
    echo "Tablas disponibles: " . implode(', ', $tables) . "<br>";
    
} catch (PDOException $e) {
    echo "<span style='color:red'>❌ Error PDO: " . $e->getMessage() . "</span><br>";
}

echo "<h2>6. Soluciones posibles</h2>";
echo "<ol>";
echo "<li><strong>Verificar el hostname</strong>: Asegúrate de que el hostname sea correcto</li>";
echo "<li><strong>Probar con IP directa</strong>: Si conoces la IP del servidor Supabase</li>";
echo "<li><strong>Verificar firewall</strong>: Asegúrate de que el puerto 5432/6543 esté abierto</li>";
echo "<li><strong>Usar Connection Pooler</strong>: Supabase recomienda usar el puerto 6543 para conexiones persistentes</li>";
echo "<li><strong>Probar con SSL disabled</strong>: Temporalmente para diagnóstico: <code>sslmode=disable</code></li>";
echo "</ol>";
?>