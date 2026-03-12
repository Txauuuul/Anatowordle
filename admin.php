<?php
include 'includes/db.php';

// SEGURIDAD
if (!isset($_SESSION['usuario_id']) && !isset($_SESSION['is_guest'])) {
    header("Location: auth/login.php");
    exit();
}
if (isset($_SESSION['is_guest']) && $_SESSION['is_guest'] == true) {
    die("<div style='color:white; background:#121213; height:100vh; display:flex; flex-direction:column; align-items:center; justify-content:center; font-family:sans-serif;'><h1>🚫 Acceso Denegado</h1><p>Los invitados no pueden acceder al Panel de Profesor.</p><a href='auth/login.php' style='color:#f02109;'>Inicia sesión con tu cuenta</a></div>");
}

$mensaje = "";

// AÑADIR PALABRA
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['accion']) && $_POST['accion'] == 'crear') {
    $palabra = strtoupper(trim($_POST['palabra']));
    $pista = trim($_POST['pista']);
    $descripcion = trim($_POST['descripcion']);
    $imagen = trim($_POST['imagen']);

    $longitud = strlen($palabra);
    
    if ($longitud >= 3 && $longitud <= 12) {
        try {
            $stmt = $pdo->prepare("INSERT INTO terminos_anatomia (palabra, pista, descripcion, imagen_url) VALUES (?, ?, ?, ?)");
            $stmt->execute([$palabra, $pista, $descripcion, $imagen]);
            $mensaje = "<p style='color: #4cd137; font-weight:bold; background: rgba(76, 209, 55, 0.1); padding: 10px; border-radius: 5px; text-align:center;'>✅ '$palabra' añadida correctamente.</p>";
        } catch (PDOException $e) {
            $mensaje = "<p style='color: #f02109; text-align:center;'>❌ Error: Esa palabra ya existe.</p>";
        }
    } else {
        $mensaje = "<p style='color: #e1b12c; text-align:center;'>⚠️ La palabra debe tener entre 3 y 12 letras.</p>";
    }
}

// BORRAR PALABRA (Extra útil para limpiar pruebas)
if (isset($_GET['borrar'])) {
    $idBorrar = $_GET['borrar'];
    $stmt = $pdo->prepare("DELETE FROM terminos_anatomia WHERE id = ?");
    $stmt->execute([$idBorrar]);
    header("Location: admin.php"); // Recargar para limpiar URL
    exit();
}

// OBTENER LISTA DE PALABRAS
$lista = $pdo->query("SELECT * FROM terminos_anatomia ORDER BY id DESC")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel Profesor - AnatoWordle</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body style="background-color: #121213; color: white; font-family: 'Segoe UI', sans-serif; display: flex; flex-direction: column; align-items: center; padding-bottom: 50px;">

    <header style="width: 100%; border-bottom: 1px solid #3a3a3c; padding: 20px 0; text-align: center; margin-bottom: 30px; position: relative;">
        <a href="index.php" style="position: absolute; left: 20px; top: 20px; color: #818384; text-decoration: none; border: 1px solid #3a3a3c; padding: 5px 10px; border-radius: 5px;">&larr; Volver</a>
        <h1 style="color: #f02109; margin: 0; text-transform: uppercase;">Panel Profesor</h1>
    </header>

    <main style="width: 90%; max-width: 800px; display: flex; flex-direction: column; gap: 40px;">
        
        <div style="background-color: #1e1e1f; padding: 30px; border-radius: 10px; border: 1px solid #3a3a3c;">
            <h2 style="margin-top: 0; text-align: center; color: white;">Añadir Nuevo Término</h2>
            <?php echo $mensaje; ?>
            <form method="POST" action="admin.php" style="display: flex; flex-direction: column; gap: 15px;">
                <input type="hidden" name="accion" value="crear">
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                    <div>
                        <label style="display: block; color: #818384; font-size: 0.8rem;">Palabra</label>
                        <input type="text" name="palabra" maxlength="12" required placeholder="Ej: CRANEO" style="width: 100%; padding: 10px; background: #121213; border: 1px solid #3a3a3c; color: white; font-weight: bold; text-transform: uppercase;">
                    </div>
                    <div>
                        <label style="display: block; color: #818384; font-size: 0.8rem;">Pista</label>
                        <input type="text" name="pista" required placeholder="Ej: Hueso de la cabeza" style="width: 100%; padding: 10px; background: #121213; border: 1px solid #3a3a3c; color: white;">
                    </div>
                </div>
                <div>
                    <label style="display: block; color: #818384; font-size: 0.8rem;">Descripción</label>
                    <input type="text" name="descripcion" required placeholder="Explicación..." style="width: 100%; padding: 10px; background: #121213; border: 1px solid #3a3a3c; color: white;">
                </div>
                <div>
                    <label style="display: block; color: #818384; font-size: 0.8rem;">URL Imagen</label>
                    <input type="url" name="imagen" placeholder="https://..." style="width: 100%; padding: 10px; background: #121213; border: 1px solid #3a3a3c; color: white;">
                </div>
                <button type="submit" style="background-color: #f02109; color: white; border: none; padding: 12px; font-weight: bold; cursor: pointer; border-radius: 5px; text-transform: uppercase;">Guardar Término</button>
            </form>
        </div>

        <div style="background-color: #1e1e1f; padding: 20px; border-radius: 10px; border: 1px solid #3a3a3c;">
            <h2 style="margin-top: 0; text-align: center; color: #818384; font-size: 1.2rem;">Base de Datos Actual (<?php echo count($lista); ?> términos)</h2>
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse; font-size: 0.9rem;">
                    <thead>
                        <tr style="border-bottom: 2px solid #3a3a3c; text-align: left;">
                            <th style="padding: 10px; color: #f02109;">PALABRA</th>
                            <th style="padding: 10px; color: #818384;">PISTA</th>
                            <th style="padding: 10px; text-align: right;">ACCIÓN</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($lista as $term): ?>
                        <tr style="border-bottom: 1px solid #2a2a2c;">
                            <td style="padding: 10px; font-weight: bold;"><?php echo $term['palabra']; ?></td>
                            <td style="padding: 10px; color: #ccc;"><?php echo $term['pista']; ?></td>
                            <td style="padding: 10px; text-align: right;">
                                <a href="admin.php?borrar=<?php echo $term['id']; ?>" onclick="return confirm('¿Seguro que quieres borrar <?php echo $term['palabra']; ?>?')" style="color: #f02109; text-decoration: none; font-weight: bold;">🗑️ Borrar</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </main>
</body>
</html>