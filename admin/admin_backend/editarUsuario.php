<?php
session_start();
require_once '../../db/conexion.php';

header('Content-Type: application/json');

if (!isset($_SESSION['id_usuario']) || $_SESSION['id_rol'] != 2) {
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit();
}

$id = isset($_POST['id_usuario']) ? (int)$_POST['id_usuario'] : 0;
$nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
$correo = isset($_POST['correo']) ? trim($_POST['correo']) : '';
$rol = isset($_POST['rol']) ? (int)$_POST['rol'] : 1;
$telefono = isset($_POST['telefono']) ? trim($_POST['telefono']) : null;
$direccion = isset($_POST['direccion']) ? trim($_POST['direccion']) : null;
$estatus = isset($_POST['estatus']) ? trim($_POST['estatus']) : 'activo';
$contrasena = isset($_POST['contrasena']) ? $_POST['contrasena'] : '';

if ($id <= 0 || $nombre === '' || $correo === '') {
    echo json_encode(['success' => false, 'message' => 'Datos inválidos']);
    exit();
}

// validar formato de correo
if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Correo no válido.']);
    exit();
}

$q = mysqli_prepare($conn, "SELECT ID_Usuario, ID_Rol FROM usuarios WHERE ID_Usuario = ?");
mysqli_stmt_bind_param($q, 'i', $id);
mysqli_stmt_execute($q);
$res = mysqli_stmt_get_result($q);
$row = mysqli_fetch_assoc($res);
mysqli_stmt_close($q);

if (!$row) {
    echo json_encode(['success' => false, 'message' => 'Usuario no encontrado']);
    exit();
}

$q2 = mysqli_prepare($conn, "SELECT ID_Usuario FROM usuarios WHERE Correo = ? AND ID_Usuario <> ? LIMIT 1");
mysqli_stmt_bind_param($q2, 'si', $correo, $id);
mysqli_stmt_execute($q2);
$r2 = mysqli_stmt_get_result($q2);
if (mysqli_fetch_assoc($r2)) {
    mysqli_stmt_close($q2);
    echo json_encode(['success' => false, 'message' => 'El correo ya está en uso por otro usuario']);
    exit();
}
mysqli_stmt_close($q2);

// preparar update básico
if ($contrasena !== '') {
    // validar contraseña: mínimo 8 caracteres, al menos un dígito y un carácter especial
    $patron = '/^(?=.*\d)(?=.*[^\w\s]).{8,}$/u';
    if (!preg_match($patron, $contrasena)) {
        echo json_encode(['success' => false, 'message' => 'La contraseña debe contener al menos un número, un carácter especial y tener mínimo 8 caracteres.']);
        exit();
    }
    $hash = password_hash($contrasena, PASSWORD_DEFAULT);
    $stmt = mysqli_prepare($conn, "UPDATE usuarios SET Nombre = ?, Correo = ?, Contraseña = ?, ID_Rol = ?, Direccion = ?, Telefono = ?, Estatus = ? WHERE ID_Usuario = ?");
    mysqli_stmt_bind_param($stmt, 'sssisssi', $nombre, $correo, $hash, $rol, $direccion, $telefono, $estatus, $id);
} else {
    $stmt = mysqli_prepare($conn, "UPDATE usuarios SET Nombre = ?, Correo = ?, ID_Rol = ?, Direccion = ?, Telefono = ?, Estatus = ? WHERE ID_Usuario = ?");
    mysqli_stmt_bind_param($stmt, 'ssisssi', $nombre, $correo, $rol, $direccion, $telefono, $estatus, $id);
}

if (!mysqli_stmt_execute($stmt)) {
    $err = mysqli_error($conn);
    mysqli_stmt_close($stmt);
    echo json_encode(['success' => false, 'message' => 'Error al actualizar usuario: ' . $err]);
    exit();
}
mysqli_stmt_close($stmt);
mysqli_close($conn);

echo json_encode(['success' => true, 'message' => 'Usuario actualizado correctamente']);
exit();

?>
