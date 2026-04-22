<?php
include '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = trim($_POST['username']); 
    $pass = password_hash($_POST['password'], PASSWORD_BCRYPT);

    try {
        $stmt = $pdo->prepare("INSERT INTO usuarios (username, password, puntos_totales, nivel) VALUES (?, ?, 0, 1)");
        $stmt->execute([$user, $pass]);
        
        header("Location: login.php?msg=Cuenta creada correctamente");
        exit();
    } catch (PDOException $e) {
        die("<h3 style='color:white; background:#333; padding:20px;'>Error: El usuario '$user' ya existe. <a href='register.php' style='color:red'>Inténtalo de nuevo</a></h3>");
    }
}
?>
