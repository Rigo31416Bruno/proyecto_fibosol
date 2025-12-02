<?php
header('Content-Type: application/json; charset=utf-8');

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../db/conexion.php';
include_once __DIR__ . '/validarSesion.php';

function json_error($msg, $code = 400) {
    http_response_code($code);
    echo json_encode(['success' => false, 'message' => $msg]);
    exit;
}

if (!function_exists('validarSesion') || !validarSesion()) {
    json_error('Usuario no autenticado', 401);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_error('Método no permitido', 405);
}

$id_producto = isset($_POST['id_producto']) ? (int)$_POST['id_producto'] : 0;
$id_talla = isset($_POST['id_talla']) ? (int)$_POST['id_talla'] : 0;
$cantidad = isset($_POST['cantidad']) ? (int)$_POST['cantidad'] : 0;

// Si cliente envía JSON en body, leerlo (fallback)
if ($id_producto === 0 && strpos($_SERVER['CONTENT_TYPE'] ?? '', 'application/json') !== false) {
    $raw = file_get_contents('php://input');
    $json = json_decode($raw, true);
    if (is_array($json)) {
        $id_producto = isset($json['id_producto']) ? (int)$json['id_producto'] : $id_producto;
        $id_talla = isset($json['id_talla']) ? (int)$json['id_talla'] : $id_talla;
        $cantidad = isset($json['cantidad']) ? (int)$json['cantidad'] : $cantidad;
    }
}

if ($id_producto <= 0) json_error('Producto invalido');
if ($cantidad <= 0) json_error('Cantidad invalida');

$possibleKeys = ['ID_Usuario','id_usuario','user_id','ID','id','usuario_id'];
$ID_Usuario = null;
foreach ($possibleKeys as $k) {
    if (!empty($_SESSION[$k])) { $ID_Usuario = (int)$_SESSION[$k]; break; }
}
if (empty($ID_Usuario)) json_error('Usuario no encontrado en sesión', 401);

$precio = null;
$sql = "SELECT precio FROM productos WHERE id_producto = ? LIMIT 1";
if (!($stmt = mysqli_prepare($conn, $sql))) json_error('Error en la consulta (producto)');
mysqli_stmt_bind_param($stmt, 'i', $id_producto);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $precio_found);
mysqli_stmt_fetch($stmt);
mysqli_stmt_close($stmt);
if ($precio_found === null) json_error('Producto no encontrado', 404);
$precio = (float)$precio_found;

$id_producto_talla = null;
$sql = "SELECT id_producto_talla FROM producto_tallas WHERE id_producto = ? AND id_talla = ? LIMIT 1";
if (!($stmt = mysqli_prepare($conn, $sql))) json_error('Error en la consulta (tallas)');
mysqli_stmt_bind_param($stmt, 'ii', $id_producto, $id_talla);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $pt_id);
if (mysqli_stmt_fetch($stmt)) {
    $id_producto_talla = (int)$pt_id;
}
mysqli_stmt_close($stmt);

if ($id_producto_talla === null) {
    json_error('Talla no válida para este producto', 400);
}

// Iniciar transacción para reservar/decrementar stock de forma atomica
if (!mysqli_begin_transaction($conn)) {
    json_error('No se pudo iniciar la transacción', 500);
}

// Bloquear fila de producto_tallas y obtener stock actual (FOR UPDATE)
$stock = 0;
$sql = "SELECT stock FROM producto_tallas WHERE id_producto_talla = ? FOR UPDATE";
if (!($stmt = mysqli_prepare($conn, $sql))) {
    mysqli_rollback($conn);
    json_error('Error en la consulta (lock talla)', 500);
}
mysqli_stmt_bind_param($stmt, 'i', $id_producto_talla);
mysqli_stmt_execute($stmt);
mysqli_stmt_bind_result($stmt, $pt_stock_locked);
if (mysqli_stmt_fetch($stmt)) {
    $stock = (int)$pt_stock_locked;
}
mysqli_stmt_close($stmt);

if ($stock < $cantidad) {
    mysqli_rollback($conn);
    json_error('Stock insuficiente', 409);
}

