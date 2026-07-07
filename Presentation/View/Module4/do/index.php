<?php
/**
 * views/do/index.php
 * Replaces: resources/views/do/index.blade.php
 * Blade removed:
 *   @extends / @section  → layout closure pattern
 *   {{ }}                → e()
 *   @foreach / @forelse  → foreach / if(empty())
 *   route()              → url()
 *   @include partials    → status_badge()
 */

$title = 'Delivery Orders';

// Build current URL without page param for filter pills
$statusParam  = $_GET['status'] ?? '';
$baseUrl      = url('delivery-orders');

$content = function() use ($deliveryOrders, $statuses, $activeStatus, $baseUrl, $statusParam) {
?>

<div class="page-header">
    <div>
        <span class="eyebrow">Module 4 — Internal Review</span>
        <h1>Delivery Orders</h1>
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
                <th>DO Number</th>
                <th>PO Number</th>
                <th>Vendor</th>
                <th>Reviewer</th>
                <th>Status</th>
                <th>Submitted</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($deliveryOrders)): ?>
                <tr class="empty-row">
                    <td colspan="7">No delivery orders found for this filter.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($deliveryOrders as $do): ?>
                    <tr>
                        <td class="code"><?= e($do['DO_number']) ?></td>
                        <td class="code"><?= e($do['PO_number']) ?></td>
                        <td><?= e($do['Supplier_name'] ?? '—') ?></td>
                        <td><?= e($do['reviewer_name'] ?? 'Unassigned') ?></td>
                        <td><?= status_badge($do['Status']) ?></td>
                        <td><?= fmt_date($do['created_date']) ?></td>
                        <td>
                            <a href="<?= url('delivery-orders/' . $do['DO_id']) ?>">Review →</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?php
}; // end $content

require __DIR__ . '/../layouts/app.php';
