<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';

function requestData(): array {
    if (!empty($_POST)) {
        return $_POST;
    }

    $raw = file_get_contents('php://input');
    if ($raw === false || $raw === '') {
        return [];
    }

    $decoded = json_decode($raw, true);
    return is_array($decoded) ? $decoded : [];
}

$db = new WorkshopDB();
$db->init();

$action = $_GET['action'] ?? 'dashboard';
$payload = requestData();

// Login
if ($action === 'login') {
    $username = trim((string) ($payload['username'] ?? ''));
    $password = (string) ($payload['password'] ?? '');

    if ($username === '' || $password === '') {
        jsonResponse(['success' => false, 'error' => 'Usuario y contraseña obligatorios.']);
    }

    $user = $db->getUserByUsername($username);
    if (!$user || !password_verify($password, $user['password_hash'])) {
        jsonResponse(['success' => false, 'error' => 'Credenciales inválidas.']);
    }

    if ($user['status'] !== 'activo') {
        jsonResponse(['success' => false, 'error' => 'Usuario desactivado.']);
    }

    $_SESSION['user_id'] = (int) $user['id'];
    $_SESSION['user_name'] = $user['name'];
    $_SESSION['user_username'] = $user['username'];
    $_SESSION['user_role'] = $user['role'];

    $db->updateLastLogin((int) $user['id']);
    logEvent('LOGIN', 'Inicio de sesión exitoso');

    jsonResponse([
        'success' => true,
        'user' => [
            'id' => (int) $user['id'],
            'name' => $user['name'],
            'username' => $user['username'],
            'role' => $user['role'],
        ]
    ]);
}

// Check session
if ($action === 'session') {
    if (empty($_SESSION['user_id'])) {
        jsonResponse(['success' => false, 'logged' => false]);
    }

    jsonResponse([
        'success' => true,
        'logged' => true,
        'user' => [
            'id' => (int) $_SESSION['user_id'],
            'name' => $_SESSION['user_name'],
            'username' => $_SESSION['user_username'],
            'role' => $_SESSION['user_role'],
        ]
    ]);
}

// Logout
if ($action === 'logout') {
    logEvent('LOGOUT', 'Cierre de sesión');
    session_unset();
    session_destroy();
    jsonResponse(['success' => true]);
}

requireAuth();

// Dashboard
if ($action === 'dashboard') {
    jsonResponse(['success' => true, 'data' => $db->dashboardData((int) $_SESSION['user_id'])]);
}

// Módulos CRUD
if (in_array($action, ['clients', 'vehicles', 'budgets', 'work_orders', 'invoices', 'agenda', 'spare_parts', 'purchases', 'stock', 'repairs', 'users'])) {
    $mode = $payload['mode'] ?? 'list';
    
    if ($mode === 'list') {
        jsonResponse(['success' => true, 'data' => $db->listModule($action, $payload)]);
    }

    if ($mode === 'save') {
        $record = $payload;
        unset($record['mode']);
        jsonResponse(['success' => true, 'data' => $db->saveModule($action, $record)]);
    }

    if ($mode === 'delete') {
        $id = (int) ($payload['id'] ?? 0);
        jsonResponse(['success' => $db->deleteModule($action, $id)]);
    }
}

// Settings
if ($action === 'settings') {
    $mode = $payload['mode'] ?? 'get';
    
    if ($mode === 'get') {
        $settings = [];
        foreach (['company_name', 'company_address', 'company_phone', 'company_email', 'currency', 'tax_rate', 'labor_hourly_rate'] as $key) {
            $settings[$key] = $db->getSetting($key);
        }
        jsonResponse(['success' => true, 'data' => $settings]);
    }

    if ($mode === 'save') {
        foreach ($payload as $key => $value) {
            if ($key !== 'mode') {
                $db->setSetting($key, $value);
            }
        }
        logEvent('SETTINGS', 'Configuración actualizada');
        jsonResponse(['success' => true]);
    }
}

// Backup
if ($action === 'backup') {
    if ($_SESSION['user_role'] !== 'admin') {
        jsonResponse(['success' => false, 'error' => 'Permiso denegado']);
    }
    $backup_file = $db->createBackup();
    jsonResponse(['success' => true, 'file' => basename($backup_file)]);
}

jsonResponse(['success' => false, 'error' => 'Acción no válida.']);
