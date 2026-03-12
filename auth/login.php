<?php include '../includes/db.php'; ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - AnatoWordle</title>
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
            background-color: var(--color-primary); /* Tu rojo #f02109 */
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
        <h2>Iniciar Sesión</h2>
        <form action="process_login.php" method="POST">
            <input type="text" name="username" placeholder="Nombre de usuario" required>
            <input type="password" name="password" placeholder="Contraseña" required>
            <button type="submit">Entrar</button>
        </form>
        <p style="margin-top:15px; text-align:center;">
            ¿No tienes cuenta? <a href="register.php">Regístrate aquí</a>
        </p>
    </div>
</form>

<div style="margin-top: 20px; border-top: 1px dashed var(--color-border); padding-top: 20px;">
    <form action="process_guest.php" method="POST">
        <button type="submit" style="background-color: transparent; border: 2px solid var(--color-primary); color: var(--color-primary);">
            Jugar como Invitado
        </button>
    </form>
</div>
</body>
</html>