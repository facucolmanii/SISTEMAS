<?php
require_once 'conexion.php';
$productos = $conn->query('SELECT id, nombre, precio_venta, stock FROM productos WHERE stock > 0 ORDER BY nombre ASC');
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Nueva venta | CRM Ventas</title>
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
    <a class="nav-link active" href="ventas.php"><i class="fa-solid fa-cart-plus"></i> Ventas</a>
    <a class="nav-link" href="reportes.php"><i class="fa-solid fa-file-lines"></i> Reportes</a>
  </aside>

  <div class="content">
    <header class="topbar container">
      <button class="sidebar-toggle" id="sidebarToggle"><i class="fa-solid fa-bars"></i></button>
      <div><h1>Nueva venta</h1><p>Registra ventas con cálculo automático</p></div>
      <div class="inline-buttons">
        <button id="themeToggle" class="btn outline" type="button"><i class="fa-solid fa-moon"></i> Oscuro</button>
        <span class="user-pill"><i class="fa-solid fa-user"></i> Admin</span>
      </div>
    </header>

    <main class="container">
      <section class="card">
        <h2>Detalle de venta</h2>
        <form id="formVenta" method="POST" action="guardar_venta.php">
          <div class="table-wrap">
            <table id="tablaVenta">
              <thead>
              <tr><th>Producto</th><th>Precio</th><th>Stock</th><th>Cantidad</th><th>Subtotal</th><th></th></tr>
              </thead>
              <tbody id="detalleVenta"></tbody>
            </table>
          </div>
          <br>
          <button type="button" class="btn big" id="agregarFila"><i class="fa-solid fa-plus"></i> Agregar producto</button>
          <div class="card" style="margin-top: 1rem;">
            <h3>Total: $<span id="totalVenta">0.00</span></h3>
            <input type="hidden" name="total" id="totalInput" value="0">
            <button class="btn big" type="submit"><i class="fa-solid fa-floppy-disk"></i> Guardar venta</button>
          </div>
        </form>
      </section>
    </main>
  </div>
</div>
<script>const productos = <?= json_encode($productos->fetch_all(MYSQLI_ASSOC)) ?>;</script>
<script src="ui.js"></script>
<script src="app.js"></script>
</body>
</html>
