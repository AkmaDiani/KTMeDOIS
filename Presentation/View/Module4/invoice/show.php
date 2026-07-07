<?php
/**
 * views/invoice/show.php
 * Replaces: resources/views/invoice/show.blade.php
 */

$title = $inv['Invoice_num'];

$stages       = ['Submitted', 'Finance Review', 'Payment Processing', 'Paid'];
$currentIndex = array_search($inv['Status'], $stages);
$rejected     = $inv['Status'] === 'Rejected';

$content = function() use ($inv, $financeOfficers, $stages, $currentIndex, $rejected) {
?>

<div class="page-header">
    <div>
        <span class="eyebrow"><a href="<?= url('invoices') ?>">← Invoices</a></span>
        <h1 class="code"><?= e($inv['Invoice_num']) ?></h1>
    </div>
    <div style="display:flex; align-items:center; gap:12px;">
        <?= status_badge($inv['Status']) ?>
        <a href="<?= url('invoices/' . $inv['Invoice_id'] . '/export') ?>" class="btn btn-ghost">Download PDF</a>
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
        <div class="panel">
            <h2>Invoice Details</h2>
            <dl class="kv">
                <dt>Linked DO</dt>
                <dd class="code">
                    <?php if ($inv['DO_id']): ?>
                        <a href="<?= url('delivery-orders/' . $inv['DO_id']) ?>"><?= e($inv['DO_number'] ?? '—') ?></a>
                    <?php else: ?>—<?php endif; ?>
                </dd>
                <dt>Vendor</dt><dd><?= e($inv['Supplier_name'] ?? '—') ?></dd>
                <dt>Description</dt><dd><?= e($inv['Description'] ?? '—') ?></dd>
                <dt>Issue Date</dt><dd><?= fmt_date($inv['issue_date']) ?></dd>
                <dt>Subtotal</dt><dd><?= money($inv['Subtotal']) ?></dd>
                <dt>Tax</dt><dd><?= money($inv['Tax']) ?></dd>
                <dt>Credit Note</dt><dd><?= money($inv['Credit_note']) ?></dd>
                <dt>Total</dt><dd><strong><?= money($inv['Total']) ?></strong></dd>
                <dt>Handled By</dt><dd><?= e($inv['handler_name'] ?? 'Unassigned') ?></dd>
                <?php if ($rejected): ?>
                    <dt>Rejection Reason</dt><dd><?= e($inv['Reason']) ?></dd>
                <?php endif; ?>
            </dl>
        </div>
    </div>

    <div>
        {{-- Forward to Finance --}}
        <?php if ($inv['Status'] === 'Submitted'): ?>
            <div class="panel">
                <h2>Forward to Finance</h2>
                <form method="POST" action="<?= url('invoices/' . $inv['Invoice_id'] . '/forward') ?>">
                    <?= csrf_field() ?>
                    <div class="field">
                        <label class="field-label">Finance Officer</label>
                        <select name="staff_id" required>
                            <option value="">Select finance officer…</option>
                            <?php foreach ($financeOfficers as $officer): ?>
                                <option value="<?= e($officer['User_ID']) ?>">
                                    <?= e($officer['Username']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary" style="width:100%; justify-content:center;">
                        Forward to Finance
                    </button>
                </form>
            </div>
        <?php endif; ?>

        {{-- Update Stage --}}
        <?php if (in_array($inv['Status'], ['Finance Review', 'Payment Processing'])): ?>
            <div class="panel">
                <h2>Update Stage</h2>
                <form method="POST" action="<?= url('invoices/' . $inv['Invoice_id'] . '/status') ?>">
                    <?= csrf_field() ?>
                    <div class="field">
                        <label class="field-label">Move to</label>
                        <select name="status" required>
                            <?php if ($inv['Status'] === 'Finance Review'): ?>
                                <option value="Payment Processing">Payment Processing</option>
                            <?php elseif ($inv['Status'] === 'Payment Processing'): ?>
                                <option value="Paid">Paid</option>
                            <?php endif; ?>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-approve" style="width:100%; justify-content:center;">
                        Update Status
                    </button>
                </form>
            </div>
        <?php endif; ?>

        {{-- Reject Invoice --}}
        <?php if (!in_array($inv['Status'], ['Paid', 'Rejected'])): ?>
            <div class="panel">
                <h2>Reject Invoice</h2>
                <form method="POST" action="<?= url('invoices/' . $inv['Invoice_id'] . '/reject') ?>">
                    <?= csrf_field() ?>
                    <div class="field">
                        <label class="field-label">Rejection Reason</label>
                        <textarea name="reason" placeholder="e.g. Missing proof of delivery" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-reject" style="width:100%; justify-content:center;">
                        Reject Invoice
                    </button>
                </form>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php
};

require __DIR__ . '/../layouts/app.php';
