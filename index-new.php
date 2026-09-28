<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';

// Verifica requisitos
if (!extension_loaded('sqlite3')) {
    header('Location: check.php');
    exit();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión Taller Pro v2.5</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div id="login-screen" class="auth-screen">
        <div class="auth-box">
            <h1>🔧 Gestión Taller</h1>
            <p class="subtitle">Sistema profesional para talleres mecánicos</p>
            <form id="loginForm" class="auth-form">
                <label>
                    <span>Usuario</span>
                    <input type="text" id="username" name="username" placeholder="admin" required autofocus>
                </label>
                <label>
                    <span>Contraseña</span>
                    <input type="password" id="password" name="password" placeholder="admin123" required>
                </label>
                <button type="submit">🔓 Entrar</button>
            </form>
            <div class="demo-info">
                <strong>Acceso demo:</strong><br>
                Usuario: <code>admin</code><br>
                Contraseña: <code>admin123</code>
            </div>
        </div>
    </div>

    <div id="app-screen" class="app-layout hidden">
        <aside class="sidebar">
            <div class="brand">
                <h2>🔧 TallerApp</h2>
                <p class="version">v2.5</p>
            </div>
            <nav class="nav">
                <button class="nav-btn active" data-section="dashboard">📊 Dashboard</button>
                <button class="nav-btn" data-section="clients">👥 Clientes</button>
                <button class="nav-btn" data-section="vehicles">🚗 Vehículos</button>
                <button class="nav-btn" data-section="budgets">📝 Presupuestos</button>
                <button class="nav-btn" data-section="work_orders">⚙️ Órdenes</button>
                <button class="nav-btn" data-section="invoices">💳 Facturas</button>
                <button class="nav-btn" data-section="repairs">🔍 Reparaciones</button>
                <button class="nav-btn" data-section="agenda">📅 Agenda</button>
                <button class="nav-btn" data-section="spare_parts">⚙️ Repuestos</button>
                <button class="nav-btn" data-section="stock">📦 Stock</button>
                <button class="nav-btn" data-section="purchases">🛒 Compras</button>
                <button class="nav-btn" data-section="admin" id="admin-btn" style="display:none; border-top: 1px solid rgba(255,255,255,0.1); margin-top: 15px; padding-top: 15px;">⚙️ Administración</button>
            </nav>
        </aside>

        <main class="content">
            <header class="topbar">
                <div>
                    <span class="label">Usuario</span>
                    <strong id="userLabel">-</strong>
                </div>
                <button id="logoutBtn" class="logout-btn">🚪 Cerrar sesión</button>
            </header>

            <section id="dashboard-section" class="panel active"></section>
            <section id="clients-section" class="panel hidden"></section>
            <section id="vehicles-section" class="panel hidden"></section>
            <section id="budgets-section" class="panel hidden"></section>
            <section id="work_orders-section" class="panel hidden"></section>
            <section id="invoices-section" class="panel hidden"></section>
            <section id="agenda-section" class="panel hidden"></section>
            <section id="spare_parts-section" class="panel hidden"></section>
            <section id="purchases-section" class="panel hidden"></section>
            <section id="stock-section" class="panel hidden"></section>
            <section id="repairs-section" class="panel hidden"></section>
            <section id="admin-section" class="panel hidden"></section>
        </main>
    </div>

    <script src="app.js"></script>
</body>
</html>
