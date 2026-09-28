<?php
// Configuración general del sistema
define('APP_NAME', 'Gestión Taller Pro');
define('APP_VERSION', '2.0');
define('DB_FILE', __DIR__ . '/data/workshop.db');
define('LOG_FILE', __DIR__ . '/data/logs.txt');
define('BACKUP_DIR', __DIR__ . '/data/backups');

// Crear directorio de datos si no existe
if (!is_dir(__DIR__ . '/data')) {
    mkdir(__DIR__ . '/data', 0755, true);
}

if (!is_dir(BACKUP_DIR)) {
    mkdir(BACKUP_DIR, 0755, true);
}

// Configuración de sesión
session_start();
ini_set('session.gc_maxlifetime', 86400);
ini_set('session.cookie_httponly', 1);
ini_set('session.cookie_samesite', 'Strict');

// Manejo de errores
set_error_handler(function($errno, $errstr, $errfile, $errline) {
    error_log("[$errno] $errstr in $errfile:$errline");
    return true;
});

function logEvent($action, $details = '') {
    $timestamp = date('Y-m-d H:i:s');
    $user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'GUEST';
    $log_entry = "[$timestamp] User:$user_id | Action:$action | Details:$details\n";
    file_put_contents(LOG_FILE, $log_entry, FILE_APPEND);
}

function requireAuth() {
    if (empty($_SESSION['user_id'])) {
        http_response_code(401);
        exit(json_encode(['success' => false, 'error' => 'No autorizado']));
    }
}

function jsonResponse(array $payload) {
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($payload, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    exit;
}
