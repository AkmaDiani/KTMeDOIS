<?php
// Presentation/View/auth/login.php
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>KTM eDOIS - Login</title>
    <link rel="stylesheet" href="/KTMEDOIS/Presentation/Public/assets/style.css">
    <style>
        .login-container {
            max-width: 400px;
            margin: 100px auto;
            padding: 30px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        .login-container h2 {
            text-align: center;
            color: #003366;
            border-bottom: none;
        }
        .login-container .logo {
            text-align: center;
            margin-bottom: 20px;
        }
        .login-container .logo h1 {
            color: #003366;
            font-size: 24px;
            margin: 0;
        }
        .login-container .logo p {
            color: #666;
            font-size: 14px;
            margin: 5px 0 0;
        }
        .login-container label {
            display: block;
            width: 100%;
            font-weight: 600;
            margin-top: 15px;
        }
        .login-container input[type="email"],
        .login-container input[type="password"] {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 14px;
            box-sizing: border-box;
            margin-top: 5px;
        }
        .login-container .btn-login {
            width: 100%;
            padding: 12px;
            background: #003366;
            color: #fff;
            border: none;
            border-radius: 4px;
            font-size: 16px;
            cursor: pointer;
            margin-top: 20px;
        }
        .login-container .btn-login:hover {
            background: #002244;
        }
        .login-container .error {
            background: #f8d7da;
            color: #721c24;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 15px;
            border: 1px solid #f5c6cb;
        }
        .login-container .test-credentials {
            background: #f8f9fa;
            padding: 10px;
            border-radius: 4px;
            margin-top: 15px;
            font-size: 12px;
            border: 1px dashed #ccc;
        }
        .login-container .test-credentials strong {
            color: #003366;
        }
        .login-container .info {
            text-align: center;
            margin-top: 20px;
            font-size: 12px;
            color: #666;
            border-top: 1px solid #eee;
            padding-top: 15px;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="logo">
            <h1>KTM eDOIS</h1>
            <p>Electronic Delivery Order &amp; Invoice System</p>
        </div>

        <h2>Login</h2>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="error"><?= htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></div>
        <?php endif; ?>

        <form action="/KTMEDOIS/Presentation/Public/index.php?action=auth_login" method="post">
            <label for="email">Email Address</label>
            <input type="email" name="email" id="email" placeholder="Enter your email" required>

            <label for="password">Password</label>
            <input type="password" name="password" id="password" placeholder="Enter your password" required>

            <button type="submit" class="btn-login">Login</button>
        </form>

        <div class="test-credentials">
            <strong>Test Credentials:</strong><br>
            <strong>Vendor:</strong> ahmad.zaki@sazglobal.com / any password<br>
            <strong>KTM Officer:</strong> nurul.aisyah@ktm.com / staff123<br>
            <strong>Finance Officer:</strong> hakim@ktm.com / staff123
        </div>

        <div class="info">
            &copy; <?= date('Y') ?> KTM eDOIS. All rights reserved.
        </div>
    </div>
</body>
</html>