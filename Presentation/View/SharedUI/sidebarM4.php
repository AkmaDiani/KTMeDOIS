<?php
// Presentation/View/SharedUI/sidebarM4.php

$role = $_SESSION['role'] ?? '';
$activePage = $activePage ?? '';
?>
<nav class="sidebar" id="sidebar">
    <div class="sidebar-menu">
        <ul>
            <li class="brand">
                <a href="/KTMeDOIS/Presentation/Public/indexM1.php?controller=staff&action=dashboard">
                    <span class="brand-text">KTM eDOIS</span>
                </a>
            </li>

            <li class="menu-header">Review</li>
            <li>
                <a href="/KTMeDOIS/Presentation/Public/indexM4.php?action=do_index" class="<?= $activePage === 'review_document' ? 'active' : '' ?>">
                    <i class="fas fa-truck"></i>
                    <span>Delivery Orders</span>
                </a>
            </li>
            <li>
                <a href="/KTMeDOIS/Presentation/Public/indexM4.php?action=invoice_index" class="<?= $activePage === 'review_document' ? 'active' : '' ?>">
                    <i class="fas fa-file-invoice"></i>
                    <span>Invoices</span>
                </a>
            </li>
            <li>
                <a href="/KTMeDOIS/Presentation/Public/indexM4.php?action=auditlog_index" class="<?= $activePage === 'review_document' ? 'active' : '' ?>">
                    <i class="fas fa-history"></i>
                    <span>Audit Log</span>
                </a>
            </li>

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