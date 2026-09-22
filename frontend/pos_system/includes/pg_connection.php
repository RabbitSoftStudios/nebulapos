<?php
/**
 * NebulaPOS - conexión PDO local SQLite
 *
 * Mantiene el nombre histórico pg_connection.php para evitar romper los
 * módulos existentes, pero ya no utiliza PostgreSQL ni Supabase.
 */

declare(strict_types=1);

require_once __DIR__ . '/config.inc.php';
require_once __DIR__ . '/sqlite_schema.php';

function is_db_connected(PDO &$pdo): bool
{
    try {
        $pdo->query('SELECT 1');
        return true;
    } catch (Throwable $e) {
        error_log('[DB] SQLite connection check failed: ' . $e->getMessage());
        return false;
    }
}

function pg_pool(bool $force_reconnect = false): PDO
{
    static $instance = null;

    if (!$force_reconnect && $instance instanceof PDO && is_db_connected($instance)) {
        return $instance;
    }

    $dbPath = DB_PATH;
    $directory = dirname($dbPath);

    if (!is_dir($directory)) {
        if (!mkdir($directory, 0770, true) && !is_dir($directory)) {
            throw new RuntimeException('No se pudo crear el directorio SQLite: ' . $directory);
        }
    }

    try {
        $instance = new PDO('sqlite:' . $dbPath, null, null, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);

        // Compatibilidad con SQL heredado de PostgreSQL utilizado por el POS.
        if (method_exists($instance, 'sqliteCreateFunction')) {
            $instance->sqliteCreateFunction('now', static fn(): string => date('Y-m-d H:i:s'), 0);
            $instance->sqliteCreateFunction('CONCAT', static function (...$args): string {
                return implode('', array_map(static fn($v) => (string)($v ?? ''), $args));
            }, -1);
            $instance->sqliteCreateFunction('RIGHT', static function ($value, $length): string {
                $value = (string)$value;
                $length = max(0, (int)$length);
                return $length === 0 ? '' : substr($value, -$length);
            }, 2);
        }

        initializeNebulaPOSSchema($instance);

        error_log('[DB] SQLite connection established: ' . $dbPath);
        return $instance;
    } catch (PDOException $e) {
        $instance = null;
        error_log('[DB] CRITICAL SQLite error: ' . $e->getMessage());
        throw new RuntimeException('Database connection failed: ' . $e->getMessage(), 0, $e);
    }
}

// Mantener compatibilidad con módulos que esperan $db al incluir el archivo.
$db = pg_pool();
