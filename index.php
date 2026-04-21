<?php 
include 'includes/db.php'; 

// 1. Acceso
if (!isset($_SESSION['usuario_id']) && !isset($_SESSION['is_guest'])) {
    header("Location: auth/login.php");
    exit();
}
if (isset($_GET['logout'])) {
    session_destroy();
    header("Location: auth/login.php");
    exit();
}

// 2. Datos Usuario
$puntos = 0;
$nivel = 1;
$nombreUsuario = $_SESSION['username'] ?? "Invitado";

if (isset($_SESSION['usuario_id']) && $_SESSION['usuario_id'] !== null) {
    $stmt = $pdo->prepare("SELECT puntos_totales, nivel FROM usuarios WHERE id = ?");
    $stmt->execute([$_SESSION['usuario_id']]);
    $userStats = $stmt->fetch();
    if ($userStats) {
        $puntos = $userStats['puntos_totales'];
        $nivel = $userStats['nivel'];
    }
}

// 3. PALABRA DIARIA (Dinámica)
$hoy = date("Y-m-d");
// Si cambia el día o no tenemos palabra, buscamos una nueva
if (!isset($_SESSION['fecha_palabra']) || $_SESSION['fecha_palabra'] !== $hoy || !isset($_SESSION['palabra_objetivo'])) {
    
    $stmtCount = $pdo->query("SELECT COUNT(*) FROM terminos_anatomia");
    $totalPalabras = $stmtCount->fetchColumn();

    if ($totalPalabras > 0) {
        $diaDelAno = date("z");
        $indice = $diaDelAno % $totalPalabras;
        
        $stmt = $pdo->prepare("SELECT palabra, pista FROM terminos_anatomia LIMIT 1 OFFSET :offset");
        $stmt->bindParam(':offset', $indice, PDO::PARAM_INT);
        $stmt->execute();
        $wordData = $stmt->fetch();

        $_SESSION['palabra_objetivo'] = strtoupper($wordData['palabra']);
        $_SESSION['pista_objetivo'] = $wordData['pista'];
        $_SESSION['fecha_palabra'] = $hoy;
    } else {
        // Fallback
        $_SESSION['palabra_objetivo'] = "FEMUR";
        $_SESSION['pista_objetivo'] = "El hueso más largo.";
        $_SESSION['fecha_palabra'] = $hoy;
    }
}

// 4. CALCULAMOS LA LONGITUD PARA DIBUJAR EL TABLERO
$palabraObjetivo = $_SESSION['palabra_objetivo'];
$longitudPalabra = strlen($palabraObjetivo);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AnatoWordle - TFG</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body style="display: flex; flex-direction: column; align-items: center; background-color: #121213; color: white; margin: 0; min-height: 100vh; font-family: 'Segoe UI', sans-serif;">

<div id="game-config" data-length="<?php echo $longitudPalabra; ?>" style="display:none;"></div>

<header style="width: 100%; display: flex; flex-direction: column; align-items: center; border-bottom: 1px solid #3a3a3c; padding: 10px 0; margin-bottom: 20px; position: relative;">
    
    <div style="position: absolute; right: 15px; top: 15px; display: flex; gap: 15px; align-items: center;">
        <a href="leaderboard.php" style="text-decoration: none; font-size: 1.2rem;" title="Ver Ranking">🏆</a>
        
        <?php if(isset($_SESSION['usuario_id'])): ?>
            <a href="profile.php" style="text-decoration: none; font-size: 1.2rem;" title="Mi Perfil">👤</a>
        <?php endif; ?>

        <a href="index.php?logout=true" style="color: #818384; text-decoration: none; font-size: 0.8rem; border: 1px solid #3a3a3c; padding: 5px 10px; border-radius: 5px;">Salir</a>
    </div>

    <h1 style="color: #f02109; text-transform: uppercase; font-weight: 800; letter-spacing: 3px; margin: 0 0 10px 0;">AnatoWordle</h1>
    
    <div class="stats-container" style="display: flex; justify-content: space-around; width: 90%; max-width: 500px; background-color: #1e1e1f; padding: 10px; border-radius: 10px; border: 1px solid #3a3a3c;">
        <div class="stat-box" style="display: flex; flex-direction: column; align-items: center;">
            <span class="label" style="font-size: 0.7rem; color: #818384;">NIVEL</span>
            <span class="value" id="user-level" style="font-weight: bold;"><?php echo $nivel; ?></span>
        </div>
        <div class="stat-box" style="display: flex; flex-direction: column; align-items: center;">
            <span class="label" style="font-size: 0.7rem; color: #818384;">PUNTOS</span>
            <span class="value" id="user-points" style="font-weight: bold;"><?php echo $puntos; ?></span>
        </div>
        <div class="stat-box" style="display: flex; flex-direction: column; align-items: center;">
            <span class="label" style="font-size: 0.7rem; color: #818384;">MODO</span>
            <span class="value" style="color: #f02109; font-weight: bold;">
                <?php echo ($_SESSION['is_guest'] === true) ? 'Invitado' : 'Estudiante'; ?>
            </span>
        </div>
    </div>

    <div id="hint-section" style="text-align: center; margin-top: 10px;">
        <button id="btn-pista" class="btn-pista" style="background: none; border: 1px solid #3a3a3c; color: #818384; padding: 5px 15px; border-radius: 20px; cursor: pointer; font-size: 0.8rem;">
            ¿Necesitas una pista?
        </button>
        <p id="pista-texto" style="display: none; color: #c9b458; margin-top: 10px; font-style: italic; font-size: 0.9rem;">
            <?php echo $_SESSION['pista_objetivo'] ?? 'Pista no disponible'; ?>
        </p>
    </div>
