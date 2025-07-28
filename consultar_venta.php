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
    <title>Consultar Ventas</title>
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
    <h2>Ventas Registradas</h2>

    <?php
    require_once("config.php");
// Definir el tipo de ventas a mostrar según el filtro
$tipo = isset($_GET['tipo']) ? $_GET['tipo'] : 'todas';

if ($tipo === "finalizadas") {
    $stmt = $pdo->query("SELECT * FROM ventas WHERE estado = 'Entregado' AND situacion = 'Pedido Completo' ORDER BY fecha DESC");
} elseif ($tipo === "activas") {
    $stmt = $pdo->query("SELECT * FROM ventas WHERE NOT (estado = 'Entregado' AND situacion = 'Pedido Completo') ORDER BY fecha DESC");
} else {
    $stmt = $pdo->query("SELECT * FROM ventas ORDER BY fecha DESC");
}

$ventas = $stmt->fetchAll();


    echo "<table class='ventas-table'>";
    echo "<thead>
            <tr>
                <th>ID</th>
                <th>Fecha<br><input type='date' id='filtro-fecha'></th>
                <th>Remito<br><input type='text' id='filtro-remito' placeholder='Buscar...'></th>
                <th>Cliente<br><input type='text' id='filtro-cliente' placeholder='Buscar...'></th>
                <th>Precio<br><input type='text' id='filtro-precio' placeholder='Buscar...'></th>
                <th>Forma de Pago<br>
                    <select id='filtro-pago'>
                        <option value=''>Todos</option>
                        <option value='Efectivo'>Efectivo</option>
                        <option value='Tarjeta'>Tarjeta</option>
                        <option value='Transferencia'>Transferencia</option>
                        <option value='Anterior Efectivo'>Anterior Efectivo</option>
                        <option value='Anterior Transferencia'>Anterior Transferencia</option>
                        <option value='Mercado Pago'>Mercado Pago</option>
                        <option value='Débito'>Débito</option>
                        <option value='Crédito'>Crédito</option>
                        <option value='Debe'>Debe</option>
                        <option value='Cta Cte'>Cta Cte</option>
                        <option value='Otro'>Otro</option>
                        <option value='Cheque'>Cheque</option>
                        <option value='Nota de Crédito'>Nota de Crédito</option>
                    </select>
                </th>
                <th>Estado<br>
                    <select id='filtro-estado'>
                        <option value=''>Todos</option>
                        <option value='Entregado'>Entregado</option>
                        <option value='Reparto'>Reparto</option>
                        <option value='Retira cliente'>Retira Cliente</option>
                	 <option value='Trabajo'>Trabajo</option>   
		 </select>
                </th>
                <th>Fecha de Reparto<br><input type='date' id='filtro-fecha-reparto'></th>
                <th>Situación<br>
                    <select id='filtro-situacion'>
                        <option value=''>Todos</option>
                        <option value='Pedido Completo'>Pedido Completo</option>
                        <option value='Pedido Incompleto'>Pedido Incompleto</option>
                    </select>
                </th>
                <th>Observaciones</th>
            </tr>
          </thead><tbody>";

    foreach ($ventas as $venta) {
        echo "<tr>
                <td>" . htmlspecialchars($venta['id']) . "</td>
                <td>" . htmlspecialchars($venta['fecha']) . "</td>
                <td>" . htmlspecialchars($venta['remito']) . "</td>
                <td>" . htmlspecialchars($venta['cliente']) . "</td>
                <td>$" . htmlspecialchars($venta['precio']) . "</td>
                <td class='pago' data-value='" . htmlspecialchars($venta['pago']) . "'>" . htmlspecialchars($venta['pago']) . "</td>
                <td class='estado' data-value='" . htmlspecialchars($venta['estado']) . "'>" . htmlspecialchars($venta['estado']) . "</td>
                <td>" . (!empty($venta['fecha_reparto']) ? htmlspecialchars($venta['fecha_reparto']) : '-') . "</td>
                <td class='situacion' data-value='" . htmlspecialchars($venta['situacion']) . "'>" . htmlspecialchars($venta['situacion']) . "</td>
                <td class='observaciones'>" . htmlspecialchars($venta['observaciones']) . "</td>

              </tr>";
    }

    echo "</tbody></table>";
    ?>
</div>

<script>
document.addEventListener("DOMContentLoaded", function () {
    let estados = document.querySelectorAll(".estado");
    let situaciones = document.querySelectorAll(".situacion");

    (function verificarEntregasHoy() {
        const hoy = new Date().toISOString().split('T')[0];
        const filas = document.querySelectorAll(".ventas-table tbody tr");
        const remitosHoy = [];

        filas.forEach(fila => {
            const fechaReparto = fila.cells[7].innerText.trim();
            const estado = fila.cells[6].innerText.trim();
            const remito = fila.cells[2].innerText.trim();

            if (fechaReparto === hoy && estado.toLowerCase() === "reparto") {
                remitosHoy.push(remito);
                fila.classList.add("resaltado-hoy");
            }
        });

        if (remitosHoy.length > 0) {
            alert("🚚 Estos pedidos con número de remito deben ser entregados HOY:\n\n• " + remitosHoy.join("\n• "));
        }
    })();

    function asignarColor(elemento, valor) {
        const colores = {
            "Entregado": "#28a745",
            "Reparto": "#ffc107",
            "Retira cliente": "#17a2b8",
            "Pedido Completo": "#28a745",
            "Trabajo": "#cca9dd",
            "Pedido Incompleto": "#dc3545"
        };

        if (colores[valor]) {
            elemento.style.backgroundColor = colores[valor];
            elemento.style.color = "white";
            elemento.style.padding = "8px";
            elemento.style.borderRadius = "5px";
            elemento.style.textAlign = "center";
            elemento.style.fontWeight = "bold";
        }
    }

    estados.forEach(el => asignarColor(el, el.dataset.value));
    situaciones.forEach(el => asignarColor(el, el.dataset.value));

    // Filtros en tiempo real
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
