<?php
session_start();

function validarSesion()
{
    return isset($_SESSION['id_usuario']) && isset($_SESSION['correo']) && isset($_SESSION['nombre']);
}