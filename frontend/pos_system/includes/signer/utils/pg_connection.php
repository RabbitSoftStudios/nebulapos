<?php
/**
 * PostgreSQL pooled connection
 * Team MYTS
 */

require_once __DIR__ . '/pg_config.inc.php';

/**
 * Retorna una instancia única de conexión PDO a PostgreSQL.
 */
if (!function_exists('pg_pool')) {
    function pg_pool(): PDO {
        static $instance = null;

        if ($instance === null) {
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
                        // Mantiene la conexión abierta para simular el pooling
                        PDO::ATTR_PERSISTENT => true 
                    ]
                );
            } catch (PDOException $e) {
                // Manejo de error de conexión
                die("Error conectando a la base de datos: " . $e->getMessage());
            }
        }

        return $instance;
    }
}
