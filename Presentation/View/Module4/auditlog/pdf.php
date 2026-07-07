<?php
/**
 * views/auditlog/pdf.php
 * Replaces: resources/views/auditlog/pdf.blade.php
 */
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Audit Log Report</title>
    <style>
        body { font-family: Arial, sans-serif; color: #1B2430; font-size: 10.5px; margin: 20px; }
        table.log { width: 100%; border-collapse: collapse; }
        table.log th { background: #0E2A47; color: #fff; padding: 7px 9px; text-align: left; font-size: 9.5px; text-transform: uppercase; }
        table.log td { border-bottom: 1px solid #DDE3EA; padding: 7px 9px; }
        table.log tr:nth-child(even) td { background: #FAFBFC; }
        .footer-note { margin-top: 24px; font-size: 9px; color: #8A93A1; border-top: 1px solid #DDE3EA; padding-top: 8px; }
        .meta { font-size: 10px; color: #44546B; margin-bottom: 14px; }
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
                <div style="font-size:16px; font-weight:bold; color:#0E2A47; letter-spacing:1px;">KTM eDOIS</div>
                <div style="font-size:10px; color:#44546B;">Electronic Delivery Order &amp; Invoice System</div>
            </td>
            <td style="text-align:right; font-size:9.5px; color:#44546B;">
                Generated: <?= fmt_datetime(now_str()) ?><br>
                By: <?= e($_SESSION['staff_name'] ?? 'System') ?>
            </td>
        </tr>
    </table>
    <hr class="thick">
    <div class="report-title">Audit Log Report</div>

    <div class="meta">Total entries: <?= count($logs) ?></div>

    <table class="log">
        <thead>
            <tr>
                <th style="width:130px;">Timestamp</th>
                <th style="width:120px;">Staff</th>
                <th>Action</th>
                <th style="width:110px;">Affected Record</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($logs as $log): ?>
                <tr>
                    <td><?= fmt_datetime($log['Timestamp']) ?></td>
                    <td><?= e($log['staff_name'] ?? 'System') ?></td>
                    <td><?= e($log['Action']) ?></td>
                    <td><?= e($log['Affected_Record']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <div class="footer-note">
        This document is a system-generated audit trail export from KTM eDOIS,
        intended for compliance and review reference.
    </div>

    <p class="no-print" style="margin-top:24px;">
        <button onclick="window.print()" style="padding:8px 16px; cursor:pointer;">Print / Save as PDF</button>
        <a href="<?= url('audit-log') ?>" style="margin-left:12px;">← Back</a>
    </p>

    <script>window.onload = function(){ window.print(); };</script>
</body>
</html>
