<?php
/**
 * Sistema POS - Cerrar Sesión
 * =============================
 * Finaliza la sesión del usuario.
 * * @package    POS System
 * @author     Nebula DET Team
 * @version    2.0.0
 */
/**
 * Sistema POS - Logout
 * =====================================
 * Destruye la sesión de usuario de forma segura y redirige al login.
 */

// 1. Incluir SOLO la configuración mínima necesaria (BASE_URL).
// Asumimos que base_config.php está un nivel arriba de donde se ejecuta el script.
// Ajusta la ruta si 'logout.php' no está en la raíz del proyecto.
require_once __DIR__ . '/config/baseconf.php'; 

session_start();

// 2. Destruir datos de sesión
$_SESSION = array();

// 3. Destruir la cookie de sesión (Mejora de seguridad)
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// 4. Finalmente, destruir el archivo de sesión.
session_destroy();

// 5. Redireccionar a la ruta base de login (usando la constante BASE_URL)
// Esto genera la URL limpia: /posys/pos_system/login
header('Location: ' . BASE_URL . '/login');
exit();
?>
