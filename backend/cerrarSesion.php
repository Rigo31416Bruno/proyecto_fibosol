<?php
session_start();

session_destroy();
header('Location: ../frontend/inicio_de_sesion.html');
exit();
?>