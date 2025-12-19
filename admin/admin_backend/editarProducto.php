<?php
session_start();
require_once '../../db/conexion.php';

header('Content-Type: application/json');

if (!isset($_SESSION['id_usuario']) || $_SESSION['id_rol'] != 2) {
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit();
}

$id = isset($_POST['id_producto']) ? (int)$_POST['id_producto'] : 0;
$nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
$precio = isset($_POST['precio']) ? (float)$_POST['precio'] : 0;
$categoria = isset($_POST['categoria']) && $_POST['categoria'] !== '' ? (int)$_POST['categoria'] : null;
$imagen = isset($_POST['imagen']) ? trim($_POST['imagen']) : null;

if ($id <= 0 || $nombre === '') {
    echo json_encode(['success' => false, 'message' => 'Datos inválidos']);
    exit();
}

$query = "UPDATE productos SET nombre = ?, precio = ?, categoria = ?, imagen = ? WHERE id_producto = ?";
$stmt = mysqli_prepare($conn, $query);
mysqli_stmt_bind_param($stmt, "sdisi", $nombre, $precio, $categoria, $imagen, $id);

if (mysqli_stmt_execute($stmt)) {
    $affected = mysqli_stmt_affected_rows($stmt);
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    if ($affected >= 0) {
        echo json_encode(['success' => true, 'message' => 'Producto actualizado correctamente']);
    } else {
        echo json_encode(['success' => false, 'message' => 'No se pudo actualizar']);
    }
} else {
    $err = mysqli_error($conn);
    mysqli_stmt_close($stmt);
    mysqli_close($conn);
    echo json_encode(['success' => false, 'message' => 'Error al actualizar: ' . $err]);
}

?>
