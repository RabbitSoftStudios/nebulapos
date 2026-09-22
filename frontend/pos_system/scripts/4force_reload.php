<?php
/**
 * Fuerza la recarga del esquema de base de datos
 */

echo "<h1>Forzando recarga del esquema</h1>";

// ============================================
// CONEXIÓN DIRECTA CON OPCIÓN PARA LIMPIAR METADATOS
// ============================================

// Intenta diferentes configuraciones de Supabase
$configs = [
    [
        'name' => 'Connection Pooler',
        'dsn' => 'pgsql:host=aws-0-us-west-1.pooler.supabase.com;port=6543;dbname=postgres;sslmode=require',
        'user' => 'postgres.iyteellzegojaoozwhev',
        'pass' => '' // Agrega tu contraseña aquí
    ],
    [
        'name' => 'Direct Connection',
        'dsn' => 'pgsql:host=db.iyteellzegojaoozwhev.supabase.co;port=5432;dbname=postgres;sslmode=require',
        'user' => 'postgres',
        'pass' => '' // Agrega tu contraseña aquí
    ]
];

foreach ($configs as $config) {
    echo "<h2>Probando: {$config['name']}</h2>";
    
    try {
        $pdo = new PDO($config['dsn'], $config['user'], $config['pass'], [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_PERSISTENT => false, // IMPORTANTE: No usar conexiones persistentes
            PDO::ATTR_STRINGIFY_FETCHES => false,
            PDO::ATTR_EMULATE_PREPARES => false
        ]);
        
        // ============================================
        // TRUCO CRÍTICO: Forzar recarga de metadatos
        // ============================================
        
        // 1. Consulta que obliga a PDO a obtener metadatos frescos
        $pdo->query("SELECT * FROM mh_cliente_consumidor WHERE 1=0");
        
        // 2. Obtener columnas actuales directamente desde information_schema
        $stmt = $pdo->query("
            SELECT column_name, data_type, is_nullable, column_default
            FROM information_schema.columns
            WHERE table_name = 'mh_cliente_consumidor'
            AND table_schema = 'public'
            ORDER BY ordinal_position
        ");
        
        $columns = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        echo "<table border='1' cellpadding='5'>";
        echo "<tr><th>Columna</th><th>Tipo</th><th>Null?</th><th>Default</th></tr>";
        
        $activeFound = false;
        foreach ($columns as $col) {
            $isActive = ($col['column_name'] === 'active');
            $color = $isActive ? 'green' : 'black';
            $weight = $isActive ? 'bold' : 'normal';
            
            if ($isActive) $activeFound = true;
            
            echo "<tr style='color:$color; font-weight:$weight'>";
            echo "<td>{$col['column_name']}" . ($isActive ? " ✅" : "") . "</td>";
            echo "<td>{$col['data_type']}</td>";
            echo "<td>{$col['is_nullable']}</td>";
            echo "<td>" . htmlspecialchars($col['column_default'] ?? 'NULL') . "</td>";
            echo "</tr>";
        }
        echo "</table>";
        
        if ($activeFound) {
            echo "<p style='color:green;font-size:16px;'><strong>✅ Columna 'active' encontrada en la base de datos</strong></p>";
        } else {
            echo "<p style='color:red;font-size:16px;'><strong>❌ Columna 'active' NO encontrada</strong></p>";
        }
        
        // 3. Crear una tabla temporal para forzar recarga completa
        $pdo->query("CREATE TEMP TABLE IF NOT EXISTS force_schema_reload (dummy int)");
        $pdo->query("DROP TABLE IF EXISTS force_schema_reload");
        
        // 4. Consulta que usa todas las columnas (incluyendo active)
        $testData = [
            'p_nombre' => 'Test',
            'p_apellido' => 'Schema',
            'dui' => '999999999',
            'nit' => '888888888',
            'active' => true
        ];
        
        $columnsStr = implode(', ', array_keys($testData));
        $valuesStr = implode(', ', array_fill(0, count($testData), '?'));
        
        $sql = "INSERT INTO mh_cliente_consumidor ($columnsStr) VALUES ($valuesStr)";
        
        $stmt = $pdo->prepare($sql);
        $result = $stmt->execute(array_values($testData));
        
        if ($result) {
            $id = $pdo->lastInsertId();
            echo "<p style='color:green'>✅ Registro de prueba insertado con ID: $id</p>";
            
            // Limpiar el registro de prueba
            $pdo->query("DELETE FROM mh_cliente_consumidor WHERE id = $id");
        }
        
        // 5. Cerrar explícitamente la conexión
        $pdo = null;
        
    } catch (PDOException $e) {
        echo "<p style='color:red'>❌ Error: " . $e->getMessage() . "</p>";
        
        // Error específico de columna faltante
        if (strpos($e->getMessage(), 'active') !== false) {
            echo "<p><strong>Solución rápida:</strong> Actualiza tu consulta SQL para no incluir 'active'</p>";
        }
    }
}

// ============================================
// LIMPIAR CACHÉS DE PHP
// ============================================
echo "<h2>Limpiando caches PHP</h2>";

$cachesCleared = [];

// OPcache
if (function_exists('opcache_reset')) {
    opcache_reset();
    $cachesCleared[] = 'OPcache';
}

// APCu
if (function_exists('apcu_clear_cache')) {
    apcu_clear_cache();
    $cachesCleared[] = 'APCu';
}

// Cache de sesiones
if (function_exists('session_cache_limiter')) {
    session_cache_limiter('');
    session_cache_expire(0);
    $cachesCleared[] = 'Session cache';
}

if (!empty($cachesCleared)) {
    echo "<p style='color:green'>✅ Caches limpiados: " . implode(', ', $cachesCleared) . "</p>";
} else {
    echo "<p style='color:orange'>⚠️ No se encontraron caches activos para limpiar</p>";
}

// ============================================
// RECOMENDACIONES ESPECÍFICAS
// ============================================
echo "<h2>Pasos siguientes</h2>";

echo <<<HTML
<ol>
    <li><strong>Si usas PDO directamente</strong>:
        <ul>
            <li>Actualiza la consulta SQL en CustomerService para incluir/excluir 'active' según corresponda</li>
            <li>Asegúrate de que el array de datos coincida con las columnas en la consulta</li>
        </ul>
    </li>
    
    <li><strong>Si usas un ORM (Eloquent, Doctrine, etc.)</strong>:
        <ul>
            <li>Ejecuta el comando de limpieza de caché del ORM</li>
            <li>Regenera los metadatos de la entidad</li>
            <li>Actualiza el mapeo de la entidad para incluir la columna 'active'</li>
        </ul>
    </li>
    
    <li><strong>Si usas un framework</strong>:
        <ul>
            <li>Laravel: <code>php artisan cache:clear && php artisan config:clear</code></li>
            <li>Symfony: <code>php bin/console cache:clear</code></li>
            <li>CodeIgniter: Borra los archivos en <code>writable/cache/</code></li>
        </ul>
    </li>
</ol>

<p><strong>Nota:</strong> El error ocurre porque tu código PHP está preparando una consulta que incluye 'active', pero el driver de base de datos (cuando prepara la consulta) no encuentra esa columna en su caché interno de metadatos.</p>
HTML;

echo '<p><a href="../test_customer.php" style="background:blue;color:white;padding:10px;text-decoration:none;">🔄 Probar CustomerService nuevamente</a></p>';
?>