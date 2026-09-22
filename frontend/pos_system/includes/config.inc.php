<?php
// config.inc.php

// Credenciales de Supabase PostgreSQL (Session Pooler)
define('DB_HOST', 'aws-0-us-east-2.pooler.supabase.com');
define('DB_PORT', '5432');
define('DB_NAME', 'postgres');
define('DB_USER', 'postgres.iyteellzegojaoozwhev');
define('DB_PASSWORD', '6QtxhADJfRSci3jF');
define('POOLED_CONNECTION', true);
// SSL flexible para desarrollo - no requiere certificado específico
define('DB_SSL_MODE', 'require');

// Verificar que las constantes estén definidas
if (!defined('DB_HOST') || !defined('DB_USER') || !defined('DB_PASSWORD')) {
    die("ERROR: Configuración de PostgreSQL incompleta. Verifica config.inc.php");
}