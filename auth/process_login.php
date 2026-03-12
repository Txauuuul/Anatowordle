<?php
include '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // 1. Buscamos al usuario en la base de datos
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    // 2. Verificamos si existe y si la contraseña coincide
    if ($user && password_verify($password, $user['password'])) {
        
        // ¡ÉXITO! Guardamos sus datos en la sesión
        $_SESSION['usuario_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['is_guest'] = false; // Importante: ya no es invitado

        // Lo mandamos al juego
        header("Location: ../index.php");
        exit();
        
    } else {
        // FALLO: Usuario o contraseña incorrectos
        // Lo devolvemos al login con un aviso de error
        header("Location: login.php?error=credenciales_incorrectas");
        exit();
    }
} else {
    // Si intentan entrar a este archivo sin enviar el formulario
    header("Location: login.php");
    exit();
}
?>