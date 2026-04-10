<?php
require_once 'conexion.php';
$productos = $conn->query('SELECT id, nombre, precio_venta, stock FROM productos WHERE stock > 0 ORDER BY nombre ASC');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva Venta</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header class="topbar">
        <h1>Nueva venta</h1>
        <a class="btn" href="index.php">Volver al inicio</a>
    </header>

    <main class="container">
        <section class="card">
            <h2>Agregar productos a la venta</h2>
            <form id="formVenta" method="POST" action="guardar_venta.php">
                <div class="table-wrap">
                    <table id="tablaVenta">
                        <thead>
                            <tr>
                                <th>Producto</th>
                                <th>Precio</th>
                                <th>Stock</th>
                                <th>Cantidad</th>
                                <th>Subtotal</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody id="detalleVenta"></tbody>
                    </table>
                </div>

                <button type="button" class="btn big" id="agregarFila">+ Agregar producto</button>

                <div class="totals">
                    <h3>Total: $<span id="totalVenta">0.00</span></h3>
                    <input type="hidden" name="total" id="totalInput" value="0">
                </div>

                <button class="btn big" type="submit">Guardar venta</button>
            </form>
        </section>
    </main>

    <script>
        const productos = <?= json_encode($productos->fetch_all(MYSQLI_ASSOC)) ?>;
    </script>
    <script src="app.js"></script>
</body>
</html>
