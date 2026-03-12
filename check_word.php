<?php
ob_start();
error_reporting(0);
include 'includes/db.php';
header('Content-Type: application/json');

$data = json_decode(file_get_contents('php://input'), true);
$userGuess = strtoupper($data['guess'] ?? '');
$targetWord = $_SESSION['palabra_objetivo'] ?? '';
$result = [];

// Calculamos longitud dinámica
$len = strlen($targetWord);

// Validación
for ($i = 0; $i < $len; $i++) {
    $char = $userGuess[$i] ?? '';
    $targetChar = $targetWord[$i] ?? '';
    
    if ($char === $targetChar) {
        $result[] = "correct";
    } elseif (strpos($targetWord, $char) !== false) {
        $result[] = "present";
    } else {
        $result[] = "absent";
    }
}

$isWin = ($userGuess === $targetWord);
$isGameOver = $isWin || ($data['isLastAttempt'] ?? false);

$infoEducativa = null;
$newPoints = null;
$newLevel = null;
$yaJugadoHoy = false; // Variable para avisar al frontend (opcional)

if ($isGameOver) {
    $stmt = $pdo->prepare("SELECT * FROM terminos_anatomia WHERE palabra = ?");
    $stmt->execute([$targetWord]);
    $infoEducativa = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($isWin && isset($_SESSION['usuario_id']) && $_SESSION['usuario_id'] !== null) {
        $userId = $_SESSION['usuario_id'];
        $hoy = date("Y-m-d");

        // 1. EL SEGURO ANTI-FARMEO: Comprobamos si ya ganó hoy
        $stmtCheckDia = $pdo->prepare("SELECT id FROM partidas_diarias WHERE usuario_id = ? AND fecha = ?");
        $stmtCheckDia->execute([$userId, $hoy]);
        $yaGano = $stmtCheckDia->fetch();

        if (!$yaGano) {
            // SI NO HA GANADO HOY:
            
            // A. Registramos que ha ganado hoy
            $pdo->prepare("INSERT INTO partidas_diarias (usuario_id, fecha) VALUES (?, ?)")->execute([$userId, $hoy]);

            // B. Sumamos los puntos
            $sql = "UPDATE usuarios SET 
                    puntos_totales = COALESCE(puntos_totales, 0) + 10,
                    nivel = FLOOR((COALESCE(puntos_totales, 0) + 10) / 50) + 1 
                    WHERE id = ?";
            $pdo->prepare($sql)->execute([$userId]);

            // C. Devolvemos los puntos nuevos
            $stmtCheck = $pdo->prepare("SELECT puntos_totales, nivel FROM usuarios WHERE id = ?");
            $stmtCheck->execute([$userId]);
            $updatedStats = $stmtCheck->fetch(PDO::FETCH_ASSOC);
            
            $newPoints = $updatedStats['puntos_totales'];
            $newLevel = $updatedStats['nivel'];
        } else {
            // Si ya ganó, marcamos esto (aunque newPoints sea null, el JS sabrá que no suma)
            $yaJugadoHoy = true;
        }
    }
}

ob_clean();
echo json_encode([
    "status" => "success",
    "result" => $result,
    "win" => $isWin,
    "gameOver" => $isGameOver,
    "educativo" => $infoEducativa,
    "guest" => ($_SESSION['is_guest'] ?? false),
    "newPoints" => $newPoints,
    "newLevel" => $newLevel,
    "yaJugado" => $yaJugadoHoy
]);
exit;