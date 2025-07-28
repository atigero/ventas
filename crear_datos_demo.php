<?php
include('config.php'); // Asegurate que tenga la conexión $pdo

date_default_timezone_set('America/Argentina/Cordoba');

// Estados definidos por el sistema
$estados = ['Entregado', 'Trabajo', 'Reparto', 'Retira cliente'];
$situaciones = ['Pedido Completo', 'Pedido Incompleto'];
$pagos = ['Efectivo', 'Transferencia', 'Tarjeta', 'Cheque', 'Débito'];
$clientes = ['Cliente Uno', 'Cliente Dos', 'Cliente Tres', 'Cliente Cuatro', 'Cliente Cinco'];

$pdo->exec("DELETE FROM ventas");
$pdo->exec("ALTER TABLE ventas AUTO_INCREMENT = 1");

for ($i = 0; $i < 100; $i++) {
    $diaOffset = 9 - floor($i / 10);
    $fechaVenta = date('Y-m-d', strtotime("-$diaOffset days"));
    $hora = rand(9, 19) . ':' . str_pad(rand(0, 59), 2, '0', STR_PAD_LEFT);
    $fechaCompleta = "$fechaVenta $hora";

    $remito = 'RMT' . str_pad($i + 1, 4, '0', STR_PAD_LEFT);
    $cliente = $clientes[array_rand($clientes)];
    $precio = rand(5000, 25000);
    $estado = $estados[array_rand($estados)];
    $situacion = $situaciones[array_rand($situaciones)];
    $pago = $pagos[array_rand($pagos)];
    $observaciones = (rand(0, 1) ?
        'Entrega coordinada sin demoras. Cliente solicita copia electrónica del remito y devolución de materiales para reciclado.' :
        'Verificar dirección antes del reparto. Confirmar disponibilidad de espacio para descarga.');

    $fechaReparto = date('Y-m-d', strtotime($fechaVenta . ' +1 day')) . ' ' . rand(8, 17) . ':' . str_pad(rand(0, 59), 2, '0', STR_PAD_LEFT);
    $finalizada = ($estado === 'Entregado') ? 1 : 0;
    $ahora = date('Y-m-d H:i:s');

    $stmt = $pdo->prepare("
        INSERT INTO ventas (
          fecha, remito, cliente, precio,
          estado, situacion, fecha_reparto,
          pago, observaciones, finalizada,
          creado_en, ultima_modificacion
        )
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

    $stmt->execute([
        $fechaCompleta, $remito, $cliente, $precio,
        $estado, $situacion, $fechaReparto,
        $pago, $observaciones, $finalizada,
        $ahora, $ahora
    ]);
}

echo "<h2 style='font-family: sans-serif; color: #28a745;'>✅ Se generaron 100 registros de demostración con éxito.</h2>";
echo "<p style='font-family: sans-serif;'>Ahora podés ir al <a href='dashboard.php'>dashboard</a> para visualizar las ventas simuladas con estados reales.</p>";
