<?php include '../includes/db.php'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - AnatoWordle</title>
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        /* Estilos específicos para el formulario */
        .auth-container {
            margin-top: 50px;
            width: 90%;
            max-width: 400px;
            padding: 20px;
            border: 1px solid var(--color-border);
            border-radius: 8px;
            background-color: #1e1e1f;
        }
        input {
            width: 100%;
            padding: 12px;
            margin: 10px 0;
            background: #121213;
            border: 1px solid var(--color-border);
            color: white;
            box-sizing: border-box;
        }
        button {
            width: 100%;
            padding: 12px;
            background-color: var(--color-primary);
            color: white;
            border: none;
            font-weight: bold;
            cursor: pointer;
            text-transform: uppercase;
        }
        button:hover { opacity: 0.9; }
        a { color: var(--color-primary); text-decoration: none; font-size: 0.9rem; }
    </style>
</head>
<body>
    <header><h1>AnatoWordle</h1></header>
    
    <div class="auth-container">
        <h2>Crear Cuenta</h2>
        <form action="process_register.php" method="POST">
            <input type="text" name="username" placeholder="Elige un usuario" required>
            <input type="password" name="password" placeholder="Elige una contraseña" required>
            <button type="submit">Registrarse</button>
        </form>
        
        <p style="margin-top:15px; text-align:center;">
            ¿Ya tienes cuenta? <a href="login.php">Inicia sesión aquí</a>
        </p>
    </div>
</body>
</html>