<?php
// Presentation/View/SharedUI/sidebarM3.php

$role = $_SESSION['role'] ?? '';
?>
<!-- SIDEBAR -->
<nav class="sidebar" id="sidebar">
    <div class="sidebar-menu">
        <ul>
            <!-- KTM eDOIS Brand -->
            <li class="brand">
                <a href="/KTMEDOIS/Presentation/Public/index.php?action=dashboard">
                    <span class="brand-text">KTM eDOIS</span>
                </a>
            </li>
            
            <?php if ($role === 'Vendor'): ?>
                <!-- Vendor Menu -->
                <li class="menu-header">Vendor</li>
                
                <li>
                    <a href="/KTMEDOIS/Presentation/Public/index.php?action=invoice_status" class="<?= $activePage === 'invoice_claims' ? 'active' : '' ?>">
                        <span>My Invoice Claims</span>
                    </a>
                </li>
                <li>
                    <a href="/KTMEDOIS/Presentation/Public/index.php?action=invoice_submit" class="<?= $activePage === 'submit_invoice' ? 'active' : '' ?>">
                        <span>Submit Invoice</span>
                    </a>
                </li>

            <?php elseif (in_array($role, ['KTM Officer', 'Finance Officer'])): ?>
                <!-- Officer Menu -->
                <li class="menu-header">Review</li>
                <li>
                    <a href="/KTMEDOIS/Presentation/Public/index.php?action=invoice_pending" class="<?= $activePage === 'pending_invoices' ? 'active' : '' ?>">
                        <span>Invoices List</span>
                    </a>
                </li>
                
            <?php endif; ?>
        </ul>
    </div>
</nav>