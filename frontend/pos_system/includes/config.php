<?php
/**
 * NebulaPOS POS - configuración general local.
 * No establece conexiones externas; el almacenamiento es SQLite local.
 */
declare(strict_types=1);

if (!function_exists('env')) {
    function env($key, $default = null) {
        $value = $_ENV[$key] ?? $_SERVER[$key] ?? getenv($key);
        return ($value === false || $value === '' || $value === null) ? $default : $value;
    }
}

date_default_timezone_set(env('APP_TIMEZONE', 'America/El_Salvador'));
$debug = filter_var(env('APP_DEBUG', false), FILTER_VALIDATE_BOOLEAN);
error_reporting($debug ? E_ALL : 0);
ini_set('display_errors', $debug ? '1' : '0');

require_once __DIR__ . '/config.inc.php';

// Compatibilidad con vistas antiguas: el valor es únicamente un marcador local.
// El cliente compatible se conecta a SQLite y nunca realiza una llamada remota.
if (!defined('SUPABASE_URL')) define('SUPABASE_URL', 'sqlite://local');
if (!defined('SUPABASE_KEY')) define('SUPABASE_KEY', 'local');
