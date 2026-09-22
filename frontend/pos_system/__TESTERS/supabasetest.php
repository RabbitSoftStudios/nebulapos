<?php
// test_env.php (Dentro de pos_system/)

// NOTA: Asegúrate de que las rutas relativas sean correctas para tu estructura.

// ************************************************************
// ** CONFIGURACIÓN DE RUTAS Y CUMPLIMIENTO DEL ESTÁNDAR **
// ************************************************************

// 1. Definir la función env() localmente para usar $_ENV
if (!function_exists('env')) {
    /**
     * Obtiene el valor de una variable de entorno, verificando primero $_ENV y $_SERVER.
     * Esta es la clave para evitar el fallo de getenv() en algunos servidores.
     */
    function env($key, $default = null) {
        // 1. Verificar si la variable fue cargada por Dotenv en $_ENV
        if (isset($_ENV[$key])) {
            $value = $_ENV[$key];
        // 2. Verificar si está en $_SERVER
        } elseif (isset($_SERVER[$key])) {
            $value = $_SERVER[$key];
        // 3. Recurrir a getenv() (el que sabemos que puede fallar)
        } else {
            $value = getenv($key);
        }

        // Si el valor es una cadena vacía o getenv/isset falló, retornar el valor por defecto.
        if ($value === '' || $value === null || $value === false) {
            return $default;
        }

        return $value;
    }
}


echo "<h1>Prueba de Carga de Variables de Entorno</h1>";
echo "<h2>Rutas de Búsqueda (Asumidas)</h2>";

// Asumimos que vendor está 2 niveles arriba del script
$vendorPath = __DIR__ . '/../../vendor/autoload.php';
// Asumimos que .env está en el mismo directorio que este script
$envDirPath = __DIR__; 

echo "Vendor Path: " . htmlspecialchars($vendorPath) . "<br>";
echo ".env Dir Path: " . htmlspecialchars($envDirPath) . "<br>";
echo "---<br>";


// 2. Carga del Autoloader
// La ruta es '/../../' porque estamos en 'pos_system/' y 'vendor' está en la raíz del proyecto.
require_once $vendorPath; 

// 3. Carga del archivo .env
// Usamos __DIR__ que apunta al directorio pos_system/
try {
    $dotenv = Dotenv\Dotenv::createImmutable($envDirPath);
    $dotenv->safeLoad();
    echo "<p style='color: green;'>✅ Dotenv cargado correctamente (Leyó el archivo).</p>";
} catch (\Throwable $e) {
    echo "<p style='color: red;'>❌ ERROR CRÍTICO al cargar Dotenv: " . htmlspecialchars($e->getMessage()) . "</p>";
}

// 4. PRUEBA DE LA VARIABLE
$supabaseUrl_env = env('SUPABASE_URL');
$supabaseUrl_raw_env = $_ENV['SUPABASE_URL'] ?? null;
$supabaseUrl_raw_getenv = getenv('SUPABASE_URL');


echo "<h2>Resultados de Variables:</h2>";

echo "1. SUPABASE_URL (Función env()): " . (empty($supabaseUrl_env) ? "<b style='color: red;'>¡VACÍA!</b>" : "<b style='color: green;'>OK</b>") . "<br>";
echo "   Valor RAW (Función env()): " . htmlspecialchars($supabaseUrl_env ?? 'NULL/EMPTY') . "<br>";
echo "---<br>";

echo "2. Valor en \$_ENV (Inyección de Dotenv): " . (empty($supabaseUrl_raw_env) ? "<b style='color: red;'>¡VACÍA!</b>" : "<b style='color: green;'>OK</b>") . "<br>";
echo "   Valor RAW (\$_ENV): " . htmlspecialchars($supabaseUrl_raw_env ?? 'NULL/EMPTY') . "<br>";
echo "---<br>";

echo "3. Valor en getenv() (Lo que usa tu constante antigua): " . (empty($supabaseUrl_raw_getenv) ? "<b style='color: red;'>¡VACÍA!</b>" : "<b style='color: green;'>OK</b>") . "<br>";
echo "   Valor RAW (getenv()): " . htmlspecialchars($supabaseUrl_raw_getenv ?? 'NULL/EMPTY') . "<br>";
echo "---<br>";


// 5. Mensaje de diagnóstico final
if (!empty($supabaseUrl_env)) {
    echo "<p style='color: green;'><b>¡DIAGNÓSTICO FINAL: ÉXITO!</b> La URL de Supabase se leyó correctamente de \$E/SERVER. **El sistema principal DEBE usar la función `env()` para definir la constante.**</p>";
} else {
    echo "<p style='color: red;'><b>¡DIAGNÓSTICO FINAL: FALLO!</b> El valor de SUPABASE_URL es nulo o vacío en \$E/SERVER. **Revisa la línea de SUPABASE_URL en tu archivo .env: debe ser `SUPABASE_URL=\"https://...\"`**</p>";
}

// V1.0
// echo "<h1>Prueba de Carga de Variables de Entorno</h1>";
// echo "<h2>Rutas de Búsqueda (Asumidas)</h2>";
// echo "Vendor: " . __DIR__ . '/../../vendor/autoload.php' . "<br>";
// echo ".env Dir: " . __DIR__ . '/../' . "<br>";
// echo "---<br>";


// // 1. Carga del Autoloader (debe subir 2 niveles si .env está en pos_system/)
// require_once __DIR__ . '/../../vendor/autoload.php'; 

// // 2. Carga del archivo .env (dotenv)
// // El .env está en la carpeta actual pos_system/
// try {
//     $dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
//     //$dotenv = Dotenv\Dotenv::createImmutable(__DIR__); // Busca .env en el directorio actual
//     $dotenv->safeLoad();
//     echo "<p style='color: green;'>✅ Dotenv cargado correctamente.</p>";
// } catch (\Throwable $e) {
//     echo "<p style='color: red;'>❌ ERROR CRÍTICO al cargar Dotenv: " . htmlspecialchars($e->getMessage()) . "</p>";
// }

// // 3. Definir la función env() para testing
// if (!function_exists('env')) {
//     function env($key, $default = null) {
//         $value = getenv($key);
//         return ($value === '' || $value === false) ? $default : $value;
//     }
// }

// // 4. PRUEBA DE LA VARIABLE
// $supabaseUrl = env('SUPABASE_URL');

// echo "<h2>Resultados de Variables:</h2>";
// echo "SUPABASE_URL (getenv): " . (empty($supabaseUrl) ? "<b style='color: red;'>¡VACÍA!</b>" : "<b style='color: green;'>OK</b>") . "<br>";
// echo "Valor RAW: " . htmlspecialchars($supabaseUrl) . "<br>";
// echo "---<br>";

// // Mensaje de diagnóstico final
// if (empty($supabaseUrl)) {
//     echo "<p style='color: red;'><b>CONCLUSIÓN:</b> La librería dotenv NO pudo leer la variable. Revisa el formato de tu .env o la ruta de búsqueda.</p>";
// } else {
//     echo "<p style='color: green;'><b>CONCLUSIÓN:</b> La carga de variables FUNCIONA. El problema está en la definición de constantes o el orden de inclusión en el sistema principal.</p>";
// }
?>