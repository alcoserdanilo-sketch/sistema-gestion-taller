<?php
require_once __DIR__ . '/config.php';

if (isset($_GET['action']) && $_GET['action'] === 'list') {
    $backup_dir = BACKUP_DIR;
    $files = array_filter(scandir($backup_dir), fn($f) => strpos($f, 'backup_') === 0 && pathinfo($f, PATHINFO_EXTENSION) === 'db');
    rsort($files);
    
    echo '<pre>';
    echo "Copias de seguridad disponibles:\n\n";
    foreach ($files as $file) {
        $filepath = $backup_dir . '/' . $file;
        $size = filesize($filepath);
        $date = date('d/m/Y H:i:s', filemtime($filepath));
        printf("- %s (Tamaño: %.2f MB | Fecha: %s)\n", $file, $size / (1024 * 1024), $date);
    }
    echo '</pre>';
}
?>
