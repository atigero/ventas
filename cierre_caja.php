<?php
session_start();
if (!isset($_SESSION["usuario"])) {
    header("Location: login.php");
    exit();
}

require_once("config.php");

$fechaSeleccionada = isset($_GET['fecha']) ? $_GET['fecha'] : date('Y-m-d');
$sql = "SELECT pago, SUM(precio) AS total FROM ventas WHERE fecha = :fechaSeleccionada GROUP BY pago";
$stmt = $pdo->prepare($sql);
$stmt->execute(['fechaSeleccionada' => $fechaSeleccionada]);
$resultados = $stmt->fetchAll();
$totalGeneral = 0;
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Cierre de Caja</title>
  <link rel="stylesheet" href="css/estilos.css" />
</head>
<body>
  <div class="header">Sistema de Ventas</div>

  <nav class="navbar">
    <a href="dashboard.php">Inicio</a>
    <a href="nueva_venta.php">Nueva Venta</a>
    <a href="editar_venta.php">Editar Venta</a>
    <a href="inicio_ventas.php">Consultar Ventas</a>
    <a href="cierre_caja.php" class="activo">Cierre de Caja</a>
  </nav>

  <div class="container">
    <h2>💰 Cierre de Caja Diario</h2>

    <form method="GET">
      <label for="fecha">Seleccionar fecha:</label>
      <input type="date" name="fecha" value="<?= htmlspecialchars($fechaSeleccionada) ?>" required>
      <button type="submit">Consultar</button>
    </form>

    <table class="ventas-table" style="margin-top: 30px;">
      <thead>
        <tr>
          <th>Método de Pago</th>
          <th>Total Vendido</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($resultados as $fila): ?>
          <tr>
            <td><?= htmlspecialchars($fila['pago']) ?></td>
            <td>$<?= number_format($fila['total'], 2, ',', '.') ?></td>
          </tr>
          <?php $totalGeneral += $fila['total']; ?>
        <?php endforeach; ?>
        <tr style="font-weight: bold;">
          <td>Total General</td>
          <td>$<?= number_format($totalGeneral, 2, ',', '.') ?></td>
        </tr>
      </tbody>
    </table>
  </div>
</body>
</html>
