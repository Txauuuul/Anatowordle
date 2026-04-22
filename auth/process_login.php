<?php
include '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        
        $_SESSION['usuario_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];
        $_SESSION['is_guest'] = false; 

        header("Location: ../index.php");
        exit();
        
    } else {
        header("Location: login.php?error=credenciales_incorrectas");
        exit();
    }
} else {
    header("Location: login.php");
    exit();
}
?>
