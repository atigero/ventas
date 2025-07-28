<?php
include 'conexion.php';

$fecha = $_POST['fecha'];
$remito = $_POST['remito'];
$cliente = $_POST['cliente'];
$precio = $_POST['precio'];
$pago = $_POST['pago'];
$estado = $_POST['estado'];
$situacion = $_POST['situacion'];
$observaciones = isset($_POST['observaciones']) ? $_POST['observaciones'] : '';

$sql = "INSERT INTO ventas (fecha, remito, cliente, precio, pago, estado, situacion, observaciones)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)";

$stmt = $pdo->prepare($sql);
$stmt->execute([$fecha, $remito, $cliente, $precio, $pago, $estado, $situacion, $observaciones]);

echo "<script>alert('Venta registrada correctamente'); window.location.href='nueva_venta.php';</script>";
?>
