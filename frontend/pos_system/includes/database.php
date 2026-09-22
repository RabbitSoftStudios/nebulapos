<?php
/**
 * NebulaPOS POS - database compatibility include.
 *
 * El POS ya no inicializa Supabase aquí. Todos los módulos utilizan la
 * persistencia SQLite definida en supabase.php/pg_connection.php.
 */
require_once __DIR__ . '/supabase.php';
