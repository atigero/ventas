<?php
session_start();
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

if (!isset($_SESSION["usuario"])) {
    header("Location: login.php");
    exit();
}

include('config.php');

// Función días en español
function diaSemanaEsp($fecha) {
  $dias = ['Dom','Lun','Mar','Mié','Jue','Vie','Sáb'];
  return $dias[date('w', strtotime($fecha))];
}

// Ventas por día (últimos 7)
$ventasPorDia = $pdo->query("
  SELECT DATE(fecha) as fecha, SUM(precio) as total, COUNT(*) as cantidad
  FROM ventas
  WHERE fecha >= CURDATE() - INTERVAL 6 DAY
  GROUP BY DATE(fecha)
")->fetchAll();

// Métodos de pago (hoy)
$metodos = $pdo->query("
  SELECT pago, SUM(precio) as total
  FROM ventas
  WHERE fecha = CURDATE()
  GROUP BY pago
")->fetchAll();

$totalHoy = $pdo->query("SELECT SUM(precio) FROM ventas WHERE fecha = CURDATE()")->fetchColumn();
$pendientes = $pdo->query("SELECT COUNT(*) FROM ventas WHERE estado != 'Finalizada'")->fetchColumn();

// Repartos programados para hoy
$repartosHoy = $pdo->query("
  SELECT id, cliente, fecha_reparto
  FROM ventas
  WHERE DATE(fecha_reparto) = CURDATE()
")->fetchAll();
?>

<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <title>Dashboard</title>
  <link rel="stylesheet" href="css/estilos.css">
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>

<div class="header">Sistema de Ventas</div>

    <nav class="navbar">
  <a href="dashboard.php">Inicio</a>
  <a href="nueva_venta.php">Nueva Venta</a>
  <a href="editar_venta.php">Editar Venta</a>
  <a href="inicio_ventas.php">Consultar Ventas</a>
  <a href="cierre_caja.php">Caja</a>
</nav>
<div class="container">

  

  <!-- Paneles de métricas y repartos -->
  <div class="grid-container">
    <div class="card-dashboard">
      <h4>🚚 Repartos del Día</h4>
      <?php if ($repartosHoy): ?>
        <ul>
          <?php foreach ($repartosHoy as $r): ?>
            <li><strong>#<?= $r['id'] ?></strong> <?= htmlspecialchars($r['cliente']) ?> (<?= date('H:i', strtotime($r['fecha_reparto'])) ?>)</li>
          <?php endforeach; ?>
        </ul>
      <?php else: ?>
        <p>No hay entregas asignadas.</p>
      <?php endif; ?>
    </div>

    <div class="card-dashboard">
      <h4>📦 Cantidad de Ventas</h4>
      <p><?= array_sum(array_column($ventasPorDia, 'cantidad')) ?> remitos</p>
    </div>

    <div class="card-dashboard">
      <h4>💵 Total Vendido Hoy</h4>
      <p>$<?= number_format($totalHoy ?? 0, 2, ',', '.') ?></p>
    </div>

    <div class="card-dashboard">
      <h4>📍 Remitos Pendientes</h4>
      <p><?= $pendientes ?> en curso</p>
    </div>
  </div>

  <!-- Gráficos -->
  <div class="grid-container">
    <div class="card">
      <canvas id="graficoSemana"></canvas>
    </div>
    <div class="card">
      <canvas id="graficoPago"></canvas>
    </div>
  </div>

</div>

<script>
const ctx1 = document.getElementById('graficoSemana').getContext('2d');
const graficoSemana = new Chart(ctx1, {
  type: 'line',
  data: {
    labels: [<?= implode(',', array_map(function($v) {
      return "'" . diaSemanaEsp($v['fecha']) . "'";
    }, $ventasPorDia)) ?>],
    datasets: [
      {
        label: 'Monto de Ventas ($)',
        data: [<?= implode(',', array_map(function($v) { return $v['total']; }, $ventasPorDia)) ?>],
        borderColor: '#007bff',
        backgroundColor: 'rgba(0,123,255,0.1)',
        fill: true,
        tension: 0.3
      },
      {
        label: 'Cantidad de Ventas',
        data: [<?= implode(',', array_map(function($v) { return $v['cantidad']; }, $ventasPorDia)) ?>],
        borderColor: '#28a745',
        backgroundColor: 'rgba(40,167,69,0.1)',
        fill: true,
        tension: 0.3
      }
    ]
  },
  options: {
    responsive: true,
    plugins: {
      legend: { position: 'top' }
    },
    scales: {
      y: { beginAtZero: true }
    }
  }
});

const ctx2 = document.getElementById('graficoPago').getContext('2d');
const graficoPago = new Chart(ctx2, {
  type: 'doughnut',
  data: {
    labels: [<?= implode(',', array_map(function($m) { return "'".$m['pago']."'"; }, $metodos)) ?>],
    datasets: [{
      data: [<?= implode(',', array_map(function($m) { return $m['total']; }, $metodos)) ?>],
      backgroundColor: ['#007bff', '#28a745', '#ffc107', '#17a2b8', '#6f42c1']
    }]
  },
  options: {
    responsive: true,
    maintainAspectRatio: false,
    plugins: {
      legend: { position: 'bottom' }
    }
  }
});
</script>

</body>
</html>


