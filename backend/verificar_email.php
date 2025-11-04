<?php
header('Content-Type: application/json; charset=utf-8');
require __DIR__ . '/../db/conexion.php';

$token = isset($_GET['token']) ? trim($_GET['token']) : null;
if (!$token || strlen($token) < 10) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Token inválido.']);
    exit;
}

try {
    $dbObj = new Conexion();
    $conn = $dbObj->getConnection();

    $stmt = $conn->prepare('SELECT idCuenta, token_expires FROM usuarios WHERE verification_token = ? LIMIT 1');
    if (!$stmt) throw new RuntimeException($conn->error);
    $stmt->bind_param('s', $token);
    $stmt->execute();
    $stmt->bind_result($idCuenta, $token_expires);
    if (!$stmt->fetch()) {
        http_response_code(404);
        echo json_encode(['success' => false, 'message' => 'Token no encontrado.']);
        $stmt->close();
        exit;
    }
    $stmt->close();

    $now = new DateTime();
    $expires = new DateTime($token_expires);
    if ($expires < $now) {
        http_response_code(410);
        echo json_encode(['success' => false, 'message' => 'Token expirado. Solicita uno nuevo.']);
        exit;
    }

    $upd = $conn->prepare('UPDATE usuarios SET correo_verificado = 1, verification_token = NULL, token_expires = NULL WHERE idCuenta = ?');
    if (!$upd) throw new RuntimeException('Prepare failed: ' . $conn->error);
    $upd->bind_param('i', $idCuenta);
    $upd->execute();
    $upd->close();

    echo json_encode(['success' => true, 'message' => 'Correo verificado correctamente.']);
    exit;
} catch (Exception $e) {
    // error_log($e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Error de base de datos.']);
    exit;
}