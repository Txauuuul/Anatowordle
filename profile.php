<?php
include 'includes/db.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: auth/login.php");
    exit();
}

$userId = $_SESSION['usuario_id'];

$stmt = $pdo->prepare("SELECT username, puntos_totales, nivel FROM usuarios WHERE id = ?");
$stmt->execute([$userId]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    session_destroy();
    header("Location: auth/login.php?error=sesion_fantasma");
    exit();
}

$miUsuario = $user['username'] ?? 'Estudiante';
$miNivel = $user['nivel'] ?? 1;
$misPuntos = $user['puntos_totales'] ?? 0;

$progreso = ($misPuntos % 50) * 2; 

try {
    $stmtWins = $pdo->prepare("SELECT COUNT(*) FROM partidas_diarias WHERE usuario_id = ?");
    $stmtWins->execute([$userId]);
    $victorias = $stmtWins->fetchColumn();
} catch (Exception $e) {
    $victorias = 0;
}

$inicial = strtoupper(substr($miUsuario, 0, 1));
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil - AnatoWordle</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <style>
        .profile-card {
            background-color: #1e1e1f;
            border: 1px solid #3a3a3c;
            border-radius: 15px;
            padding: 30px;
            text-align: center;
            max-width: 400px;
            width: 90%;
            margin-top: 20px;
        }
        .avatar-circle {
            width: 80px;
            height: 80px;
            background-color: var(--color-primary);
            color: white;
            font-size: 2.5rem;
            font-weight: bold;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 15px auto;
            border: 3px solid #121213;
            box-shadow: 0 0 0 2px var(--color-primary);
        }
        .stat-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
            margin-top: 25px;
        }
        .stat-item {
            background-color: #121213;
            padding: 15px;
            border-radius: 8px;
            border: 1px solid #3a3a3c;
        }
        .progress-bar-bg {
            background-color: #3a3a3c;
            height: 10px;
            border-radius: 5px;
            margin-top: 10px;
            overflow: hidden;
        }
        .progress-bar-fill {
            background-color: #4cd137;
            height: 100%;
            transition: width 0.5s ease;
        }
    </style>
</head>
<body style="display: flex; flex-direction: column; align-items: center;">

    <header style="width: 100%; border-bottom: 1px solid #3a3a3c; padding: 20px 0; text-align: center; position: relative;">
        <a href="index.php" style="position: absolute; left: 20px; top: 20px; color: #818384; text-decoration: none; border: 1px solid #3a3a3c; padding: 5px 10px; border-radius: 5px;">&larr; Volver</a>
        <h1 style="color: #f02109; margin: 0; text-transform: uppercase;">Mi Perfil</h1>
    </header>

    <div class="profile-card">
        <div class="avatar-circle"><?php echo $inicial; ?></div>
        
        <h2 style="margin: 0; color: white;"><?php echo htmlspecialchars($miUsuario); ?></h2>
        <p style="color: #818384; margin-top: 5px;">Estudiante de Anatomía</p>

        <div style="margin-top: 20px; text-align: left;">
            <div style="display: flex; justify-content: space-between; font-size: 0.8rem; color: #ccc;">
                <span>Nivel <?php echo $miNivel; ?></span>
                <span>Siguiente: Nivel <?php echo $miNivel + 1; ?></span>
            </div>
            <div class="progress-bar-bg">
                <div class="progress-bar-fill" style="width: <?php echo $progreso; ?>%;"></div>
            </div>
            <p style="font-size: 0.7rem; color: #818384; margin-top: 5px; text-align: center;">
                <?php echo $misPuntos; ?> puntos acumulados
            </p>
        </div>

        <div class="stat-grid">
            <div class="stat-item">
                <span style="display: block; font-size: 2rem; font-weight: bold; color: #f02109;"><?php echo $victorias; ?></span>
                <span style="font-size: 0.8rem; color: #818384;">DÍAS GANADOS</span>
            </div>
            <div class="stat-item">
                <span style="display: block; font-size: 2rem; font-weight: bold; color: #4cd137;"><?php echo $miNivel; ?></span>
                <span style="font-size: 0.8rem; color: #818384;">RANGO ACTUAL</span>
            </div>
        </div>

        <a href="index.php?logout=true" style="display: block; margin-top: 30px; color: #f02109; text-decoration: none; font-size: 0.9rem; border: 1px solid #f02109; padding: 10px; border-radius: 5px;">
            Cerrar Sesión
        </a>
    </div>

</body>
</html>
