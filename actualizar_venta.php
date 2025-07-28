<?php
require_once("config.php");

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Capturar los datos del formulario
    $id = $_POST["id"];
    $fecha = $_POST["fecha"];
    $remito = $_POST["remito"];
    $cliente = $_POST["cliente"];
    $precio = $_POST["precio"];
    $pago = $_POST["pago"];
    $estado = $_POST["estado"];
    $situacion = $_POST["situacion"];
    $observaciones = !empty($_POST["observaciones"]) ? $_POST["observaciones"] : null;
    $fecha_reparto = !empty($_POST["fecha_reparto"]) ? $_POST["fecha_reparto"] : null;
    $finalizada = isset($_POST["finalizada"]) ? 1 : 0;

    // Consulta SQL para actualizar la venta
    $sql = "UPDATE ventas SET fecha = :fecha, remito = :remito, cliente = :cliente, precio = :precio, pago = :pago, estado = :estado, situacion = :situacion, observaciones = :observaciones, fecha_reparto = :fecha_reparto, finalizada = :finalizada, ultima_modificacion = NOW() WHERE id = :id";
    
    $stmt = $pdo->prepare($sql);

    try {
        // Ejecutar la actualización
        $stmt->execute([
            ":fecha" => $fecha,
            ":remito" => $remito,
            ":cliente" => $cliente,
            ":precio" => $precio,
            ":pago" => $pago,
            ":estado" => $estado,
            ":situacion" => $situacion,
            ":observaciones" => $observaciones,
            ":fecha_reparto" => $fecha_reparto,
            ":finalizada" => $finalizada,
            ":id" => $id
        ]);
        
        echo "<script>alert('Venta actualizada correctamente'); window.location.href='editar_venta.php';</script>";
    } catch (PDOException $e) {
        die("Error al actualizar la venta: " . $e->getMessage());
    }
} else {
    echo "<script>alert('Acceso inválido'); window.location.href='editar_venta.php';</script>";
}
?>

