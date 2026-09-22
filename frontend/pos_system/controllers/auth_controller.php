<?php
/**
 * Controlador de Autenticación
 * ============================
 * Maneja la verificación de sesión.
 */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!function_exists('checkAdminAuth')) {
    function checkAdminAuth() {
        if (empty($_SESSION['pos_authenticated'])) {
            // Ajustar ruta si estamos en subdirectorio views/
            $redirect = 'login.php';
            if (basename(getcwd()) === 'views') {
                $redirect = '../login.php';
            }
            header("Location: $redirect");
            exit;
        }
    }
}

// Ejecutar verificación si se incluye
// checkAdminAuth(); // Comentado para evitar redirección en inclusión si no es deseado explícitamente, pero las vistas lo llaman.
