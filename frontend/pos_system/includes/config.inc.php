<?php
/**
 * NebulaPOS POS - configuración local SQLite
 * Team MYTS
 *
 * Producción: la base de datos vive fuera del webroot en
 * /opt/nebulapos/backend/database.sqlite
 */

define('DB_DRIVER', 'sqlite');
define('DB_PATH', getenv('NEBULAPOS_DB_PATH') ?: '/opt/nebulapos/backend/database.sqlite');
define('POOLED_CONNECTION', false);
define('DB_SSL_MODE', null);
