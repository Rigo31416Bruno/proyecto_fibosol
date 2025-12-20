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
 $stockArr = isset($_POST['stock']) && is_array($_POST['stock']) ? $_POST['stock'] : [];

 if ($id <= 0 || $nombre === '') {
     echo json_encode(['success' => false, 'message' => 'Datos inválidos']);
     exit();
 }

// obtener imagen actual para poder eliminar si se sube una nueva
$currentImagen = null;
$q0 = mysqli_prepare($conn, "SELECT imagen FROM productos WHERE id_producto = ?");
mysqli_stmt_bind_param($q0, 'i', $id);
mysqli_stmt_execute($q0);
$r0 = mysqli_stmt_get_result($q0);
if ($r0) {
    $row0 = mysqli_fetch_assoc($r0);
    if ($row0) $currentImagen = $row0['imagen'];
}
mysqli_stmt_close($q0);

// manejo de archivo (si se envía una imagen nueva)
$uploadDir = __DIR__ . '/../../img/';
if (!is_dir($uploadDir)) @mkdir($uploadDir, 0755, true);
$newImagenPath = null;
if (!empty($_FILES['imagen']) && isset($_FILES['imagen']['error']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
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
    $newImagenPath = '../img/' . $safeName;
}

 $imagenToSave = $newImagenPath !== null ? $newImagenPath : $currentImagen;
 $query = "UPDATE productos SET nombre = ?, precio = ?, categoria = ?, imagen = ? WHERE id_producto = ?";
 $stmt = mysqli_prepare($conn, $query);
 mysqli_stmt_bind_param($stmt, "sdisi", $nombre, $precio, $categoria, $imagenToSave, $id);

 if (!mysqli_stmt_execute($stmt)) {
     $err = mysqli_error($conn);
     mysqli_stmt_close($stmt);
     mysqli_close($conn);
     echo json_encode(['success' => false, 'message' => 'Error al actualizar: ' . $err]);
     exit();
 }
 mysqli_stmt_close($stmt);

 // Actualizar/insertar stock por talla usando INSERT ... ON DUPLICATE KEY UPDATE
 if (!empty($stockArr)) {
     $stmt2 = mysqli_prepare($conn, "INSERT INTO producto_tallas (id_producto, id_talla, stock) VALUES (?, ?, ?) ON DUPLICATE KEY UPDATE stock = VALUES(stock)");
     foreach ($stockArr as $id_talla => $stockVal) {
         $id_t = (int)$id_talla;
         $st = (int)$stockVal;
         mysqli_stmt_bind_param($stmt2, 'iii', $id, $id_t, $st);
         mysqli_stmt_execute($stmt2);
     }
     mysqli_stmt_close($stmt2);
 }

 mysqli_close($conn);
// eliminar imagen antigua si se subió una nueva
if ($newImagenPath && $currentImagen) {
    $oldFile = $uploadDir . basename($currentImagen);
    if (file_exists($oldFile)) @unlink($oldFile);
}

echo json_encode(['success' => true, 'message' => 'Producto actualizado correctamente']);

?>
