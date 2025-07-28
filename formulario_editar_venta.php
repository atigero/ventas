<?php
require_once("config.php");

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $stmt = $pdo->prepare("SELECT * FROM ventas WHERE id = ?");
    $stmt->execute([$id]);
    $venta = $stmt->fetch();

    if ($venta) {
        // Formulario dentro de una celda que ocupa todas las columnas
        ?>
        <td colspan="10">
          <form method="POST" action="actualizar_venta.php">
            <input type="hidden" name="id" value="<?= htmlspecialchars($venta['id']) ?>" />
            Fecha: <input type="date" name="fecha" value="<?= htmlspecialchars($venta['fecha']) ?>" required />
            Remito: <input type="text" name="remito" value="<?= htmlspecialchars($venta['remito']) ?>" required />
            Cliente: <input type="text" name="cliente" value="<?= htmlspecialchars($venta['cliente']) ?>" required />
            Precio: <input type="number" step="0.01" name="precio" value="<?= htmlspecialchars($venta['precio']) ?>" required />
            Pago:
            <select name="pago">
              <?php
              $opciones = ["Efectivo", "Tarjeta", "Transferencia", "Anterior Efectivo", "Anterior Transferencia", "Mercado Pago", "Débito", "Crédito", "Debe", "Cta Cte", "Otro", "Cheque", "Nota de Crédito"];
              foreach ($opciones as $opcion) {
                $sel = ($venta['pago'] == $opcion) ? 'selected' : '';
                echo "<option value=\"$opcion\" $sel>$opcion</option>";
              }
              ?>
            </select>
            Estado:
            <select name="estado" id="estado">
              <?php
              $estados = ["Entregado", "Reparto", "Trabajo", "Retira cliente"];
              foreach ($estados as $estado) {
                $sel = ($venta['estado'] == $estado) ? 'selected' : '';
                echo "<option value=\"$estado\" $sel>$estado</option>";
              }
              ?>
            </select>
            <span id="label-fecha-reparto" style="display: <?= ($venta['estado'] == 'Reparto') ? 'inline' : 'none' ?>">Fecha Reparto:</span>
            <input type="date" name="fecha_reparto" id="fecha-reparto" value="<?= htmlspecialchars($venta['fecha_reparto']) ?>" style="display: <?= ($venta['estado'] == 'Reparto') ? 'inline' : 'none' ?>" />
            Situacion:
            <select name="situacion">
              <option value="Pedido Completo" <?= ($venta['situacion'] == 'Pedido Completo') ? 'selected' : '' ?>>Pedido Completo</option>
              <option value="Pedido Incompleto" <?= ($venta['situacion'] == 'Pedido Incompleto') ? 'selected' : '' ?>>Pedido Incompleto</option>
            </select>
            <br />
            Observaciones:<br />
            <textarea name="observaciones" rows="2"><?= htmlspecialchars($venta['observaciones']) ?></textarea>
            <br />
            <button type="submit">Actualizar</button>
          </form>

          <script>
            // Mostrar / ocultar fecha reparto dependiendo del estado
            document.getElementById('estado').addEventListener('change', function() {
              const val = this.value;
              const labelFecha = document.getElementById('label-fecha-reparto');
              const inputFecha = document.getElementById('fecha-reparto');

              if (val === 'Reparto') {
                labelFecha.style.display = 'inline';
                inputFecha.style.display = 'inline';
                inputFecha.required = true;
              } else {
                labelFecha.style.display = 'none';
                inputFecha.style.display = 'none';
                inputFecha.required = false;
                inputFecha.value = '';
              }
            });
          </script>
        </td>
        <?php
    } else {
        echo "<td colspan='10'>Venta no encontrada.</td>";
    }
}
?>
