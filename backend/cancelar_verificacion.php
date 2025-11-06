<?php
header('Content-Type: application/json; charset=utf-8');
require('../db/conexion.php');

$verification_id = isset($_POST['verification_id']) ? (int)$_POST['verification_id'] : 0;
if ($verification_id <= 0) {
    echo json_encode(['success' => false, 'message' => 'ID de verificación inválido.']);
    exit();
}

$del = mysqli_prepare($conn, "DELETE FROM verificaciones WHERE ID = ?");
if (!$del) {
    echo json_encode(['success' => false, 'message' => 'Error al preparar la petición.']);
    exit();
}
mysqli_stmt_bind_param($del, "i", $verification_id);
mysqli_stmt_execute($del);
$deleted = mysqli_stmt_affected_rows($del);
mysqli_stmt_close($del);
mysqli_close($conn);

if ($deleted > 0) {
    echo json_encode(['success' => true, 'message' => 'Verificación cancelada.']);
} else {
    echo json_encode(['success' => false, 'message' => 'No se encontró la verificación.']);
}
exit();
?>