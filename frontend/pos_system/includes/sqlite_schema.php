<?php
/**
 * NebulaPOS - SQLite schema
 * Team MYTS
 *
 * Idempotent schema for the local POS database.
 */

declare(strict_types=1);

function initializeNebulaPOSSchema(PDO $pdo): void
{
    $pdo->exec('PRAGMA foreign_keys = ON');
    $pdo->exec('PRAGMA journal_mode = WAL');
    $pdo->exec('PRAGMA busy_timeout = 5000');

    $tables = [
        "CREATE TABLE IF NOT EXISTS users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name TEXT NOT NULL,
            email TEXT NOT NULL UNIQUE,
            password TEXT NOT NULL,
            company_id INTEGER,
            active INTEGER NOT NULL DEFAULT 1,
            created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
        )",
        "CREATE TABLE IF NOT EXISTS companies (
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
        )",
        "CREATE TABLE IF NOT EXISTS dte_productos (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            company_id INTEGER,
            codigo_producto TEXT,
            codigo_barras TEXT,
            nombre_producto TEXT NOT NULL,
            descripcion TEXT,
            precio_venta REAL NOT NULL DEFAULT 0,
            precio_compra REAL NOT NULL DEFAULT 0,
            stock_actual REAL NOT NULL DEFAULT 0,
            stock_minimo REAL NOT NULL DEFAULT 0,
            es_venta_libre INTEGER NOT NULL DEFAULT 0,
            frecuente INTEGER NOT NULL DEFAULT 0,
            unidad_medida TEXT DEFAULT '59',
            iva_incluido INTEGER NOT NULL DEFAULT 1,
            borrado_logico INTEGER NOT NULL DEFAULT 0,
            created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
            actualizado_el TEXT DEFAULT CURRENT_TIMESTAMP
        )",
        "CREATE TABLE IF NOT EXISTS mh_cliente_consumidor (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            company_id INTEGER,
            p_nombre TEXT,
            s_nombre TEXT,
            p_apellido TEXT,
            s_apellido TEXT,
            dui TEXT,
            nit TEXT,
            nrc TEXT,
            telefono TEXT,
            email TEXT,
            direccion TEXT,
            municipio TEXT,
            departamento TEXT,
            active INTEGER NOT NULL DEFAULT 1,
            created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
        )",
        "CREATE TABLE IF NOT EXISTS mh_proveedor_contribuyente (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            company_id INTEGER,
            p_nombre TEXT,
            s_nombre TEXT,
            p_apellido TEXT,
            s_apellido TEXT,
            dui TEXT,
            nit TEXT,
            nrc TEXT,
            razon_social TEXT,
            nombre_comercial TEXT,
            telefono TEXT,
            email TEXT,
            direccion TEXT,
            created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
        )",
        "CREATE TABLE IF NOT EXISTS inventory_movements (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            company_id INTEGER,
            producto_id INTEGER NOT NULL,
            tipo TEXT NOT NULL,
            cantidad REAL NOT NULL,
            stock_anterior REAL,
            stock_nuevo REAL,
            referencia TEXT,
            usuario_id INTEGER,
            created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
        )",
        "CREATE TABLE IF NOT EXISTS pos_sales (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            company_id INTEGER,
            customer_id INTEGER,
            usuario_id INTEGER,
            numero_control TEXT,
            codigo_generacion TEXT,
            tipo_dte TEXT DEFAULT '01',
            fecha TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
            subtotal REAL NOT NULL DEFAULT 0,
            iva REAL NOT NULL DEFAULT 0,
            descuento REAL NOT NULL DEFAULT 0,
            total REAL NOT NULL DEFAULT 0,
            estado TEXT NOT NULL DEFAULT 'pendiente',
            metodo_pago TEXT,
            created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
        )",
        "CREATE TABLE IF NOT EXISTS pos_ventas (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            company_id INTEGER,
            customer_id INTEGER,
            usuario_id INTEGER,
            numero_control TEXT,
            codigo_generacion TEXT,
            tipo_dte TEXT DEFAULT '01',
            fecha TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP,
            subtotal REAL NOT NULL DEFAULT 0,
            iva REAL NOT NULL DEFAULT 0,
            descuento REAL NOT NULL DEFAULT 0,
            total REAL NOT NULL DEFAULT 0,
            estado TEXT NOT NULL DEFAULT 'pendiente',
            metodo_pago TEXT,
            created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
        )",
        "CREATE TABLE IF NOT EXISTS detalle_ventas (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            venta_id INTEGER NOT NULL,
            producto_id INTEGER NOT NULL,
            cantidad REAL NOT NULL,
            precio_unitario REAL NOT NULL DEFAULT 0,
            descuento REAL NOT NULL DEFAULT 0,
            subtotal REAL NOT NULL DEFAULT 0,
            iva REAL NOT NULL DEFAULT 0,
            total REAL NOT NULL DEFAULT 0
        )",
        "CREATE TABLE IF NOT EXISTS dte_facturas (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            company_id INTEGER,
            codigo_generacion TEXT UNIQUE,
            emisor_nit TEXT,
            numero_control TEXT,
            tipo_dte TEXT,
            fecha_emision TEXT,
            total_pagar REAL NOT NULL DEFAULT 0,
            documento_json TEXT,
            firma_local TEXT,
            sello_recepcion TEXT,
            respuesta_mh TEXT,
            estado_firma TEXT DEFAULT 'pendiente',
            origen TEXT,
            created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
        )",
        "CREATE TABLE IF NOT EXISTS transactions (
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
        )",
        "CREATE TABLE IF NOT EXISTS payments (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            sale_id INTEGER,
            transaction_id INTEGER,
            method TEXT NOT NULL,
            amount REAL NOT NULL DEFAULT 0,
            status TEXT NOT NULL DEFAULT 'pending',
            reference TEXT,
            created_at TEXT NOT NULL DEFAULT CURRENT_TIMESTAMP
        )",
        "CREATE INDEX IF NOT EXISTS idx_products_barcode ON dte_productos(codigo_barras)",
        "CREATE INDEX IF NOT EXISTS idx_products_company ON dte_productos(company_id)",
        "CREATE INDEX IF NOT EXISTS idx_customers_company ON mh_cliente_consumidor(company_id)",
        "CREATE INDEX IF NOT EXISTS idx_suppliers_company ON mh_proveedor_contribuyente(company_id)",
        "CREATE INDEX IF NOT EXISTS idx_sales_company ON pos_sales(company_id)",
        "CREATE INDEX IF NOT EXISTS idx_dte_company ON dte_facturas(company_id)",
        "CREATE INDEX IF NOT EXISTS idx_transactions_sale ON transactions(sale_id)"
    ];

    foreach ($tables as $sql) {
        $pdo->exec($sql);
    }
}
