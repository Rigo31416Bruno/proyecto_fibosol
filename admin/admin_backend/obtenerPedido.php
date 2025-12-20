<?php
session_start();
require_once '../../db/conexion.php';

header('Content-Type: application/json');

if (!isset($_SESSION['id_usuario']) || $_SESSION['id_rol'] != 2) {
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit();
}

$id = isset($_GET['id_pedido']) ? (int)$_GET['id_pedido'] : (isset($_POST['id_pedido']) ? (int)$_POST['id_pedido'] : 0);
if ($id <= 0) {
    echo json_encode(['success' => false, 'message' => 'ID inválido']);
    exit();
}

$q = mysqli_prepare($conn, "SELECT p.*, u.Nombre as usuario_nombre, u.Correo as usuario_correo FROM pedidos p LEFT JOIN usuarios u ON p.id_usuario = u.ID_Usuario WHERE p.id_pedido = ? LIMIT 1");
mysqli_stmt_bind_param($q, 'i', $id);
mysqli_stmt_execute($q);
$res = mysqli_stmt_get_result($q);
$order = mysqli_fetch_assoc($res);
mysqli_stmt_close($q);

if (!$order) {
    echo json_encode(['success' => false, 'message' => 'Pedido no encontrado']);
    exit();
}

$items = [];
$q2 = mysqli_prepare($conn, "SELECT dp.id_producto, dp.id_producto_talla, dp.cantidad, dp.precio_unitario, pr.nombre, t.nombre as talla_nombre FROM detalle_pedido dp LEFT JOIN productos pr ON dp.id_producto = pr.id_producto LEFT JOIN producto_tallas pt ON dp.id_producto_talla = pt.id_producto_talla LEFT JOIN tallas t ON pt.id_talla = t.id_talla WHERE dp.id_pedido = ?");
mysqli_stmt_bind_param($q2, 'i', $id);
mysqli_stmt_execute($q2);
$r2 = mysqli_stmt_get_result($q2);
if ($r2) {
    while ($row = mysqli_fetch_assoc($r2)) {
        $items[] = $row;
    }
}
mysqli_stmt_close($q2);

echo json_encode(['success' => true, 'order' => $order, 'items' => $items]);
exit();

?>
