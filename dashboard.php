<?php
require_once 'conexion.php';

$ventasDia = $conn->query("SELECT COALESCE(SUM(total), 0) AS total FROM ventas WHERE DATE(fecha) = CURDATE()")->fetch_assoc()['total'] ?? 0;
$totalIngresos = $conn->query("SELECT COALESCE(SUM(total), 0) AS total FROM ventas")->fetch_assoc()['total'] ?? 0;
$stockBajo = $conn->query("SELECT id, nombre, stock FROM productos WHERE stock <= 5 ORDER BY stock ASC");
$cajaDia = $ventasDia; // Caja diaria
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header class="topbar">
        <h1>Dashboard</h1>
        <a class="btn" href="index.php">Volver al inicio</a>
    </header>

    <main class="container">
        <section class="stats-grid">
            <article class="card stat">
                <h2>Ventas del día</h2>
                <p>$<?= number_format((float)$ventasDia, 2) ?></p>
            </article>
            <article class="card stat">
                <h2>Caja del día</h2>
                <p>$<?= number_format((float)$cajaDia, 2) ?></p>
            </article>
            <article class="card stat">
                <h2>Total ingresos</h2>
                <p>$<?= number_format((float)$totalIngresos, 2) ?></p>
            </article>
        </section>

        <section class="card">
            <h2>Productos con stock bajo (≤ 5)</h2>
            <ul>
                <?php while ($p = $stockBajo->fetch_assoc()): ?>
                    <li>#<?= (int)$p['id'] ?> - <?= htmlspecialchars($p['nombre']) ?> (Stock: <?= (int)$p['stock'] ?>)</li>
                <?php endwhile; ?>
            </ul>
        </section>
    </main>
</body>
</html>
