# Sistema Web de Ventas - Taller de Limpieza de Autos

Aplicación web en **PHP puro + MySQL** para gestión de productos, ventas, reportes, caja y dashboard.

## Estructura de carpetas

```text
SISTEMAS/
├── app.js
├── conexion.php
├── dashboard.php
├── database.sql
├── guardar_venta.php
├── index.php
├── productos.php
├── reportes.php
├── styles.css
└── ventas.php
```

## Instalación paso a paso en XAMPP

1. **Copiar el proyecto**
   - Copia la carpeta `SISTEMAS` dentro de `C:/xampp/htdocs/`.

2. **Iniciar servicios**
   - Abre el panel de XAMPP.
   - Inicia **Apache** y **MySQL**.

3. **Crear la base de datos**
   - Entra a `http://localhost/phpmyadmin`.
   - Crea una base de datos o importa directamente el archivo `database.sql`.
   - Si importas `database.sql`, se creará automáticamente `taller_ventas_limpieza` y sus tablas.

4. **Verificar conexión**
   - Abre `conexion.php` y valida credenciales:
     - Host: `localhost`
     - Usuario: `root`
     - Contraseña: `""` (vacía por defecto en XAMPP)
     - BD: `taller_ventas_limpieza`

5. **Ejecutar sistema**
   - Abre en navegador: `http://localhost/SISTEMAS/index.php`

## Módulos incluidos

- **Productos (`productos.php`)**
  - CRUD completo: crear, editar, eliminar.
  - Campos: nombre, precio_compra, precio_venta, stock.

- **Ventas (`ventas.php` + `guardar_venta.php`)**
  - Agregar múltiples productos con cantidad.
  - Cálculo automático de subtotal y total.
  - Guarda cabecera y detalle de venta.
  - Descuenta stock automáticamente.

- **Reportes (`reportes.php`)**
  - Vista por día, semana o mes.
  - Total vendido por periodo.
  - Listado de ventas.

- **Caja y Dashboard (`dashboard.php`)**
  - Caja diaria = ventas del día.
  - Resumen de ventas del día, stock bajo y total ingresos.

## Validaciones básicas

- Validación de campos obligatorios y valores no negativos.
- Verificación de stock antes de registrar una venta.
- Transacciones en `guardar_venta.php` para mantener consistencia de datos.

## Notas

- Diseño responsive con botones grandes para uso en celular.
- Código comentado y estructura simple para facilitar mantenimiento.
