<?php
/** NebulaPOS - autenticación pública SQLite */
declare(strict_types=1);
header('Content-Type: application/json');
require_once __DIR__ . '/db.php';
if (session_status() === PHP_SESSION_NONE) session_start();
$pdo = getDBConnection();

$rawInput = file_get_contents('php://input');
$data = json_decode($rawInput, true);
if (!is_array($data)) $data = $_POST;
$action = $data['action'] ?? $_GET['action'] ?? '';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Método HTTP no permitido.']);
    exit;
}

if ($action === 'register') registerUser($pdo, $data);
elseif ($action === 'login') loginUser($pdo, $data);
else echo json_encode(['status' => 'error', 'message' => 'Acción no válida.']);

function registerUser(PDO $pdo, array $data): void
{
    $name = trim((string)($data['name'] ?? ''));
    $email = strtolower(trim((string)($data['email'] ?? '')));
    $password = (string)($data['password'] ?? '');

    if ($name === '' || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($password) < 8) {
        echo json_encode(['status' => 'error', 'message' => 'Datos inválidos. La contraseña debe tener al menos 8 caracteres.']);
        return;
    }

    $check = $pdo->prepare('SELECT id FROM users WHERE lower(email)=lower(?) LIMIT 1');
    $check->execute([$email]);
    if ($check->fetch()) {
        echo json_encode(['status' => 'error', 'message' => 'El correo electrónico ya está registrado.']);
        return;
    }

    try {
        $pdo->beginTransaction();
        $company = $pdo->prepare('INSERT INTO companies (nombre,email) VALUES (?,?)');
        $company->execute([$name, $email]);
        $companyId = (int)$pdo->lastInsertId();

        $stmt = $pdo->prepare('INSERT INTO users (name,email,password,company_id) VALUES (?,?,?,?)');
        $stmt->execute([$name, $email, password_hash($password, PASSWORD_DEFAULT), $companyId]);
        $userId = (int)$pdo->lastInsertId();
        $pdo->commit();

        echo json_encode(['status' => 'success', 'message' => 'Usuario registrado exitosamente.', 'user' => ['id' => $userId, 'name' => $name, 'email' => $email, 'company_id' => $companyId]]);
    } catch (Throwable $e) {
        if ($pdo->inTransaction()) $pdo->rollBack();
        error_log('[AUTH_REGISTER] ' . $e->getMessage());
        echo json_encode(['status' => 'error', 'message' => 'No fue posible completar el registro.']);
    }
}

function loginUser(PDO $pdo, array $data): void
{
    $email = strtolower(trim((string)($data['email'] ?? '')));
    $password = (string)($data['password'] ?? '');
    if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $password === '') {
        echo json_encode(['status' => 'error', 'message' => 'Correo y contraseña requeridos.']);
        return;
    }

    $stmt = $pdo->prepare('SELECT id,name,email,password,company_id,active FROM users WHERE lower(email)=lower(?) LIMIT 1');
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if (!$user || (int)$user['active'] !== 1 || !password_verify($password, (string)$user['password'])) {
        echo json_encode(['status' => 'error', 'message' => 'Credenciales incorrectas.']);
        return;
    }

    session_regenerate_id(true);
    $_SESSION['pos_authenticated'] = true;
    $_SESSION['user_id'] = (int)$user['id'];
    $_SESSION['empresa_id'] = $user['company_id'] !== null ? (int)$user['company_id'] : null;
    $_SESSION['cashier'] = ['id' => (int)$user['id'], 'name' => $user['name'], 'code' => 'USER-' . $user['id'], 'rol' => 'user'];

    echo json_encode(['status' => 'success', 'message' => 'Inicio de sesión exitoso.', 'user' => ['id' => $user['id'], 'name' => $user['name'], 'email' => $user['email'], 'company_id' => $user['company_id']]]);
}
