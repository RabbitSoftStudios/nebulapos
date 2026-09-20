<?php
/**
 * NebulaPOS - Módulo de Autenticación y Registro de Usuarios
 */

header('Content-Type: application/json');
require_once __DIR__ . '/db.php';

$pdo = getDBConnection();

$action = $_GET['action'] ?? $_POST['action'] ?? '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Si viene como raw JSON
    $rawInput = file_get_contents('php://input');
    $data = json_decode($rawInput, true);
    if (!is_array($data)) {
        $data = $_POST;
    }

    $action = $data['action'] ?? $action;

    if ($action === 'register') {
        registerUser($pdo, $data);
    } elseif ($action === 'login') {
        loginUser($pdo, $data);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Acción no válida.']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Método HTTP no permitido.']);
}

function registerUser($pdo, $data) {
    $name = trim($data['name'] ?? '');
    $email = trim(strtolower($data['email'] ?? ''));
    $password = $data['password'] ?? '';

    if (empty($name) || empty($email) || empty($password)) {
        echo json_encode(['status' => 'error', 'message' => 'Todos los campos son obligatorios.']);
        return;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo json_encode(['status' => 'error', 'message' => 'Correo electrónico no válido.']);
        return;
    }

    // Verificar si el usuario ya existe
    $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        echo json_encode(['status' => 'error', 'message' => 'El correo electrónico ya está registrado.']);
        return;
    }

    // Hash de contraseña
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    // Insertar nuevo usuario
    $stmt = $pdo->prepare('INSERT INTO users (name, email, password) VALUES (?, ?, ?)');
    if ($stmt->execute([$name, $email, $hashedPassword])) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Usuario registrado exitosamente.',
            'user' => [
                'id' => $pdo->lastInsertId(),
                'name' => $name,
                'email' => $email
            ]
        ]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error al registrar usuario.']);
    }
}

function loginUser($pdo, $data) {
    $email = trim(strtolower($data['email'] ?? ''));
    $password = $data['password'] ?? '';

    if (empty($email) || empty($password)) {
        echo json_encode(['status' => 'error', 'message' => 'Correo y contraseña requeridos.']);
        return;
    }

    $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ?');
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Inicio de sesión exitoso.',
            'user' => [
                'id' => $user['id'],
                'name' => $user['name'],
                'email' => $user['email']
            ]
        ]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Credenciales incorrectas.']);
    }
}
