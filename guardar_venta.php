<?php
require_once 'conexion.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ventas.php');
    exit;
}

$productoIds = $_POST['producto_id'] ?? [];
$cantidades = $_POST['cantidad'] ?? [];
$total = floatval($_POST['total'] ?? 0);

if (empty($productoIds) || empty($cantidades) || $total <= 0) {
    die('Datos de venta inválidos. <a href="ventas.php">Volver</a>');
}

$conn->begin_transaction();

try {
    $stmtVenta = $conn->prepare('INSERT INTO ventas (fecha, total) VALUES (NOW(), ?)');
    $stmtVenta->bind_param('d', $total);
    $stmtVenta->execute();
    $ventaId = $conn->insert_id;
    $stmtVenta->close();

    $stmtProducto = $conn->prepare('SELECT stock, precio_venta FROM productos WHERE id = ? FOR UPDATE');
    $stmtDetalle = $conn->prepare('INSERT INTO detalle_ventas (venta_id, producto_id, cantidad, precio_unitario, subtotal) VALUES (?, ?, ?, ?, ?)');
    $stmtStock = $conn->prepare('UPDATE productos SET stock = stock - ? WHERE id = ?');

    $totalCalculado = 0;

    for ($i = 0; $i < count($productoIds); $i++) {
        $productoId = intval($productoIds[$i]);
        $cantidad = intval($cantidades[$i]);

        if ($productoId <= 0 || $cantidad <= 0) {
            throw new Exception('Cantidad o producto inválido.');
        }

        $stmtProducto->bind_param('i', $productoId);
        $stmtProducto->execute();
        $resultadoProducto = $stmtProducto->get_result();
        $producto = $resultadoProducto->fetch_assoc();

        if (!$producto) {
            throw new Exception('Producto no encontrado.');
        }

        if ((int)$producto['stock'] < $cantidad) {
            throw new Exception('Stock insuficiente para uno de los productos.');
        }

        $precioUnitario = (float)$producto['precio_venta'];
        $subtotal = $precioUnitario * $cantidad;
        $totalCalculado += $subtotal;

        $stmtDetalle->bind_param('iiidd', $ventaId, $productoId, $cantidad, $precioUnitario, $subtotal);
        $stmtDetalle->execute();

        $stmtStock->bind_param('ii', $cantidad, $productoId);
        $stmtStock->execute();
    }

    if (abs($totalCalculado - $total) > 0.01) {
        throw new Exception('El total no coincide.');
    }

    $stmtProducto->close();
    $stmtDetalle->close();
    $stmtStock->close();

    $conn->commit();
    header('Location: reportes.php?ok=1');
    exit;
} catch (Exception $e) {
    $conn->rollback();
    die('Error al guardar la venta: ' . htmlspecialchars($e->getMessage()) . ' <a href="ventas.php">Volver</a>');
}
?>
