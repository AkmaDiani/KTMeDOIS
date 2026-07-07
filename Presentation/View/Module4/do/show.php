<?php
/**
 * views/do/show.php
 * Replaces: resources/views/do/show.blade.php
 * Blade removed: all @directives → plain PHP, route() → url(), @include → status_badge()
 */

$title = $do['DO_number'];

// Workflow stages (replaces DeliveryOrder::STAGES constant)
$stages       = ['Submitted', 'Under Review', 'Approved'];
$currentIndex = array_search($do['Status'], $stages);
$rejected     = $do['Status'] === 'Rejected';

$content = function() use ($do, $items, $invoices, $officers, $stages, $currentIndex, $rejected) {
?>

<div class="page-header">
    <div>
        <span class="eyebrow"><a href="<?= url('delivery-orders') ?>">← Delivery Orders</a></span>
        <h1 class="code"><?= e($do['DO_number']) ?></h1>
    </div>
    <div style="display:flex; align-items:center; gap:12px;">
        <?= status_badge($do['Status']) ?>
        <a href="<?= url('delivery-orders/' . $do['DO_id'] . '/export') ?>" class="btn btn-ghost">Download PDF</a>
    </div>
</div>

{{-- Rail tracker --}}
<div class="panel">
    <div class="tracker">
        <div class="tracker-line">
            <?php if (!$rejected && $currentIndex > 0): ?>
                <div class="fill" style="width: <?= ($currentIndex / (count($stages) - 1)) * 100 ?>%"></div>
            <?php endif; ?>

            <?php foreach ($stages as $i => $stage): ?>
                <?php
                if ($rejected) {
                    $cls = '';
                } elseif ($i < $currentIndex) {
                    $cls = 'done';
                } elseif ($i === $currentIndex) {
                    $cls = 'current';
                } else {
                    $cls = '';
                }
                ?>
                <div class="station <?= $cls ?>">
                    <div class="dot"></div>
                    <label><?= e($stage) ?></label>
                </div>
            <?php endforeach; ?>

            <?php if ($rejected): ?>
                <div class="station rejected">
                    <div class="dot"></div>
                    <label>Rejected</label>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="detail-grid">
    <div>
        {{-- DO Details panel --}}
        <div class="panel">
            <h2>Delivery Order Details</h2>
            <dl class="kv">
                <dt>PO Number</dt><dd class="code"><?= e($do['PO_number']) ?></dd>
                <dt>Vendor</dt><dd><?= e($do['Supplier_name'] ?? '—') ?></dd>
                <dt>Contact Person</dt><dd><?= e($do['Contact_person'] ?? '—') ?></dd>
                <dt>Submitted On</dt><dd><?= fmt_datetime($do['created_date']) ?></dd>
                <dt>Reviewer</dt><dd><?= e($do['reviewer_name'] ?? 'Unassigned') ?></dd>
                <?php if ($rejected): ?>
                    <dt>Rejection Reason</dt><dd><?= e($do['Reason']) ?></dd>
                <?php endif; ?>
            </dl>
        </div>

        {{-- Items panel --}}
        <div class="panel">
            <h2>Items</h2>
            <?php if (empty($items)): ?>
                <p class="hint">No line items recorded for this delivery order.</p>
            <?php else: ?>
                <table>
                    <thead><tr><th>Description</th><th>Quantity</th></tr></thead>
                    <tbody>
                        <?php foreach ($items as $item): ?>
                            <tr>
                                <td><?= e($item['item_description']) ?></td>
                                <td><?= e($item['quantity']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif; ?>
        </div>

        {{-- Linked Invoices panel --}}
        <?php if (!empty($invoices)): ?>
            <div class="panel">
                <h2>Linked Invoices</h2>
                <table>
                    <thead><tr><th>Invoice No.</th><th>Total</th><th>Status</th><th></th></tr></thead>
                    <tbody>
                        <?php foreach ($invoices as $inv): ?>
                            <tr>
                                <td class="code"><?= e($inv['Invoice_num']) ?></td>
                                <td><?= money($inv['Total']) ?></td>
                                <td><?= status_badge($inv['Status']) ?></td>
                                <td><a href="<?= url('invoices/' . $inv['Invoice_id']) ?>">View →</a></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>

    <div>
        {{-- Assign Reviewer panel --}}
        <div class="panel">
            <h2>Assign Reviewer</h2>
            <form method="POST" action="<?= url('delivery-orders/' . $do['DO_id'] . '/assign') ?>">
                <?= csrf_field() ?>
                <div class="field">
                    <label class="field-label">KTM Officer</label>
                    <select name="staff_id" required>
                        <option value="">Select officer…</option>
                        <?php foreach ($officers as $officer): ?>
                            <option value="<?= e($officer['User_ID']) ?>"
                                <?= (string)$do['Staff_id'] === (string)$officer['User_ID'] ? 'selected' : '' ?>>
                                <?= e($officer['Username']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary" style="width:100%; justify-content:center;">Assign</button>
            </form>
        </div>

        {{-- Decision panel (hidden when already Approved or Rejected) --}}
        <?php if (!in_array($do['Status'], ['Approved', 'Rejected'])): ?>
            <div class="panel">
                <h2>Decision</h2>

                <form method="POST" action="<?= url('delivery-orders/' . $do['DO_id'] . '/approve') ?>">
                    <?= csrf_field() ?>
                    <button type="submit" class="btn btn-approve" style="width:100%; justify-content:center;">
                        Approve Delivery Order
                    </button>
                </form>

                <form method="POST" action="<?= url('delivery-orders/' . $do['DO_id'] . '/reject') ?>" style="margin-top:12px;">
                    <?= csrf_field() ?>
                    <div class="field">
                        <label class="field-label">Rejection Reason</label>
                        <textarea name="reason" placeholder="e.g. Invalid supporting document" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-reject" style="width:100%; justify-content:center;">
                        Reject Delivery Order
                    </button>
                </form>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
};

require __DIR__ . '/../layouts/app.php';
