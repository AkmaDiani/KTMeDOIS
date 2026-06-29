<?php
// Presentation/View/SharedUI/topbar.php
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?? 'KTM eDOIS' ?></title>
    <link rel="stylesheet" href="/KTMEDOIS/Presentation/Public/assets/style.css">
    
</head>
<body>

<div class="app-container">
    
    <!-- TOP BAR -->
    <header class="topbar">
        <div class="topbar-left">
            <button class="sidebar-toggle" onclick="toggleSidebar()">
                <i class="fas fa-bars"></i>
            </button>
            <div class="logo">
                <a href="/KTMEDOIS/Presentation/Public/index.php?action=dashboard">
                    <span>KTM eDOIS</span>
                </a>
            </div>
        </div>
        <div class="topbar-center">
            <nav class="topbar-nav">
                <ul>
                    
                    <li><a href="#" class="<?= $activePage === 'manage_vendor' ? 'active' : '' ?>">Manage Vendor</a></li>
                    <li><a href="#" class="<?= $activePage === 'manage_do' ? 'active' : '' ?>">Manage Delivery Order</a></li>
                    <li><a href="/KTMEDOIS/Presentation/Public/index.php?action=invoice_status" class="<?= $activePage === 'invoice_status' ? 'active' : '' ?>">Manage Invoice</a></li>
                    <li><a href="#" class="<?= $activePage === 'review_document' ? 'active' : '' ?>">Manage & Review Approved Document</a></li>
                    <li><a href="#" class="<?= $activePage === 'notifications' ? 'active' : '' ?>">Notifications</a></li>
                    <li><a href="#" class="<?= $activePage === 'user' ? 'active' : '' ?>">User</a></li>
                </ul>
            </nav>
        </div>
        <div class="topbar-right">
            <div class="user-profile">
                <span class="username"><?= htmlspecialchars($_SESSION['username'] ?? 'Guest') ?></span>
                <span class="role-badge role-<?= strtolower(str_replace(' ', '-', $_SESSION['role'] ?? '')) ?>">
                    <?= htmlspecialchars($_SESSION['role'] ?? '') ?>
                </span>
                <a href="/KTMEDOIS/Presentation/Public/index.php?action=logout" class="logout-btn">Logout
                    <i class="fas fa-sign-out-alt"></i>
                </a>
            </div>
        </div>
    </header>

    
    <div class="sidebar-overlay" id="sidebarOverlay" onclick="toggleSidebar()"></div>

    <div class="main-wrapper">