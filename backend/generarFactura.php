<?php
session_start();
require('../db/conexion.php');
require('../fpdf186/fpdf.php');

header('Content-Type: application/json');

// Verificar sesión
if (!isset($_SESSION['id_usuario'])) {
    echo json_encode(['success' => false, 'message' => 'Sesión no válida']);
    exit();
}

$id_usuario = $_SESSION['id_usuario'];
$pedido_id = isset($_POST['pedido_id']) ? (int)$_POST['pedido_id'] : 0;

if ($pedido_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'ID de pedido inválido']);
    exit();
}

// Verificar que el pedido pertenece al usuario
$sql_pedido = "SELECT p.*, u.Nombre, u.Correo, u.Direccion, u.Telefono 
               FROM pedidos p
               JOIN usuarios u ON p.id_usuario = u.ID_Usuario
               WHERE p.id_pedido = ? AND p.id_usuario = ?";

$stmt = $conn->prepare($sql_pedido);
$stmt->bind_param("ii", $pedido_id, $id_usuario);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    echo json_encode(['success' => false, 'message' => 'Pedido no encontrado']);
    exit();
}

$pedido = $result->fetch_assoc();
$stmt->close();

// Obtener detalles del pedido
$sql_detalles = "SELECT dp.*, p.nombre as producto_nombre, t.nombre as talla_nombre
                 FROM detalle_pedido dp
                 JOIN productos p ON dp.id_producto = p.id_producto
                 LEFT JOIN producto_tallas pt ON dp.id_producto_talla = pt.id_producto_talla
                 LEFT JOIN tallas t ON pt.id_talla = t.id_talla
                 WHERE dp.id_pedido = ?";

$stmt_det = $conn->prepare($sql_detalles);
$stmt_det->bind_param("i", $pedido_id);
$stmt_det->execute();
$result_det = $stmt_det->get_result();

$detalles = [];
while ($row = $result_det->fetch_assoc()) {
    $detalles[] = $row;
}
$stmt_det->close();

// Crear PDF
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 20);

// Encabezado
$pdf->SetFillColor(26, 115, 232);
$pdf->SetTextColor(255, 255, 255);
$pdf->Cell(0, 15, 'FACTURA', 0, 1, 'C', true);
$pdf->Ln(5);

// Información de la empresa
$pdf->SetFont('Arial', 'B', 14);
$pdf->SetTextColor(0, 0, 0);
$pdf->Cell(0, 8, 'Shopware', 0, 1, 'L');
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(0, 5, 'RFC: SHW123456789', 0, 1, 'L');
$pdf->Cell(0, 5, 'Av. Principal #123, Culiacan, Sinaloa', 0, 1, 'L');
$pdf->Cell(0, 5, 'Tel: +52 (667) 123-4567', 0, 1, 'L');
$pdf->Ln(5);

// Información del cliente y factura
$pdf->SetFillColor(240, 240, 240);
$pdf->Rect(10, $pdf->GetY(), 190, 35, 'F');

$y_position = $pdf->GetY();
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(95, 6, 'DATOS DEL CLIENTE:', 0, 0, 'L');
$pdf->Cell(95, 6, 'DATOS DE LA FACTURA:', 0, 1, 'L');

$pdf->SetFont('Arial', '', 9);
$pdf->Cell(95, 5, 'Nombre: ' . utf8_decode($pedido['Nombre']), 0, 0, 'L');
$pdf->Cell(95, 5, 'No. Factura: ' . str_pad($pedido_id, 8, '0', STR_PAD_LEFT), 0, 1, 'L');

$pdf->Cell(95, 5, 'Email: ' . $pedido['Correo'], 0, 0, 'L');
$pdf->Cell(95, 5, 'Fecha: ' . date('d/m/Y H:i', strtotime($pedido['fecha_pedido'])), 0, 1, 'L');

$pdf->Cell(95, 5, 'Tel: ' . ($pedido['Telefono'] ?: 'N/A'), 0, 0, 'L');
$pdf->Cell(95, 5, 'No. Pedido: #' . $pedido_id, 0, 1, 'L');

