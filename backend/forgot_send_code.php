<?php
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../db/conexion.php';
require_once __DIR__ . '/correo.php';

$email = isset($_POST['email']) ? trim($_POST['email']) : '';

if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Correo requerido o inválido.']);
    exit;
}

// verificar que exista el usuario
$st = mysqli_prepare($conn, "SELECT ID_Usuario, Nombre FROM usuarios WHERE Correo = ? LIMIT 1");
if (!$st) {
    echo json_encode(['success' => false, 'message' => 'Error en la consulta.']);
    mysqli_close($conn);
    exit;
}
mysqli_stmt_bind_param($st, "s", $email);
mysqli_stmt_execute($st);
$res = mysqli_stmt_get_result($st);
$user = mysqli_fetch_assoc($res);
mysqli_stmt_close($st);

if (!$user) {
    // Si no quieres revelar existencia del correo, devuelve éxito genérico.
    echo json_encode(['success' => false, 'message' => 'No se encontró cuenta con ese correo.']);
    mysqli_close($conn);
    exit;
}

// eliminar códigos antiguos para ese correo
$del = mysqli_prepare($conn, "DELETE FROM recuperaciones WHERE Correo = ?");
if ($del) { mysqli_stmt_bind_param($del, "s", $email); mysqli_stmt_execute($del); mysqli_stmt_close($del); }

// generar código 6 dígitos y expiración
$code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
$expires = date('Y-m-d H:i:s', time() + 3600); // 1 hora
$created = date('Y-m-d H:i:s');

// insertar en la tabla
$ins = mysqli_prepare($conn, "INSERT INTO recuperaciones (Correo, Codigo, ExpiresAt, CreatedAt) VALUES (?, ?, ?, ?)");
if (!$ins) {
    echo json_encode(['success' => false, 'message' => 'Error al preparar inserción.']);
    mysqli_close($conn);
    exit;
}
mysqli_stmt_bind_param($ins, "ssss", $email, $code, $expires, $created);
$ok = mysqli_stmt_execute($ins);
mysqli_stmt_close($ins);

if (!$ok) {
    echo json_encode(['success' => false, 'message' => 'Error al guardar el código.']);
    mysqli_close($conn);
    exit;
}

// enviar correo usando [correo.php](http://_vscodecontentref_/0)
$sent = false;
try {
    $sent = sendVerificationEmail($email, $user['Nombre'], $code);
} catch (Exception $e) {
    error_log('forgot_send_code mail error: ' . $e->getMessage());
    $sent = false;
}

if ($sent) {
    echo json_encode(['success' => true, 'message' => 'Código enviado. Revisa tu correo.']);
} else {
    echo json_encode(['success' => false, 'message' => 'No se pudo enviar el código por correo.']);
}

mysqli_close($conn);
exit;
?>