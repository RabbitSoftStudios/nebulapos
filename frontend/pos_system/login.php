<?php
/**
 * NebulaPOS POS - Login local.
 */
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/config/constants.php';
require_once __DIR__ . '/includes/auth.php';

if (session_status() === PHP_SESSION_NONE) session_start();

$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim((string)($_POST['username'] ?? ''));
    $password = (string)($_POST['password'] ?? '');

    try {
        $db = pg_pool();
        $stmt = $db->prepare(
            'SELECT u.id, u.name, u.email, u.password, u.company_id, u.active
             FROM users u
             WHERE (lower(u.email) = lower(:identity) OR lower(u.name) = lower(:identity))
             LIMIT 1'
        );
        $stmt->execute([':identity' => $username]);
        $user = $stmt->fetch();

        if ($user && (int)$user['active'] === 1 && password_verify($password, (string)$user['password'])) {
            session_regenerate_id(true);
            $_SESSION['pos_authenticated'] = true;
            $_SESSION['user_id'] = (int)$user['id'];
            $_SESSION['empresa_id'] = $user['company_id'] !== null ? (int)$user['company_id'] : null;
            $_SESSION['cashier'] = [
                'id' => (int)$user['id'],
                'name' => $user['name'],
                'code' => 'USER-' . $user['id'],
                'rol' => 'user'
            ];

            header('Location: /pos_system/');
            exit;
        }

        $error_message = 'Usuario o contraseña incorrectos.';
    } catch (Throwable $e) {
        error_log('[AUTH] login error: ' . $e->getMessage());
        $error_message = 'No fue posible validar la sesión.';
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars(APP_NAME) ?> - Login</title>
    <style>
        body { background: #f4f6f8; min-height: 100vh; display:flex; align-items:center; justify-content:center; margin:0; padding:20px; font-family:Arial,sans-serif; }
        .login-container { background:#fff; border-radius:15px; padding:40px; box-shadow:0 20px 40px rgba(0,0,0,.1); max-width:400px; width:100%; }
        .login-header { text-align:center; margin-bottom:30px; }
        .login-header h2 { margin:0 0 8px; }
        label { display:block; margin-bottom:6px; }
        input { width:100%; box-sizing:border-box; padding:12px; margin-bottom:18px; border:1px solid #ccc; border-radius:8px; }
        button { width:100%; padding:13px; border:0; border-radius:8px; cursor:pointer; font-weight:700; }
        .error { background:#fee2e2; color:#991b1b; padding:12px; border-radius:8px; margin-bottom:18px; }
    </style>
</head>
<body>
<div class="login-container">
    <div class="login-header">
        <h2><?= htmlspecialchars(APP_NAME) ?></h2>
        <p>Acceso al Punto de Venta</p>
    </div>
    <?php if ($error_message): ?><div class="error"><?= htmlspecialchars($error_message) ?></div><?php endif; ?>
    <form method="POST" autocomplete="on">
        <label for="username">Usuario o correo</label>
        <input type="text" id="username" name="username" required autofocus>
        <label for="password">Contraseña</label>
        <input type="password" id="password" name="password" required>
        <button type="submit">Iniciar sesión</button>
    </form>
</div>
</body>
</html>
