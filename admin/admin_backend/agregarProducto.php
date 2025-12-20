<?php
session_start();
require_once '../../db/conexion.php';

header('Content-Type: application/json');

if (!isset($_SESSION['id_usuario']) || $_SESSION['id_rol'] != 2) {
    echo json_encode(['success' => false, 'message' => 'No autorizado']);
    exit();
}

$nombre = isset($_POST['nombre']) ? trim($_POST['nombre']) : '';
$precio = isset($_POST['precio']) ? (float)$_POST['precio'] : 0;
$categoria = isset($_POST['categoria']) && $_POST['categoria'] !== '' ? (int)$_POST['categoria'] : null;
$stockArr = isset($_POST['stock']) && is_array($_POST['stock']) ? $_POST['stock'] : [];

if ($nombre === '' || $precio <= 0 || empty($categoria)) {
    echo json_encode(['success' => false, 'message' => 'Datos incompletos o inválidos']);
    exit();
}

// Manejo de archivo: guardar en ../img/ para mantener consistencia con otras imágenes
$uploadDir = __DIR__ . '/../../img/';
if (!is_dir($uploadDir)) @mkdir($uploadDir, 0755, true);
$imagenPath = null;
if (!empty($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
    $file = $_FILES['imagen'];
    $allowed = ['image/jpeg','image/png','image/gif','image/webp'];
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    if (!in_array($mime, $allowed)) {
        echo json_encode(['success' => false, 'message' => 'Tipo de imagen no permitido']);
        exit();
    }
    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $safeName = time() . '_' . bin2hex(random_bytes(6)) . '.' . $ext;
    $dest = $uploadDir . $safeName;
    if (!move_uploaded_file($file['tmp_name'], $dest)) {
        echo json_encode(['success' => false, 'message' => 'Error al guardar la imagen']);
        exit();
    }
    // ruta relativa guardada en DB (usar ../img/ como las demás imágenes)
    $imagenPath = '../img/' . $safeName;
}

// Insertar producto
$stmt = mysqli_prepare($conn, "INSERT INTO productos (nombre, precio, categoria, imagen) VALUES (?, ?, ?, ?)");
mysqli_stmt_bind_param($stmt, 'sdis', $nombre, $precio, $categoria, $imagenPath);
if (!mysqli_stmt_execute($stmt)) {
    $err = mysqli_error($conn);
    mysqli_stmt_close($stmt);
    // eliminar archivo subido si existe
    if ($imagenPath && file_exists($dest)) @unlink($dest);
    echo json_encode(['success' => false, 'message' => 'Error al insertar producto: ' . $err]);
    exit();
}
$newId = mysqli_insert_id($conn);
mysqli_stmt_close($stmt);

// Insertar stock por talla
if (!empty($stockArr) && is_array($stockArr)) {
    $stmt2 = mysqli_prepare($conn, "INSERT INTO producto_tallas (id_producto, id_talla, stock) VALUES (?, ?, ?)");
    foreach ($stockArr as $id_talla => $stockVal) {
        $id_t = (int)$id_talla;
        $st = (int)$stockVal;
        mysqli_stmt_bind_param($stmt2, 'iii', $newId, $id_t, $st);
        mysqli_stmt_execute($stmt2);
    }
    mysqli_stmt_close($stmt2);
}

mysqli_close($conn);

echo json_encode(['success' => true, 'message' => 'Producto creado', 'id_producto' => $newId]);
exit();

?>
