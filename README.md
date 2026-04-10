# Sistema Web de Ventas - Taller de Limpieza de Autos

Aplicación web en **PHP puro + MySQL** para gestión de productos, ventas, reportes, caja y dashboard, con interfaz moderna tipo CRM/SaaS.

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
├── ui.js
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
   - Importa el archivo `database.sql`.

4. **Verificar conexión**
   - Abre `conexion.php` y valida credenciales:
     - Host: `localhost`
     - Usuario: `root`
     - Contraseña: `""` (vacía por defecto en XAMPP)
     - BD: `taller_ventas_limpieza`

5. **Ejecutar sistema**
   - Abre en navegador: `http://localhost/SISTEMAS/index.php`

## Mejoras de UI/UX incluidas

- Sidebar lateral y header superior tipo dashboard profesional.
- Tipografía moderna (Inter), paleta suave y espaciado consistente.
- Cards con sombras suaves y animación de entrada.
- Botones modernos grandes y formularios visualmente mejorados.
- Iconografía con Font Awesome.
- Tablas modernas con búsqueda en tiempo real y paginación simple (`ui.js`).
- Sidebar colapsable en móvil y diseño responsive.

## Módulos funcionales

- **Productos:** CRUD completo.
- **Ventas:** selección múltiple, cálculo automático, guardado con detalle y descuento de stock.
- **Reportes:** día/semana/mes, total vendido y listado.
- **Dashboard:** ventas del día, total ingresos y stock bajo.

## Validaciones básicas

- Campos obligatorios y valores no negativos.
- Verificación de stock antes de registrar venta.
- Transacciones en `guardar_venta.php` para consistencia de datos.
