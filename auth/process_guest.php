<?php
include '../includes/db.php';

// Iniciamos una sesión marcada como invitado
$_SESSION['usuario_id'] = null; // No tiene ID de base de datos
$_SESSION['username'] = "Invitado";
$_SESSION['is_guest'] = true;

header("Location: ../index.php");
exit();
?>