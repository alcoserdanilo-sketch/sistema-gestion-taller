<?php
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';

if (empty($_SESSION['user_id'])) {
    http_response_code(401);
    exit('No autorizado');
}

$db = new WorkshopDB();
$db->init();

$invoice_id = (int) ($_GET['id'] ?? 0);
if ($invoice_id <= 0) {
    exit('Factura no encontrada');
}

$invoices = $db->listModule('invoices');
$invoice = array_values(array_filter($invoices, fn($i) => (int) $i['id'] === $invoice_id))[0] ?? null;

if (!$invoice) {
    exit('Factura no encontrada');
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
    <title>Factura <?php echo htmlspecialchars($invoice['number']); ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background: white;
        }
        .invoice-header {
            display: grid;
            grid-template-columns: 1fr 1fr;
            border-bottom: 3px solid #0f6cbd;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .company-info h1 {
            margin: 0;
            color: #0f6cbd;
        }
        .company-info p {
            margin: 5px 0;
            color: #666;
            font-size: 0.9em;
        }
        .invoice-meta {
            text-align: right;
        }
        .invoice-meta div {
            margin: 5px 0;
        }
        .invoice-meta strong {
            color: #0f6cbd;
        }
        .invoice-details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 30px;
            page-break-inside: avoid;
        }
        .invoice-details h3 {
            margin: 0 0 10px;
            color: #0f6cbd;
            font-size: 0.85em;
            text-transform: uppercase;
        }
        .invoice-details p {
            margin: 3px 0;
            font-size: 0.9em;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        th {
            background: #0f6cbd;
            color: white;
            padding: 10px;
            text-align: left;
            font-size: 0.85em;
        }
        td {
            padding: 10px;
            border-bottom: 1px solid #ddd;
        }
        .amount {
            text-align: right;
        }
        .summary {
            display: grid;
            grid-template-columns: 1fr 200px;
            gap: 20px;
            margin-bottom: 20px;
        }
        .totals {
            background: #f0f5fc;
            padding: 15px;
            border-radius: 5px;
            page-break-inside: avoid;
        }
        .totals div {
            display: flex;
            justify-content: space-between;
            margin: 5px 0;
        }
        .total-amount {
            font-size: 1.5em;
            font-weight: bold;
            color: #0f6cbd;
            border-top: 2px solid #0f6cbd;
            padding-top: 10px;
            margin-top: 10px;
        }
        .footer {
            text-align: center;
            color: #999;
            font-size: 0.8em;
            margin-top: 40px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
        }
        @media print {
            body { margin: 0; padding: 0; }
            .no-print { display: none; }
        }
        .print-btn {
            display: block;
            margin: 10px 0;
            padding: 10px 20px;
            background: #0f6cbd;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1em;
        }
        .print-btn:hover {
            background: #0a4d87;
        }
    </style>
</head>
<body>
    <button class="print-btn no-print" onclick="window.print()">Imprimir</button>

    <div class="invoice-header">
        <div class="company-info">
            <h1><?php echo htmlspecialchars($company_name); ?></h1>
            <p><?php echo htmlspecialchars($company_address); ?></p>
            <p><?php echo htmlspecialchars($company_phone); ?></p>
            <p><?php echo htmlspecialchars($company_email); ?></p>
        </div>
        <div class="invoice-meta">
            <div><strong>FACTURA</strong></div>
            <div><?php echo htmlspecialchars($invoice['number']); ?></div>
            <div style="margin-top: 10px;">
                <div>Fecha: <?php echo date('d/m/Y', strtotime($invoice['date'])); ?></div>
                <div>Vencimiento: <?php echo $invoice['due_date'] ? date('d/m/Y', strtotime($invoice['due_date'])) : '-'; ?></div>
            </div>
        </div>
    </div>

    <div class="invoice-details">
        <div>
            <h3>Cliente</h3>
            <p><?php echo htmlspecialchars($invoice['client_name'] ?? 'N/A'); ?></p>
        </div>
        <div>
            <h3>Estado</h3>
            <p><?php echo ucfirst(str_replace('_', ' ', $invoice['status'])); ?></p>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th>Concepto</th>
                <th class="amount">Importe (<?php echo htmlspecialchars($currency); ?>)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Mano de obra</td>
                <td class="amount"><?php echo number_format((float) $invoice['labor_amount'], 2, ',', '.'); ?></td>
            </tr>
            <tr>
                <td>Repuestos</td>
                <td class="amount"><?php echo number_format((float) $invoice['parts_amount'], 2, ',', '.'); ?></td>
            </tr>
            <tr style="background: #f5f5f5;">
                <td><strong>Subtotal</strong></td>
                <td class="amount"><strong><?php echo number_format((float) $invoice['labor_amount'] + (float) $invoice['parts_amount'], 2, ',', '.'); ?></strong></td>
            </tr>
        </tbody>
    </table>

    <div class="summary">
        <div></div>
        <div class="totals">
            <div>
                <span>Base imponible</span>
                <span><?php echo number_format((float) $invoice['labor_amount'] + (float) $invoice['parts_amount'], 2, ',', '.'); ?></span>
            </div>
            <div>
                <span>IVA (<?php echo htmlspecialchars($invoice['tax_rate']); ?>%)</span>
                <span><?php echo number_format((float) $invoice['tax_amount'], 2, ',', '.'); ?></span>
            </div>
            <div class="total-amount">
                <span>Total</span>
                <span><?php echo number_format((float) $invoice['total_amount'], 2, ',', '.'); ?></span>
            </div>
        </div>
    </div>

    <div class="footer">
        <p>Documento generado automáticamente el <?php echo date('d/m/Y H:i'); ?></p>
        <p>Gestión Taller Pro v2.0</p>
    </div>
</body>
</html>
