<?php
if (!isset($_SESSION)) session_start();
$usuario = $_SESSION['usuario'] ?? 'Usuario';
?>

<!-- Tabler Icons CDN -->
<link rel="stylesheet" href="https://unpkg.com/@tabler/icons@latest/iconfont/tabler-icons.min.css">

<header class="encabezado-moderno">
  <div class="encabezado-contenedor">
    <div class="zona-izquierda">
      <span class="encabezado-logo"><i class="ti ti-box-seam"></i> Sistema de Ventas</span>
    </div>

    <nav class="zona-centro">
      <a href="dashboard.php"><i class="ti ti-home"></i> Inicio</a>
      <a href="nueva_venta.php"><i class="ti ti-plus"></i> Nueva Venta</a>
      <a href="editar_venta.php"><i class="ti ti-edit"></i> Editar Venta</a>
      <a href="inicio_ventas.php"><i class="ti ti-search"></i> Consultar Ventas</a>
      <a href="cierre_caja.php"><i class="ti ti-cash"></i> Caja</a>
    </nav>

    <div class="zona-derecha">
      <span class="encabezado-usuario">👤 <?= htmlspecialchars($usuario) ?></span>
      <a href="logout.php" class="cerrar-sesion"><i class="ti ti-logout"></i> Cerrar sesión</a>
    </div>
  </div>
</header>

<script>
  window.addEventListener('scroll', () => {
    document.body.classList.toggle('scrolled', window.scrollY > 10);
  });
</script>
