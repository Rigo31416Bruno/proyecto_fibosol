<?php
header('Content-Type: application/json; charset=utf-8');

require('../db/conexion.php');

$correo = isset($_POST['correo']) ? trim($_POST['correo']) : '';
$contrasena = isset($_POST['contrasena']) ? $_POST['contrasena'] : '';

if ($correo === '' || $contrasena === '') {
    echo json_encode(['success' => false, 'message' => 'Complete los campos.']);
    exit();
}
if (!filter_var($correo, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Correo no valido.']);
    exit();
}

$stmt = mysqli_prepare($conn, "SELECT ID_Usuario, Nombre, Contraseña, Estatus, ID_Rol FROM usuarios WHERE Correo = ? LIMIT 1");
if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Error en la consulta']);
    exit();
}
mysqli_stmt_bind_param($stmt, "s", $correo);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$user = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

if (!$user) {
    echo json_encode(['success' => false, 'message' => 'Correo no registrado']);
    exit();
}

if (!empty($user['Estatus']) && strtolower($user['Estatus']) !== 'activo') {
    echo json_encode(['success' => false, 'message' => 'Cuenta Baneada. Contacta al administrador']);
    exit();
}

$hash = $user['Contraseña'] ?? '';
if ($hash === '' || !password_verify($contrasena, $hash)) {
    echo json_encode(['success' => false, 'message' => 'Contraseña incorrecta.']);
    exit();
}

session_start();
$_SESSION['id_usuario'] = (int)$user['ID_Usuario'];
$_SESSION['nombre'] = $user['Nombre'];
$_SESSION['correo'] = $correo;
$_SESSION['id_rol'] = isset($user['ID_Rol']) ? (int)$user['ID_Rol'] : null;

echo json_encode([
    'success' => true,
    'message' => 'Inicio de sesion correcto. Bienvenido ' . ($user['Nombre'] ?? ''),
    'role_id' => isset($user['ID_Rol']) ? (int)$user['ID_Rol'] : null
], JSON_UNESCAPED_UNICODE);
exit();
?>