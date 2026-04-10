<?php
require_once 'conexion.php';
$mensaje = '';
$error = '';

if (isset($_POST['crear'])) {
  $nombre = trim($_POST['nombre'] ?? '');
  $precioCompra = floatval($_POST['precio_compra'] ?? 0);
  $precioVenta = floatval($_POST['precio_venta'] ?? 0);
  $stock = intval($_POST['stock'] ?? 0);

  if ($nombre === '' || $precioCompra < 0 || $precioVenta < 0 || $stock < 0) {
    $error = 'Datos inválidos. Revisa los campos.';
  } else {
    $stmt = $conn->prepare('INSERT INTO productos (nombre, precio_compra, precio_venta, stock) VALUES (?, ?, ?, ?)');
    $stmt->bind_param('sddi', $nombre, $precioCompra, $precioVenta, $stock);
    $mensaje = $stmt->execute() ? 'Producto creado correctamente.' : 'No se pudo crear el producto.';
    $stmt->close();
  }
}

if (isset($_POST['editar'])) {
  $id = intval($_POST['id'] ?? 0);
  $nombre = trim($_POST['nombre'] ?? '');
  $precioCompra = floatval($_POST['precio_compra'] ?? 0);
  $precioVenta = floatval($_POST['precio_venta'] ?? 0);
  $stock = intval($_POST['stock'] ?? 0);

  if ($id <= 0 || $nombre === '' || $precioCompra < 0 || $precioVenta < 0 || $stock < 0) {
    $error = 'Datos inválidos para editar.';
  } else {
    $stmt = $conn->prepare('UPDATE productos SET nombre = ?, precio_compra = ?, precio_venta = ?, stock = ? WHERE id = ?');
    $stmt->bind_param('sddii', $nombre, $precioCompra, $precioVenta, $stock, $id);
    $mensaje = $stmt->execute() ? 'Producto actualizado correctamente.' : 'No se pudo actualizar el producto.';
    $stmt->close();
  }
}

if (isset($_GET['eliminar'])) {
  $idEliminar = intval($_GET['eliminar']);
  if ($idEliminar > 0) {
    $stmt = $conn->prepare('DELETE FROM productos WHERE id = ?');
    $stmt->bind_param('i', $idEliminar);
    if ($stmt->execute()) {
      $mensaje = 'Producto eliminado.';
    } else {
      $error = 'No se pudo eliminar el producto (puede tener ventas asociadas).';
    }
    $stmt->close();
  }
}

$productoEditar = null;
if (isset($_GET['editar'])) {
  $idEditar = intval($_GET['editar']);
  if ($idEditar > 0) {
    $stmt = $conn->prepare('SELECT * FROM productos WHERE id = ?');
    $stmt->bind_param('i', $idEditar);
    $stmt->execute();
    $productoEditar = $stmt->get_result()->fetch_assoc();
    $stmt->close();
  }
}

$resultadoProductos = $conn->query('SELECT * FROM productos ORDER BY id DESC');
?>
<!DOCTYPE html>
<html lang="es">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Productos | CRM Ventas</title>
  <link rel="stylesheet" href="styles.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css">
