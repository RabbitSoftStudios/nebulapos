<?php
/**
 * NebulaPOS - conexión SQLite compartida.
 * La base de datos permanece fuera del webroot.
 */

declare(strict_types=1);

function getDBConnection(): PDO
{
    $dbPath = getenv('NEBULAPOS_DB_PATH') ?: '/opt/nebulapos/backend/database.sqlite';
    $dir = dirname($dbPath);
    if (!is_dir($dir)) mkdir($dir, 0770, true);

    try {
        $pdo = new PDO('sqlite:' . $dbPath, null, null, [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]);
        $pdo->exec('PRAGMA foreign_keys = ON');
        $pdo->exec('PRAGMA busy_timeout = 5000');
        initTables($pdo);
        return $pdo;
    } catch (PDOException $e) {
        http_response_code(500);
        header('Content-Type: application/json');
        echo json_encode(['status' => 'error', 'message' => 'Error al conectar a la base de datos.']);
        exit;
    }
}

function initTables(PDO $pdo): void
{
    $pdo->exec("CREATE TABLE IF NOT EXISTS users (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        name TEXT NOT NULL,
        email TEXT UNIQUE NOT NULL,
        password TEXT NOT NULL,
        company_id INTEGER,
        active INTEGER NOT NULL DEFAULT 1,
        created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
    )");

    $pdo->exec("CREATE TABLE IF NOT EXISTS companies (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        nit TEXT UNIQUE,
        nombre TEXT NOT NULL,
        nombre_comercial TEXT,
        nrc TEXT,
        direccion TEXT,
        municipio TEXT,
        departamento TEXT,
        telefono TEXT,
        email TEXT,
        activo INTEGER NOT NULL DEFAULT 1,
        created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
    )");

    $pdo->exec("CREATE TABLE IF NOT EXISTS transactions (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        sale_id INTEGER,
        transaction_id TEXT UNIQUE,
        reference TEXT NOT NULL,
        amount_in_cents INTEGER NOT NULL,
        currency TEXT NOT NULL DEFAULT 'USD',
        customer_email TEXT,
        status TEXT NOT NULL,
        payment_method_type TEXT,
        wompi_response TEXT,
        created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
    )");

    $pdo->exec("CREATE TABLE IF NOT EXISTS payments (
        id INTEGER PRIMARY KEY AUTOINCREMENT,
        sale_id INTEGER,
        transaction_id INTEGER,
        method TEXT NOT NULL,
        amount REAL NOT NULL DEFAULT 0,
        status TEXT NOT NULL DEFAULT 'pending',
        reference TEXT,
        created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
    )");
}
