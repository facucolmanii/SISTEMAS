<?php
require_once 'conexion.php';
$ventasDia = $conn->query("SELECT COALESCE(SUM(total), 0) AS total FROM ventas WHERE DATE(fecha) = CURDATE()")->fetch_assoc()['total'] ?? 0;
$totalIngresos = $conn->query("SELECT COALESCE(SUM(total), 0) AS total FROM ventas")->fetch_assoc()['total'] ?? 0;
$stockBajo = $conn->query("SELECT id, nombre, stock FROM productos WHERE stock <= 5 ORDER BY stock ASC");
$productosBajo = [];
while ($row = $stockBajo->fetch_assoc()) { $productosBajo[] = $row; }

$labelsDias = [];
$totalesDias = [];
for ($i = 6; $i >= 0; $i--) {
  $fecha = date('Y-m-d', strtotime("-$i days"));
  $stmt = $conn->prepare("SELECT COALESCE(SUM(total),0) AS total FROM ventas WHERE DATE(fecha) = ?");
  $stmt->bind_param('s', $fecha);
  $stmt->execute();
  $totalDia = $stmt->get_result()->fetch_assoc()['total'] ?? 0;
  $stmt->close();
  $labelsDias[] = date('d/m', strtotime($fecha));
  $totalesDias[] = (float)$totalDia;
}

$topProductos = $conn->query("
  SELECT p.nombre, COALESCE(SUM(d.cantidad),0) AS unidades
  FROM productos p
  LEFT JOIN detalle_ventas d ON d.producto_id = p.id
  GROUP BY p.id, p.nombre
  ORDER BY unidades DESC
  LIMIT 5
");
$labelsTop = [];
$dataTop = [];
while ($tp = $topProductos->fetch_assoc()) {
  $labelsTop[] = $tp['nombre'];
  $dataTop[] = (int)$tp['unidades'];
}
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
<div class="app-loader" id="appLoader"><div class="loader-dot"></div></div>
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
      <div class="inline-buttons">
        <button id="themeToggle" class="btn outline" type="button"><i class="fa-solid fa-moon"></i> Oscuro</button>
        <span class="user-pill"><i class="fa-solid fa-user"></i> Admin</span>
      </div>
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

      <section class="chart-grid">
        <article class="card chart-card">
          <h2><i class="fa-solid fa-chart-line"></i> Ventas últimos 7 días</h2>
          <canvas id="ventasSemanaChart"></canvas>
        </article>

        <article class="card chart-card">
          <h2><i class="fa-solid fa-ranking-star"></i> Productos más vendidos</h2>
          <canvas id="topProductosChart"></canvas>
          <div class="kpi-row" style="margin-top:.8rem;">
            <div class="mini-stat"><strong>Ticket promedio:</strong> $<?= number_format((float)($totalIngresos / max(1, $conn->query("SELECT COUNT(*) as c FROM ventas")->fetch_assoc()['c'] ?? 1)), 2) ?></div>
            <div class="mini-stat"><strong>Ventas registradas:</strong> <?= (int)($conn->query("SELECT COUNT(*) as c FROM ventas")->fetch_assoc()['c'] ?? 0) ?></div>
          </div>
        </article>
      </section>
    </main>
  </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
const labelsDias = <?= json_encode($labelsDias) ?>;
const totalesDias = <?= json_encode($totalesDias) ?>;
const labelsTop = <?= json_encode($labelsTop) ?>;
const dataTop = <?= json_encode($dataTop) ?>;

new Chart(document.getElementById('ventasSemanaChart'), {
  type: 'line',
  data: {
    labels: labelsDias,
    datasets: [{
      label: 'Ventas ($)',
      data: totalesDias,
      borderColor: '#2563eb',
      backgroundColor: 'rgba(37,99,235,0.2)',
      tension: 0.35,
      fill: true
    }]
  },
  options: { responsive: true, maintainAspectRatio: false }
});

new Chart(document.getElementById('topProductosChart'), {
  type: 'bar',
  data: {
    labels: labelsTop,
    datasets: [{
      label: 'Unidades',
      data: dataTop,
      backgroundColor: ['#60a5fa', '#34d399', '#f59e0b', '#a78bfa', '#f472b6']
    }]
  },
  options: { responsive: true, maintainAspectRatio: false, indexAxis: 'y' }
});
</script>
<script src="ui.js"></script>
</body>
</html>
