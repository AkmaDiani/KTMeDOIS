<?php
// Presentation/View/auth/login.php
$title = 'Login - KTM eDOIS';
$showTopbar = false;
$showSidebar = false;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $title; ?></title>
    <!-- Bootstrap CSS (or your own styles) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body {
            background: #f0f2f5;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .login-box {
            background: white;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            width: 100%;
            max-width: 400px;
        }
        .login-box h2 {
            color: #003366;
            font-weight: bold;
            text-align: center;
            margin-bottom: 5px;
        }
        .login-box p {
            text-align: center;
            color: #6c757d;
            margin-bottom: 25px;
        }
        .form-group {
            margin-bottom: 18px;
        }
        .form-group label {
            font-weight: 600;
            font-size: 14px;
            display: block;
            margin-bottom: 5px;
        }
        .form-control {
            border-radius: 8px;
            border: 1px solid #ced4da;
            padding: 10px 14px;
        }
        .btn-primary {
            background: #003366;
            border: none;
            padding: 12px;
            font-weight: bold;
            border-radius: 8px;
            width: 100%;
        }
        .btn-primary:hover {
            background: #002244;
        }
        .alert {
            border-radius: 8px;
        }
        .logo-img {
            display: block;
            margin: 0 auto 20px;
            max-width: 120px;
        }
    </style>
</head>
<body>
    <div class="login-box">
        <!-- Optional logo -->
        <img src="/KTMedOIS/Presentation/Public/assets/images/ktm_logo(1).jpg" alt="KTM Logo" class="logo-img" onerror="this.style.display='none'">
        <h2>KTM eDOIS</h2>
        <p>Sign in to your account</p>

        <?php if (!empty($error)): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form action="/KTMedOIS/Presentation/Public/indexM1.php?controller=auth&action=login" method="POST">
            <div class="form-group">
                <label for="email">Email</label>
                <input type="text" name="email" id="email" class="form-control" placeholder="Enter your email" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" class="form-control" placeholder="Enter your password" required>
            </div>
            <div class="form-group">
                <label for="login_type">Login as</label>
                <select name="login_type" id="login_type" class="form-control" onchange="toggleRole()">
                    <option value="staff">Staff</option>
                    <option value="supplier">Supplier</option>
                </select>
            </div>
            <div class="form-group" id="role-group">
                <label for="role">Role (for staff)</label>
                <select name="role" id="role" class="form-control">
                    <option value="KTM Officer">KTM Officer</option>
                    <option value="Finance Officer">Finance Officer</option>
                    <option value="Audit Officer">Audit Officer</option>
                    <option value="System Admin">System Admin</option>
                </select>
            </div>
            <button type="submit" class="btn btn-primary">Login</button>
        </form>

        <p class="mt-3 text-center text-muted small">KTM eDOIS &copy; <?php echo date('Y'); ?></p>
    </div>

    <script>
        function toggleRole() {
            var type = document.getElementById('login_type').value;
            var roleGroup = document.getElementById('role-group');
            roleGroup.style.display = (type === 'staff') ? 'block' : 'none';
        }
        // Initial state
        toggleRole();
    </script>
</body>
</html>