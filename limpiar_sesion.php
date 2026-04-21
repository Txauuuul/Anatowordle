<?php
include 'includes/db.php';

// Limpiar sesión actual
session_destroy();

echo "<h2 style='color: green; text-align: center;'>✅ Sesión eliminada</h2>";
echo "<p style='text-align: center;'>Ahora cuando inicies sesión nuevamente, verás CRANEO como palabra del día.</p>";
echo "<p style='text-align: center;'><a href='auth/login.php' style='color: #f02109; text-decoration: none; font-weight: bold;'>← Volver a Login</a></p>";
?>
