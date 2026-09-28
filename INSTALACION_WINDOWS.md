# Gestión Taller Pro v2.5

**Sistema integral de gestión para talleres mecánicos - Listo para usar en Windows**

## ✨ Características principales

### 📋 Gestión de negocio
- ✅ Clientes con historial completo
- ✅ Vehículos por cliente
- ✅ Presupuestos profesionales con impresión
- ✅ Órdenes de trabajo con prioridades
- ✅ Facturas con detalles de mano de obra y repuestos
- ✅ Agenda integrada con recordatorios
- ✅ Seguimiento de reparaciones en tiempo real

### 📦 Control de almacén
- ✅ Gestión de repuestos con código y categoría
- ✅ Control de compras a proveedores
- ✅ Stock en tiempo real con alertas
- ✅ Movimientos de almacén trazables

### 👥 Usuarios y seguridad
- ✅ Login seguro con contraseña
- ✅ Roles: Administrador, Mecánico, Recepción
- ✅ Auditoría de operaciones
- ✅ Historial de cambios

### 🎨 Interfaz profesional
- ✅ Diseño moderno y responsivo
- ✅ Dashboard con métricas clave
- ✅ Impresión de documentos
- ✅ Interfaz intuitiva en español

### 💾 Datos y seguridad
- ✅ Base de datos SQLite (no requiere servidor)
- ✅ Copias de seguridad automáticas
- ✅ Acceso desde cualquier PC de la red
- ✅ Sin dependencias externas

---

## 🚀 Instalación en Windows

### Opción 1: Con XAMPP (Recomendado)

1. **Descargar XAMPP:**
   - Ve a https://www.apachefriends.org/es/download.html
   - Descarga XAMPP para Windows
   - Instala con las opciones por defecto

2. **Copiar el proyecto:**
   - Copia la carpeta `sistema-gestion-taller` en:
     ```
     C:\xampp\htdocs\sistema-gestion-taller
     ```

3. **Iniciar XAMPP:**
   - Abre el panel de control de XAMPP
   - Haz clic en "Start" en Apache
   - Haz clic en "Start" en MySQL (opcional)

4. **Acceder a la aplicación:**
   - Abre tu navegador
   - Ve a: http://localhost/sistema-gestion-taller/
   - Credenciales: admin / admin123

### Opción 2: Con PHP Local (Sin instalar XAMPP)

1. **Descargar PHP:**
   - Ve a https://www.php.net/downloads
   - Descarga PHP (versión 8.0+)
   - Extrae en `C:\php`

2. **Copiar la carpeta del proyecto:**
   - Copia `sistema-gestion-taller` en `C:\mis-proyectos\`

3. **Inicia el servidor PHP:**
   - Abre Command Prompt (cmd)
   - Ve a la carpeta del proyecto:
     ```bash
     cd C:\mis-proyectos\sistema-gestion-taller
     ```
   - Ejecuta:
     ```bash
     php -S localhost:8000
     ```

4. **Acceder:**
   - Abre: http://localhost:8000
   - Credenciales: admin / admin123

---

## 🌐 Acceso desde otro PC de la red

1. **Obtén la IP de tu PC:**
   - Abre Command Prompt (cmd)
   - Ejecuta: `ipconfig`
   - Busca "Dirección IPv4", ej: `192.168.1.50`

2. **Desde otro PC, abre:**
   ```
   http://192.168.1.50/sistema-gestion-taller/
   ```
   o si usas PHP local:
   ```
   http://192.168.1.50:8000
   ```

3. **Los dos PCs deben estar en la misma red local (WiFi o cable).**

---

## 📝 Primer acceso

**Credenciales por defecto:**
- Usuario: `admin`
- Contraseña: `admin123`

**Recomendaciones iniciales:**
1. Ingresa a **Administración** (botón de engranaje, solo visible para admin)
2. Configura los datos de tu taller:
   - Nombre, dirección, teléfono, email
   - CIF/NIF
   - Tarifa horaria de mano de obra
3. Crea usuarios adicionales (Mecánicos, Recepción)
4. Comienza a registrar clientes y vehículos

---

## 🎯 Flujo de uso típico

### 1. Cliente llega al taller
- Registra el cliente (si es nuevo)
- Registra el vehículo
- Abre una orden de trabajo

### 2. Diagnóstico y presupuesto
- Describe la avería en la orden
- Crea un presupuesto
- Imprime para mostrar al cliente

### 3. Ejecución
- Registra repuestos utilizados
- Actualiza horas de mano de obra
- Cambia estado de la orden

### 4. Cierre
- Genera factura
- Registra pago
- El cliente puede consultar su historial

---

## 📚 Módulos disponibles

| Módulo | Descripción |
|--------|-------------|
| **Dashboard** | Resumen de operaciones y KPIs |
| **Clientes** | Base de datos de clientes |
| **Vehículos** | Vehículos asignados a clientes |
| **Presupuestos** | Cotizaciones para trabajos |
| **Órdenes** | Trabajos en taller |
| **Facturas** | Documentos de cobro |
| **Agenda** | Citas y eventos |
| **Repuestos** | Catálogo de piezas |
| **Compras** | Pedidos a proveedores |
| **Stock** | Control de almacén |
| **Reparaciones** | Seguimiento detallado |
| **Administración** | Usuarios, configuración, backups |

---

## 🔒 Seguridad

- ✅ Contraseñas con hash bcrypt
- ✅ Sesiones protegidas
- ✅ Acceso restringido por roles
- ✅ Auditoría de cambios
- ✅ Copias de seguridad automáticas

---

## 📊 Base de datos

La aplicación usa **SQLite**, que no requiere instalación de servidor:
- Base de datos: `data/workshop.db`
- Logs: `data/logs.txt`
- Backups: `data/backups/`

Todos los archivos se crean automáticamente.

---

## 🛠️ Mantenimiento

### Crear una copia de seguridad manual
1. Accede a **Administración**
2. Haz clic en "Crear copia ahora"
3. El archivo se guarda en `data/backups/`

### Restaurar una copia
1. Detén el servidor
2. Reemplaza `data/workshop.db` con el backup
3. Reinicia el servidor

---

## 📞 Soporte

**Si algo no funciona:**

1. Verifica que PHP 8.0+ esté instalado
2. Asegúrate de que SQLite3 esté habilitado en PHP
3. Comprueba que la carpeta `data/` tenga permisos de escritura
4. Revisa el archivo de logs: `data/logs.txt`

---

## 🎨 Personalización

Puedes personalizar:
- Colores (en `style.css`)
- Campos adicionales (en `db.php`)
- Nuevos módulos (en `app.js`)

---

## 📈 Roadmap futuro

- [ ] Exportación a PDF/Excel avanzada
- [ ] Integración con contabilidad
- [ ] Notificaciones por email/SMS
- [ ] App móvil
- [ ] Integración de cámaras (fotos de vehículos)
- [ ] Gestión de proveedores
- [ ] Análisis y reportes avanzados
- [ ] Recordatorios de mantenimiento

---

## 📄 Licencia

Uso libre para talleres pequeños y medianos.

---

**Gestión Taller Pro v2.5** © 2024

*Sistema profesional de gestión para talleres mecánicos*
