<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';

if (empty($_SESSION['user_id'])) {
    http_response_code(401);
    exit('No autorizado');
}

$db = new WorkshopDB();
$db->init();

$workorder_id = (int) ($_GET['id'] ?? 0);
if ($workorder_id <= 0) {
    exit('Orden de trabajo no encontrada');
}

$workorders = $db->listModule('work_orders');
$workorder = array_values(array_filter($workorders, fn($w) => (int) $w['id'] === $workorder_id))[0] ?? null;

if (!$workorder) {
    exit('Orden de trabajo no encontrada');
}

$company_name = $db->getSetting('company_name', 'Mi Taller');
$company_address = $db->getSetting('company_address', '');
$company_phone = $db->getSetting('company_phone', '');
$company_email = $db->getSetting('company_email', '');
$currency = $db->getSetting('currency', 'EUR');

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Orden de Trabajo <?php echo htmlspecialchars($workorder['number']); ?></title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            max-width: 900px;
            margin: 0 auto;
            padding: 30px 20px;
            background: white;
            color: #333;
        }
        .header {
            display: grid;
            grid-template-columns: 1fr 1fr;
            border-bottom: 4px solid #d97706;
            padding-bottom: 30px;
            margin-bottom: 40px;
        }
        .company-info h1 {
            margin: 0 0 12px;
            color: #d97706;
            font-size: 28px;
        }
        .company-info p {
            margin: 4px 0;
            color: #666;
            font-size: 13px;
        }
        .document-meta {
            text-align: right;
        }
        .document-meta div {
            margin: 8px 0;
        }
        .document-meta .label {
            color: #999;
            font-size: 11px;
            text-transform: uppercase;
        }
        .document-meta .value {
            color: #d97706;
            font-weight: bold;
            font-size: 16px;
        }
        .doc-type {
            text-transform: uppercase;
            font-size: 20px;
            font-weight: bold;
            margin-bottom: 15px;
        }
        .parties {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 40px;
            margin-bottom: 40px;
        }
        .party {
            background: #fffbf0;
            border: 1px solid #fed7aa;
            border-radius: 8px;
            padding: 20px;
        }
        .party h3 {
            margin: 0 0 12px;
            color: #d97706;
            font-size: 12px;
            text-transform: uppercase;
        }
        .party p {
            margin: 6px 0;
            font-size: 13px;
        }
        .details-grid {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 20px;
            margin-bottom: 30px;
            background: #f9f5f0;
            padding: 20px;
            border-radius: 8px;
        }
        .detail-item h4 {
            margin: 0 0 8px;
            color: #d97706;
            font-size: 11px;
            text-transform: uppercase;
        }
        .detail-item p {
            margin: 0;
            font-size: 13px;
            color: #333;
        }
        .issue-section {
            background: #fff9f0;
            border-left: 4px solid #d97706;
            padding: 20px;
            margin-bottom: 30px;
            border-radius: 4px;
        }
        .issue-section h3 {
            margin: 0 0 10px;
            color: #d97706;
            font-size: 13px;
            text-transform: uppercase;
        }
        .issue-section p {
            margin: 0;
            font-size: 13px;
            line-height: 1.6;
            color: #333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        th {
            background: #d97706;
            color: white;
            padding: 14px;
            text-align: left;
            font-size: 12px;
            text-transform: uppercase;
        }
        td {
            padding: 14px;
            border-bottom: 1px solid #fed7aa;
            font-size: 13px;
        }
        .amount-cell {
            text-align: right;
            font-weight: 500;
        }
        .status-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
        }
        .status-abierta {
            background: #fef3c7;
            color: #92400e;
        }
        .status-en_proceso {
            background: #dbeafe;
            color: #1e40af;
        }
        .status-finalizado {
            background: #d1fae5;
            color: #065f46;
        }
        .footer {
            text-align: center;
            color: #999;
            font-size: 11px;
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #fed7aa;
        }
        .actions {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            justify-content: center;
        }
        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            transition: all 0.3s;
        }
        .btn-print {
            background: #d97706;
            color: white;
        }
        .btn-print:hover {
            background: #b45309;
        }
        @media print {
            body { margin: 0; padding: 0; }
            .actions { display: none; }
        }
    </style>
