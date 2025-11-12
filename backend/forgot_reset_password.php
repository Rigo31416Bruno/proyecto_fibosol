<?php

header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../db/conexion.php';

$email = isset($_POST['email']) ? trim($_POST['email']) : '';
$code  = isset($_POST['code'])  ? trim($_POST['code'])  : '';
$pass  = isset($_POST['password']) ? $_POST['password'] : '';

if ($email === '' || $code === '' || $pass === '') {
    echo json_encode(['success' => false, 'message' => 'Email, código y contraseña requeridos.']);
    exit;
}
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Email inválido.']);
    exit;
}
if (strlen($pass) < 6) {
    echo json_encode(['success' => false, 'message' => 'Contraseña mínima 6 caracteres.']);
    exit;
}

// validar código
$stmt = mysqli_prepare($conn, "SELECT ID, ExpiresAt FROM recuperaciones WHERE Correo = ? AND Codigo = ? LIMIT 1");
if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Error en la consulta (prepare).']);
    mysqli_close($conn);
    exit;
}
mysqli_stmt_bind_param($stmt, "ss", $email, $code);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
$row = mysqli_fetch_assoc($res);
mysqli_stmt_close($stmt);

if (!$row) {
    echo json_encode(['success' => false, 'message' => 'Código inválido.']);
    mysqli_close($conn);
    exit;
}

// comprobar expiración
$expires = new DateTime($row['ExpiresAt']);
$now = new DateTime();
if ($expires < $now) {
    // borrar registro expirado
    $del = mysqli_prepare($conn, "DELETE FROM recuperaciones WHERE ID = ?");
    if ($del) { mysqli_stmt_bind_param($del, "i", $row['ID']); mysqli_stmt_execute($del); mysqli_stmt_close($del); }
    echo json_encode(['success' => false, 'message' => 'El código ha expirado.']);
    mysqli_close($conn);
    exit;
}

// actualizar contraseña (hashear)
$hash = password_hash($pass, PASSWORD_DEFAULT);

// Ajusta el nombre de la columna si tu DB usa otro campo para la contraseña
$upd = mysqli_prepare($conn, "UPDATE usuarios SET `Contraseña` = ? WHERE Correo = ? LIMIT 1");
if (!$upd) {
    echo json_encode(['success' => false, 'message' => 'Error al preparar actualización.']);
    mysqli_close($conn);
    exit;
}
mysqli_stmt_bind_param($upd, "ss", $hash, $email);
$ok = mysqli_stmt_execute($upd);
mysqli_stmt_close($upd);

if (!$ok) {
    echo json_encode(['success' => false, 'message' => 'Error al actualizar la contraseña.']);
    mysqli_close($conn);
    exit;
}

// borrar registro de recuperacion usado
$del = mysqli_prepare($conn, "DELETE FROM recuperaciones WHERE ID = ?");
if ($del) { mysqli_stmt_bind_param($del, "i", $row['ID']); mysqli_stmt_execute($del); mysqli_stmt_close($del); }

echo json_encode(['success' => true, 'message' => 'Contraseña actualizada correctamente.']);
mysqli_close($conn);
exit;
?>