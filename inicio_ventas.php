<?php
session_start();
if (!isset($_SESSION["usuario"])) {
    header("Location: login.php"); // Si no está autenticado, lo manda al login
    exit();
}
?>


<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Inicio - Sistema de Ventas</title>
    <link rel="stylesheet" href="css/estilos.css">
</head>

<body>

<div class="header">Sistema de Ventas</div>

<nav class="navbar">
    <a href="dashboard.php">Inicio</a>
    <a href="nueva_venta.php">Nueva Venta</a>
    <a href="editar_venta.php">Editar Venta</a>
    <a href="consultar_venta.php">Consultar Ventas</a>
</nav>

<div class="container">
    <h2>Selecciona una categoría</h2>

    <div class="grid-container">
        <div class="card ventas-finalizadas">
            <h3>📌 Ventas Finalizadas</h3>
            <p>Ver todas las ventas que han sido entregadas y completadas.</p>
            <a href="consultar_venta.php?tipo=finalizadas" class="btn">Ver Ventas Finalizadas</a>
        </div>

        <div class="card ventas-activas">
            <h3>🚀 Ventas Activas</h3>
            <p>Consultar ventas en proceso que aún no fueron finalizadas.</p>
            <a href="consultar_venta.php?tipo=activas" class="btn">Ver Ventas Activas</a>
        </div>
    </div>
</div>

</body>
</html>
