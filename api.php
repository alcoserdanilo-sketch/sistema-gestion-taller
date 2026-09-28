<?php
require_once __DIR__ . '/db.php';

session_start();

function jsonResponse(array $payload): void {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    exit;
}

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

    $_SESSION['user_id'] = (int) $user['id'];
    $_SESSION['user_name'] = $user['name'];
    $_SESSION['user_username'] = $user['username'];
    jsonResponse([
        'success' => true,
        'user' => [
            'id' => (int) $user['id'],
            'name' => $user['name'],
            'username' => $user['username'],
        ]
    ]);
}

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
        ]
    ]);
}

if ($action === 'logout') {
    session_unset();
    session_destroy();
    jsonResponse(['success' => true]);
}

if (empty($_SESSION['user_id'])) {
    jsonResponse(['success' => false, 'error' => 'Sesión no válida.']);
}

switch ($action) {
    case 'dashboard':
        jsonResponse(['success' => true, 'data' => $db->dashboardData()]);
        break;

    case 'clients':
    case 'vehicles':
    case 'budgets':
    case 'work_orders':
    case 'invoices':
    case 'agenda':
    case 'spare_parts':
    case 'purchases':
    case 'stock':
    case 'repairs':
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
        break;

    default:
        jsonResponse(['success' => false, 'error' => 'Acción no válida.']);
}

