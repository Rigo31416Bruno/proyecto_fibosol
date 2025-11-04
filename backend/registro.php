<?php

require("../db/conexion.php");

try {
    $usuario = $_POST['usuario'];
    $email = $_POST['email'];
    $contrasena = password_hash($_POST['contrasena'], PASSWORD_DEFAULT);

    echo json_encode(['success' => true , 'response' => 'Exito']);
} catch (Exception $th) {
    echo json_encode(['success' => false, 'response' => 'Algo fallo']);
    exit;
}

$pdo = DB::get();
$query = "INSERT INTO usuarios (nombre, correo, contraseña) VALUES (?, ?, ?)";

$db->prepare($query);
$db->blind_param();
?>