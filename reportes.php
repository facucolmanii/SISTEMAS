<?php
require_once 'conexion.php';

$periodo = $_GET['periodo'] ?? 'dia';
$filtroSQL = 'DATE(fecha) = CURDATE()';

if ($periodo === 'semana') {
    $filtroSQL = 'YEARWEEK(fecha, 1) = YEARWEEK(CURDATE(), 1)';
} elseif ($periodo === 'mes') {
    $filtroSQL = 'YEAR(fecha) = YEAR(CURDATE()) AND MONTH(fecha) = MONTH(CURDATE())';
}

$queryTotal = "SELECT COALESCE(SUM(total), 0) AS total_vendido FROM ventas WHERE $filtroSQL";
$totalVendido = $conn->query($queryTotal)->fetch_assoc()['total_vendido'] ?? 0;

$queryVentas = "SELECT id, fecha, total FROM ventas WHERE $filtroSQL ORDER BY fecha DESC";
$ventas = $conn->query($queryVentas);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reportes</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header class="topbar">
        <h1>Reportes de Ventas</h1>
        <a class="btn" href="index.php">Volver al inicio</a>
    </header>

    <main class="container">
        <?php if (isset($_GET['ok'])): ?>
            <div class="alert success">Venta guardada correctamente.</div>
        <?php endif; ?>

        <section class="card">
            <h2>Filtrar por periodo</h2>
            <div class="inline-buttons">
                <a class="btn <?= $periodo === 'dia' ? 'active' : '' ?>" href="reportes.php?periodo=dia">Día</a>
                <a class="btn <?= $periodo === 'semana' ? 'active' : '' ?>" href="reportes.php?periodo=semana">Semana</a>
                <a class="btn <?= $periodo === 'mes' ? 'active' : '' ?>" href="reportes.php?periodo=mes">Mes</a>
            </div>

            <h3>Total vendido: $<?= number_format((float)$totalVendido, 2) ?></h3>
        </section>

        <section class="card">
            <h2>Listado de ventas</h2>
            <div class="table-wrap">
                <table>
                    <thead>
                        <tr>
                            <th>ID Venta</th>
                            <th>Fecha</th>
                            <th>Total</th>
                        </tr>
                    </thead>
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
        </section>
    </main>
</body>
</html>
