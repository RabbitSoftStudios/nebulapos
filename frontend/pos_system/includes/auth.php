<?php
/**
 * Sistema POS - Funciones de Autenticación
 * ==========================================
 * Contiene funciones para manejo de sesión y permisos.
 * * @package    POS System
 * @author     Nebula DET Team
 * @version    2.0.0
 */

// Estas funciones dependen de la lógica en login.php y de la sesión.
// La función `authenticateUser` está definida en `login.php`.

/**
 * Verifica si el usuario está autenticado para acceder al POS.
 */
function checkPOSAuth() {
    // Nota: session_start() debe ser llamado antes si no se usa en el index.php
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    if (!isset($_SESSION['pos_authenticated']) || $_SESSION['pos_authenticated'] !== true) {
        // Redirigir al login usando BASE_URL de forma absoluta
        header('Location: ' . BASE_URL . '/login'); 
        exit();
    }

    // MODO DEVELOPER: Permitir acceso si es administrador
    if (isset($_SESSION['cashier']['code']) && $_SESSION['cashier']['code'] === 'ADMIN') {
        return; // Acceso total concedido
    }
}

/**
 * Verifica si el usuario está autenticado y tiene permisos de administrador.
 */
function checkAdminAuth() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    if (!isset($_SESSION['pos_authenticated']) || $_SESSION['pos_authenticated'] !== true) {
        header('Location: ' . BASE_URL . '/login'); 
        exit();
    }
    
    // Lógica de permisos de administrador simulada
    $isAdmin = ($_SESSION['cashier']['code'] ?? 'USER') === 'ADMIN'; 
    
    if (!$isAdmin) {
        // Redirigir al POS si no es admin, usando BASE_URL
        header('Location: ' . BASE_URL . '/pos');
        exit();
    }
}

/**
 * Simula el registro de un nuevo usuario en Supabase.
 */
function registerUser($username, $email, $password) {
    // Implementar lógica de registro en Supabase Auth
    // return supabase()->auth->signUpWithEmailAndPassword($email, $password);
    return true; // Simulación
}
?>
