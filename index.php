<?php
// ============================================
// ENTRY POINT - START SESSION
// ============================================

session_start();

// ============================================
// DATABASE CONNECTIONS
// ============================================
$host = 'localhost';
$port = '3306';
$username = 'root';
$password = '';

$conn_main = mysqli_connect($host, $username, $password, 'ktm_edois', $port);
if (!$conn_main) die("Main DB connection failed: " . mysqli_connect_error());
mysqli_set_charset($conn_main, "utf8mb4");

$conn_supplier = mysqli_connect($host, $username, $password, 'supplier', $port);
if (!$conn_supplier) die("Supplier DB connection failed: " . mysqli_connect_error());
mysqli_set_charset($conn_supplier, "utf8mb4");

// ============================================
// GET PARAMETERS - Support URL Cantik
// ============================================

$url = $_GET['url'] ?? '';

if (empty($url)) {
    $controller = $_GET['controller'] ?? 'auth';
    $action = $_GET['action'] ?? 'login';
    $id = $_GET['id'] ?? null;
} else {
    $segments = explode('/', trim($url, '/'));

    if (count($segments) >= 1) {
        $controller = $segments[0];
        $action = $segments[1] ?? 'dashboard';
        $id = $segments[2] ?? null;
    } else {
        $controller = 'auth';
        $action = 'login';
        $id = null;
    }
}

// ============================================
// ROUTE TO CONTROLLER
// ============================================
if ($controller == 'auth') {
    require_once __DIR__ . '/../../Application/Controllers/AuthController.php';
    $auth = new AuthController($conn_main, $conn_supplier);
    switch ($action) {
        case 'login':
            $auth->login();
            break;
        case 'logout':
            $auth->logout();
            break;
        default:
            $auth->login();
    }
} elseif ($controller == 'staff') {
    require_once __DIR__ . '/../../Application/Controllers/StaffController.php';
    $staff = new StaffController($conn_main);
    switch ($action) {
        case 'dashboard':
            $staff->dashboard();
            break;
        case 'profile':
            $staff->profile();
            break;
        case 'vendor':
        case 'vendor_registry':
            $staff->vendorRegistry();
            break;
        case 'report':
        case 'vendor_report':
            $staff->vendorReport();
            break;
        case 'add':
        case 'vendor_create':
            $staff->vendorCreate();
            break;
        case 'edit':
        case 'vendor_edit':
            $_GET['id'] = $id;
            $staff->vendorEdit();
            break;
        case 'view':
        case 'vendor_view':
            $_GET['id'] = $id;
            $staff->vendorView();
            break;
        case 'delete':
        case 'vendor_delete':
            $_GET['id'] = $id;
            $staff->vendorDelete();
            break;
        default:
            $staff->dashboard();
    }
} elseif ($controller == 'supplier') {
    require_once __DIR__ . '/../../Application/Controllers/SupplierController.php';
    $supplier = new SupplierController($conn_main, $conn_supplier);
    switch ($action) {
        case 'dashboard':
            $supplier->dashboard();
            break;
        case 'do':
            $supplier->doList();
            break;
        case 'payment':
            $supplier->payment();
            break;
        case 'profile':
            $supplier->profile();
            break;
        default:
            $supplier->dashboard();
    }
} else {
    require_once __DIR__ . '/../../Application/Controllers/AuthController.php';
    $auth = new AuthController($conn_main, $conn_supplier);
    $auth->login();
}
