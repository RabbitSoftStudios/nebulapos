<?php
/**
 * Sistema POS - Pantalla de Login
 * ================================
 * Maneja la autenticación de usuarios.
 * * @package    POS System
 * @author     Nebula DET Team
 * @version    2.0.0
 */

require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/config/constants.php';
require_once __DIR__ . '/includes/auth.php'; // Incluye el manejo de autenticación

// session_start(); // Ahora se maneja en constants.php

$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    // Función de autenticación simulada (en un sistema real usaría auth.php y Supabase)
    $authResult = authenticateUser($username, $password);
    
    if ($authResult && isset($authResult['user'])) {
        $user = $authResult['user'];
        
        // Autenticación exitosa
        $_SESSION['pos_authenticated'] = true;
        $_SESSION['user_id'] = $user['id'] ?? 1;
        $_SESSION['empresa_uuid'] = $user['empresa_uuid'] ?? null;
        $_SESSION['cashier'] = [
            'id' => $user['id'] ?? 1,
            'name' => $user['nombre'] ?? $username,
            'code' => $user['codigo'] ?? 'ADMIN',
            'rol' => $user['rol'] ?? 'admin'
        ];
        
        // Redireccionar según el rol
        if (($user['rol'] ?? 'admin') === 'admin') {
            header('Location: ' . BASE_URL . '/dashboard');
        } else {
            header('Location: ' . BASE_URL . '/pos');
        }
        exit();
    } else {
        $error_message = 'Usuario o contraseña incorrectos.';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= APP_NAME ?> - Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { 
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); 
            min-height: 100vh; 
            display: flex; 
            align-items: center; 
            justify-content: center; 
            padding: 20px; 
        }
        .login-container {
            background: white;
            border-radius: 15px;
            padding: 40px;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
            max-width: 400px;
            width: 100%;
        }
        .login-header {
            text-align: center;
            margin-bottom: 30px;
        }
        .login-header h2 {
            color: #2c3e50;
            font-weight: 700;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <h2 class="text-primary"><?= APP_NAME ?></h2>
            <p>Acceso al Punto de Venta</p>
        </div>
        
        <?php if (!empty($error_message)): ?>
            <div class="alert alert-danger" role="alert">
                <?= htmlspecialchars($error_message) ?>
            </div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="mb-3">
                <label for="username" class="form-label"><i class="fas fa-user"></i> Usuario</label>
                <input type="text" class="form-control" id="username" name="username" value="admin" required autofocus>
            </div>
            <div class="mb-4">
                <label for="password" class="form-label"><i class="fas fa-lock"></i> Contraseña</label>
                <input type="password" class="form-control" id="password" name="password" value="admin123" required>
            </div>
            <div class="d-grid">
                <button type="submit" class="btn btn-primary btn-lg"><i class="fas fa-sign-in-alt"></i> Iniciar Sesión</button>
            </div>
        </form>
        
        <div class="text-center mt-4">
            <small class="text-muted">Versión <?= APP_VERSION ?></small>
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
<?php
// Función de autenticación simulada (DEBE SER REEMPLAZADA)
function authenticateUser($username, $password) {
    // **ADVERTENCIA: ESTO ES SOLO PARA DEMO. NUNCA USAR ASÍ EN PRODUCCIÓN.**
    // La autenticación real debe ir contra la tabla de usuarios de Supabase.
    
    // Si la configuración aún no existe, permitir credenciales de setup
    if (!defined('APP_VERSION')) {
        if ($username === 'admin' && $password === 'password') {
            return [
                'success' => true,
                'user' => [
                    'id' => 1,
                    'nombre' => 'Administrador',
                    'email' => 'admin@example.com',
                    'rol' => 'admin',
                    'empresa_uuid' => 'demo-uuid-empresa-1'
                ]
            ];
        }
        return false;
    }
    
    // Simulación de credenciales fijas para demo
    if ($username === 'admin' && $password === 'admin123') {
        return [
            'success' => true,
            'user' => [
                'id' => 1,
                'nombre' => 'Administrador',
                'email' => 'admin@example.com',
                'rol' => 'admin',
                'codigo' => 'ADMIN',
                'empresa_uuid' => 'demo-uuid-empresa-1'
            ]
        ];
    }

    // TODO: Lógica para autenticar contra Supabase (usando el cliente de supabase.php)
    // require_once __DIR__ . '/includes/supabase.php';
    // require_once __DIR__ . '/includes/UsuarioService.php';
    // $userService = new UsuarioService();
    // $result = $userService->verifyCredentials($username, $password);
    // if ($result['success']) {
    //     return [
    //         'success' => true,
    //         'user' => $result['data']
    //     ];
    // }
    
    return false;
}
?>
