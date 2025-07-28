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
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Editar Venta</title>
  <link rel="stylesheet" href="css/estilos.css" />
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
    <h2>Listado de Ventas</h2>
    <table class="ventas-table">
<thead>
  <tr>
    <th>ID</th>
    <th>Fecha<br><input type="date" id="filtro-fecha" /></th>
    <th>Remito<br><input type="text" id="filtro-remito" placeholder="Buscar..." /></th>
    <th>Cliente<br><input type="text" id="filtro-cliente" placeholder="Buscar..." /></th>
    <th>Precio<br><input type="text" id="filtro-precio" placeholder="Buscar..." /></th>
    <th>Forma de Pago<br>
      <select id="filtro-pago">
        <option value="">Todos</option>
        <option value="Efectivo">Efectivo</option>
        <option value="Tarjeta">Tarjeta</option>
        <option value="Transferencia">Transferencia</option>
        <option value="Anterior Efectivo">Anterior Efectivo</option>
        <option value="Anterior Transferencia">Anterior Transferencia</option>
        <option value="Mercado Pago">Mercado Pago</option>
        <option value="Débito">Débito</option>
        <option value="Crédito">Crédito</option>
        <option value="Debe">Debe</option>
        <option value="Cta Cte">Cta Cte</option>
        <option value="Otro">Otro</option>
        <option value="Cheque">Cheque</option>
        <option value="Nota de Crédito">Nota de Crédito</option>
      </select>
    </th>
    <th>Estado<br>
      <select id="filtro-estado">
        <option value="">Todos</option>
        <option value="Entregado">Entregado</option>
        <option value="Reparto">Reparto</option>
        <option value="Trabajo">Trabajo</option>
        <option value="Retira cliente">Retira Cliente</option>
      </select>
    </th>
    <th>Fecha de Reparto<br><input type="date" id="filtro-fecha-reparto" /></th>
    <th>Situación<br>
      <select id="filtro-situacion">
        <option value="">Todos</option>
        <option value="Pedido Completo">Pedido Completo</option>
        <option value="Pedido Incompleto">Pedido Incompleto</option>
      </select>
    </th>
    <th>Editar</th>
  </tr>
</thead>

