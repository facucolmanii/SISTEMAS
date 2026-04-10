<?php ?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Inicio | CRM Ventas</title>
  <link rel="stylesheet" href="styles.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>
<body>
<div class="app-loader" id="appLoader"><div class="loader-dot"></div></div>
<div class="app">
  <aside class="sidebar" id="sidebar">
    <div class="brand"><i class="fa-solid fa-sparkles"></i> AutoLimpio CRM</div>
    <a class="nav-link active" href="index.php"><i class="fa-solid fa-house"></i> Inicio</a>
    <a class="nav-link" href="dashboard.php"><i class="fa-solid fa-chart-line"></i> Dashboard</a>
    <a class="nav-link" href="productos.php"><i class="fa-solid fa-box"></i> Productos</a>
    <a class="nav-link" href="ventas.php"><i class="fa-solid fa-cart-plus"></i> Ventas</a>
    <a class="nav-link" href="reportes.php"><i class="fa-solid fa-file-lines"></i> Reportes</a>
  </aside>

  <div class="content">
    <header class="topbar container">
      <button class="sidebar-toggle" id="sidebarToggle"><i class="fa-solid fa-bars"></i></button>
      <div>
        <h1>Panel principal</h1>
        <p>Sistema de ventas para taller de limpieza automotriz</p>
      </div>
      <div class="inline-buttons">
        <button id="themeToggle" class="btn outline" type="button"><i class="fa-solid fa-moon"></i> Oscuro</button>
        <span class="user-pill"><i class="fa-solid fa-user"></i> Admin</span>
      </div>
    </header>

    <main class="container">
      <section class="card">
        <h2>Accesos rápidos</h2>
        <div class="grid-buttons">
          <a class="btn big" href="dashboard.php"><i class="fa-solid fa-chart-pie"></i> Dashboard</a>
          <a class="btn big" href="productos.php"><i class="fa-solid fa-layer-group"></i> Productos</a>
          <a class="btn big" href="ventas.php"><i class="fa-solid fa-cash-register"></i> Nueva venta</a>
          <a class="btn big" href="reportes.php"><i class="fa-solid fa-table-list"></i> Reportes</a>
        </div>
      </section>
    </main>
  </div>
</div>
<script src="ui.js"></script>
</body>
</html>
