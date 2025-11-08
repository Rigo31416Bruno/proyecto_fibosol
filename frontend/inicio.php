<?php
include '../backend/validarSesion.php';

if(!validarSesion())
{
    echo "No se puede acceder sin iniciar sesion";
    header('Location: inicio_de_sesion.html');
    exit();
}
?>

<!DOCTYPE html>
<html lang="es>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../styles/inicio.css">
    <title>Inicio</title>
</head>
<body>
    <h1>Pagina de Inicio</h1>

    <div class="cuadroCerrarSesion">
        
    </div>
    
</body>
</html>