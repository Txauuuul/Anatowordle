<?php
include '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user = trim($_POST['username']); // trim quita espacios en blanco accidentales
    $pass = password_hash($_POST['password'], PASSWORD_BCRYPT);

    try {
        // Insertamos usuario, contraseña, puntos (0) y nivel (1)
        $stmt = $pdo->prepare("INSERT INTO usuarios (username, password, puntos_totales, nivel) VALUES (?, ?, 0, 1)");
        $stmt->execute([$user, $pass]);
        
        // Redirigimos al login con mensaje de éxito
        header("Location: login.php?msg=Cuenta creada correctamente");
        exit();
    } catch (PDOException $e) {
        // Mensaje de error más amigable si el usuario ya existe
        die("<h3 style='color:white; background:#333; padding:20px;'>Error: El usuario '$user' ya existe. <a href='register.php' style='color:red'>Inténtalo de nuevo</a></h3>");
    }
}
?>