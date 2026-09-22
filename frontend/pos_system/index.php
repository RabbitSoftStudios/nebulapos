<?php
/**
 * Sistema POS - Punto de Entrada
 * ===============================
 * Carga las configuraciones y dirige la solicitud al enrutador.
 * * @package    POS System
 * @author     Nebula DET Team
 * @version    2.0.0
 */

// 1. Cargar dependencias y constantes
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/config/constants.php';
require_once __DIR__ . '/config/routes.php';

// 2. Inicializar la sesión
// 2. Inicializar la sesión
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 3. Ejecutar el enrutador
$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
route($uri, $routes);
?>
