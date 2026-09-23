<?php
// reload_cache.php
// Intenta forzar la recarga del esquema de Supabase

$host = 'db.iyteellzegojaoozwhev.supabase.co'; // Host estándar de Supabase
$port = 5432;
$dbname = 'postgres';
$username = 'postgres';
$password = '6QtxhADJfRSci3jF'; // Contraseña proporcionada por el usuario anteriormente

try {
    $dsn = "pgsql:host=$host;port=$port;dbname=$dbname;sslmode=require";
    $pdo = new PDO($dsn, $username, $password, [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]);
    
    echo "Conexión PDO exitosa.\n";
    
    // Comando mágico para recargar el esquema de PostgREST (Supabase API)
    $pdo->exec("NOTIFY pgrst, 'reload schema'");
    echo "Comando NOTIFY enviado con éxito. El caché de la API debería refrescarse ahora.\n";
    
    // También verificamos el tipo de dato real de la columna nit
    $stmt = $pdo->prepare("SELECT data_type FROM information_schema.columns WHERE table_name = 'mh_cliente_consumidor' AND column_name = 'nit'");
    $stmt->execute();
    $type = $stmt->fetchColumn();
    echo "Tipo de dato real de la columna 'nit': $type\n";

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
?>
