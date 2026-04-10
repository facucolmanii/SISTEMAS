<?php
require_once 'conexion.php';

$mensaje = '';
$error = '';

// Crear producto
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
        if ($stmt->execute()) {
            $mensaje = 'Producto creado correctamente.';
        } else {
            $error = 'No se pudo crear el producto.';
        }
        $stmt->close();
    }
}

// Actualizar producto
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
        if ($stmt->execute()) {
            $mensaje = 'Producto actualizado correctamente.';
        } else {
            $error = 'No se pudo actualizar el producto.';
        }
        $stmt->close();
    }
}

// Eliminar producto
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

// Obtener datos para editar
$productoEditar = null;
if (isset($_GET['editar'])) {
    $idEditar = intval($_GET['editar']);
    if ($idEditar > 0) {
        $stmt = $conn->prepare('SELECT * FROM productos WHERE id = ?');
        $stmt->bind_param('i', $idEditar);
        $stmt->execute();
        $resultadoEditar = $stmt->get_result();
        $productoEditar = $resultadoEditar->fetch_assoc();
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
    <title>Productos</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header class="topbar">
        <h1>Productos</h1>
        <a class="btn" href="index.php">Volver al inicio</a>
    </header>

    <main class="container">
        <?php if ($mensaje): ?>
            <div class="alert success"><?= htmlspecialchars($mensaje) ?></div>
        <?php endif; ?>

        <?php if ($error): ?>
            <div class="alert error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <section class="card">
            <h2><?= $productoEditar ? 'Editar producto' : 'Nuevo producto' ?></h2>
            <form method="POST" class="form-grid">
                <?php if ($productoEditar): ?>
                    <input type="hidden" name="id" value="<?= (int)$productoEditar['id'] ?>">
                <?php endif; ?>

                <label>Nombre
                    <input type="text" name="nombre" required value="<?= htmlspecialchars($productoEditar['nombre'] ?? '') ?>">
                </label>

                <label>Precio compra
                    <input type="number" step="0.01" min="0" name="precio_compra" required value="<?= htmlspecialchars($productoEditar['precio_compra'] ?? '') ?>">
                </label>

                <label>Precio venta
                    <input type="number" step="0.01" min="0" name="precio_venta" required value="<?= htmlspecialchars($productoEditar['precio_venta'] ?? '') ?>">
                </label>

                <label>Stock
                    <input type="number" min="0" name="stock" required value="<?= htmlspecialchars($productoEditar['stock'] ?? '') ?>">
                </label>

                <div class="actions">
                    <?php if ($productoEditar): ?>
                        <button class="btn big" type="submit" name="editar">Guardar cambios</button>
                        <a class="btn secondary" href="productos.php">Cancelar</a>
                    <?php else: ?>
                        <button class="btn big" type="submit" name="crear">Crear producto</button>
                    <?php endif; ?>
                </div>
            </form>
        </section>

        <section class="card">
            <h2>Listado de productos</h2>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nombre</th>
                            <th>P. Compra</th>
                            <th>P. Venta</th>
                            <th>Stock</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($producto = $resultadoProductos->fetch_assoc()): ?>
                        <tr>
                            <td><?= (int)$producto['id'] ?></td>
                            <td><?= htmlspecialchars($producto['nombre']) ?></td>
                            <td>$<?= number_format((float)$producto['precio_compra'], 2) ?></td>
                            <td>$<?= number_format((float)$producto['precio_venta'], 2) ?></td>
                            <td><?= (int)$producto['stock'] ?></td>
                            <td class="inline-buttons">
                                <a class="btn" href="productos.php?editar=<?= (int)$producto['id'] ?>">Editar</a>
                                <a class="btn danger" href="productos.php?eliminar=<?= (int)$producto['id'] ?>" onclick="return confirm('¿Eliminar producto?');">Eliminar</a>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>
</body>
</html>
