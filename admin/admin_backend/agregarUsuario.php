<?php
session_start();
require_once '../../db/conexion.php';

header('Content-Type: application/json');

if (!isset($_SESSION['id_usuario']) || $_SESSION['id_rol'] != 2) {
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit();
}

$nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
$correo = isset($_POST['correo']) ? trim($_POST['correo']) : '';
$contrasena = isset($_POST['contrasena']) ? $_POST['contrasena'] : '';
$rol = isset($_POST['rol']) ? (int)$_POST['rol'] : 1;
$telefono = isset($_POST['telefono']) ? trim($_POST['telefono']) : null;
$direccion = isset($_POST['direccion']) ? trim($_POST['direccion']) : null;
$estatus = isset($_POST['estatus']) ? trim($_POST['estatus']) : 'activo';

// validar campos básicos
if ($nombre === '' || $correo === '' || $contrasena === '') {
    echo json_encode(['success' => false, 'message' => 'Completa todos los campos.']);
    exit();
}

// validar formato de correo
if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Correo no válido.']);
    exit();
}

// validar contraseña: mínimo 8 caracteres, al menos un dígito y un carácter especial
$patron = '/^(?=.*\d)(?=.*[^\w\s]).{8,}$/u';
if (!preg_match($patron, $contrasena)) {
    echo json_encode(['success' => false, 'message' => 'La contraseña debe contener al menos un número, un carácter especial y tener mínimo 8 caracteres.']);
    exit();
}

$q = mysqli_prepare($conn, "SELECT ID_Usuario FROM usuarios WHERE Correo = ? LIMIT 1");
mysqli_stmt_bind_param($q, 's', $correo);
mysqli_stmt_execute($q);
$r = mysqli_stmt_get_result($q);
if (mysqli_fetch_assoc($r)) {
    mysqli_stmt_close($q);
    echo json_encode(['success' => false, 'message' => 'El correo ya está en uso']);
    exit();
}
mysqli_stmt_close($q);

$hash = password_hash($contrasena, PASSWORD_DEFAULT);

$stmt = mysqli_prepare($conn, "INSERT INTO usuarios (Nombre, Correo, Contraseña, ID_Rol, Direccion, Telefono, Estatus) VALUES (?, ?, ?, ?, ?, ?, ?)");
mysqli_stmt_bind_param($stmt, 'sssisss', $nombre, $correo, $hash, $rol, $direccion, $telefono, $estatus);
if (!mysqli_stmt_execute($stmt)) {
    $err = mysqli_error($conn);
    mysqli_stmt_close($stmt);
    echo json_encode(['success' => false, 'message' => 'Error al insertar usuario: ' . $err]);
    exit();
}
$newId = mysqli_insert_id($conn);
mysqli_stmt_close($stmt);
mysqli_close($conn);

echo json_encode(['success' => true, 'message' => 'Usuario creado', 'id_usuario' => $newId]);
exit();

?>
