<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';

if (empty($_SESSION['user_id'])) {
    http_response_code(401);
    exit('No autorizado');
}

$db = new WorkshopDB();
$db->init();

$budget_id = (int) ($_GET['id'] ?? 0);
if ($budget_id <= 0) {
    exit('Presupuesto no encontrado');
}

$budgets = $db->listModule('budgets');
$budget = array_values(array_filter($budgets, fn($b) => (int) $b['id'] === $budget_id))[0] ?? null;

if (!$budget) {
    exit('Presupuesto no encontrado');
}

$company_name = $db->getSetting('company_name', 'Mi Taller');
$company_address = $db->getSetting('company_address', '');
$company_phone = $db->getSetting('company_phone', '');
$company_email = $db->getSetting('company_email', '');
$company_tax_id = $db->getSetting('company_tax_id', '');
$currency = $db->getSetting('currency', 'EUR');

?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Presupuesto <?php echo htmlspecialchars($budget['number']); ?></title>
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
            border-bottom: 4px solid #0f6cbd;
            padding-bottom: 30px;
            margin-bottom: 40px;
        }
        .company-info h1 {
            margin: 0 0 12px;
            color: #0f6cbd;
            font-size: 28px;
        }
        .company-info p {
            margin: 4px 0;
            color: #666;
            font-size: 13px;
            line-height: 1.6;
        }
        .document-meta {
            text-align: right;
        }
        .document-meta div {
            margin: 8px 0;
            font-size: 14px;
        }
        .document-meta .label {
            color: #999;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .document-meta .value {
            color: #0f6cbd;
            font-weight: bold;
            font-size: 16px;
            margin-top: 2px;
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
            page-break-inside: avoid;
        }
        .party {
            background: #f9fbff;
            border: 1px solid #e0e8f5;
            border-radius: 8px;
            padding: 20px;
        }
        .party h3 {
            margin: 0 0 12px;
            color: #0f6cbd;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .party p {
            margin: 6px 0;
            font-size: 13px;
            line-height: 1.6;
        }
        .vehicle-info {
            background: #fff9f0;
            border: 1px solid #ffe8d0;
            border-radius: 8px;
            padding: 15px;
            margin-bottom: 30px;
            page-break-inside: avoid;
        }
        .vehicle-info h4 {
            margin: 0 0 10px;
            color: #d97706;
            font-size: 12px;
            text-transform: uppercase;
        }
        .vehicle-grid {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 15px;
            font-size: 13px;
        }
        .vehicle-grid div {
            display: flex;
            flex-direction: column;
        }
        .vehicle-grid .label {
            color: #999;
            font-size: 11px;
            text-transform: uppercase;
            margin-bottom: 3px;
        }
        .vehicle-grid .value {
            color: #333;
            font-weight: 500;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
            page-break-inside: avoid;
        }
        th {
            background: #0f6cbd;
            color: white;
            padding: 14px;
            text-align: left;
            font-size: 12px;
            text-transform: uppercase;
            font-weight: 600;
            letter-spacing: 0.5px;
        }
        td {
            padding: 14px;
            border-bottom: 1px solid #e0e8f5;
            font-size: 13px;
        }
        tr:hover {
            background: #f9fbff;
        }
        .amount-cell {
            text-align: right;
            font-weight: 500;
            color: #0f6cbd;
        }
        .summary {
            display: grid;
            grid-template-columns: 1fr 320px;
            gap: 30px;
            margin-bottom: 40px;
            page-break-inside: avoid;
        }
        .notes {
            background: #f0f5fc;
            border-left: 4px solid #0f6cbd;
            padding: 20px;
            border-radius: 4px;
        }
        .notes h4 {
            margin: 0 0 10px;
            color: #0f6cbd;
            font-size: 13px;
            text-transform: uppercase;
        }
        .notes p {
            margin: 0;
            font-size: 13px;
            line-height: 1.6;
            color: #333;
        }
        .totals-box {
            background: linear-gradient(135deg, #f0f5fc 0%, #e8eef8 100%);
            border: 2px solid #0f6cbd;
            border-radius: 8px;
            padding: 20px;
        }
        .total-row {
            display: flex;
            justify-content: space-between;
            margin-bottom: 12px;
            font-size: 13px;
        }
        .total-row .label {
            color: #666;
        }
        .total-row .amount {
            color: #333;
            font-weight: 600;
        }
        .total-row.separator {
            border-top: 1px solid #d9e6ff;
            padding-top: 12px;
            margin-top: 12px;
        }
        .total-amount {
            display: flex;
            justify-content: space-between;
            font-size: 18px;
            font-weight: bold;
            color: #0f6cbd;
            background: white;
            padding: 15px;
            border-radius: 6px;
            margin-top: 12px;
        }
        .footer {
            text-align: center;
            color: #999;
            font-size: 11px;
            margin-top: 60px;
            padding-top: 20px;
            border-top: 1px solid #e0e8f5;
            page-break-inside: avoid;
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
            background: #0f6cbd;
            color: white;
        }
        .btn-print:hover {
            background: #0a4d87;
        }
        .btn-download {
            background: #2d9f6b;
            color: white;
        }
        .btn-download:hover {
            background: #1f6f4d;
        }
        .status-badge {
            display: inline-block;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .status-pendiente {
            background: #fef3c7;
            color: #92400e;
        }
        .status-aceptado {
            background: #d1fae5;
            color: #065f46;
        }
        .status-rechazado {
            background: #fee2e2;
            color: #7f1d1d;
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
        <button class="btn btn-download" onclick="descargarPDF()">⬇️ Descargar PDF</button>
    </div>

    <div class="header">
        <div class="company-info">
            <h1><?php echo htmlspecialchars($company_name); ?></h1>
            <p><?php echo htmlspecialchars($company_address); ?></p>
            <p>☎️ <?php echo htmlspecialchars($company_phone); ?></p>
            <p>✉️ <?php echo htmlspecialchars($company_email); ?></p>
            <?php if ($company_tax_id): ?>
            <p>CIF/NIF: <?php echo htmlspecialchars($company_tax_id); ?></p>
            <?php endif; ?>
        </div>
        <div class="document-meta">
            <div class="doc-type">Presupuesto</div>
            <div>
                <div class="label">Número</div>
                <div class="value"><?php echo htmlspecialchars($budget['number']); ?></div>
            </div>
            <div>
                <div class="label">Fecha</div>
                <div class="value"><?php echo date('d/m/Y', strtotime($budget['date'])); ?></div>
            </div>
            <div>
                <div class="label">Válido hasta</div>
                <div class="value"><?php echo $budget['valid_until'] ? date('d/m/Y', strtotime($budget['valid_until'])) : '30 días'; ?></div>
            </div>
            <div style="margin-top: 15px;">
                <span class="status-badge status-<?php echo htmlspecialchars($budget['status']); ?>"><?php echo ucfirst($budget['status']); ?></span>
            </div>
        </div>
    </div>

    <div class="parties">
        <div class="party">
            <h3>Cliente</h3>
            <p><strong><?php echo htmlspecialchars($budget['client_name'] ?? 'N/A'); ?></strong></p>
            <p><?php echo htmlspecialchars($budget['client_address'] ?? ''); ?></p>
            <p><?php echo htmlspecialchars($budget['client_city'] ?? ''); ?></p>
        </div>
        <div class="party">
            <h3>Vehículo</h3>
            <p><strong><?php echo htmlspecialchars($budget['vehicle_plate'] ?? 'N/A'); ?></strong></p>
            <p><?php echo htmlspecialchars($budget['vehicle_brand'] ?? '') . ' ' . htmlspecialchars($budget['vehicle_model'] ?? ''); ?></p>
        </div>
    </div>

    <div class="vehicle-info">
        <h4>Detalles del vehículo</h4>
        <div class="vehicle-grid">
            <div>
                <span class="label">Matrícula</span>
                <span class="value"><?php echo htmlspecialchars($budget['vehicle_plate'] ?? '-'); ?></span>
            </div>
            <div>
                <span class="label">Marca/Modelo</span>
                <span class="value"><?php echo htmlspecialchars($budget['vehicle_brand'] ?? '') . ' ' . htmlspecialchars($budget['vehicle_model'] ?? ''); ?></span>
            </div>
            <div>
                <span class="label">Año</span>
                <span class="value"><?php echo htmlspecialchars($budget['vehicle_year'] ?? '-'); ?></span>
            </div>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Descripción del trabajo</th>
                <th style="text-align: right; width: 30%;">Importe (<?php echo htmlspecialchars($currency); ?>)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Trabajo</td>
                <td class="amount-cell"><?php echo number_format((float) $budget['labor_amount'], 2, ',', '.'); ?></td>
            </tr>
            <tr>
                <td>Repuestos</td>
                <td class="amount-cell"><?php echo number_format((float) $budget['parts_amount'], 2, ',', '.'); ?></td>
            </tr>
        </tbody>
    </table>

    <div class="summary">
        <div class="notes">
            <h4>Observaciones</h4>
            <p><?php echo nl2br(htmlspecialchars($budget['notes'] ?? 'Sin observaciones')); ?></p>
        </div>
        <div class="totals-box">
            <div class="total-row">
                <span class="label">Trabajo</span>
                <span class="amount"><?php echo number_format((float) $budget['labor_amount'], 2, ',', '.'); ?> <?php echo htmlspecialchars($currency); ?></span>
            </div>
            <div class="total-row">
                <span class="label">Repuestos</span>
                <span class="amount"><?php echo number_format((float) $budget['parts_amount'], 2, ',', '.'); ?> <?php echo htmlspecialchars($currency); ?></span>
            </div>
            <div class="total-row separator">
                <span class="label">Subtotal</span>
                <span class="amount"><?php echo number_format((float) $budget['labor_amount'] + (float) $budget['parts_amount'], 2, ',', '.'); ?> <?php echo htmlspecialchars($currency); ?></span>
            </div>
            <div class="total-row">
                <span class="label">IVA (21%)</span>
                <span class="amount"><?php echo number_format((float) $budget['tax_amount'], 2, ',', '.'); ?> <?php echo htmlspecialchars($currency); ?></span>
            </div>
            <div class="total-amount">
                <span>Total</span>
                <span><?php echo number_format((float) $budget['total_amount'], 2, ',', '.'); ?></span>
            </div>
        </div>
    </div>

    <div class="footer">
        <p>Documento generado automáticamente el <?php echo date('d/m/Y H:i:s'); ?></p>
        <p>Gestión Taller Pro v2.5 - Sistema de gestión para talleres</p>
        <p style="margin-top: 10px; color: #ccc; font-size: 10px;">Este presupuesto es válido hasta la fecha indicada. Para cualquier duda, contacte con nosotros.</p>
    </div>

    <script>
        function descargarPDF() {
            alert('Función de descarga PDF disponible en próximas versiones');
        }
    </script>
</body>
</html>
