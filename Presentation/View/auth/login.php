<?php
$title = 'Login - KTM eDOIS';
$showTopbar = false;
$showSidebar = false;
?>

<div class="login-page">
    <div class="login-card">
        <div class="logo">
            <img src="/SDW/KTMeDOIS/Presentation/Public/assets/images/KTM_logo.png" alt="KTM Logo" style="max-width:150px;">
            <h1>KTM <span>eDOIS</span></h1>
            <p class="subtitle">ELECTRONIC DELIVERY ORDER &amp; INVOICE SYSTEM</p>
        </div>

        <?php if (isset($error) && $error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <div class="demo-credentials">
            <small><i class="fas fa-info-circle"></i> <strong>Demo Credentials:</strong></small>
            <small><strong>Staff:</strong> <code>officer1</code> | Pass: <code>123456</code> | Role: <code>KTM Officer</code></small>
            <small><strong>Supplier:</strong> <code>30001</code> | Pass: <code>123456</code></small>
        </div>

        <form method="POST" action="/SDW/KTMeDOIS/login">
            <div class="mb-3">
                <label class="form-label fw-bold">Login As</label>
                <div class="d-flex gap-3">
                    <div class="login-type-btn active" onclick="selectLoginType('staff')" id="staffBtn">
                        <i class="fas fa-user-tie text-primary"></i>
                        <div class="label">Staff</div>
                        <div class="desc">KTM Employee</div>
                    </div>
                    <div class="login-type-btn" onclick="selectLoginType('supplier')" id="supplierBtn">
                        <i class="fas fa-building text-success"></i>
                        <div class="label">Supplier</div>
                        <div class="desc">External Vendor</div>
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold" id="usernameLabel">ID Number / Username</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                    <input type="text" class="form-control" id="username" name="username"
                        placeholder="e.g: officer1 or 30001" required>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-bold">Password</label>
                <div class="password-wrapper">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        <input type="password" class="form-control" id="password" name="password"
                            placeholder="Enter your password" required>
                    </div>
                    <span class="password-toggle" onclick="togglePassword()">
                        <i class="fas fa-eye" id="eyeIcon"></i>
                    </span>
                </div>
            </div>

            <div class="mb-4 role-select show" id="roleContainer">
                <label class="form-label fw-bold">Role <span class="text-danger">*</span></label>
                <select class="form-select" name="role" id="roleSelect">
                    <option value="">-- Select Role --</option>
                    <option value="KTM Officer">KTM Officer</option>
                    <option value="Finance Officer">Finance Officer</option>
                    <option value="Manager">Manager</option>
                    <option value="Admin">Admin</option>
                </select>
                <small class="text-muted">Required for staff login</small>
            </div>

            <input type="hidden" name="login_type" id="loginType" value="staff">

            <button type="submit" class="btn-login">
                <i class="fas fa-sign-in-alt me-2"></i> Login
            </button>
        </form>

        <div class="text-center mt-3">
            <small class="text-muted"><i class="fas fa-shield-alt me-1"></i> Secure Portal <span class="ktm-badge ms-1">KTM</span></small>
        </div>
    </div>
</div>

<style>
    .login-page {
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #0a1628 0%, #1a237e 50%, #0d47a1 100%);
    }

    .login-card {
        background: white;
        border-radius: 20px;
        padding: 45px 40px;
        width: 100%;
        max-width: 450px;
        box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
    }

    .login-card .logo {
        text-align: center;
        margin-bottom: 30px;
    }

    .login-card .logo h1 {
        color: #1a237e;
        font-weight: 700;
        font-size: 28px;
    }

    .login-card .logo h1 span {
        color: #e53935;
    }

    .login-card .logo .subtitle {
        color: #666;
        font-size: 13px;
        letter-spacing: 2px;
    }

    .login-card .form-control {
        border-radius: 10px;
        padding: 12px 15px;
        border: 2px solid #e0e0e0;
    }

    .login-card .form-control:focus {
        border-color: #1a237e;
    }

    .login-card .input-group-text {
        background: white;
        border: 2px solid #e0e0e0;
        border-right: none;
    }

    .login-card .input-group .form-control {
        border-left: none;
    }

    .login-card .btn-login {
        background: linear-gradient(135deg, #1a237e, #0d47a1);
        color: white;
        padding: 14px;
        border-radius: 10px;
        font-weight: 600;
        width: 100%;
        border: none;
    }

    .login-card .btn-login:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(26, 35, 126, 0.3);
    }

    .login-card .password-toggle {
        cursor: pointer;
        position: absolute;
        right: 15px;
        top: 50%;
        transform: translateY(-50%);
        color: #999;
        z-index: 10;
    }

    .login-card .password-wrapper {
        position: relative;
    }

    .login-card .password-wrapper .form-control {
        padding-right: 45px;
    }

    .ktm-badge {
        background: #e53935;
        color: white;
        padding: 2px 12px;
        border-radius: 20px;
        font-size: 11px;
    }

    .login-type-btn {
        padding: 12px 20px;
        border-radius: 10px;
        border: 2px solid #e0e0e0;
        background: white;
        cursor: pointer;
        transition: all 0.3s;
        text-align: center;
        flex: 1;
    }

    .login-type-btn:hover {
        border-color: #1a237e;
        background: #f0f4ff;
    }

    .login-type-btn.active {
        border-color: #1a237e;
        background: #e8eaf6;
    }

    .login-type-btn i {
        font-size: 24px;
        display: block;
        margin-bottom: 5px;
    }

    .login-type-btn .label {
        font-size: 14px;
        font-weight: 600;
        color: #333;
    }

    .login-type-btn .desc {
        font-size: 11px;
        color: #999;
    }

    .role-select {
        display: none;
    }

    .role-select.show {
        display: block;
    }

    .demo-credentials {
        background: #f8f9fa;
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 20px;
        font-size: 13px;
    }

    .demo-credentials small {
        display: block;
        color: #666;
    }

    .demo-credentials code {
        background: #e9ecef;
        padding: 2px 8px;
        border-radius: 4px;
        font-weight: bold;
    }
</style>

<script>
    function togglePassword() {
        const p = document.getElementById('password');
        const e = document.getElementById('eyeIcon');
        p.type === 'password' ? (p.type = 'text', e.className = 'fas fa-eye-slash') : (p.type = 'password', e.className = 'fas fa-eye');
    }

    function selectLoginType(type) {
        const staffBtn = document.getElementById('staffBtn');
        const supplierBtn = document.getElementById('supplierBtn');
        const roleContainer = document.getElementById('roleContainer');
        const usernameLabel = document.getElementById('usernameLabel');
        const loginType = document.getElementById('loginType');

        staffBtn.classList.remove('active');
        supplierBtn.classList.remove('active');

        if (type === 'staff') {
            staffBtn.classList.add('active');
            roleContainer.classList.add('show');
            usernameLabel.textContent = 'ID Number / Username';
            document.getElementById('username').placeholder = 'e.g: officer1';
            loginType.value = 'staff';
        } else {
            supplierBtn.classList.add('active');
            roleContainer.classList.remove('show');
            usernameLabel.textContent = 'Username / Email';
            document.getElementById('username').placeholder = 'e.g: 30001';
            loginType.value = 'supplier';
        }
    }

    document.getElementById('loginForm').addEventListener('submit', function(e) {
        if (document.getElementById('loginType').value === 'staff') {
            if (!document.getElementById('roleSelect').value) {
                e.preventDefault();
                alert('Please select a role for staff login.');
            }
        }
    });
</script>

<?php include __DIR__ . '/../SharedUI/footer.php'; ?>