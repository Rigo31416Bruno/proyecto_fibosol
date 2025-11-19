<?php
// Asegurar sesión antes de cualquier include que la use
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

include 'validarSesion.php';
require('../db/conexion.php');

// Validar que el usuario esté autenticado
if (!function_exists('validarSesion') || !validarSesion()) {
    http_response_code(401);
    echo "Usuario no autenticado";
    exit();
}

// Solo POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo "Método no permitido";
    exit();
}

if (empty($_POST['direccion'])) {
    http_response_code(400);
    echo "Dirección NO válida";
    exit();
}

// Intentar obtener ID_Usuario desde varias claves comunes en $_SESSION
$possibleKeys = ['ID_Usuario','id_usuario','user_id','ID','id','usuario_id'];
$ID_Usuario = null;
foreach ($possibleKeys as $k) {
    if (!empty($_SESSION[$k])) {
        $ID_Usuario = (int) $_SESSION[$k];
        break;
    }
}

// Si no hay id en sesión, intentar resolver por email/nombre almacenado en sesión
if (empty($ID_Usuario)) {
    if (!empty($_SESSION['email']) || !empty($_SESSION['correo']) || !empty($_SESSION['nombre'])) {
        $email = $_SESSION['email'] ?? $_SESSION['correo'] ?? null;
        $nombre = $_SESSION['nombre'] ?? null;

        if ($email) {
            $stmt_get = mysqli_prepare($conn, "SELECT ID_Usuario FROM usuarios WHERE Email = ? LIMIT 1");
            if ($stmt_get) {
                mysqli_stmt_bind_param($stmt_get, "s", $email);
                mysqli_stmt_execute($stmt_get);
                mysqli_stmt_bind_result($stmt_get, $foundId);
                if (mysqli_stmt_fetch($stmt_get)) {
                    $ID_Usuario = (int) $foundId;
                }
                mysqli_stmt_close($stmt_get);
            }
        } elseif ($nombre) {
            $stmt_get = mysqli_prepare($conn, "SELECT ID_Usuario FROM usuarios WHERE Nombre = ? LIMIT 1");
            if ($stmt_get) {
                mysqli_stmt_bind_param($stmt_get, "s", $nombre);
                mysqli_stmt_execute($stmt_get);
                mysqli_stmt_bind_result($stmt_get, $foundId);
                if (mysqli_stmt_fetch($stmt_get)) {
                    $ID_Usuario = (int) $foundId;
                }
                mysqli_stmt_close($stmt_get);
            }
        }
    }
}

if (empty($ID_Usuario)) {
    http_response_code(401);
    echo "Usuario no autenticado (ID no encontrado en sesión)";
    exit();
}

$direccion = trim($_POST['direccion']);

// Prepared statement para actualizar
$stmt = mysqli_prepare($conn, "UPDATE usuarios SET Direccion = ? WHERE ID_Usuario = ?");
if (!$stmt) {
    http_response_code(500);
    echo "Error en la consulta: " . mysqli_error($conn);
    exit();
}

mysqli_stmt_bind_param($stmt, "si", $direccion, $ID_Usuario);

if (mysqli_stmt_execute($stmt)) {
    echo "Dirección actualizada correctamente";
} else {
    http_response_code(500);
    echo "Error al actualizar dirección: " . mysqli_stmt_error($stmt);
}

mysqli_stmt_close($stmt);