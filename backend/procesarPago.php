<?php
session_start();
require('../db/conexion.php');
header('Content-Type: application/json');

// Verificar sesión
if (!isset($_SESSION['id_usuario'])) {
    echo json_encode(['success' => false, 'message' => 'Sesión no válida']);
    exit();
}

$id_usuario = $_SESSION['id_usuario'];

// Validar método POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit();
}

// Obtener datos del formulario
$cardNumber = str_replace(' ', '', $_POST['cardNumber'] ?? '');
$cardHolder = trim($_POST['cardHolder'] ?? '');
$expiry = $_POST['expiry'] ?? '';
$cvv = $_POST['cvv'] ?? '';

// Validaciones de tarjeta (simuladas para proyecto educativo)
$errors = [];

// Validar número de tarjeta (longitud fija: 16 dígitos)
if (empty($cardNumber) || !preg_match('/^\d{16}$/', $cardNumber)) {
    $errors[] = 'Número de tarjeta inválido (debe tener 16 dígitos)';
}

// Validar titular
if (empty($cardHolder) || !preg_match('/^[a-zA-ZáéíóúÁÉÍÓÚñÑ\s]{3,}$/', $cardHolder)) {
    $errors[] = 'Nombre del titular inválido';
}

// Validar fecha de vencimiento
if (!preg_match('/^(0[1-9]|1[0-2])\/\d{2}$/', $expiry)) {
    $errors[] = 'Fecha de vencimiento inválida (formato MM/AA)';
} else {
    list($month, $year) = explode('/', $expiry);
    $currentYear = (int)date('y');
    $currentMonth = (int)date('m');
    $expYear = (int)$year;
    $expMonth = (int)$month;
    
    if ($expYear < $currentYear || ($expYear == $currentYear && $expMonth < $currentMonth)) {
        $errors[] = 'La tarjeta está vencida';
    }
}

// Validar CVV
if (!preg_match('/^\d{3,4}$/', $cvv)) {
    $errors[] = 'CVV inválido';
}

// Si hay errores, retornar
if (!empty($errors)) {
    echo json_encode(['success' => false, 'message' => implode(', ', $errors)]);
    exit();
}

// Obtener items del carrito
$sql = "SELECT c.id_carrito, c.id_producto, c.id_producto_talla, c.cantidad, p.precio
        FROM carrito c
        JOIN productos p ON c.id_producto = p.id_producto
        WHERE c.id_usuario = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_usuario);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    echo json_encode(['success' => false, 'message' => 'El carrito está vacío']);
    exit();
}

$carrito_items = [];
$total = 0;

while ($row = $result->fetch_assoc()) {
    $carrito_items[] = $row;
    $total += $row['precio'] * $row['cantidad'];
}
$stmt->close();

// Total (sin IVA)
$total_con_iva = $total;

// Iniciar transacción
$conn->begin_transaction();

try {
    // Insertar pedido
    $sql_pedido = "INSERT INTO pedidos (id_usuario, fecha_pedido, total) VALUES (?, NOW(), ?)";
    $stmt_pedido = $conn->prepare($sql_pedido);
    $stmt_pedido->bind_param("id", $id_usuario, $total_con_iva);
    $stmt_pedido->execute();
    $id_pedido = $conn->insert_id;
    $stmt_pedido->close();
    
    // Insertar detalles del pedido
    $sql_detalle = "INSERT INTO detalle_pedido (id_pedido, id_producto, id_producto_talla, cantidad, precio_unitario) 
                    VALUES (?, ?, ?, ?, ?)";
    $stmt_detalle = $conn->prepare($sql_detalle);
    
    foreach ($carrito_items as $item) {
        $stmt_detalle->bind_param("iiiid", 
            $id_pedido, 
            $item['id_producto'], 
            $item['id_producto_talla'], 
            $item['cantidad'], 
            $item['precio']
        );
        $stmt_detalle->execute();
        
        // Actualizar stock
        $sql_stock = "UPDATE producto_tallas SET stock = stock - ? WHERE id_producto_talla = ?";
        $stmt_stock = $conn->prepare($sql_stock);
        $stmt_stock->bind_param("ii", $item['cantidad'], $item['id_producto_talla']);
        $stmt_stock->execute();
        $stmt_stock->close();
    }
    $stmt_detalle->close();
    
    // Vaciar carrito
    $sql_vaciar = "DELETE FROM carrito WHERE id_usuario = ?";
    $stmt_vaciar = $conn->prepare($sql_vaciar);
    $stmt_vaciar->bind_param("i", $id_usuario);
    $stmt_vaciar->execute();
    $stmt_vaciar->close();
    
    // Confirmar transacción
    $conn->commit();
    
    echo json_encode([
        'success' => true, 
        'message' => 'Pago procesado exitosamente',
        'pedido_id' => $id_pedido
    ]);
    
} catch (Exception $e) {
    // Revertir transacción en caso de error
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => 'Error al procesar el pago: ' . $e->getMessage()]);
}

$conn->close();
?>
