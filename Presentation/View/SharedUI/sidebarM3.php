<?php
// Presentation/View/SharedUI/sidebarM3.php

$role = $_SESSION['role'] ?? '';
$userType = $_SESSION['user_type'] ?? '';
$activePage = $activePage ?? '';
?>
<!-- SIDEBAR - MODULE 3 -->
<nav class="sidebar" id="sidebar">
    <div class="sidebar-menu">
        <ul>
            <!-- Brand -->
            <li class="brand">
                <a href="/KTMeDOIS/Presentation/Public/indexM1.php?controller=staff&action=dashboard">
                    <span class="brand-text">KTM eDOIS</span>
                </a>
            </li>

            <?php if ($userType === 'supplier' || $role === 'Supplier' || $role === 'Vendor'): ?>
                <!-- ===== SUPPLIER MENU ===== -->
                <li class="menu-header">Dashboard</li>
                <li>
                    <a href="/KTMeDOIS/Presentation/Public/indexM1.php?controller=supplier&action=dashboard" class="<?= $activePage === 'dashboard' ? 'active' : '' ?>">
                        <i class="fas fa-chart-pie"></i>
                        <span>Dashboard</span>
                    </a>
                </li>

                <li class="menu-header">Delivery Orders</li>
                <li>
                    <a href="/KTMeDOIS/Presentation/View/Module2/do_history.php" class="<?= $activePage === 'manage_do' ? 'active' : '' ?>">
                        <i class="fas fa-truck"></i>
                        <span>My Delivery Orders</span>
                    </a>
                </li>

                <li class="menu-header">Invoices</li>
                <li>
                    <a href="/KTMeDOIS/Presentation/View/Module3/invoice_status.php" class="<?= $activePage === 'invoice_status' ? 'active' : '' ?>">
                        <i class="fas fa-file-invoice"></i>
                        <span>My Invoice Claims</span>
                    </a>
                </li>
                <li>
                    <a href="/KTMeDOIS/Presentation/View/Module3/invoice_submit.php" class="<?= $activePage === 'submit_invoice' ? 'active' : '' ?>">
                        <i class="fas fa-plus-circle"></i>
                        <span>Submit Invoice</span>
                    </a>
                </li>

                <li class="menu-header">Profile</li>
                <li>
                    <a href="/KTMeDOIS/Presentation/Public/indexM1.php?controller=supplier&action=profile" class="<?= $activePage === 'profile' ? 'active' : '' ?>">
                        <i class="fas fa-user-circle"></i>
                        <span>My Profile</span>
                    </a>
                </li>

            <?php elseif ($userType === 'staff' || in_array($role, ['KTM Officer', 'Finance Officer', 'Audit Officer', 'System Admin'])): ?>
                <!-- ===== STAFF / OFFICER MENU ===== -->
                <li class="menu-header">Dashboard</li>
                <li>
                    <a href="/KTMeDOIS/Presentation/Public/indexM1.php?controller=staff&action=dashboard" class="<?= $activePage === 'dashboard' ? 'active' : '' ?>">
                        <i class="fas fa-chart-pie"></i>
                        <span>Dashboard</span>
                    </a>
                </li>

                <li class="menu-header">Vendor Management</li>
                <li>
                    <a href="/KTMeDOIS/Presentation/Public/indexM1.php?controller=staff&action=vendor_registry" class="<?= $activePage === 'manage_vendor' ? 'active' : '' ?>">
                        <i class="fas fa-building"></i>
                        <span>Vendor Registry</span>
                    </a>
                </li>

                <li class="menu-header">Invoices</li>
                <li>
                    <a href="/KTMeDOIS/Presentation/Public/indexM3.php?action=invoice_pending" class="<?= $activePage === 'pending_invoices' ? 'active' : '' ?>">
                        <i class="fas fa-file-invoice"></i>
                        <span>Pending Invoices</span>
                    </a>
                </li>

                <li class="menu-header">Profile</li>
                <li>
                    <a href="/KTMeDOIS/Presentation/Public/indexM1.php?controller=staff&action=profile" class="<?= $activePage === 'profile' ? 'active' : '' ?>">
                        <i class="fas fa-user-circle"></i>
                        <span>My Profile</span>
                    </a>
                </li>

            <?php else: ?>
                <!-- Guest -->
                <li class="menu-header">Navigation</li>
                <li>
                    <a href="/KTMeDOIS/Presentation/Public/indexM1.php?controller=auth&action=login">
                        <i class="fas fa-sign-in-alt"></i>
                        <span>Login</span>
                    </a>
                </li>
            <?php endif; ?>

            <!-- Logout -->
            <li class="menu-header">Account</li>
            <li>
                <a href="/KTMeDOIS/Presentation/Public/indexM1.php?controller=auth&action=logout" class="logout-link">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </a>
            </li>
        </ul>
    </div>
</nav>