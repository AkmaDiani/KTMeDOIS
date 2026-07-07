<?php
/**
 * views/auditlog/index.php
 * Replaces: resources/views/auditlog/index.blade.php
 * Blade pagination ($logs->links()) → custom pagination_links() helper
 */

$title = 'Audit Log';

$content = function() use ($logs, $paginator) {
?>

<div class="page-header">
    <div>
        <span class="eyebrow">Module 4 — Internal Review</span>
        <h1>Audit Log</h1>
    </div>
    <a href="<?= url('audit-log/export') ?>" class="btn btn-ghost">Export PDF</a>
</div>

<div class="card">
    <table>
        <thead>
            <tr>
                <th>Timestamp</th>
                <th>Staff</th>
                <th>Action</th>
                <th>Affected Record</th>
            </tr>
        </thead>
        <tbody>
            <?php if (empty($logs)): ?>
                <tr class="empty-row"><td colspan="4">No audit entries yet.</td></tr>
            <?php else: ?>
                <?php foreach ($logs as $log): ?>
                    <tr>
                        <td><?= fmt_datetime($log['Timestamp']) ?></td>
                        <td><?= e($log['staff_name'] ?? 'System') ?></td>
                        <td><?= e($log['Action']) ?></td>
                        <td class="code"><?= e($log['Affected_Record']) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
        </tbody>
    </table>
</div>

<?= pagination_links($paginator, url('audit-log')) ?>

<?php
};

require __DIR__ . '/../layouts/app.php';
