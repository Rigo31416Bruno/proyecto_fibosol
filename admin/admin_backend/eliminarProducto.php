<?php
session_start();
require_once '../../db/conexion.php';

header('Content-Type: application/json');

if (!isset($_SESSION['id_usuario']) || $_SESSION['id_rol'] != 2) {
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['id'])) {
    echo json_encode(['success' => false, 'message' => 'Solicitud inválida']);
    exit();
}

$id_producto = intval($_POST['id']);

if ($id_producto <= 0) {
    echo json_encode(['success' => false, 'message' => 'ID inválido']);
    exit();
}

// Verificar que el producto existe y pertenece a la tienda (por seguridad)
$check = mysqli_prepare($conn, "SELECT id_producto FROM productos WHERE id_producto = ?");
mysqli_stmt_bind_param($check, "i", $id_producto);
mysqli_stmt_execute($check);
mysqli_stmt_store_result($check);

if (mysqli_stmt_num_rows($check) === 0) {
    echo json_encode(['success' => false, 'message' => 'Producto no encontrado']);
    mysqli_stmt_close($check);
    exit();
}
mysqli_stmt_close($check);

// Eliminar imágenes asociadas si existen (ajusta según tu estructura)
$img_query = mysqli_prepare($conn, "SELECT imagen FROM productos WHERE id_producto = ?");
mysqli_stmt_bind_param($img_query, "i", $id_producto);
mysqli_stmt_execute($img_query);
$result = mysqli_stmt_get_result($img_query);
$row = mysqli_fetch_assoc($result);
mysqli_stmt_close($img_query);

if ($row && !empty($row['imagen']) && file_exists('../' . $row['imagen'])) {
    unlink('../' . $row['imagen']);
}

// Eliminar tallas asociadas (producto_tallas)
mysqli_query($conn, "DELETE FROM producto_tallas WHERE id_producto = $id_producto");

// Eliminar el producto
$stmt = mysqli_prepare($conn, "DELETE FROM productos WHERE id_producto = ?");
mysqli_stmt_bind_param($stmt, "i", $id_producto);
$success = mysqli_stmt_execute($stmt);

if ($success) {
    echo json_encode(['success' => true, 'message' => 'Producto eliminado correctamente']);
} else {
    echo json_encode(['success' => false, 'message' => 'Error al eliminar: ' . mysqli_error($conn)]);
}

mysqli_stmt_close($stmt);
mysqli_close($conn);
?>