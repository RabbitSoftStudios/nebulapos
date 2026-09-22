<?php
/**
 * NebulaPOS POS - configuración general local.
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
