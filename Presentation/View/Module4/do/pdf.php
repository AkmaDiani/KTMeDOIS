<?php
/**
 * views/do/pdf.php
 * Replaces: resources/views/do/pdf.blade.php
 *
 * Laravel used dompdf (barryvdh/laravel-dompdf) to render this as a binary PDF.
 * In plain PHP we output this as an HTML page with a print-optimised stylesheet
 * and trigger window.print() automatically — the user saves/prints to PDF from
 * the browser dialog. This requires no extra PHP library.
 *
 * Variables expected: $do (array), $items (array)
 */

$statusCls = match($do['Status']) {
    'Approved' => 'status-approved',
    'Rejected' => 'status-rejected',
    default    => 'status-pending',
};
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>DO Report — <?= e($do['DO_number']) ?></title>
    <style>
        body { font-family: Arial, sans-serif; color: #1B2430; font-size: 11.5px; margin: 20px; }
        table.kv { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
        table.kv td { padding: 6px 0; border-bottom: 1px solid #DDE3EA; vertical-align: top; }
        table.kv td.label { width: 160px; color: #44546B; font-weight: bold; font-size: 10.5px; text-transform: uppercase; }
        table.items { width: 100%; border-collapse: collapse; margin-bottom: 16px; }
        table.items th { background: #FAFBFC; border: 1px solid #DDE3EA; padding: 7px 9px; text-align: left; font-size: 10px; text-transform: uppercase; color: #44546B; }
        table.items td { border: 1px solid #DDE3EA; padding: 7px 9px; }
        .status-pill { display: inline-block; padding: 3px 10px; border-radius: 10px; font-size: 10px; font-weight: bold; text-transform: uppercase; }
        .status-approved { background: #E6F4EC; color: #2F855A; }
        .status-rejected { background: #FBEAEA; color: #C53030; }
        .status-pending  { background: #FCF1DF; color: #8A5A0F; }
        .section-title { font-size: 12px; font-weight: bold; color: #0E2A47; text-transform: uppercase; margin: 18px 0 8px; }
        .footer-note { margin-top: 30px; font-size: 9px; color: #8A93A1; border-top: 1px solid #DDE3EA; padding-top: 8px; }
        .reason-box { background: #FBEAEA; border-left: 3px solid #C53030; padding: 10px 12px; font-size: 11px; margin-top: 6px; }
        .letterhead-org { font-size: 16px; font-weight: bold; color: #0E2A47; letter-spacing: 1px; }
        .letterhead-sub { font-size: 10px; color: #44546B; }
        .letterhead-meta { font-size: 9.5px; color: #44546B; text-align: right; }
        hr.thick { border: none; border-top: 2px solid #0E2A47; margin: 12px 0 16px; }
        .report-title { font-size: 14px; font-weight: bold; color: #0E2A47; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 14px; }
        @media print { .no-print { display: none; } }
    </style>
</head>
<body>

    {{-- Letterhead --}}
    <table style="width:100%; margin-bottom:18px;">
        <tr>
            <td>
                <div class="letterhead-org">KTM eDOIS</div>
                <div class="letterhead-sub">Electronic Delivery Order &amp; Invoice System</div>
            </td>
            <td class="letterhead-meta">
                Generated: <?= fmt_datetime(now_str()) ?><br>
                By: <?= e($_SESSION['staff_name'] ?? 'System') ?>
            </td>
        </tr>
    </table>
    <hr class="thick">
    <div class="report-title">Delivery Order Review Report</div>

    {{-- Key-value summary --}}
    <table class="kv">
        <tr>
            <td class="label">DO Number</td>
            <td><?= e($do['DO_number']) ?></td>
            <td class="label">Status</td>
            <td><span class="status-pill <?= $statusCls ?>"><?= e($do['Status']) ?></span></td>
        </tr>
        <tr>
            <td class="label">PO Number</td>
            <td><?= e($do['PO_number']) ?></td>
            <td class="label">Submitted On</td>
            <td><?= fmt_datetime($do['created_date']) ?></td>
        </tr>
        <tr>
            <td class="label">Vendor</td>
            <td><?= e($do['Supplier_name'] ?? '—') ?></td>
            <td class="label">Vendor Number</td>
            <td><?= e($do['Vendor_Number'] ?? '—') ?></td>
        </tr>
        <tr>
            <td class="label">Contact Person</td>
            <td><?= e($do['Contact_person'] ?? '—') ?></td>
            <td class="label">Reviewing Officer</td>
            <td><?= e($do['reviewer_name'] ?? 'Unassigned') ?></td>
        </tr>
    </table>

    <div class="section-title">Items</div>
    <?php if (empty($items)): ?>
        <p style="color:#44546B; font-size:11px;">No line items recorded for this delivery order.</p>
    <?php else: ?>
        <table class="items">
            <thead>
                <tr><th style="width:40px;">#</th><th>Description</th><th style="width:100px;">Quantity</th></tr>
            </thead>
            <tbody>
                <?php foreach ($items as $i => $item): ?>
                    <tr>
                        <td><?= $i + 1 ?></td>
                        <td><?= e($item['item_description']) ?></td>
                        <td><?= e($item['quantity']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>

    <?php if ($do['Status'] === 'Rejected'): ?>
        <div class="section-title">Rejection Reason</div>
        <div class="reason-box"><?= e($do['Reason'] ?? '') ?></div>
    <?php endif; ?>

    <div class="footer-note">
        This document is system-generated by KTM eDOIS for internal review purposes.
        DO ID: <?= e($do['DO_id']) ?>.
    </div>

    <p class="no-print" style="margin-top:24px;">
        <button onclick="window.print()" style="padding:8px 16px; cursor:pointer;">Print / Save as PDF</button>
        <a href="<?= url('delivery-orders/' . $do['DO_id']) ?>" style="margin-left:12px;">← Back</a>
    </p>

    <script>window.onload = function(){ window.print(); };</script>
</body>
</html>