</head>
<body>
    <div class="actions no-print">
        <button class="btn btn-print" onclick="window.print()">🖨️ Imprimir</button>
    </div>

    <div class="header">
        <div class="company-info">
            <h1><?php echo htmlspecialchars($company_name); ?></h1>
            <p><?php echo htmlspecialchars($company_address); ?></p>
            <p>☎️ <?php echo htmlspecialchars($company_phone); ?></p>
        </div>
        <div class="document-meta">
            <div class="doc-type">Orden de Trabajo</div>
            <div>
                <div class="label">Número</div>
                <div class="value"><?php echo htmlspecialchars($workorder['number']); ?></div>
            </div>
            <div>
                <div class="label">Fecha</div>
                <div class="value"><?php echo date('d/m/Y', strtotime($workorder['date'])); ?></div>
            </div>
            <div style="margin-top: 15px;">
                <span class="status-badge status-<?php echo htmlspecialchars($workorder['status']); ?>"><?php echo ucfirst(str_replace('_', ' ', $workorder['status'])); ?></span>
            </div>
        </div>
    </div>

    <div class="parties">
        <div class="party">
            <h3>Cliente</h3>
            <p><strong><?php echo htmlspecialchars($workorder['client_name'] ?? 'N/A'); ?></strong></p>
        </div>
        <div class="party">
            <h3>Vehículo</h3>
            <p><strong><?php echo htmlspecialchars($workorder['vehicle_plate'] ?? 'N/A'); ?></strong></p>
        </div>
    </div>

    <div class="details-grid">
        <div class="detail-item">
            <h4>Asignado a</h4>
            <p><?php echo htmlspecialchars($workorder['assigned_name'] ?? 'No asignado'); ?></p>
        </div>
        <div class="detail-item">
            <h4>Prioridad</h4>
            <p><?php echo ucfirst($workorder['priority'] ?? 'normal'); ?></p>
        </div>
        <div class="detail-item">
            <h4>Horas estimadas</h4>
            <p><?php echo htmlspecialchars($workorder['labor_hours'] ?? '0'); ?> horas</p>
        </div>
    </div>

    <div class="issue-section">
        <h3>Descripción de la avería</h3>
        <p><?php echo nl2br(htmlspecialchars($workorder['issue'] ?? '')); ?></p>
    </div>

    <?php if ($workorder['diagnosis']): ?>
    <div class="issue-section">
        <h3>Diagnóstico</h3>
        <p><?php echo nl2br(htmlspecialchars($workorder['diagnosis'])); ?></p>
    </div>
    <?php endif; ?>

    <table>
        <thead>
            <tr>
                <th>Concepto</th>
                <th style="text-align: right; width: 25%;">Importe (<?php echo htmlspecialchars($currency); ?>)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Mano de obra (<?php echo htmlspecialchars($workorder['labor_hours'] ?? '0'); ?> horas)</td>
                <td class="amount-cell"><?php echo number_format((float) $workorder['labor_amount'], 2, ',', '.'); ?></td>
            </tr>
            <tr>
                <td>Repuestos</td>
                <td class="amount-cell"><?php echo number_format((float) $workorder['parts_amount'], 2, ',', '.'); ?></td>
            </tr>
            <tr style="background: #f9f5f0;">
                <td><strong>Subtotal</strong></td>
                <td class="amount-cell"><strong><?php echo number_format((float) $workorder['labor_amount'] + (float) $workorder['parts_amount'], 2, ',', '.'); ?></strong></td>
            </tr>
            <tr>
                <td>IVA (21%)</td>
                <td class="amount-cell"><?php echo number_format((float) $workorder['tax_amount'], 2, ',', '.'); ?></td>
            </tr>
            <tr style="background: #fed7aa; font-weight: bold; font-size: 14px;">
                <td>Total</td>
                <td class="amount-cell"><?php echo number_format((float) $workorder['total_amount'], 2, ',', '.'); ?></td>
            </tr>
        </tbody>
    </table>

    <?php if ($workorder['notes']): ?>
    <div class="issue-section">
        <h3>Notas adicionales</h3>
        <p><?php echo nl2br(htmlspecialchars($workorder['notes'])); ?></p>
    </div>
    <?php endif; ?>

    <div class="footer">
        <p>Documento generado automáticamente el <?php echo date('d/m/Y H:i:s'); ?></p>
        <p>Gestión Taller Pro v2.5 - Sistema de gestión para talleres</p>
    </div>
</body>
</html>
