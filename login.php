<?php
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario = $_POST["usuario"];
    $clave = $_POST["clave"];

    // Credenciales temporales (más adelante podrían guardarse en la BD)
    $usuario_correcto = "admin";
    $clave_correcta = "Elherrero!2018";

    if ($usuario === $usuario_correcto && $clave === $clave_correcta) {
        $_SESSION["usuario"] = $usuario; // Guardar sesión
        header("Location: inicio_ventas.php"); // Redirigir al sistema
        exit();
    } else {
        $error = "❌ Usuario o contraseña incorrectos";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login - Sistema de Ventas</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>
<body>

<div class="container">
    <h2>🔐 Iniciar sesión</h2>
    <form method="POST">
        <label for="usuario">Usuario:</label>
        <input type="text" name="usuario" required>

        <label for="clave">Contraseña:</label>
        <input type="password" name="clave" required>

        <button type="submit">Ingresar</button>
    </form>

    <?php if (isset($error)): ?>
        <p style="color: red;"><?= $error ?></p>
    <?php endif; ?>
</div>

</body>
</html>
