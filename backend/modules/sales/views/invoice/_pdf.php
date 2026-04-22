<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Invoice <?= htmlspecialchars($model->invoice_number) ?></title>
<style>
    * { box-sizing: border-box; margin: 0; padding: 0; }

    body {
        font-family: 'Segoe UI', Arial, sans-serif;
        font-size: 13px;
        color: #333;
        background: #fff;
        padding: 40px;
    }

    /* ── HEADER ── */
    .inv-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 36px;
        padding-bottom: 20px;
        border-bottom: 2px solid #1a56db;
    }
    .inv-brand h1 {
        font-size: 26px;
        font-weight: 700;
        color: #1a56db;
        letter-spacing: 1px;
    }
    .inv-brand p {
        font-size: 11px;
        color: #666;
        margin-top: 2px;
    }
    .inv-label {
        text-align: right;
    }
    .inv-label h2 {
        font-size: 28px;
        font-weight: 700;
        color: #1a56db;
        text-transform: uppercase;
        letter-spacing: 3px;
    }
    .inv-label .inv-number {
        font-size: 13px;
        color: #555;
        margin-top: 4px;
    }
    .inv-label .status-badge {
        display: inline-block;
        margin-top: 6px;
        padding: 3px 12px;
        border-radius: 20px;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    .status-draft    { background: #e5e7eb; color: #374151; }
    .status-sent     { background: #dbeafe; color: #1e40af; }
    .status-paid     { background: #d1fae5; color: #065f46; }
    .status-overdue  { background: #fee2e2; color: #991b1b; }

    /* ── META ROW (Bill To / Dates) ── */
    .inv-meta {
        display: flex;
        justify-content: space-between;
        margin-bottom: 32px;
        gap: 24px;
    }
    .inv-meta-block { flex: 1; }
    .inv-meta-block h4 {
        font-size: 10px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1px;
        color: #1a56db;
        margin-bottom: 6px;
    }
    .inv-meta-block p {
        font-size: 13px;
        color: #333;
        line-height: 1.6;
    }
    .inv-meta-block .label {
        font-size: 10px;
        color: #888;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-top: 8px;
    }

    /* ── ITEMS TABLE ── */
    table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 24px;
    }
    thead tr {
        background: #1a56db;
        color: #fff;
    }
    thead th {
        padding: 10px 12px;
        font-size: 11px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }
    thead th.text-right { text-align: right; }
    thead th.text-center { text-align: center; }
    tbody tr { border-bottom: 1px solid #e5e7eb; }
    tbody tr:nth-child(even) { background: #f9fafb; }
    tbody td {
        padding: 10px 12px;
        font-size: 13px;
        vertical-align: middle;
    }
    tbody td.text-right  { text-align: right; }
    tbody td.text-center { text-align: center; }
    tbody tr.empty td {
        text-align: center;
        color: #aaa;
        padding: 20px;
        font-style: italic;
    }

    /* ── TOTALS ── */
    .inv-totals {
        display: flex;
        justify-content: flex-end;
        margin-bottom: 36px;
    }
    .inv-totals-box {
        width: 280px;
    }
    .inv-totals-row {
        display: flex;
        justify-content: space-between;
        padding: 5px 0;
        font-size: 13px;
        border-bottom: 1px solid #e5e7eb;
    }
    .inv-totals-row .t-label { color: #666; }
    .inv-totals-row.grand {
        border-top: 2px solid #1a56db;
        border-bottom: none;
        margin-top: 4px;
        padding-top: 8px;
        font-size: 15px;
        font-weight: 700;
        color: #1a56db;
    }

    /* ── FOOTER ── */
    .inv-footer {
        border-top: 1px solid #e5e7eb;
        padding-top: 16px;
        display: flex;
        justify-content: space-between;
        align-items: flex-end;
        font-size: 11px;
        color: #888;
    }
    .inv-footer .note { max-width: 400px; line-height: 1.5; }
    .inv-footer .generated { text-align: right; }

    /* ── PRINT ── */
    @media print {
        body { padding: 20px; }
        .no-print { display: none !important; }
        a { text-decoration: none; color: inherit; }
        thead tr { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    }

    /* ── PRINT BUTTON (screen only) ── */
    .print-bar {
        position: fixed;
        top: 16px;
        right: 20px;
        display: flex;
        gap: 8px;
        z-index: 999;
    }
    .btn-print {
        padding: 8px 18px;
        background: #1a56db;
        color: #fff;
        border: none;
        border-radius: 6px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .btn-print:hover { background: #1e40af; }
    .btn-close-pdf {
        padding: 8px 18px;
        background: #fff;
        color: #555;
        border: 1px solid #d1d5db;
        border-radius: 6px;
        font-size: 13px;
        font-weight: 600;
        cursor: pointer;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .btn-close-pdf:hover { background: #f3f4f6; }
</style>
</head>
<body>

<!-- Screen-only toolbar -->
<div class="print-bar no-print">
    <a href="javascript:history.back()" class="btn-close-pdf">&#8592; Back</a>
    <button class="btn-print" onclick="window.print()">&#128438; Print / Save as PDF</button>
</div>

<?php
use yii\helpers\Html;

$items = $model->invoiceItems;

$statusClass = match($model->status) {
    'Paid'    => 'status-paid',
    'Sent'    => 'status-sent',
    'Overdue' => 'status-overdue',
    default   => 'status-draft',
};

$total = $model->total_amount;

$formatCurrency = fn($v) => 'Rp ' . number_format((float)$v, 0, ',', '.');
?>

<!-- ── HEADER ── -->
<div class="inv-header">
    <div class="inv-brand">
        <h1>CRM MNCPlaymedia</h1>
        <p>Enterprise Resource Planning</p>
    </div>
    <div class="inv-label">
        <h2>Invoice</h2>
        <div class="inv-number"><?= Html::encode($model->invoice_number) ?></div>
        <div>
            <span class="status-badge <?= $statusClass ?>">
                <?= Html::encode($model->status) ?>
            </span>
        </div>
    </div>
</div>

<!-- ── META ── -->
<div class="inv-meta">
    <div class="inv-meta-block">
        <h4>Bill To</h4>
        <p><strong><?= Html::encode($model->account?->name ?? '-') ?></strong></p>
    </div>

    <div class="inv-meta-block">
        <h4>Details</h4>
        <p class="label">Sales Order</p>
        <p><?= Html::encode($model->salesOrder?->order_number ?? '-') ?></p>
    </div>

    <div class="inv-meta-block" style="text-align:right;">
        <h4>Dates</h4>
        <p class="label">Invoice Date</p>
        <p><?= Html::encode($model->invoice_date) ?></p>
        <p class="label">Due Date</p>
        <p><strong><?= Html::encode($model->due_date) ?></strong></p>
    </div>
</div>

<!-- ── ITEMS TABLE ── -->
<table>
    <thead>
        <tr>
            <th style="width:40px;">#</th>
            <th>Product / Description</th>
            <th class="text-center" style="width:70px;">Qty</th>
            <th class="text-right" style="width:130px;">Unit Price</th>
            <th class="text-right" style="width:110px;">Discount</th>
            <th class="text-right" style="width:130px;">Total</th>
        </tr>
    </thead>
    <tbody>
        <?php if (empty($items)): ?>
            <tr class="empty"><td colspan="6">No items found</td></tr>
        <?php else: ?>
            <?php foreach ($items as $i => $item): ?>
            <tr>
                <td class="text-center"><?= $i + 1 ?></td>
                <td><?= Html::encode($item->product?->name ?? '-') ?></td>
                <td class="text-center"><?= Html::encode($item->qty) ?></td>
                <td class="text-right"><?= $formatCurrency($item->price) ?></td>
                <td class="text-right"><?= $formatCurrency($item->discount) ?></td>
                <td class="text-right"><strong><?= $formatCurrency($item->total) ?></strong></td>
            </tr>
            <?php endforeach; ?>
        <?php endif; ?>
    </tbody>
</table>

<!-- ── TOTALS ── -->
<div class="inv-totals">
    <div class="inv-totals-box">
        <div class="inv-totals-row grand">
            <span class="t-label">Total Amount</span>
            <span><?= $formatCurrency($total) ?></span>
        </div>
    </div>
</div>

<!-- ── FOOTER ── -->
<div class="inv-footer">
    <div class="note">
        <strong>Payment Instructions</strong><br>
        Please include the invoice number when making payment.<br>
        Thank you for your business.
    </div>
    <div class="generated">
        Generated: <?= date('d M Y H:i') ?><br>
        <?= Html::encode($model->invoice_number) ?>
    </div>
</div>

<script>
    // auto-open print dialog when visiting this page
    window.onload = function () {
        // small delay so the page renders fully before print dialog opens
        setTimeout(function () { window.print(); }, 400);
    };
</script>
</body>
</html>
