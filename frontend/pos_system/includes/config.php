<?php
/**
 * Sistema POS - Configuración General
 * =====================================
 * Carga las variables de entorno para su uso en la aplicación de forma robusta.
 *
 * @package    POS System
 * @author     Nebula DET Team
 * @version    2.0.0
 */

// V3.0
// includes/config.php

// ************************************************************
// ** CONFIGURACIÓN E INICIALIZACIÓN DEL ENTORNO **
// ************************************************************

// 1. CARGA DEL AUTOLOADER (VENDOR)
// Ruta: Desde includes/ subimos 3 niveles (../../..) para llegar a la raíz 'NebulaDET_DEV_FREE_MINI'
// Si la estructura cambió, ajusta la ruta:
require_once __DIR__ . '/../../../vendor/autoload.php'; 

// 2. CARGA DEL ARCHIVO .ENV (dotenv)
// Ruta: Desde includes/ subimos 2 niveles (../..) para llegar a la carpeta 'pos_system' donde está el .env
try {
    // Usamos el directorio donde reside el archivo .env
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../'); 
    $dotenv->safeLoad();
} catch (\Throwable $e) {
    // Este mensaje se registrará si el archivo .env no se encuentra o es ilegible.
    error_log("FATAL: Error al cargar .env: " . $e->getMessage());
}

// ------------------------------------------------------------
// ** BLOQUE REDUNDANTE ELIMINADO **
// Se elimina todo el código manual de carga de .env que causaba el conflicto.
// ------------------------------------------------------------


// 3. Función de ayuda para obtener variables de entorno (maneja valores vacíos)
   if (!function_exists('env')) {
        function env($key, $default = null) {
            $value = null;

            if (isset($_ENV[$key])) {
                $value = $_ENV[$key];
            } elseif (isset($_SERVER[$key])) {
                $value = $_SERVER[$key];
            } else {
                $value = getenv($key);
            }

            if ($value === '' || $value === null || $value === false) {
                return $default;
            }

            return $value;
        }
    }

// if (!function_exists('env')) {
//     /**
//      * Obtiene el valor de una variable de entorno cargada por Dotenv.
//      */
//     function env($key, $default = null) {
//         $value = getenv($key);

//         // Si el valor es una cadena vacía o getenv falló (false), retornamos el valor por defecto.
//         if ($value === '' || $value === false) {
//             return $default;
//         }

//         return $value;
//     }
// }

// 4. Establecer zona horaria
// Usamos la función env() ya que ahora es funcional.
date_default_timezone_set(env('APP_TIMEZONE', 'America/El_Salvador'));

// 5. Configuración de errores
// El uso de env() permite controlar el modo de depuración desde el archivo .env
$debug_mode = filter_var(env('APP_DEBUG', 'false'), FILTER_VALIDATE_BOOLEAN);

if ($debug_mode) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    // Modo producción
    error_reporting(0);
    ini_set('display_errors', 0);
}


// V2.0
// require_once __DIR__ . '/../../../vendor/autoload.php'; 

// // Cargar las variables de entorno
// try {
//     // La ruta debe apuntar al directorio donde reside el archivo .env
//     $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../..'); 
//     $dotenv->safeLoad();
// } catch (\Throwable $e) {
//     // Manejo de error si el archivo .env no existe o no puede ser leído
//     // En desarrollo, puedes mostrar $e->getMessage();
//     error_log("Error al cargar .env: " . $e->getMessage()); 
//     // Si estás depurando, puedes usar: echo "Error al cargar .env: " . $e->getMessage(); exit;
// }

// // 1. Cargar el archivo .env de forma robusta
// $env_path = __DIR__ . '/../.env';
// if (file_exists($env_path)) {
//     $lines = file($env_path, FILE_IGNORE_EMPTY_LINES | FILE_SKIP_EMPTY_LINES);

//     foreach ($lines as $line) {
//         $line = trim($line); 

//         if (empty($line) || strpos($line, '#') === 0 || strpos($line, '=') === false) {
//             continue;
//         }

//         list($name, $value) = explode('=', $line, 2);

//         $name = trim($name);
//         // Limpiamos comillas simples/dobles y espacios del valor
//         $value = trim($value, " \t\n\r\0\x0B\"'"); 

//         if (empty($name)) {
//             continue;
//         }

//         // Asignar solo si no existe
//         if (!array_key_exists($name, $_SERVER) && !array_key_exists($name, $_ENV)) {
//             putenv(sprintf('%s=%s', $name, $value));
//             $_ENV[$name] = $value;
//             $_SERVER[$name] = $value;
//         }
//     }
// }

// // 2. Función de ayuda para obtener variables de entorno (maneja valores vacíos)
// if (!function_exists('env')) {
//     /**
//      * Obtiene el valor de una variable de entorno.
//      */
//     function env($key, $default = null) {
//         $value = null;

