<?php
$current = basename($_SERVER['PHP_SELF']);
?>

<style>
    .brand {
        padding: 15px 20px;
        border-bottom: 1px solid rgba(255,255,255,0.1);
        margin-bottom: 10px;
    }

    .brand a {
        display: flex;
        align-items: center;
        gap: 10px;
        text-decoration: none;
        color: #fff;
        font-size: 16px;
        font-weight: bold;
    }

    .menu-header {
        padding: 10px 20px 5px 20px;
        color: rgba(255,255,255,0.5);
        font-size: 10px;
        text-transform: uppercase;
        letter-spacing: 1px;
        font-weight: 600;
    }

    .sidebar-menu ul {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .sidebar-menu ul li {
        margin: 2px 10px;
    }

    .sidebar-menu ul li a {
        display: flex;
        align-items: center;
        padding: 10px 16px;
        color: rgba(255,255,255,0.8);
        text-decoration: none;
        border-radius: 8px;
        transition: all 0.2s;
        font-size: 13px;
        gap: 10px;
    }

    .sidebar-menu ul li a:hover {
        background: rgba(255,255,255,0.1);
        color: #fff;
    }

    .sidebar-menu ul li a.active {
        background: #fff;
        color: #003366;
        font-weight: 600;
    }

    .sidebar-menu ul li a i {
        width: 20px;
        text-align: center;
    }

    /* Sidebar positioning */
    .sidebar {
        position: fixed;
        left: 0;
        top: 55px;
        width: 200px;
        height: calc(100vh - 55px);
        background: #003366;
        color: white;
        overflow-y: auto;
        z-index: 999;
        padding: 0;
    }

    .sidebar-menu {
        padding-top: 10px;
    }

    .sidebar-menu .brand {
        padding: 15px 20px;
        border-bottom: 1px solid rgba(255,255,255,0.1);
        margin-bottom: 10px;
    }

    .sidebar-menu .brand a {
        display: flex;
        align-items: center;
        gap: 10px;
        text-decoration: none;
        color: #fff;
        font-size: 16px;
        font-weight: bold;
    }

    .sidebar-menu ul {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .sidebar-menu ul li {
        margin: 2px 10px;
    }

    .sidebar-menu ul li a {
        display: flex;
        align-items: center;
        padding: 10px 16px;
        color: rgba(255,255,255,0.8);
        text-decoration: none;
        border-radius: 8px;
        transition: all 0.2s;
        font-size: 13px;
        gap: 10px;
    }

    .sidebar-menu ul li a:hover {
        background: rgba(255,255,255,0.1);
        color: #fff;
    }

    .sidebar-menu ul li a.active {
        background: #fff;
        color: #003366;
        font-weight: 600;
    }

    .sidebar-menu ul li a i {
        width: 20px;
        text-align: center;
    }

    /* Content area adjustment when sidebar is present */
    .content-area {
        margin-left: 200px;
        padding: 20px;
        min-height: calc(100vh - 55px);
    }

    /* For pages using this sidebar */
    .content {
        margin-left: 200px;
        padding: 20px;
        min-height: calc(100vh - 55px);
    }

    /* Responsive */
    @media (max-width: 768px) {
        .sidebar {
            width: 0;
            transform: translateX(-100%);
            transition: all 0.3s ease;
        }
        
        .sidebar.open {
            width: 200px;
            transform: translateX(0);
        }
        
        .content, .content-area {
            margin-left: 0;
            width: 100%;
        }
    }
</style>

<aside class="sidebar" id="sidebar">
    <div class="sidebar-menu">
        <div class="brand">
            <a href="/KTMEDOIS/Presentation/Public/index.php?action=dashboard">
                <span>KTM eDOIS</span>
            </a>
        </div>

        <div class="menu-header">Delivery Orders</div>
        <ul>
            <li>
                <a class="<?= ($current == "submit_do.php") ? "active" : ""; ?>"
                   href="../Module2/submit_do.php">
                    <i class="fas fa-file-upload"></i>
                    Submit DO
                </a>
            </li>
            <li>
                <a class="<?= ($current == "do_history.php") ? "active" : ""; ?>"
                   href="../Module2/do_history.php">
                    <i class="fas fa-history"></i>
                    DO History
                </a>
            </li>
        </ul>
</aside>