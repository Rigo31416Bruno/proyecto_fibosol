<?php
header('Content-Type: application/json; charset=utf-8');
require('../db/conexion.php');

$verification_id = isset($_POST['verification_id']) ? (int)$_POST['verification_id'] : 0;
$code = isset($_POST['code']) ? trim($_POST['code']) : '';

if ($verification_id <= 0 || $code === '') {
    echo json_encode(['success' => false, 'message' => 'ID de verificación y código requeridos.']);
    exit();
}

// buscar verificacion
$stmt = mysqli_prepare($conn, "SELECT ID, Nombre, Correo, ContrasenaHash, Codigo, ExpiresAt FROM verificaciones WHERE ID = ? LIMIT 1");
if (!$stmt) { echo json_encode(['success'=>false,'message'=>'Error en consulta.']); exit(); }
mysqli_stmt_bind_param($stmt, "i", $verification_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$row = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$row) {
    echo json_encode(['success' => false, 'message' => 'Verificación no encontrada o ya usada.']);
    exit();
}

if (new DateTime($row['ExpiresAt']) < new DateTime()) {
    $del = mysqli_prepare($conn, "DELETE FROM verificaciones WHERE ID = ?");
    if ($del) { mysqli_stmt_bind_param($del, "i", $verification_id); mysqli_stmt_execute($del); mysqli_stmt_close($del); }
    echo json_encode(['success' => false, 'message' => 'El código ha expirado. Vuelve a registrarte.']);
    exit();
}

if (!hash_equals($row['Codigo'], $code)) {
    echo json_encode(['success' => false, 'message' => 'Código incorrecto.']);
    exit();
}

$chk = mysqli_prepare($conn, "SELECT 1 FROM usuarios WHERE Correo = ? LIMIT 1");
if (!$chk) { echo json_encode(['success'=>false,'message'=>'Error en verificación.']); exit(); }
mysqli_stmt_bind_param($chk, "s", $row['Correo']);
mysqli_stmt_execute($chk);
mysqli_stmt_store_result($chk);
if (mysqli_stmt_num_rows($chk) > 0) {
    mysqli_stmt_close($chk);
    $del = mysqli_prepare($conn, "DELETE FROM verificaciones WHERE ID = ?");
    if ($del) { mysqli_stmt_bind_param($del, "i", $verification_id); mysqli_stmt_execute($del); mysqli_stmt_close($del); }
    echo json_encode(['success' => false, 'message' => 'El correo ya fue registrado.']);
    exit();
}
mysqli_stmt_close($chk);

$estatus = 'activo';
$ins = mysqli_prepare($conn, "INSERT INTO usuarios (Nombre, Correo, `Contraseña`, Estatus, ID_Rol) VALUES (?, ?, ?, ?, 1)");
if (!$ins) { echo json_encode(['success'=>false,'message'=>'Error al preparar inserción.']); exit(); }
mysqli_stmt_bind_param($ins, "ssss", $row['Nombre'], $row['Correo'], $row['ContrasenaHash'], $estatus);
if (!mysqli_stmt_execute($ins)) {
    $err = mysqli_stmt_error($ins);
    mysqli_stmt_close($ins);
    echo json_encode(['success' => false, 'message' => 'Error al insertar usuario: ' . $err]);
    exit();
}
mysqli_stmt_close($ins);

// eliminar verificacion
$del = mysqli_prepare($conn, "DELETE FROM verificaciones WHERE ID = ?");
if ($del) { mysqli_stmt_bind_param($del, "i", $verification_id); mysqli_stmt_execute($del); mysqli_stmt_close($del); }

mysqli_close($conn);
echo json_encode(['success' => true, 'message' => 'Cuenta verificada y creada correctamente.'], JSON_UNESCAPED_UNICODE);
exit();
?>