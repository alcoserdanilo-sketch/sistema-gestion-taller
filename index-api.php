<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';

// Si no hay sesión, redirige a verificación
if (empty($_SESSION['user_id']) && $_GET['action'] ?? '' !== 'login' && $_GET['action'] ?? '' !== 'session') {
    // Verifica que SQLite esté disponible
    if (!extension_loaded('sqlite3')) {
        header('Location: check.php');
        exit();
    }
}

// Incluye el resto de la lógica de API
require_once __DIR__ . '/api.php';
?>
