<?php
// Presentation/View/SharedUI/sidebarM1.php

$role = $_SESSION['role'] ?? '';
$userType = $_SESSION['user_type'] ?? '';
$activePage = $activePage ?? '';
?>
<!-- SIDEBAR - MODULE 1 -->
<nav class="sidebar" id="sidebar">
    <div class="sidebar-menu">
        <ul>
            <!-- KTM eDOIS Brand -->
            <li class="brand">
                <a href="/KTMedOIS/Presentation/Public/indexM1.php?controller=staff&action=dashboard">
                    <span class="brand-text">KTM eDOIS</span>
                </a>
            </li>
            
            <?php if ($userType === 'staff' || in_array($role, ['KTM Officer', 'Finance Officer', 'Audit Officer', 'System Admin'])): ?>
                <!-- ========================================= -->
                <!-- STAFF / OFFICER MENU                      -->
                <!-- ========================================= -->
                <li class="menu-header">Dashboard</li>
                <li>
                    <a href="/KTMedOIS/Presentation/Public/indexM1.php?controller=staff&action=dashboard" class="<?= $activePage === 'dashboard' ? 'active' : '' ?>">
                        <i class="fas fa-chart-pie"></i>
                        <span>Dashboard</span>
                    </a>
                </li>

                <li class="menu-header">Vendor Management</li>
                <li>
                    <a href="/KTMedOIS/Presentation/Public/indexM1.php?controller=staff&action=vendor_registry" class="<?= $activePage === 'vendor_registry' ? 'active' : '' ?>">
                        <i class="fas fa-building"></i>
                        <span>Vendor Registry</span>
                    </a>
                </li>
                <li>
                    <a href="/KTMedOIS/Presentation/Public/indexM1.php?controller=staff&action=vendor_report" class="<?= $activePage === 'vendor_report' ? 'active' : '' ?>">
                        <i class="fas fa-file-alt"></i>
                        <span>Vendor Report</span>
                    </a>
                </li>
                <li>
                    <a href="/KTMedOIS/Presentation/Public/indexM1.php?controller=staff&action=vendor_create" class="<?= $activePage === 'vendor_create' ? 'active' : '' ?>">
                        <i class="fas fa-user-plus"></i>
                        <span>Add Vendor</span>
                    </a>
                </li>

                <li class="menu-header">Profile</li>
                <li>
                    <a href="/KTMedOIS/Presentation/Public/indexM1.php?controller=staff&action=profile" class="<?= $activePage === 'profile' ? 'active' : '' ?>">
                        <i class="fas fa-user-circle"></i>
                        <span>My Profile</span>
                    </a>
                </li>

            <?php elseif ($userType === 'supplier' || $role === 'Supplier' || $role === 'Vendor'): ?>
                <!-- ========================================= -->
                <!-- SUPPLIER / VENDOR MENU                    -->
                <!-- ========================================= -->
                <li class="menu-header">Dashboard</li>
                <li>
                    <a href="/KTMedOIS/Presentation/Public/indexM1.php?controller=supplier&action=dashboard" class="<?= $activePage === 'dashboard' ? 'active' : '' ?>">
                        <i class="fas fa-chart-pie"></i>
                        <span>Dashboard</span>
                    </a>
                </li>

                <li class="menu-header">Delivery Orders</li>
                <li>
                    <a href="/KTMedOIS/Presentation/Public/indexM1.php?controller=supplier&action=do" class="<?= $activePage === 'do_list' ? 'active' : '' ?>">
                        <i class="fas fa-truck"></i>
                        <span>My Delivery Orders</span>
                    </a>
                </li>

                <li class="menu-header">Payments</li>
                <li>
                    <a href="/KTMedOIS/Presentation/Public/indexM1.php?controller=supplier&action=payment" class="<?= $activePage === 'payment' ? 'active' : '' ?>">
                        <i class="fas fa-credit-card"></i>
                        <span>Payment Status</span>
                    </a>
                </li>

                <li class="menu-header">Profile</li>
                <li>
                    <a href="/KTMedOIS/Presentation/Public/indexM1.php?controller=supplier&action=profile" class="<?= $activePage === 'profile' ? 'active' : '' ?>">
                        <i class="fas fa-user-circle"></i>
                        <span>My Profile</span>
                    </a>
                </li>

            <?php else: ?>
                <!-- ========================================= -->
                <!-- DEFAULT / GUEST MENU                      -->
                <!-- ========================================= -->
                <li class="menu-header">Navigation</li>
                <li>
                    <a href="/KTMedOIS/Presentation/Public/indexM1.php?controller=auth&action=login">
                        <i class="fas fa-sign-in-alt"></i>
                        <span>Login</span>
                    </a>
                </li>
            <?php endif; ?>
        </ul>
    </div>
</nav>