<tbody>
<?php
  require_once("config.php");
  $idEditar = isset($_GET['id']) ? $_GET['id'] : null;
  $stmt = $pdo->query("SELECT * FROM ventas WHERE finalizada = 0 ORDER BY fecha DESC");
  while ($row = $stmt->fetch()) {
    echo "<tr>
            <td>{$row['id']}</td>
            <td>{$row['fecha']}</td>
            <td>{$row['remito']}</td>
            <td>{$row['cliente']}</td>
            <td>\${$row['precio']}</td>
            <td class='pago' data-value='{$row['pago']}'>{$row['pago']}</td>
            <td class='estado' data-value='{$row['estado']}'>{$row['estado']}</td>
            <td>" . (!empty($row['fecha_reparto']) ? $row['fecha_reparto'] : '-') . "</td>
            <td class='situacion' data-value='{$row['situacion']}'>{$row['situacion']}</td>
            <td><a class='btn-editar' href='editar_venta.php?id={$row['id']}'>Editar</a></td>
          </tr>";

    // Si esta fila es la que quieres editar, insertamos el formulario justo debajo
    if ($idEditar && $row['id'] == $idEditar) {
      // Recuperamos datos de la fila para el formulario
      // Puedes usar el mismo $row, porque ya tienes todos los datos
      ?>
      <tr>
        <td colspan="10">
          <form action="actualizar_venta.php" method="POST">
            <input type="hidden" name="id" value="<?= htmlspecialchars($row['id']) ?>">

            <label for="fecha">Fecha:</label>
            <input type="date" name="fecha" value="<?= htmlspecialchars($row['fecha']) ?>" required>

            <label for="remito">Número de Remito:</label>
            <input type="text" name="remito" value="<?= htmlspecialchars($row['remito']) ?>" required>

            <label for="cliente">Nombre del Cliente:</label>
            <input type="text" name="cliente" value="<?= htmlspecialchars($row['cliente']) ?>" required>

            <label for="precio">Precio:</label>
            <input type="number" name="precio" step="0.01" value="<?= htmlspecialchars($row['precio']) ?>" required>

            <label for="pago">Método de Pago:</label>
            <select name="pago">
              <?php
                $opciones_pago = [
                  "Efectivo", "Tarjeta", "Transferencia", "Anterior Efectivo",
                  "Anterior Transferencia", "Mercado Pago", "Débito", "Crédito",
                  "Debe", "Cta Cte", "Otro", "Cheque", "Nota de Crédito"
                ];
                foreach ($opciones_pago as $opcion) {
                  $selected = $row['pago'] == $opcion ? 'selected' : '';
                  echo "<option value=\"$opcion\" $selected>$opcion</option>";
                }
              ?>
            </select>

            <div class="estado-situacion-container">
              <div class="estado-box">
                <label for="estado">Estado:</label>
                <select name="estado" id="estado">
                  <?php
                    $estados = ["Entregado", "Reparto", "Trabajo", "Retira cliente"];
                    foreach ($estados as $estado) {
                      $selected = $row['estado'] == $estado ? 'selected' : '';
                      echo "<option value=\"$estado\" $selected>$estado</option>";
                    }
                  ?>
                </select>

                <label for="fecha_reparto" id="label-fecha-reparto" style="display:none;">Fecha de Reparto:</label>
                <input type="date" name="fecha_reparto" id="fecha-reparto" style="display:none;" value="<?= !empty($row['fecha_reparto']) ? htmlspecialchars($row['fecha_reparto']) : '' ?>">
              </div>

              <div class="situacion-box">
                <label for="situacion">Situación:</label>
                <select name="situacion" class="situacion" id="situacion">
                  <option value="Pedido Completo" <?= $row['situacion'] == 'Pedido Completo' ? 'selected' : '' ?>>Pedido Completo</option>
                  <option value="Pedido Incompleto" <?= $row['situacion'] == 'Pedido Incompleto' ? 'selected' : '' ?>>Pedido Incompleto</option>
                </select>
              </div>
            </div>

<label for="finalizada" class="label-finalizada">
    <input type="checkbox" name="finalizada" id="finalizada" class="input-finalizada" <?= $venta['finalizada'] ? 'checked' : '' ?>> Venta Finalizada
</label>


            <label for="observaciones">Observaciones:</label>
            <textarea name="observaciones" rows="4" cols="50"><?= htmlspecialchars($row['observaciones']) ?></textarea>

<button type="submit" class="btn-actualizar" id="btn-actualizar">Actualizar Venta</button>

          </form>
        </td>
      </tr>
      <?php
    }
  }
