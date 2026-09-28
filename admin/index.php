<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';

if (empty($_SESSION['user_id']) || $_SESSION['user_role'] !== 'admin') {
    header('Location: index.php');
    exit();
}

$db = new WorkshopDB();
$db->init();

$users = $db->listModule('users');
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administración - Gestión Taller Pro</title>
    <link rel="stylesheet" href="../style.css">
    <style>
        .admin-panel {
            background: #fff3e0;
            border-left: 4px solid #d97706;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 8px;
        }
        .admin-panel h2 {
            color: #d97706;
            margin-top: 0;
        }
        .user-card {
            background: white;
            border: 1px solid #fed7aa;
            padding: 15px;
            margin: 10px 0;
            border-radius: 6px;
            display: grid;
            grid-template-columns: 1fr auto;
            align-items: center;
        }
        .user-info h4 {
            margin: 0 0 5px;
            color: #333;
        }
        .user-info p {
            margin: 3px 0;
            font-size: 0.9em;
            color: #666;
        }
        .role-badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 0.8em;
            font-weight: 600;
        }
        .role-admin {
            background: #f87171;
            color: white;
        }
        .role-mecanico {
            background: #60a5fa;
            color: white;
        }
        .role-recepcion {
            background: #34d399;
            color: white;
        }
        .btn-small {
            padding: 8px 12px;
            font-size: 0.85em;
            margin: 2px;
        }
    </style>
</head>
<body>
    <div id="admin-screen" class="app-layout">
        <aside class="sidebar">
            <div class="brand">
                <h2>⚙️ Admin</h2>
            </div>
            <nav class="nav">
                <a href="../index.php" class="nav-btn">← Volver al sistema</a>
            </nav>
        </aside>

        <main class="content">
            <header class="topbar">
                <div>
                    <span class="label">Administración del Sistema</span>
                    <strong>Gestión Taller Pro v2.5</strong>
                </div>
                <a href="../index.php?logout=1" class="logout-btn">Cerrar</a>
            </header>

            <div class="admin-panel">
                <h2>👥 Gestión de Usuarios</h2>
                <div id="users-list">
                    <?php foreach ($users as $user): ?>
                    <div class="user-card">
                        <div class="user-info">
                            <h4><?php echo htmlspecialchars($user['name']); ?></h4>
                            <p><strong>Usuario:</strong> <?php echo htmlspecialchars($user['username']); ?></p>
                            <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email'] ?? 'N/A'); ?></p>
                            <p><strong>Rol:</strong> <span class="role-badge role-<?php echo htmlspecialchars($user['role']); ?>"><?php echo ucfirst($user['role']); ?></span></p>
                            <p><strong>Estado:</strong> <span style="color: <?php echo $user['status'] === 'activo' ? '#2d9f6b' : '#d93c3c'; ?>"><?php echo ucfirst($user['status']); ?></span></p>
                            <p><strong>Último acceso:</strong> <?php echo $user['last_login'] ? date('d/m/Y H:i', strtotime($user['last_login'])) : 'Nunca'; ?></p>
                        </div>
                        <div>
                            <button class="btn-small action-btn" onclick="editUser(<?php echo (int) $user['id']; ?>)">✏️ Editar</button>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="admin-panel">
                <h2>💾 Copias de Seguridad</h2>
                <p>Las copias de seguridad se crean automáticamente diariamente. Última copia: <?php echo date('d/m/Y H:i', filemtime(DB_FILE)); ?></p>
                <button class="btn-small" onclick="crearBackup()">🔄 Crear copia ahora</button>
            </div>

            <div class="admin-panel">
                <h2>⚙️ Configuración del Taller</h2>
                <form id="settingsForm" class="module-form">
                    <label>
                        <span>Nombre del taller</span>
                        <input type="text" id="company_name" name="company_name">
                    </label>
                    <label>
                        <span>Dirección</span>
                        <input type="text" id="company_address" name="company_address">
                    </label>
                    <label>
                        <span>Teléfono</span>
                        <input type="tel" id="company_phone" name="company_phone">
                    </label>
                    <label>
                        <span>Email</span>
                        <input type="email" id="company_email" name="company_email">
                    </label>
                    <label>
                        <span>CIF/NIF</span>
                        <input type="text" id="company_tax_id" name="company_tax_id">
                    </label>
                    <label>
                        <span>Moneda</span>
                        <select id="currency" name="currency">
                            <option value="EUR">EUR (€)</option>
                            <option value="USD">USD ($)</option>
                            <option value="GBP">GBP (£)</option>
                        </select>
                    </label>
                    <label>
                        <span>Tarifa horaria de mano de obra (€)</span>
                        <input type="number" id="labor_hourly_rate" name="labor_hourly_rate" step="0.01">
                    </label>
                    <label>
                        <span>Tipo impositivo (%)</span>
                        <input type="number" id="tax_rate" name="tax_rate" step="0.01">
                    </label>
                    <button type="submit" class="primary">Guardar configuración</button>
                </form>
            </div>
        </main>
    </div>

    <script>
        const API = '../api.php';

        async function crearBackup() {
            const response = await fetch(API + '?action=backup', { method: 'POST' });
            const data = await response.json();
            if (data.success) {
                alert('✓ Copia de seguridad creada: ' + data.file);
            } else {
                alert('Error: ' + (data.error || 'No se pudo crear'));
            }
        }

        async function editUser(userId) {
            const newRole = prompt('Nuevo rol (admin/mecanico/recepcion):', 'mecanico');
            if (newRole) {
                // Implementar en versión futura
                alert('Edición de usuario disponible en próximas versiones');
            }
        }

        async function cargarConfiguracion() {
            const response = await fetch(API + '?action=settings&mode=get', { method: 'POST' });
            const data = await response.json();
            if (data.success) {
                Object.entries(data.data).forEach(([key, value]) => {
                    const input = document.getElementById(key);
                    if (input) input.value = value;
                });
            }
        }

        document.getElementById('settingsForm').addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(e.target);
            const payload = Object.fromEntries(formData);
            payload.mode = 'save';

            const response = await fetch(API + '?action=settings&mode=save', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(payload)
            });
            const data = await response.json();
            if (data.success) {
                alert('✓ Configuración guardada');
            } else {
                alert('Error: ' + (data.error || 'No se pudo guardar'));
            }
        });

        cargarConfiguracion();
    </script>
</body>
</html>
