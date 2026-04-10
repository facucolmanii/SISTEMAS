<?php
require_once 'conexion.php';
$periodo = $_GET['periodo'] ?? 'dia';
$filtroSQL = 'DATE(fecha) = CURDATE()';
if ($periodo === 'semana') {
  $filtroSQL = 'YEARWEEK(fecha, 1) = YEARWEEK(CURDATE(), 1)';
} elseif ($periodo === 'mes') {
  $filtroSQL = 'YEAR(fecha) = YEAR(CURDATE()) AND MONTH(fecha) = MONTH(CURDATE())';
}
$totalVendido = $conn->query("SELECT COALESCE(SUM(total), 0) AS total_vendido FROM ventas WHERE $filtroSQL")->fetch_assoc()['total_vendido'] ?? 0;
$ventas = $conn->query("SELECT id, fecha, total FROM ventas WHERE $filtroSQL ORDER BY fecha DESC");
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Reportes | CRM Ventas</title>
  <link rel="stylesheet" href="styles.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>
<body>
<div class="app">
  <aside class="sidebar" id="sidebar">
    <div class="brand"><i class="fa-solid fa-sparkles"></i> AutoLimpio CRM</div>
    <a class="nav-link" href="index.php"><i class="fa-solid fa-house"></i> Inicio</a>
    <a class="nav-link" href="dashboard.php"><i class="fa-solid fa-chart-line"></i> Dashboard</a>
    <a class="nav-link" href="productos.php"><i class="fa-solid fa-box"></i> Productos</a>
    <a class="nav-link" href="ventas.php"><i class="fa-solid fa-cart-plus"></i> Ventas</a>
    <a class="nav-link active" href="reportes.php"><i class="fa-solid fa-file-lines"></i> Reportes</a>
  </aside>

  <div class="content">
    <header class="topbar container">
      <button class="sidebar-toggle" id="sidebarToggle"><i class="fa-solid fa-bars"></i></button>
      <div><h1>Reportes de ventas</h1><p>Analiza resultados por periodo</p></div>
      <div class="inline-buttons">
        <button id="themeToggle" class="btn outline" type="button"><i class="fa-solid fa-moon"></i> Oscuro</button>
        <span class="user-pill"><i class="fa-solid fa-user"></i> Admin</span>
      </div>
    </header>

    <main class="container">
      <?php if (isset($_GET['ok'])): ?><div class="alert success">Venta guardada correctamente.</div><?php endif; ?>
      <section class="card">
        <h2>Filtros</h2>
        <div class="inline-buttons">
          <a class="btn <?= $periodo === 'dia' ? '' : 'outline' ?>" href="reportes.php?periodo=dia">Día</a>
          <a class="btn <?= $periodo === 'semana' ? '' : 'outline' ?>" href="reportes.php?periodo=semana">Semana</a>
          <a class="btn <?= $periodo === 'mes' ? '' : 'outline' ?>" href="reportes.php?periodo=mes">Mes</a>
        </div>
        <h3>Total vendido: $<?= number_format((float)$totalVendido, 2) ?></h3>
      </section>

      <section class="card" data-table>
        <h2>Listado de ventas</h2>
        <div class="table-toolbar">
          <input class="search-input" type="text" data-search placeholder="Buscar por ID o fecha...">
        </div>
        <div class="table-wrap">
          <table>
            <thead><tr><th>ID Venta</th><th>Fecha</th><th>Total</th></tr></thead>
            <tbody>
            <?php while ($venta = $ventas->fetch_assoc()): ?>
              <tr>
                <td><?= (int)$venta['id'] ?></td>
                <td><?= htmlspecialchars($venta['fecha']) ?></td>
                <td>$<?= number_format((float)$venta['total'], 2) ?></td>
              </tr>
            <?php endwhile; ?>
            </tbody>
          </table>
        </div>
        <div class="pagination">
          <button type="button" class="btn outline" data-prev>Anterior</button>
          <span data-page-label></span>
          <button type="button" class="btn outline" data-next>Siguiente</button>
        </div>
      </section>
    </main>
  </div>
</div>
<script src="ui.js"></script>
</body>
</html>
