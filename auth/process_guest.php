<?php
include '../includes/db.php';

$_SESSION['usuario_id'] = null;
$_SESSION['username'] = "Invitado";
$_SESSION['is_guest'] = true;

header("Location: ../index.php");
exit();
?>
