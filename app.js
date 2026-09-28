# Gestión de Taller - Sistema web en una sola carpeta

Este proyecto es una solución rápida para gestionar un taller mecánico con login por usuario y contraseña, pensado para funcionar desde un navegador en la red local.

## Requisitos

- PHP 8+
- Extensión SQLite3 habilitada
- Un servidor web local (Apache/Nginx/XAMPP/WAMP o PHP built-in server)

## Instalación rápida

1. Copia esta carpeta en el directorio web del servidor.
2. Abre la URL del proyecto en el navegador, por ejemplo:
   - http://localhost/gestion-taller/
   - o http://192.168.1.10/gestion-taller/
3. Inicia sesión con:
   - Usuario: admin
   - Contraseña: admin123

## Funcionalidades incluidas

- Gestión de clientes
- Gestión de vehículos
- Presupuestos
- Órdenes de trabajo
- Facturas
- Agenda
- Repuestos
- Compras
- Stock
- Reparaciones
- Dashboard con resumen operativo
- Acceso protegido con usuario y contraseña

## Estructura

- index.php: pantalla de login y panel principal
- api.php: API JSON con lógica del sistema
- db.php: base de datos SQLite y configuración inicial
- style.css: diseño general
- app.js: lógica de interacción de la interfaz
- workshop.db: base de datos SQLite generada automáticamente

## Uso en red local

Puedes abrir la app desde otro PC de la red siempre que ambos estén dentro de la misma LAN y el servidor esté activo en el equipo que comparte la carpeta o tiene el servicio web levantado.

Ejemplo:

- Equipo servidor: 192.168.1.5
- URL desde otro equipo: http://192.168.1.5/gestion-taller/

## Nota importante

Este es un prototipo funcional de gestión de taller, preparado para una implementación rápida y en una sola carpeta. Si quieres, en una siguiente versión se puede ampliar con:

- exportación a PDF/Excel
- impresión de facturas y presupuestos
- más permisos por perfil de usuario
- integración de documentos
- backup y restauración automática
- un diseño más profesional o responsive


## Datos por defecto

Usuario administrador:

- usuario: admin
- contraseña: admin123