//         if (isset($_ENV[$key])) {
//             $value = $_ENV[$key];
//         } elseif (isset($_SERVER[$key])) {
//             $value = $_SERVER[$key];
//         } else {
//             $value = getenv($key);
//         }

//         // Si el valor es una cadena vacía, retornamos el valor por defecto.
//         if ($value === '' || $value === null || $value === false) {
//             return $default;
//         }

//         return $value;
//     }
// }

// // 3. Establecer zona horaria
// date_default_timezone_set(env('APP_TIMEZONE', 'America/El_Salvador'));

// // 4. Configuración de errores
// // if (filter_var(env('APP_DEBUG'), FILTER_VALIDATE_BOOLEAN)) {
// //     error_reporting(E_ALL);
// //     ini_set('display_errors', 1);
// // } else {
// //     error_reporting(0);
// //     ini_set('display_errors', 0);
// // }
// error_reporting(E_ALL);
// ini_set('display_errors', 1);

// v1.0
// 1. Cargar el archivo .env de forma robusta
// $env_path = __DIR__ . '/../.env';
// if (file_exists($env_path)) {
//     $lines = file($env_path, FILE_IGNORE_EMPTY_LINES | FILE_SKIP_EMPTY_LINES);

//     foreach ($lines as $line) {
//         $line = trim($line); 

//         if (empty($line) || strpos($line, '#') === 0 || strpos($line, '=') === false) {
//             continue;
//         }

//         list($name, $value) = explode('=', $line, 2);

//         $name = trim($name);
//         // Limpiamos comillas simples/dobles y espacios del valor
//         $value = trim($value, " \t\n\r\0\x0B\"'"); 

//         if (empty($name)) {
//             continue;
//         }

//         // Asignar solo si no existe
//         if (!array_key_exists($name, $_SERVER) && !array_key_exists($name, $_ENV)) {
//             putenv(sprintf('%s=%s', $name, $value));
//             $_ENV[$name] = $value;
//             $_SERVER[$name] = $value;
//         }
//     }
// }

// // 2. Función de ayuda para obtener variables de entorno (maneja valores vacíos)
// if (!function_exists('env')) {
//     /**
//      * Obtiene el valor de una variable de entorno.
//      */
//     function env($key, $default = null) {
//         $value = null;

//         if (isset($_ENV[$key])) {
//             $value = $_ENV[$key];
//         } elseif (isset($_SERVER[$key])) {
//             $value = $_SERVER[$key];
//         } else {
//             $value = getenv($key);
//         }

//         // Si el valor es una cadena vacía, retornamos el valor por defecto.
//         if ($value === '' || $value === null || $value === false) {
//             return $default;
//         }

//         return $value;
//     }
// }

// // 3. Establecer zona horaria
// date_default_timezone_set(env('APP_TIMEZONE', 'America/El_Salvador'));

// // 4. Configuración de errores
// // if (filter_var(env('APP_DEBUG'), FILTER_VALIDATE_BOOLEAN)) {
// //     error_reporting(E_ALL);
// //     ini_set('display_errors', 1);
// // } else {
// //     error_reporting(0);
// //     ini_set('display_errors', 0);
// // }
// error_reporting(E_ALL);
// ini_set('display_errors', 1);

// 4. DEFINICIÓN DE CONSTANTES GLOBALES (¡Añadir o verificar esto!)
// define('APP_NAME', env('APP_NAME', 'Sistema POS'));

// // 5. Constantes de la caja/cajero
// define('POS_CAJA_NUMERO', env('CAJA_NUMERO', 1));
// define('POS_CAJERO_DEFAULT', env('CAJERO_DEFAULT_CODE', '0001'));

// // 6. Constantes de la base de datos (Supabase)
// define('TABLE_PRODUCTS', env('DB_TABLE_PRODUCTS', 'products'));
// define('IVA_RATE', env('IVA_RATE', 0.13));

// // 7. DEFINICIÓN DE CONSTANTES DE SUPABASE (¡Añadir esto!)
// define('SUPABASE_URL', env('SUPABASE_URL', 'https://iyteellzegojaoozwhev.supabase.co'));
// define('SUPABASE_KEY', env('SUPABASE_KEY', 'eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJpc3MiOiJzdXBhYmFzZSIsInJlZiI6Iml5dGVlbGx6ZWdvamFvb3p3aGV2Iiwicm9sZSI6ImFub24iLCJpYXQiOjE3NDQ1NjMyNjksImV4cCI6MjA2MDEzOTI2OX0._qLHN4oGn08OSKNNAj9oftGaKGuKdbFcWuvOlFk2ilw'));
// define('SUPABASE_JWT', env('SUPABASE_JWT', null)); // Opcional, si usas una clave JWT estática

// // 8. Constante necesaria para el tiempo de espera de la solicitud (Línea 131 en supabase.php)
// define('API_TIMEOUT', env('API_TIMEOUT', 30)); // 30 segundos por defecto.

