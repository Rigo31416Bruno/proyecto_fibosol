<?php
session_start();
require_once '../../db/conexion.php';

header('Content-Type: application/json');

if (!isset($_SESSION['id_usuario']) || $_SESSION['id_rol'] != 2) {
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit();
}

$id = isset($_GET['id_producto']) ? (int)$_GET['id_producto'] : (isset($_POST['id_producto']) ? (int)$_POST['id_producto'] : 0);
if ($id <= 0) {
    echo json_encode(['success' => false, 'message' => 'ID inválido']);
    exit();
}

$stmt = mysqli_prepare($conn, "SELECT id_producto, nombre, precio, categoria, imagen FROM productos WHERE id_producto = ?");
mysqli_stmt_bind_param($stmt, 'i', $id);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
$prod = mysqli_fetch_assoc($res);
mysqli_stmt_close($stmt);

if (!$prod) {
    echo json_encode(['success' => false, 'message' => 'Producto no encontrado']);
    exit();
}

$stock = [];
$q2 = mysqli_prepare($conn, "SELECT id_talla, stock FROM producto_tallas WHERE id_producto = ?");
mysqli_stmt_bind_param($q2, 'i', $id);
mysqli_stmt_execute($q2);
$r2 = mysqli_stmt_get_result($q2);
while ($row = mysqli_fetch_assoc($r2)) {
    $stock[(int)$row['id_talla']] = (int)$row['stock'];
}
mysqli_stmt_close($q2);

echo json_encode(['success' => true, 'product' => $prod, 'stock' => $stock]);
exit();

?>