// Verificar si ya existe fila en carrito para este usuario + producto_talla
$existing_id = null;
$existing_qty = 0;
$sql = "SELECT id_carrito, cantidad FROM carrito WHERE id_usuario = ? AND id_producto_talla = ? LIMIT 1";
if ($stmt = mysqli_prepare($conn, $sql)) {
    mysqli_stmt_bind_param($stmt, 'ii', $ID_Usuario, $id_producto_talla);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $ec_id, $ec_qty);
    if (mysqli_stmt_fetch($stmt)) { $existing_id = (int)$ec_id; $existing_qty = (int)$ec_qty; }
    mysqli_stmt_close($stmt);
}

// Si existe, actualizar cantidad (sumar la cantidad solicitada) y reducir stock en producto_tallas
if ($existing_id) {
    $new_qty = $existing_qty + $cantidad;
    // ya verificamos que $stock >= $cantidad al bloquear la fila, así que aquí sólo actualizamos
    $new_total = $precio * $new_qty;
    $sql = "UPDATE carrito SET cantidad = ?, total = ? WHERE id_carrito = ?";
    if (!($stmt = mysqli_prepare($conn, $sql))) {
        mysqli_rollback($conn);
        json_error('Error al preparar actualización', 500);
    }
    mysqli_stmt_bind_param($stmt, 'idi', $new_qty, $new_total, $existing_id);
    $ok = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    if (!$ok) {
        mysqli_rollback($conn);
        json_error('Error al actualizar carrito', 500);
    }
    // Reducir stock reservado
    $sql = "UPDATE producto_tallas SET stock = stock - ? WHERE id_producto_talla = ?";
    if (!($stmt = mysqli_prepare($conn, $sql))) {
        mysqli_rollback($conn);
        json_error('Error al preparar update stock', 500);
    }
    mysqli_stmt_bind_param($stmt, 'ii', $cantidad, $id_producto_talla);
    $ok = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    if (!$ok) {
        mysqli_rollback($conn);
        json_error('Error al actualizar stock', 500);
    }
} else {
    // Insertar nueva fila y reducir stock
    $total = $precio * $cantidad;
    $sql = "INSERT INTO carrito (id_usuario, id_producto, id_producto_talla, cantidad, total) VALUES (?, ?, ?, ?, ?)";
    if (!($stmt = mysqli_prepare($conn, $sql))) {
        mysqli_rollback($conn);
        json_error('Error al preparar inserción', 500);
    }
    mysqli_stmt_bind_param($stmt, 'iiiid', $ID_Usuario, $id_producto, $id_producto_talla, $cantidad, $total);
    $ok = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    if (!$ok) {
        mysqli_rollback($conn);
        json_error('Error al insertar en carrito', 500);
    }
    // Reducir stock reservado
    $sql = "UPDATE producto_tallas SET stock = stock - ? WHERE id_producto_talla = ?";
    if (!($stmt = mysqli_prepare($conn, $sql))) {
        mysqli_rollback($conn);
        json_error('Error al preparar update stock', 500);
    }
    mysqli_stmt_bind_param($stmt, 'ii', $cantidad, $id_producto_talla);
    $ok = mysqli_stmt_execute($stmt);
    mysqli_stmt_close($stmt);
    if (!$ok) {
        mysqli_rollback($conn);
        json_error('Error al actualizar stock', 500);
    }
}

// Commit de la transaccion
if (!mysqli_commit($conn)) {
    mysqli_rollback($conn);
    json_error('Error al confirmar la transacción', 500);
}

// Calcular nuevo contador total de items del carrito para este usuario
$cartCount = 0;
$sql = "SELECT SUM(cantidad) FROM carrito WHERE id_usuario = ?";
if ($stmt = mysqli_prepare($conn, $sql)) {
    mysqli_stmt_bind_param($stmt, 'i', $ID_Usuario);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_bind_result($stmt, $sum_qty);
    if (mysqli_stmt_fetch($stmt)) { $cartCount = (int)$sum_qty; }
    mysqli_stmt_close($stmt);
}

echo json_encode([
    'success' => true,
    'message' => 'Producto agregado al carrito',
    'cartCount' => $cartCount
]);
exit;

?>
