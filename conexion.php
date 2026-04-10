<?php
/**
 * Configuración de conexión a MySQL usando mysqli.
 * Ajusta las credenciales según tu entorno XAMPP.
 */
$host = 'localhost';
$usuario = 'root';
$contrasena = '';
$base_datos = 'taller_ventas_limpieza';

$conn = new mysqli($host, $usuario, $contrasena, $base_datos);

if ($conn->connect_error) {
    die('Error de conexión: ' . $conn->connect_error);
}

$conn->set_charset('utf8mb4');
?>