</head>
<body>
<div class="app-loader" id="appLoader"><div class="loader-dot"></div></div>
<div class="app">
  <aside class="sidebar" id="sidebar">
    <div class="brand"><i class="fa-solid fa-sparkles"></i> AutoLimpio CRM</div>
    <a class="nav-link" href="index.php"><i class="fa-solid fa-house"></i> Inicio</a>
    <a class="nav-link" href="dashboard.php"><i class="fa-solid fa-chart-line"></i> Dashboard</a>
    <a class="nav-link active" href="productos.php"><i class="fa-solid fa-box"></i> Productos</a>
    <a class="nav-link" href="ventas.php"><i class="fa-solid fa-cart-plus"></i> Ventas</a>
    <a class="nav-link" href="reportes.php"><i class="fa-solid fa-file-lines"></i> Reportes</a>
  </aside>

  <div class="content">
    <header class="topbar container">
      <button class="sidebar-toggle" id="sidebarToggle"><i class="fa-solid fa-bars"></i></button>
      <div><h1>Gestión de productos</h1><p>CRUD completo de inventario</p></div>
      <div class="inline-buttons">
        <button id="themeToggle" class="btn outline" type="button"><i class="fa-solid fa-moon"></i> Oscuro</button>
        <span class="user-pill"><i class="fa-solid fa-user"></i> Admin</span>
      </div>
    </header>

    <main class="container">
      <?php if ($mensaje): ?><div class="alert success"><?= htmlspecialchars($mensaje) ?></div><?php endif; ?>
      <?php if ($error): ?><div class="alert error"><?= htmlspecialchars($error) ?></div><?php endif; ?>

      <section class="card">
        <h2><?= $productoEditar ? 'Editar producto' : 'Nuevo producto' ?></h2>
        <form method="POST" class="form-grid" novalidate>
          <?php if ($productoEditar): ?><input type="hidden" name="id" value="<?= (int)$productoEditar['id'] ?>"><?php endif; ?>
          <div class="field"><input placeholder=" " type="text" name="nombre" required value="<?= htmlspecialchars($productoEditar['nombre'] ?? '') ?>"><label>Nombre</label></div>
          <div class="field"><input placeholder=" " type="number" step="0.01" min="0" name="precio_compra" required value="<?= htmlspecialchars($productoEditar['precio_compra'] ?? '') ?>"><label>Precio compra</label></div>
          <div class="field"><input placeholder=" " type="number" step="0.01" min="0" name="precio_venta" required value="<?= htmlspecialchars($productoEditar['precio_venta'] ?? '') ?>"><label>Precio venta</label></div>
          <div class="field"><input placeholder=" " type="number" min="0" name="stock" required value="<?= htmlspecialchars($productoEditar['stock'] ?? '') ?>"><label>Stock</label></div>
          <div class="actions">
            <?php if ($productoEditar): ?>
              <button class="btn big" type="submit" name="editar"><i class="fa-solid fa-floppy-disk"></i> Guardar cambios</button>
              <a class="btn secondary" href="productos.php">Cancelar</a>
            <?php else: ?>
              <button class="btn big" type="submit" name="crear"><i class="fa-solid fa-plus"></i> Crear producto</button>
            <?php endif; ?>
          </div>
        </form>
      </section>

      <section class="card" data-table>
        <h2>Listado de productos</h2>
        <div class="table-toolbar">
          <input class="search-input" type="text" data-search placeholder="Buscar producto...">
        </div>
        <div class="table-wrap">
          <table>
            <thead>
            <tr><th>ID</th><th>Nombre</th><th>P. Compra</th><th>P. Venta</th><th>Stock</th><th>Acciones</th></tr>
            </thead>
            <tbody>
            <?php while ($producto = $resultadoProductos->fetch_assoc()): ?>
              <tr>
                <td><?= (int)$producto['id'] ?></td>
                <td><?= htmlspecialchars($producto['nombre']) ?></td>
                <td>$<?= number_format((float)$producto['precio_compra'], 2) ?></td>
                <td>$<?= number_format((float)$producto['precio_venta'], 2) ?></td>
                <td>
                  <?php if ((int)$producto['stock'] <= 5): ?>
                    <span class="badge danger"><?= (int)$producto['stock'] ?></span>
                  <?php elseif ((int)$producto['stock'] <= 10): ?>
                    <span class="badge warn"><?= (int)$producto['stock'] ?></span>
                  <?php else: ?>
                    <span class="badge ok"><?= (int)$producto['stock'] ?></span>
                  <?php endif; ?>
                </td>
                <td class="inline-buttons">
                  <a class="btn" href="productos.php?editar=<?= (int)$producto['id'] ?>"><i class="fa-solid fa-pen"></i> Editar</a>
                  <a class="btn danger" href="productos.php?eliminar=<?= (int)$producto['id'] ?>" onclick="return confirm('¿Eliminar producto?');"><i class="fa-solid fa-trash"></i> Eliminar</a>
                </td>
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