?>
</tbody>

    </table>
  </div>

  <script>
    document.addEventListener("DOMContentLoaded", function () {
      const pagos = document.querySelectorAll(".pago");
      const estados = document.querySelectorAll(".estado");
      const situaciones = document.querySelectorAll(".situacion");

      function asignarColor(elemento, valor) {
        const colores = {
          "Pendiente": "#dc3545",
          "Completado": "#28a745",
          "Cancelado": "#6c757d",
          "Normal": "#007bff",
          "Pedido Incompleto": "#ff5722",
          "Pedido Completo": "#28a745",
          "Retira cliente": "#17a2b8",
          "Entregado": "#28a745",
          "Reparto": "#ffc107",
          "Trabajo": "#cca9dd"
 };
        if (colores[valor]) {
          elemento.style.backgroundColor = colores[valor];
          elemento.style.color = "white";
          elemento.style.padding = "5px";
          elemento.style.borderRadius = "5px";
          elemento.style.textAlign = "center";
        }
      }

      pagos.forEach(el => asignarColor(el, el.dataset.value));
      estados.forEach(el => asignarColor(el, el.dataset.value));
      situaciones.forEach(el => asignarColor(el, el.dataset.value));
    });

    document.addEventListener("DOMContentLoaded", function () {
      const estadoSelect = document.getElementById("estado");
      const fechaRepartoLabel = document.getElementById("label-fecha-reparto");
      const fechaRepartoInput = document.getElementById("fecha-reparto");

      function verificarEstado() {
        if (estadoSelect.value === "Reparto") {
          fechaRepartoLabel.style.display = "block";
          fechaRepartoInput.style.display = "block";
          fechaRepartoInput.required = true;
        } else {
          fechaRepartoLabel.style.display = "none";
          fechaRepartoInput.style.display = "none";
          fechaRepartoInput.required = false;
          fechaRepartoInput.value = "";
        }
      }

      estadoSelect.addEventListener("change", verificarEstado);
      verificarEstado();
    });
  </script>
<script>
document.addEventListener("DOMContentLoaded", function () {
    let estadoSelect = document.getElementById("estado");
    let situacionSelect = document.getElementById("situacion");
    let finalizadaCheckbox = document.getElementById("finalizada");
    let btnActualizar = document.getElementById("btn-actualizar");

    function validarFinalizacion() {
        if (estadoSelect.value === "Entregado" && situacionSelect.value === "Pedido Completo") {
            finalizadaCheckbox.disabled = false;
        } else {
            finalizadaCheckbox.checked = false;
            finalizadaCheckbox.disabled = true;
        }
    }

    function validarEnvioFormulario(event) {
        if (finalizadaCheckbox.checked && (estadoSelect.value !== "Entregado" || situacionSelect.value !== "Pedido Completo")) {
            event.preventDefault();
            alert("No puedes finalizar la venta hasta que el estado sea 'Entregado' y la situación sea 'Pedido Completo'.");
        }
    }

    estadoSelect.addEventListener("change", validarFinalizacion);
    situacionSelect.addEventListener("change", validarFinalizacion);
    btnActualizar.addEventListener("click", validarEnvioFormulario);

    validarFinalizacion(); // Verificar al cargar la página
});
</script>

<script>
document.addEventListener("DOMContentLoaded", function () {
    const filtros = {
        fecha: document.getElementById("filtro-fecha"),
        remito: document.getElementById("filtro-remito"),
        cliente: document.getElementById("filtro-cliente"),
        precio: document.getElementById("filtro-precio"),
        pago: document.getElementById("filtro-pago"),
        estado: document.getElementById("filtro-estado"),
        fechaReparto: document.getElementById("filtro-fecha-reparto"),
        situacion: document.getElementById("filtro-situacion")
    };

    const filas = document.querySelectorAll(".ventas-table tbody tr");

    function filtrarVentas() {
        filas.forEach(fila => {
            const datos = {
                fecha: fila.cells[1].innerText.trim(),
                remito: fila.cells[2].innerText.trim(),
                cliente: fila.cells[3].innerText.trim(),
                precio: fila.cells[4].innerText.trim(),
                pago: fila.cells[5].innerText.trim(),
                estado: fila.cells[6].innerText.trim(),
                fechaReparto: fila.cells[7].innerText.trim(),
                situacion: fila.cells[8].innerText.trim()
            };

            const mostrar = Object.keys(filtros).every(key => {
                if (!filtros[key].value) return true;
                if (filtros[key].type === "date") {
                    return datos[key] === filtros[key].value;
                }
                return datos[key].toLowerCase().includes(filtros[key].value.toLowerCase());
            });

            fila.style.display = mostrar ? "" : "none";
        });
    }

    Object.values(filtros).forEach(filtro => {
        filtro.addEventListener("input", filtrarVentas);
    });
});
</script>

</body>
</html>
