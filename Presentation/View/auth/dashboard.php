<?php
// Presentation/View/auth/dashboard.php

$pageTitle = 'Dashboard';
$activePage = 'dashboard';

include __DIR__ . '/../SharedUI/topbar.php';
include __DIR__ . '/../SharedUI/sidebarM3.php';
?>

<div class="content-area">
    <div class="welcome">
        <h2>Welcome to KTM eDOIS</h2>
        <p>Select a module below to get started.</p>
    </div>

    <div class="menu-grid">
        <?php if ($_SESSION['role'] === 'Vendor'): ?>
            <a href="/KTMEDOIS/Presentation/Public/index.php?action=invoice_submit" class="menu-card">
                <div class="icon">📄</div>
                <h3>Submit Invoice</h3>
                <p>Create and submit new invoice</p>
            </a>
            <a href="/KTMEDOIS/Presentation/Public/index.php?action=invoice_status" class="menu-card">
                <div class="icon">📊</div>
                <h3>My Invoice Claims</h3>
                <p>Track your invoice status</p>
            </a>
        <?php elseif (in_array($_SESSION['role'], ['KTM Officer', 'Finance Officer'])): ?>
            <a href="/KTMEDOIS/Presentation/Public/index.php?action=invoice_pending" class="menu-card">
                <div class="icon">📋</div>
                <h3>Pending Invoices</h3>
                <p>Review and approve invoices</p>
            </a>
            <a href="/KTMEDOIS/Presentation/Public/index.php?action=invoice_pending" class="menu-card">
                <div class="icon">📊</div>
                <h3>All Invoices</h3>
                <p>View all invoice records</p>
            </a>
        <?php endif; ?>
    </div>
</div>

<script>
function toggleSidebar() {
    document.getElementById('sidebar').classList.toggle('open');
    document.getElementById('sidebarOverlay').classList.toggle('active');
}
</script>

</body>
</html>