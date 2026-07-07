<?php
/**
 * views/invoice/index.php
 * Replaces: resources/views/invoice/index.blade.php
 */

$title   = 'Invoices';
$baseUrl = url('invoices');

$content = function() use ($invoices, $statuses, $activeStatus, $baseUrl) {
?>

<div class="page-header">
    <div>
        <span class="eyebrow">Module 4 — Internal Review</span>
        <h1>Invoices</h1>
    </div>
</div>

<div class="pillbar">
    <a href="<?= $baseUrl ?>" class="<?= !$activeStatus ? 'active' : '' ?>">All</a>
    <?php foreach ($statuses as $s): ?>
        <a href="<?= e($baseUrl . '?status=' . urlencode($s)) ?>"
           class="<?= $activeStatus === $s ? 'active' : '' ?>"><?= e($s) ?></a>
    <?php endforeach; ?>
</div>

<div class="card">
    <table>
        <thead>
            <tr>
                <th>Invoice No.</th>
                <th>Linked DO</th>
                <th>Vendor</th>
                <th>Total</th>
                <th>Handler</th>
                <th>Status</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($invoices)): ?>
                <tr class="empty-row">
                    <td colspan="7">No invoices found for this filter.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($invoices as $inv): ?>
                    <tr>
                        <td class="code"><?= e($inv['Invoice_num']) ?></td>
                        <td class="code"><?= e($inv['DO_number'] ?? '—') ?></td>
                        <td><?= e($inv['Supplier_name'] ?? '—') ?></td>
                        <td><?= money($inv['Total']) ?></td>
                        <td><?= e($inv['handler_name'] ?? 'Unassigned') ?></td>
                        <td><?= status_badge($inv['Status']) ?></td>
                        <td><a href="<?= url('invoices/' . $inv['Invoice_id']) ?>">Review →</a></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php
};

require __DIR__ . '/../layouts/app.php';
