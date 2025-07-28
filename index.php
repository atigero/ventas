{\rtf1\ansi\ansicpg1252\cocoartf1561\cocoasubrtf610
{\fonttbl\f0\fswiss\fcharset0 Helvetica;\f1\fnil\fcharset0 AppleColorEmoji;}
{\colortbl;\red255\green255\blue255;}
{\*\expandedcolortbl;;}
\paperw11900\paperh16840\margl1440\margr1440\vieww38200\viewh18900\viewkind0
\pard\tx566\tx1133\tx1700\tx2267\tx2834\tx3401\tx3968\tx4535\tx5102\tx5669\tx6236\tx6803\pardirnatural\partightenfactor0

\f0\fs24 \cf0 ini_set('display_errors', 1);\
ini_set('display_startup_errors', 1);\
error_reporting(E_ALL);\
\
\
<?php\
include('config.php');\
\
// Ventas por d\'eda (\'faltimos 7)\
$ventasPorDia = $pdo->query("\
  SELECT DATE(fecha) as fecha, SUM(precio) as total\
  FROM ventas\
  WHERE fecha >= CURDATE() - INTERVAL 6 DAY\
  GROUP BY DATE(fecha)\
")->fetchAll();\
\
// Totales por m\'e9todo de pago\
$metodos = $pdo->query("\
  SELECT pago, SUM(precio) as total\
  FROM ventas\
  WHERE fecha = CURDATE()\
  GROUP BY pago\
")->fetchAll();\
\
// Ventas totales hoy\
$totalHoy = $pdo->query("\
  SELECT SUM(precio) FROM ventas WHERE fecha = CURDATE()\
")->fetchColumn();\
\
// Remitos pendientes\
$pendientes = $pdo->query("\
  SELECT COUNT(*) FROM ventas WHERE estado != 'Finalizada'\
")->fetchColumn();\
?>\
\
<!DOCTYPE html>\
<html lang="es">\
<head>\
  <meta charset="UTF-8">\
  <title>Dashboard</title>\
  <link rel="stylesheet" href="estilos.css">\
  <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>\
</head>\
<body>\
\
<div class="header">
\f1 \uc0\u55357 \u56522 
\f0  Panel General - Sistema de Ventas</div>\
\
<div class="container">\
\
  <div class="grid-container">\
    <div class="card ventas-finalizadas">\
      
\f1 \uc0\u55357 \u56501 
\f0  Ventas Hoy\
      <h2>$<?= number_format($totalHoy ?? 0, 2, ',', '.') ?></h2>\
    </div>\
    <div class="card ventas-activas">\
      
\f1 \uc0\u55357 \u56550 
\f0  Remitos Pendientes\
      <h2><?= $pendientes ?></h2>\
    </div>\
  </div>\
\
  <div class="grid-container">\
    <div class="card">\
      <canvas id="graficoSemana"></canvas>\
    </div>\
    <div class="card">\
      <canvas id="graficoPago"></canvas>\
    </div>\
  </div>\
\
  <div class="grid-container">\
    <div class="card ventas-finalizadas">\
      <a href="nueva_venta.php" class="btn">
\f1 \uc0\u10133 
\f0  Nueva Venta</a>\
    </div>\
    <div class="card ventas-activas">\
      <a href="consultar_venta.php" class="btn">
\f1 \uc0\u55357 \u56589 
\f0  Consultar Venta</a>\
    </div>\
    <div class="card ventas-finalizadas">\
      <a href="editar_venta.php" class="btn">
\f1 \uc0\u9999 \u65039 
\f0  Editar Venta</a>\
    </div>\
    <div class="card ventas-activas">\
      <a href="actualizar_venta.php" class="btn">
\f1 \uc0\u55357 \u56580 
\f0  Actualizar</a>\
    </div>\
    <div class="card ventas-finalizadas">\
      <a href="cierre_caja.php" class="btn">\uc0\u55358 \u56830  Cierre de Caja</a>\
    </div>\
  </div>\
\
</div>\
\
<script>\
const ctx1 = document.getElementById('graficoSemana').getContext('2d');\
const graficoSemana = new Chart(ctx1, \{\
  type: 'line',\
  data: \{\
    labels: [<?= implode(',', array_map(fn($v) => "'".date('D', strtotime($v['fecha']))."'", $ventasPorDia)) ?>],\
    datasets: [\{\
      label: 'Ventas \'faltimos 7 d\'edas',\
      data: [<?= implode(',', array_map(fn($v) => $v['total'], $ventasPorDia)) ?>],\
      borderColor: '#007bff',\
      backgroundColor: 'rgba(0,123,255,0.1)',\
      fill: true\
    \}]\
  \},\
  options: \{ responsive: true \}\
\});\
\
const ctx2 = document.getElementById('graficoPago').getContext('2d');\
const graficoPago = new Chart(ctx2, \{\
  type: 'doughnut',\
  data: \{\
    labels: [<?= implode(',', array_map(fn($m) => "'".$m['pago']."'", $metodos)) ?>],\
    datasets: [\{\
      label: 'M\'e9todos de Pago Hoy',\
      data: [<?= implode(',', array_map(fn($m) => $m['total'], $metodos)) ?>],\
      backgroundColor: ['#28a745','#ffc107','#17a2b8','#dc3545','#6f42c1','#fd7e14','#20c997']\
    \}]\
  \},\
  options: \{ responsive: true \}\
\});\
</script>\
\
</body>\
</html>}