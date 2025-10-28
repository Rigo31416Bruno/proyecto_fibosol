<?php

require('../db/conexion.php');

// recibir JSON raw
$raw = file_get_contents('php://input');
$data = json_decode($raw, true) ?? [];

// sanitizar
$data = array_map(function($v){ return is_string($v) ? trim($v) : $v; }, $data);

$usuario    = $data['usuario'] ?? '';
$email      = $data['email'] ?? '';
$contrasena = $data['contrasena'] ?? '';

header('Content-Type: application/json');

// validaciones básicas
$errors = [];

if ($usuario === '' || strlen($usuario) < 3) {
    $errors['usuario'] = 'Usuario requerido (mínimo 3 caracteres).';
}
if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $errors['email'] = 'Email inválido.';
}
if ($contrasena === '' || strlen($contrasena) < 8) {
    $errors['contrasena'] = 'Contraseña requerida (mínimo 8 caracteres).';
}

if (!empty($errors)) {
    http_response_code(422);
    echo json_encode(['success' => false, 'errors' => $errors]);
    exit;
}

try {
    $pdo = DB::get();

    // verificar unicidad
    $stmt = $pdo->prepare('SELECT id, usuario, correo FROM usuarios WHERE correo = ? OR usuario = ? LIMIT 1');
    $stmt->execute([$email, $usuario]);
    $exists = $stmt->fetch();

    if ($exists) {
        http_response_code(409);
        echo json_encode(['success' => false, 'message' => 'El usuario o email ya está en uso.']);
        exit;
    }

    // registrar usuario
    $stmt = $pdo->prepare('INSERT INTO usuarios (usuario, correo, contrasena) VALUES (?, ?, ?)');
    $stmt->execute([$usuario, $email, password_hash($contrasena, PASSWORD_ARGON2I)]);

    echo json_encode(['success' => true, 'message' => 'Registro exitoso.']);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error en el servidor.']);
}