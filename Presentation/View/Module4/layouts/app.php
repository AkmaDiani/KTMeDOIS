<?php
/**
 * views/layouts/app.php
 * Replaces: resources/views/layouts/app.blade.php
 *
 * Usage in any view:
 *   $title   = 'Page title';
 *   $content = function() { ?>  ...html...  <?php };
 *   require __DIR__ . '/../layouts/app.php';
 */
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($title ?? 'KTM eDOIS') ?> — Internal Review</title>
    <link rel="stylesheet" href="<?= url('css/app.css') ?>">
</head>
<body>

    <div class="topbar">
        <div class="brand"><span class="dot"></span> KTM eDOIS</div>
        <nav>
            <?php
            // Highlight the active nav link based on current URL path
            $currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
            $isDoActive      = str_contains($currentPath, '/delivery-orders');
            $isInvActive     = str_contains($currentPath, '/invoices');
            $isAuditActive   = str_contains($currentPath, '/audit-log');
            ?>
            <a href="<?= url('delivery-orders') ?>" class="<?= $isDoActive ? 'active' : '' ?>">Delivery Orders</a>
            <a href="<?= url('invoices') ?>" class="<?= $isInvActive ? 'active' : '' ?>">Invoices</a>
            <a href="<?= url('audit-log') ?>" class="<?= $isAuditActive ? 'active' : '' ?>">Audit Log</a>
        </nav>
        <div class="who">
            <?php if (!empty($_SESSION['staff_name'])): ?>
                <span><?= e($_SESSION['staff_name']) ?> &middot; <?= e($_SESSION['staff_role']) ?></span>
                <form method="POST" action="<?= url('logout') ?>">
                    <?= csrf_field() ?>
                    <button class="logout" type="submit">Log out</button>
                </form>
            <?php endif; ?>
        </div>
    </div>

    <div class="page">
        <?php
        // Flash messages (replaces session()->flash() / @if(session('success')))
        $flashSuccess = get_flash('success');
        $flashError   = get_flash('error');
        if ($flashSuccess): ?>
            <div class="alert alert-success"><?= e($flashSuccess) ?></div>
        <?php endif; ?>
        <?php if ($flashError): ?>
            <div class="alert alert-error"><?= e($flashError) ?></div>
        <?php endif; ?>
        <?php
        // Validation errors (replaces @if($errors->any()))
        $validationErrors = get_flash('_errors');
        if ($validationErrors):
            foreach (explode('|', $validationErrors) as $err): ?>
                <div class="alert alert-error"><?= e($err) ?></div>
        <?php endforeach; endif; ?>

        <?php
        // Render the page content (passed as a closure or included inline)
        if (isset($content) && is_callable($content)) {
            ($content)();
        }
        ?>
    </div>

</body>
</html>
