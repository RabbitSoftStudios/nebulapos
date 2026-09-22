<?php
/**
 * NebulaPOS POS - autenticación local SQLite.
 */

declare(strict_types=1);

require_once __DIR__ . '/pg_connection.php';

function checkPOSAuth(): void
{
    if (session_status() === PHP_SESSION_NONE) session_start();

    if (empty($_SESSION['pos_authenticated'])) {
        header('Location: /login.html');
        exit;
    }
}

function checkAdminAuth(): void
{
    checkPOSAuth();
    if (($_SESSION['cashier']['rol'] ?? 'user') !== 'admin') {
        header('Location: /pos_system/');
        exit;
    }
}

function registerUser(string $username, string $email, string $password, ?int $companyId = null): array
{
    $username = trim($username);
    $email = strtolower(trim($email));

    if ($username === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < 8) {
        return ['success' => false, 'error' => 'Datos de registro inválidos. La contraseña debe tener al menos 8 caracteres.'];
    }

    $db = pg_pool();
    $check = $db->prepare('SELECT id FROM users WHERE lower(email) = lower(?) LIMIT 1');
    $check->execute([$email]);
    if ($check->fetch()) return ['success' => false, 'error' => 'El correo ya está registrado.'];

    if ($companyId === null) {
        $db->beginTransaction();
        try {
            $company = $db->prepare('INSERT INTO companies (nombre) VALUES (?)');
            $company->execute([$username]);
            $companyId = (int)$db->lastInsertId();

            $stmt = $db->prepare('INSERT INTO users (name,email,password,company_id) VALUES (?,?,?,?)');
            $stmt->execute([$username, $email, password_hash($password, PASSWORD_DEFAULT), $companyId]);
            $userId = (int)$db->lastInsertId();
            $db->commit();
            return ['success' => true, 'user_id' => $userId, 'company_id' => $companyId];
        } catch (Throwable $e) {
            if ($db->inTransaction()) $db->rollBack();
            error_log('[AUTH] register error: ' . $e->getMessage());
            return ['success' => false, 'error' => 'No se pudo crear el usuario.'];
        }
    }

    $stmt = $db->prepare('INSERT INTO users (name,email,password,company_id) VALUES (?,?,?,?)');
    $stmt->execute([$username, $email, password_hash($password, PASSWORD_DEFAULT), $companyId]);
    return ['success' => true, 'user_id' => (int)$db->lastInsertId(), 'company_id' => $companyId];
}
