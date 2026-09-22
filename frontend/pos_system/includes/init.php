<?php
/**
 * NebulaPOS POS - bootstrap local.
 * No carga Supabase ni requiere .env para la base de datos.
 */
declare(strict_types=1);

if (session_status() === PHP_SESSION_NONE) {
    session_start([
        'cookie_httponly' => true,
        'cookie_secure' => !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off',
        'cookie_samesite' => 'Lax',
        'use_strict_mode' => true,
    ]);
}

require_once __DIR__ . '/config.inc.php';
require_once __DIR__ . '/pg_connection.php';
