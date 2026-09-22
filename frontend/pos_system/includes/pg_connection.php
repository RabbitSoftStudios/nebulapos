<?php
/**
 * PostgreSQL pooled connection with automatic reconnection
 * Team MYTS
 */

require_once __DIR__ . '/config.inc.php';

/**
 * Verifica si la conexión PDO está activa
 * @param PDO $pdo
 * @return bool
 */
function is_db_connected(PDO &$pdo): bool {
    if ($pdo === null) return false;
    
    try {
        // Intentar un PING simple a la BD
        $pdo->query('SELECT 1');
        return true;
    } catch (PDOException $e) {
        error_log("Database connection check failed: " . $e->getMessage());
        return false;
    }
}

/**
 * Retorna una instancia única de conexión PDO a PostgreSQL con reconexión automática.
 * @param bool $force_reconnect Forzar reconexión incluso si existe instancia
 * @return PDO
 */
function pg_pool(bool $force_reconnect = false): PDO {
    static $instance = null;
    static $last_connection_attempt = 0;

    // Si forzamos reconexión o la conexión está muerta, reintentar
    if ($force_reconnect || $instance === null || !is_db_connected($instance)) {
        // Evitar intentos de conexión demasiado frecuentes (mínimo 2 segundos entre intentos)
        $now = time();
        if ($now - $last_connection_attempt < 2) {
            sleep(2 - ($now - $last_connection_attempt));
        }
        $last_connection_attempt = time();

        try {
            $dsn = sprintf(
                "pgsql:host=%s;port=%s;dbname=%s;sslmode=require;sslrootcert=%s",
                DB_HOST,
                DB_PORT,
                DB_NAME,
                __DIR__ . '/../config/rds-ca-2019-root.pem'
            );

            $instance = new PDO(
                $dsn,
                DB_USER,
                DB_PASSWORD,
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    // Desabilitar persistent para evitar problemas de conexión stale
                    PDO::ATTR_PERSISTENT => false,
                    // Timeout para conexión
                    PDO::ATTR_TIMEOUT => 10
                ]
            );

            // Configurar la sesión de PostgreSQL
            $instance->exec("SET client_encoding = 'UTF8'");
            $instance->exec("SET default_transaction_isolation = 'read committed'");
            
            error_log("Database connection established/reconnected successfully");
        } catch (PDOException $e) {
            error_log("CRITICAL: Error connecting to database: " . $e->getMessage());
            throw new Exception("Database connection failed: " . $e->getMessage());
        }
    }

    return $instance;
}

// Ejemplo de uso:
$db = pg_pool();