$pdf->MultiCell(95, 5, 'Direccion: ' . utf8_decode($pedido['Direccion'] ?: 'N/A'), 0, 'L');

$pdf->Ln(5);

// Tabla de productos
$pdf->SetFont('Arial', 'B', 10);
$pdf->SetFillColor(26, 115, 232);
$pdf->SetTextColor(255, 255, 255);
$pdf->Cell(80, 8, 'Producto', 1, 0, 'C', true);
$pdf->Cell(25, 8, 'Talla', 1, 0, 'C', true);
$pdf->Cell(25, 8, 'Cantidad', 1, 0, 'C', true);
$pdf->Cell(30, 8, 'Precio Unit.', 1, 0, 'C', true);
$pdf->Cell(30, 8, 'Subtotal', 1, 1, 'C', true);

$pdf->SetTextColor(0, 0, 0);
$pdf->SetFont('Arial', '', 9);

$subtotal = 0;
foreach ($detalles as $detalle) {
    $subtotal_item = $detalle['precio_unitario'] * $detalle['cantidad'];
    $subtotal += $subtotal_item;
    
    $pdf->Cell(80, 7, utf8_decode($detalle['producto_nombre']), 1, 0, 'L');
    $pdf->Cell(25, 7, utf8_decode($detalle['talla_nombre'] ?: 'N/A'), 1, 0, 'C');
    $pdf->Cell(25, 7, $detalle['cantidad'], 1, 0, 'C');
    $pdf->Cell(30, 7, '$' . number_format($detalle['precio_unitario'], 2), 1, 0, 'R');
    $pdf->Cell(30, 7, '$' . number_format($subtotal_item, 2), 1, 1, 'R');
}

// Totales
$pdf->Ln(3);
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(130, 7, '', 0, 0);
$pdf->Cell(30, 7, 'Subtotal:', 0, 0, 'R');
$pdf->Cell(30, 7, '$' . number_format($subtotal, 2), 0, 1, 'R');

$iva = $subtotal * 0.16;
$pdf->Cell(130, 7, '', 0, 0);
$pdf->Cell(30, 7, 'IVA (16%):', 0, 0, 'R');
$pdf->Cell(30, 7, '$' . number_format($iva, 2), 0, 1, 'R');

$pdf->SetFillColor(26, 115, 232);
$pdf->SetTextColor(255, 255, 255);
$pdf->Cell(130, 9, '', 0, 0);
$pdf->Cell(30, 9, 'TOTAL:', 1, 0, 'R', true);
$pdf->Cell(30, 9, '$' . number_format($pedido['total'], 2), 1, 1, 'R', true);

// Pie de página
$pdf->Ln(10);
$pdf->SetTextColor(0, 0, 0);
$pdf->SetFont('Arial', 'I', 8);
$pdf->MultiCell(0, 4, utf8_decode('Gracias por su compra. Esta factura es un comprobante válido de su transacción.'), 0, 'C');
$pdf->Cell(0, 4, utf8_decode('Para cualquier duda o aclaración, contáctenos a info@shopware.com'), 0, 1, 'C');

// Guardar PDF
$facturas_dir = '../facturas/';
if (!file_exists($facturas_dir)) {
    mkdir($facturas_dir, 0777, true);
}

$nombre_archivo = 'factura_' . $pedido_id . '_' . time() . '.pdf';
$ruta_completa = $facturas_dir . $nombre_archivo;

$pdf->Output('F', $ruta_completa);

// Registrar en la base de datos
$sql_factura = "INSERT INTO facturas (id_usuario, id_pedido, ruta_pdf, fecha_creacion) 
                VALUES (?, ?, ?, NOW())";
$stmt_fac = $conn->prepare($sql_factura);
$stmt_fac->bind_param("iis", $id_usuario, $pedido_id, $ruta_completa);
$stmt_fac->execute();
$stmt_fac->close();

$conn->close();

// Retornar éxito con la URL del PDF
echo json_encode([
    'success' => true,
    'message' => 'Factura generada exitosamente',
    'url' => $ruta_completa
]);
?>
