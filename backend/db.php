<?php
/**
 * NebulaPOS - Base de Datos SQLite PDO
 */

function getDBConnection() {
    $dbPath = __DIR__ . '/database.sqlite';

    try {
        $pdo = new PDO('sqlite:' . $dbPath);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

        // Inicialización de esquemas/tablas si no existen
        initTables($pdo);

        return $pdo;
    } catch (PDOException $e) {
        header('Content-Type: application/json');
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => 'Error al conectar a la base de datos: ' . $e->getMessage()]);
        exit;
    }
}

function initTables($pdo) {
    // Tabla de Usuarios
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            email TEXT UNIQUE NOT NULL,
            password TEXT NOT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )
    ");

    // Tabla de Transacciones / Pagos (Wompi)
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS transactions (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            transaction_id TEXT UNIQUE,
            reference TEXT NOT NULL,
            amount_in_cents INTEGER NOT NULL,
            currency TEXT NOT NULL,
            customer_email TEXT NOT NULL,
            status TEXT NOT NULL,
            payment_method_type TEXT,
            wompi_response TEXT,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        )
    ");
}
