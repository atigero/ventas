<?php
require_once("config.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Capturar los datos enviados desde el formulario
    $fecha = $_POST["fecha"];
    $remito = $_POST["remito"];
    $cliente = $_POST["cliente"];
    $precio = $_POST["precio"];
    $pago = $_POST["pago"];
    $estado = $_POST["estado"];
    $situacion = $_POST["situacion"];
    $observaciones = !empty($_POST["observaciones"]) ? $_POST["observaciones"] : null;

    // Si el estado es "Reparto", capturar la fecha de reparto, si no, dejarla como NULL
    $fecha_reparto = ($estado === "Reparto" && isset($_POST["fecha_reparto"]) && !empty($_POST["fecha_reparto"])) ? $_POST["fecha_reparto"] : null;

    // Preparar la consulta SQL con la fecha de reparto
    $sql = "INSERT INTO ventas (fecha, remito, cliente, precio, pago, estado, situacion, observaciones, fecha_reparto) 
            VALUES (:fecha, :remito, :cliente, :precio, :pago, :estado, :situacion, :observaciones, :fecha_reparto)";
    
    $stmt = $pdo->prepare($sql);

    try {
        // Ejecutar la consulta con los valores
        $stmt->execute([
            ":fecha" => $fecha,
            ":remito" => $remito,
            ":cliente" => $cliente,
            ":precio" => $precio,
            ":pago" => $pago,
            ":estado" => $estado,
            ":situacion" => $situacion,
            ":observaciones" => $observaciones,
            ":fecha_reparto" => $fecha_reparto
        ]);
        
        echo "<script>alert('Venta registrada correctamente'); window.location.href='nueva_venta.php';</script>";
    } catch (PDOException $e) {
        die("Error al registrar la venta: " . $e->getMessage());
    }
} else {
    echo "<script>alert('Acceso inválido'); window.location.href='nueva_venta.php';</script>";
}
?>
