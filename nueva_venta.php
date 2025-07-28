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
    <title>Nueva Venta</title>
    <link rel="stylesheet" href="css/estilos.css">

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
        <form action="procesar_venta.php" method="POST">
            <label for="codigo_qr">Escanear Código:</label>
            <input type="text" id="codigo_qr" name="codigo_qr" oninput="procesarCodigo()" placeholder="Escanear aquí">

            <label for="fecha">Fecha:</label>
            <input type="date" id="fecha" name="fecha" required>

            <label for="remito">Número de Remito:</label>
            <input type="text" id="remito" name="remito" required>

            <label for="cliente">Nombre del Cliente:</label>
            <input type="text" id="cliente" name="cliente" required>

            <label for="precio">Precio:</label>
            <input type="number" id="precio" name="precio" step="0.01" required>

            <label for="pago">Método de Pago:</label>
<select name="pago">
    <option value="Efectivo" <?= $venta['pago'] == 'Efectivo' ? 'selected' : '' ?>>Efectivo</option>
    <option value="Tarjeta" <?= $venta['pago'] == 'Tarjeta' ? 'selected' : '' ?>>Tarjeta</option>
    <option value="Transferencia" <?= $venta['pago'] == 'Transferencia' ? 'selected' : '' ?>>Transferencia</option>
    <option value="Anterior Efectivo" <?= $venta['pago'] == 'Anterior Efectivo' ? 'selected' : '' ?>>Anterior Efectivo</option>
    <option value="Anterior Transferencia" <?= $venta['pago'] == 'Anterior Transferencia' ? 'selected' : '' ?>>Anterior Transferencia</option>
    <option value="Mercado Pago" <?= $venta['pago'] == 'Mercado Pago' ? 'selected' : '' ?>>Mercado Pago</option>
    <option value="Débito" <?= $venta['pago'] == 'Débito' ? 'selected' : '' ?>>Débito</option>
    <option value="Crédito" <?= $venta['pago'] == 'Crédito' ? 'selected' : '' ?>>Crédito</option>
    <option value="Debe" <?= $venta['pago'] == 'Debe' ? 'selected' : '' ?>>Debe</option>
    <option value="Cta Cte" <?= $venta['pago'] == 'Cta Cte' ? 'selected' : '' ?>>Cuenta Corriente</option>
    <option value="Otro" <?= $venta['pago'] == 'Otro' ? 'selected' : '' ?>>Otro</option>
    <option value="Cheque" <?= $venta['pago'] == 'Cheque' ? 'selected' : '' ?>>Cheque</option>
    <option value="Nota de Crédito" <?= $venta['pago'] == 'Nota de Crédito' ? 'selected' : '' ?>>Nota de Crédito</option>
</select>

            <label for="estado">Estado:</label>
		<select name="estado" id="estado">
    <option value="Entregado" <?= $venta['estado'] == 'Entregado' ? 'selected' : '' ?>>Entregado</option>
    <option value="Reparto" <?= $venta['estado'] == 'Reparto' ? 'selected' : '' ?>>Reparto</option>
    <option value="Trabajo" <?= $venta['estado'] == 'Trabajo' ? 'selected' : '' ?>>Trabajo</option>
    <option value="Retira cliente" <?= $venta['estado'] == 'Retira cliente' ? 'selected' : '' ?>>Retira cliente</option>
</select>
<label for="fecha_reparto" id="label-fecha-reparto" style="display:none;">Fecha de Reparto:</label>
<input type="date" name="fecha_reparto" id="fecha-reparto" style="display:none;">
<script>
document.addEventListener("DOMContentLoaded", function () {
    let estadoSelect = document.getElementById("estado");
    let fechaRepartoLabel = document.getElementById("label-fecha-reparto");
    let fechaRepartoInput = document.getElementById("fecha-reparto");

    function verificarEstado() {
        if (estadoSelect.value === "Reparto") {
            fechaRepartoLabel.style.display = "block";
            fechaRepartoInput.style.display = "block";
            fechaRepartoInput.required = true;
        } else {
            fechaRepartoLabel.style.display = "none";
            fechaRepartoInput.style.display = "none";
            fechaRepartoInput.required = false;
        }
    }

    estadoSelect.addEventListener("change", verificarEstado);
    verificarEstado(); // Verificar al cargar la página
});
</script>

            <label for="situacion">Situación:</label>
            <select id="situacion" name="situacion" onchange="mostrarObservaciones()" required>
                <option value="Pedido Completo">Pedido Completo</option>
                <option value="Pedido Incompleto">Pedido Incompleto</option>
            </select>

            <div id="observacionesDiv" style="display:none;">
                <label for="observaciones">Observaciones</label>
                <textarea name="observaciones" rows="4"></textarea>
            </div>

<button type="submit" class="btn-actualizar">Registrar Venta</button>        </form>
    </div>

    <script>
        function mostrarObservaciones() {
            var situacion = document.getElementById('situacion').value;
            var obsDiv = document.getElementById('observacionesDiv');
            obsDiv.style.display = (situacion === 'Pedido Incompleto') ? 'block' : 'none';
        }

        function procesarCodigo() {
            var codigo = document.getElementById('codigo_qr').value.trim();
            var partes = codigo.split(" "); // Separar por espacios

            // Verificamos que la estructura tenga exactamente 4 elementos
            if (partes.length === 4) {
                document.getElementById('fecha').value = formatearFecha(partes[0]);
                document.getElementById('remito').value = partes[1];
                document.getElementById('cliente').value = partes[2] + " " + partes[3]; // Nombre y apellido juntos
                document.getElementById('precio').value = partes[3]; // Puede requerir conversión
            }
        }

        function formatearFecha(fecha) {
            var partes = fecha.split("/");
            if (partes.length === 3) {
                return partes[2] + "-" + partes[1] + "-" + partes[0]; // Convertir a formato YYYY-MM-DD
            }
            return "";
        }
    </script>
<script>
document.addEventListener("DOMContentLoaded", function () {
  const form = document.querySelector("form");
  const remitoInput = document.getElementById("remito");

  form.addEventListener("submit", function (e) {
    e.preventDefault(); // Evita envío inmediato

    const remito = remitoInput.value.trim();

    fetch("verificar_remito.php?remito=" + encodeURIComponent(remito))
      .then(res => res.json())
      .then(data => {
        if (data.duplicado) {
          if (confirm("⚠️ Este número de remito ya fue cargado. ¿Quierés cargarlo de todos modos?")) {
            form.submit(); // Si acepta, enviamos igual
          } else {
            alert("Carga cancelada.");
          }
        } else {
          form.submit(); // No está duplicado, seguimos normal
        }
      })
      .catch(() => {
        alert("No se pudo verificar el remito. Intentalo de nuevo.");
      });
  });
});
</script>

</body>
</html>

