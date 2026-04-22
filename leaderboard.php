<?php
include 'includes/db.php';

if (!isset($_SESSION['usuario_id']) && !isset($_SESSION['is_guest'])) {
    header("Location: auth/login.php");
    exit();
}

$stmt = $pdo->query("SELECT username, nivel, puntos_totales FROM usuarios ORDER BY puntos_totales DESC LIMIT 10");
$topPlayers = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ranking - AnatoWordle</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body style="background-color: #121213; color: white; font-family: 'Segoe UI', sans-serif; display: flex; flex-direction: column; align-items: center;">

    <header style="width: 100%; border-bottom: 1px solid #3a3a3c; padding: 20px 0; margin-bottom: 30px; position: relative; display: flex; justify-content: center; align-items: center;">
        
        <a href="index.php" style="position: absolute; left: 20px; color: #818384; text-decoration: none; font-size: 0.9rem; border: 1px solid #3a3a3c; padding: 8px 15px; border-radius: 5px; transition: all 0.2s ease;">
            &larr; Volver
        </a>

        <h1 style="color: #f02109; margin: 0; text-transform: uppercase; letter-spacing: 2px; font-size: 1.8rem;">Salón de la Fama</h1>
    </header>

    <main style="width: 90%; max-width: 600px;">
        <div class="leaderboard-container">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="border-bottom: 2px solid #f02109;">
                        <th style="padding: 15px; text-align: left; color: #f02109;">POS</th>
                        <th style="padding: 15px; text-align: left; color: #818384;">ESTUDIANTE</th>
                        <th style="padding: 15px; text-align: center; color: #818384;">NIVEL</th>
                        <th style="padding: 15px; text-align: right; color: #f02109;">PUNTOS</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $pos = 1;
                    foreach ($topPlayers as $player): 
                        $esYo = (isset($_SESSION['username']) && $_SESSION['username'] === $player['username']);
                        $claseFila = $esYo ? 'background-color: #1e1e1f; border: 1px solid #f02109;' : 'border-bottom: 1px solid #3a3a3c;';
                        
                        $medalla = '';
                        if ($pos === 1) $medalla = '🥇';
                        elseif ($pos === 2) $medalla = '🥈';
                        elseif ($pos === 3) $medalla = '🥉';
                    ?>
                    <tr style="<?php echo $claseFila; ?>">
                        <td style="padding: 15px; font-weight: bold;"><?php echo $pos . ' ' . $medalla; ?></td>
                        <td style="padding: 15px;"><?php echo htmlspecialchars($player['username']); ?></td>
                        <td style="padding: 15px; text-align: center; font-weight: bold;"><?php echo $player['nivel']; ?></td>
                        <td style="padding: 15px; text-align: right; font-weight: bold; color: #f02109;"><?php echo $player['puntos_totales']; ?></td>
                    </tr>
                    <?php $pos++; endforeach; ?>
                </tbody>
            </table>
        </div>

        <?php if (count($topPlayers) === 0): ?>
            <p style="text-align: center; color: #818384; margin-top: 30px;">Aún no hay estudiantes en el ranking. ¡Sé el primero!</p>
        <?php endif; ?>
    </main>

</body>
</html>
