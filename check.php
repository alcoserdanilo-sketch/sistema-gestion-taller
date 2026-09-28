<?php
// Archivo de verificación de requisitos del sistema

echo "<!DOCTYPE html>
<html lang='es'>
<head>
    <meta charset='UTF-8'>
    <meta name='viewport' content='width=device-width, initial-scale=1.0'>
    <title>Verificador de Requisitos - Gestión Taller</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
            max-width: 600px;
            width: 100%;
            padding: 40px;
        }
        h1 {
            color: #333;
            margin-bottom: 10px;
            font-size: 28px;
        }
        .subtitle {
            color: #999;
            margin-bottom: 30px;
            font-size: 14px;
        }
        .check-item {
            display: flex;
            align-items: center;
            padding: 15px;
            margin: 10px 0;
            border-radius: 8px;
            background: #f8f9fa;
            border-left: 4px solid #ddd;
        }
        .check-item.success {
            background: #d4edda;
            border-left-color: #28a745;
        }
        .check-item.error {
            background: #f8d7da;
            border-left-color: #dc3545;
        }
        .check-item.warning {
            background: #fff3cd;
            border-left-color: #ffc107;
        }
        .check-icon {
            font-size: 24px;
            margin-right: 15px;
            min-width: 30px;
            text-align: center;
        }
        .check-content {
            flex: 1;
        }
        .check-content h3 {
            margin: 0 0 5px 0;
            font-size: 16px;
            color: #333;
        }
        .check-content p {
            margin: 0;
            font-size: 13px;
            color: #666;
        }
        .solution {
            background: #fff9e6;
            border-left: 4px solid #ffc107;
            padding: 15px;
            margin: 20px 0;
            border-radius: 6px;
            font-size: 13px;
            line-height: 1.6;
        }
        .solution strong {
            display: block;
            color: #ff6b00;
            margin-bottom: 8px;
        }
        .solution code {
            background: #f5f5f5;
            padding: 2px 6px;
            border-radius: 3px;
            font-family: 'Courier New', monospace;
        }
        .button {
            display: inline-block;
            margin-top: 20px;
            padding: 12px 30px;
            background: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 6px;
            text-align: center;
            font-weight: 600;
            transition: all 0.3s;
        }
        .button:hover {
            background: #764ba2;
            transform: translateY(-2px);
        }
        .button.disabled {
            background: #ccc;
            cursor: not-allowed;
            transform: none;
        }
    ";

    echo "</style>
</head>
<body>
    <div class='container'>
        <h1>🔍 Verificador de Requisitos</h1>
        <p class='subtitle'>Gestión Taller Pro v2.5</p>

        <div style='margin-top: 30px;'>
";

    $checks = [
        [
            'name' => 'PHP versión',
            'required' => '8.0+',
            'check' => version_compare(PHP_VERSION, '8.0.0') >= 0,
            'current' => PHP_VERSION,
            'type' => 'version'
        ],
        [
            'name' => 'Extensión SQLite3',
            'required' => 'Habilitada',
            'check' => extension_loaded('sqlite3'),
            'type' => 'extension'
        ],
        [
            'name' => 'Extensión PDO',
            'required' => 'Habilitada',
            'check' => extension_loaded('pdo'),
            'type' => 'extension'
        ],
        [
            'name' => 'JSON',
            'required' => 'Habilitado',
            'check' => extension_loaded('json'),
            'type' => 'extension'
        ],
        [
            'name' => 'Carpeta /data',
            'required' => 'Existe y escribible',
            'check' => is_writable(__DIR__ . '/data') || mkdir(__DIR__ . '/data', 0755, true),
            'type' => 'folder'
        ],
    ];

    $all_ok = true;
    $sqlite_ok = false;

    foreach ($checks as $check) {
        $status = $check['check'] ? 'success' : 'error';
        if (!$check['check']) {
            $all_ok = false;
        }
        if ($check['name'] === 'Extensión SQLite3') {
            $sqlite_ok = $check['check'];
        }

        $icon = $check['check'] ? '✅' : '❌';
        $current = isset($check['current']) ? ' (' . $check['current'] . ')' : '';

        echo "<div class='check-item $status'>
            <div class='check-icon'>$icon</div>
            <div class='check-content'>
                <h3>{$check['name']}</h3>
                <p>Requerido: {$check['required']}$current</p>
            </div>
        </div>";
    }

    if (!$sqlite_ok) {
        echo "<div class='solution'>
            <strong>🔧 SOLUCIÓN: Habilitar SQLite3 en PHP</strong>
            <p>
                <strong>Ubicación del archivo php.ini:</strong><br>
                Busca: <code>C:\\xampp\\php\\php.ini</code>
            </p>
            <p style='margin-top: 10px;'>
                <strong>Pasos:</strong>
                <br>1. Abre el archivo php.ini con Bloc de notas
                <br>2. Busca la línea: <code>;extension=sqlite3</code>
                <br>3. Quítale el punto y coma del inicio: <code>extension=sqlite3</code>
                <br>4. Guarda el archivo
                <br>5. Reinicia Apache desde XAMPP
                <br>6. Recarga esta página
            </p>
        </div>";
    }

    if ($all_ok) {
        echo "<div style='text-align: center; margin-top: 30px;'>
            <div style='font-size: 48px; margin-bottom: 15px;'>🎉</div>
            <p style='font-size: 18px; font-weight: bold; color: #28a745; margin-bottom: 20px;'>Sistema listo para usar</p>
            <a href='index.php' class='button'>Ir a Gestión Taller</a>
        </div>";
    } else {
        echo "<div style='text-align: center; margin-top: 30px;'>
            <p style='font-size: 16px; color: #dc3545; margin-bottom: 20px;'>🛑 Hay requisitos que solucionar</p>
            <a href='check.php' class='button' onclick='location.reload()'>Reintentar</a>
        </div>";
    }

    echo "        </div>
    </div>
</body>
</html>";
?>
