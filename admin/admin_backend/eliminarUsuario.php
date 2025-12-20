<?php
session_start();
require_once '../../db/conexion.php';

header('Content-Type: application/json');

if (!isset($_SESSION['id_usuario']) || $_SESSION['id_rol'] != 2) {
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit();
}

$id = isset($_POST['id']) ? (int)$_POST['id'] : (isset($_GET['id']) ? (int)$_GET['id'] : 0);
if ($id <= 0) {
    echo json_encode(['success' => false, 'message' => 'ID inválido']);
    exit();
}

// evitar borrar administradores
$q = mysqli_prepare($conn, "SELECT ID_Rol FROM usuarios WHERE ID_Usuario = ?");
mysqli_stmt_bind_param($q, 'i', $id);
mysqli_stmt_execute($q);
$res = mysqli_stmt_get_result($q);
$row = mysqli_fetch_assoc($res);
mysqli_stmt_close($q);

if (!$row) {
    echo json_encode(['success' => false, 'message' => 'Usuario no encontrado']);
    exit();
}

if ((int)$row['ID_Rol'] === 2) {
    echo json_encode(['success' => false, 'message' => 'No se puede eliminar un usuario administrador']);
    exit();
}

// evitar que un admin se borre a sí mismo por accidente
if ((int)$_SESSION['id_usuario'] === $id) {
    echo json_encode(['success' => false, 'message' => 'No puedes eliminar tu propia cuenta']);
    exit();
}

$stmt = mysqli_prepare($conn, "DELETE FROM usuarios WHERE ID_Usuario = ?");
mysqli_stmt_bind_param($stmt, 'i', $id);
if (!mysqli_stmt_execute($stmt)) {
    $err = mysqli_error($conn);
    mysqli_stmt_close($stmt);
    echo json_encode(['success' => false, 'message' => 'Error al eliminar: ' . $err]);
    exit();
}
mysqli_stmt_close($stmt);
mysqli_close($conn);

echo json_encode(['success' => true, 'message' => 'Usuario eliminado correctamente']);
exit();

?>
