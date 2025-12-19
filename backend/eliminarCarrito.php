<?php
header('Content-Type: application/json; charset=utf-8');
require 'validarSesion.php';
require_once __DIR__ . '/../db/conexion.php';

if (!validarSesion()) {
    echo json_encode(['success' => false, 'message' => 'No autenticado']);
    exit();
}

$id_usuario = isset($_SESSION['id_usuario']) ? (int)$_SESSION['id_usuario'] : 0;
if ($id_usuario <= 0) {
    echo json_encode(['success' => false, 'message' => 'Usuario inválido']);
    exit();
}

$id_carrito = isset($_POST['id_carrito']) ? (int)$_POST['id_carrito'] : 0;
if ($id_carrito <= 0) {
    echo json_encode(['success' => false, 'message' => 'ID inválido']);
    exit();
}

if (!isset($conn) || !$conn) {
    echo json_encode(['success' => false, 'message' => 'Conexión fallida']);
    exit();
}

// Delete the cart item, ensuring it belongs to this user
$sql = "DELETE FROM carrito WHERE id_carrito = ? AND id_usuario = ?";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Error en la consulta']);
    exit();
}
$stmt->bind_param('ii', $id_carrito, $id_usuario);
$executed = $stmt->execute();
$affected = $stmt->affected_rows;
$stmt->close();

if (!$executed) {
    echo json_encode(['success' => false, 'message' => 'No se pudo eliminar']);
    exit();
}

// Recalculate totals and count for the user
$res = $conn->prepare("SELECT COUNT(*) AS cnt, COALESCE(SUM(p.precio * c.cantidad),0) AS total
    FROM carrito c
    JOIN productos p ON c.id_producto = p.id_producto
    WHERE c.id_usuario = ?");
$res->bind_param('i', $id_usuario);
$res->execute();
$result = $res->get_result();
$data = $result->fetch_assoc();
$res->close();

$count = isset($data['cnt']) ? (int)$data['cnt'] : 0;
$total = isset($data['total']) ? (float)$data['total'] : 0.0;

echo json_encode([
    'success' => true,
    'deleted' => $affected > 0,
    'count' => $count,
    'total' => number_format((float)$total, 2, '.', '')
]);
exit();

?>
