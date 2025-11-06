<?php
header('Content-Type: application/json; charset=utf-8');

require('../db/conexion.php');
require('correo.php');

$nombre = isset($_POST['usuario']) ? trim($_POST['usuario']) : '';
$correo = isset($_POST['correo']) ? trim($_POST['correo']) : '';
$contrasena = isset($_POST['password']) ? $_POST['password'] : '';

if($nombre === '' || $correo === '' || $contrasena === ''){
    echo json_encode(['success' => false, 'message' => 'Completa todos los campos.']);
    exit();
}

if(!filter_var($correo, FILTER_VALIDATE_EMAIL)){
    echo json_encode(['success' => false, 'message' => 'Correo no válido.']);
    exit();
}

if(strlen($contrasena) < 4){
    echo json_encode(['success' => false, 'message' => 'La contraseña debe contener al menos 4 caracteres.']);
    exit();
}

// patrón: al menos un dígito y un carácter especial, mínimo 8 caracteres (ajusta si necesitas otra regla)
$patron = '/^(?=.*\d)(?=.*[^\w\s]).{8,}$/u';
if (!preg_match($patron, $contrasena)) {
    echo json_encode(['success' => false, 'message' => 'La contraseña debe contener al menos un número y un carácter especial y tener mínimo 8 caracteres.']);
    exit();
}

// comprobar si el correo ya existe en usuarios
$stmt = mysqli_prepare($conn, "SELECT 1 FROM usuarios WHERE Correo = ? LIMIT 1");
if(!$stmt){
    echo json_encode(['success' => false, 'message' => 'Error en la consulta de comprobación.']);
    exit();
}
mysqli_stmt_bind_param($stmt, "s", $correo);
mysqli_stmt_execute($stmt);
mysqli_stmt_store_result($stmt);
if(mysqli_stmt_num_rows($stmt) > 0){
    mysqli_stmt_close($stmt);
    echo json_encode(['success' => false, 'message' => 'El correo ya está registrado.']);
    exit();
}
mysqli_stmt_close($stmt);

// hashear la contraseña (no insertamos aún en usuarios)
$hashed = password_hash($contrasena, PASSWORD_DEFAULT);

// generar código numérico de 6 dígitos
$code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
$expires = (new DateTime('+1 hour'))->format('Y-m-d H:i:s');

// insertar en tabla verificaciones
$insert = mysqli_prepare($conn, "INSERT INTO verificaciones (Nombre, Correo, ContrasenaHash, Codigo, ExpiresAt) VALUES (?, ?, ?, ?, ?)");
if (!$insert) {
    echo json_encode(['success' => false, 'message' => 'Error al preparar la verificación: ' . mysqli_error($conn)]);
    exit();
}
mysqli_stmt_bind_param($insert, "sssss", $nombre, $correo, $hashed, $code, $expires);
if (!mysqli_stmt_execute($insert)) {
    $err = mysqli_stmt_error($insert);
    mysqli_stmt_close($insert);
    mysqli_close($conn);
    echo json_encode(['success' => false, 'message' => 'Error al crear la verificación: ' . $err]);
    exit();
}
$verification_id = mysqli_insert_id($conn);
mysqli_stmt_close($insert);

// enviar correo con código
$sent = sendVerificationEmail($correo, $nombre, $code);
if (!$sent) {
    // eliminar la fila si no se envió el correo
    $del = mysqli_prepare($conn, "DELETE FROM verificaciones WHERE ID = ?");
    if ($del) { mysqli_stmt_bind_param($del, "i", $verification_id); mysqli_stmt_execute($del); mysqli_stmt_close($del); }
    mysqli_close($conn);
    echo json_encode(['success' => false, 'message' => 'No se pudo enviar el correo de verificación. Revisa la configuración SMTP.']);
    exit();
}

mysqli_close($conn);
// Respondemos indicando que el frontend debe pedir el código (se incluye el id de verificación)
echo json_encode([
    'success' => true,
    'action' => 'verify',
    'verification_id' => (int)$verification_id,
    'message' => 'Se envió un código de verificación al correo. Ingresa el código para completar el registro.'
], JSON_UNESCAPED_UNICODE);
exit();
?>