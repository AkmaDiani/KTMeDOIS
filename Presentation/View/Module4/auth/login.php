<?php
/**
 * views/auth/login.php
 * Replaces: resources/views/auth/login.blade.php
 * Blade removed: @csrf → csrf_field(), @if($errors->any()) → get_flash()
 */
$errorMsg = get_flash('_login_error');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Log in — KTM eDOIS</title>
    <link rel="stylesheet" href="<?= url('css/app.css') ?>">
</head>
<body>
    <div class="login-wrap">
        <div class="login-card">
            <div class="brand">
                <span class="dot"></span><span>KTM eDOIS</span>
            </div>
            <div class="sub">Internal Review &amp; Approval Workflow</div>

            <?php if ($errorMsg): ?>
                <div class="error-text"><?= e($errorMsg) ?></div>
            <?php endif; ?>

            <form method="POST" action="<?= url('login') ?>">
                <?= csrf_field() ?>
                <div class="field">
                    <label class="field-label">Staff Email</label>
                    <input type="email" name="email"
                           value="<?= old('email') ?>"
                           placeholder="hakim@ktm.com" required autofocus>
                </div>
                <div class="field">
                    <label class="field-label">Password</label>
                    <input type="password" name="password" required>
                </div>
                <button type="submit" class="btn btn-primary">Log in</button>
            </form>
        </div>
    </div>
</body>
</html>