</header>

<main id="game-container" style="display: flex; flex-direction: column; align-items: center; width: 100%; flex-grow: 1;">
    
    <div id="board" style="display: grid; grid-template-rows: repeat(6, 1fr); gap: 5px; margin-bottom: 20px;">
        <?php
        for ($i = 0; $i < 6; $i++) {
            // AQUI ESTA LA CLAVE: repeat($longitudPalabra, 1fr)
            echo '<div class="row" style="display: grid; grid-template-columns: repeat(' . $longitudPalabra . ', 1fr); gap: 5px;">';
            for ($j = 0; $j < $longitudPalabra; $j++) {
                // Quitamos el ancho fijo (50px) para que se adapte. Usamos aspect-ratio para que sean cuadrados
                // y un min-width/max-width para que no se vean gigantes o enanas.
                echo '<div class="cell" style="
                    aspect-ratio: 1/1; 
                    width: clamp(30px, 8vw, 60px);
                    border: 2px solid #3a3a3c; 
                    display: flex; align-items: center; justify-content: center; 
                    font-weight: bold; font-size: 1.5rem; text-transform: uppercase;"></div>';
            }
            echo '</div>';
        }
        ?>
    </div>

    <div id="keyboard-container">
        <div class="keyboard-row">
            <button class="key" data-key="Q">Q</button><button class="key" data-key="W">W</button><button class="key" data-key="E">E</button><button class="key" data-key="R">R</button><button class="key" data-key="T">T</button><button class="key" data-key="Y">Y</button><button class="key" data-key="U">U</button><button class="key" data-key="I">I</button><button class="key" data-key="O">O</button><button class="key" data-key="P">P</button>
        </div>
        <div class="keyboard-row">
            <button class="key" data-key="A">A</button><button class="key" data-key="S">S</button><button class="key" data-key="D">D</button><button class="key" data-key="F">F</button><button class="key" data-key="G">G</button><button class="key" data-key="H">H</button><button class="key" data-key="J">J</button><button class="key" data-key="K">K</button><button class="key" data-key="L">L</button><button class="key" data-key="Ñ">Ñ</button>
        </div>
        <div class="keyboard-row">
            <button class="key wide" data-key="Enter">ENTER</button>
            <button class="key" data-key="Z">Z</button><button class="key" data-key="X">X</button><button class="key" data-key="C">C</button><button class="key" data-key="V">V</button><button class="key" data-key="B">B</button><button class="key" data-key="N">N</button><button class="key" data-key="M">M</button>
            <button class="key wide" data-key="Backspace">⌫</button>
        </div>
    </div>
</main>

<div id="modal-educativo" class="modal">
    <div class="modal-content">
        <h2 id="modal-titulo" style="color: #f02109;"></h2>
        <img id="modal-imagen" src="" alt="Imagen anatómica" style="max-width: 100%; height: auto; border-radius: 8px; margin: 15px 0;">
        <p id="modal-descripcion" style="font-size: 0.9rem; line-height: 1.4; color: white;"></p>
        <p style="color: #c9b458; margin-top: 15px;">¡Vuelve mañana!</p>
        <button id="btn-cerrar-modal" class="btn-siguiente">CERRAR</button>
    </div>
</div>

<script src="assets/js/game.js"></script>

</body>
</html>