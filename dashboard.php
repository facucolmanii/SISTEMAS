<?php
require_once 'conexion.php';
$ventasDia = $conn->query("SELECT COALESCE(SUM(total), 0) AS total FROM ventas WHERE DATE(fecha) = CURDATE()")->fetch_assoc()['total'] ?? 0;
$totalIngresos = $conn->query("SELECT COALESCE(SUM(total), 0) AS total FROM ventas")->fetch_assoc()['total'] ?? 0;
$stockBajo = $conn->query("SELECT id, nombre, stock FROM productos WHERE stock <= 5 ORDER BY stock ASC");
$productosBajo = [];
while ($row = $stockBajo->fetch_assoc()) { $productosBajo[] = $row; }
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard | CRM Ventas</title>
  <link rel="stylesheet" href="styles.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>
<body>
<div class="app">
  <aside class="sidebar" id="sidebar">
    <div class="brand"><i class="fa-solid fa-sparkles"></i> AutoLimpio CRM</div>
    <a class="nav-link" href="index.php"><i class="fa-solid fa-house"></i> Inicio</a>
    <a class="nav-link active" href="dashboard.php"><i class="fa-solid fa-chart-line"></i> Dashboard</a>
    <a class="nav-link" href="productos.php"><i class="fa-solid fa-box"></i> Productos</a>
    <a class="nav-link" href="ventas.php"><i class="fa-solid fa-cart-plus"></i> Ventas</a>
    <a class="nav-link" href="reportes.php"><i class="fa-solid fa-file-lines"></i> Reportes</a>
  </aside>

  <div class="content">
    <header class="topbar container">
      <button class="sidebar-toggle" id="sidebarToggle"><i class="fa-solid fa-bars"></i></button>
      <div><h1>Dashboard</h1><p>Resumen operativo del negocio</p></div>
      <span class="user-pill"><i class="fa-solid fa-user"></i> Admin</span>
    </header>

    <main class="container">
      <section class="card">
        <h2><i class="fa-solid fa-wave-square"></i> Estado general</h2>
        <p style="color:#64748b;margin-top:0;">Vista rápida de métricas clave del negocio en tiempo real.</p>
      </section>

      <section class="stats-grid">
        <article class="card kpi sales">
          <div class="stat-head"><span>Ventas del día</span><span class="icon-wrap"><i class="fa-solid fa-bolt"></i></span></div>
          <p class="stat-value">$<?= number_format((float)$ventasDia, 2) ?></p>
        </article>
        <article class="card kpi income">
          <div class="stat-head"><span>Total ingresos</span><span class="icon-wrap"><i class="fa-solid fa-wallet"></i></span></div>
          <p class="stat-value">$<?= number_format((float)$totalIngresos, 2) ?></p>
        </article>
        <article class="card kpi stock">
          <div class="stat-head"><span>Productos stock bajo</span><span class="icon-wrap"><i class="fa-solid fa-triangle-exclamation"></i></span></div>
          <p class="stat-value"><?= count($productosBajo) ?></p>
        </article>
      </section>

      <section class="card">
        <h2><i class="fa-solid fa-boxes-stacked"></i> Productos con bajo stock</h2>
        <?php if (empty($productosBajo)): ?>
          <p>No hay productos con stock bajo.</p>
        <?php else: ?>
          <ul>
            <?php foreach ($productosBajo as $p): ?>
              <li>#<?= (int)$p['id'] ?> - <?= htmlspecialchars($p['nombre']) ?>
                <span class="badge warn">Stock: <?= (int)$p['stock'] ?></span>
              </li>
            <?php endforeach; ?>
          </ul>
        <?php endif; ?>
      </section>
    </main>
  </div>
</div>
<script src="ui.js"></script>
</body>
</html>
