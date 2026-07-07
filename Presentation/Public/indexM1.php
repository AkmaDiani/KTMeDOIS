<?php
// Presentation/Public/indexM1.php

require_once __DIR__ . '/../../bootstrap.php';

$pdo = Database::getInstance()->getConnection();


$conn_main = $pdo;
$conn_supplier = $pdo; // or a separate PDO if you set it up

$url = $_GET['url'] ?? '';
if (empty($url)) {
    $controller = $_GET['controller'] ?? 'auth';
    $action     = $_GET['action'] ?? 'login';
    $id         = $_GET['id'] ?? null;
} else {
    $segments = explode('/', trim($url, '/'));
    $controller = $segments[0] ?? 'auth';
    $action     = $segments[1] ?? 'dashboard';
    $id         = $segments[2] ?? null;
}

// API request handling 
if (isset($_GET['api'])) {
    require_once ROOT_PATH . '/Application/Middleware/API_gateways/SupplierAPI.php';
    exit;
}

// Route to controllers (autoloader finds them)
if ($controller === 'auth') {
    $auth = new AuthController($conn_main, $conn_supplier);
    switch ($action) {
        case 'login':  $auth->login(); break;
        case 'logout': $auth->logout(); break;
        default:       $auth->login();
    }
} elseif ($controller === 'staff') {
    $staff = new StaffController($conn_main);
    switch ($action) {
        case 'dashboard':     $staff->dashboard(); break;
        case 'profile':       $staff->profile(); break;
        case 'vendor':
        case 'vendor_registry': $staff->vendorRegistry(); break;
        case 'vendor_report':
        case 'report':        $staff->vendorReport(); break;
        case 'vendor_create':
        case 'add':           $staff->vendorCreate(); break;
        case 'vendor_edit':
        case 'edit':          $_GET['id'] = $id; $staff->vendorEdit(); break;
        case 'vendor_view':
        case 'view':          $_GET['id'] = $id; $staff->vendorView(); break;
        case 'vendor_delete':
        case 'delete':        $_GET['id'] = $id; $staff->vendorDelete(); break;
        default:              $staff->dashboard();
    }
} elseif ($controller === 'supplier') {
    $supplier = new SupplierController($conn_main, $conn_supplier);
    switch ($action) {
        case 'dashboard':  $supplier->dashboard(); break;
        case 'do':         $supplier->doList(); break;
        case 'payment':    $supplier->payment(); break;
        case 'profile':    $supplier->profile(); break;
        default:           $supplier->dashboard();
    }
} else {
    $auth = new AuthController($conn_main, $conn_supplier);
    $auth->login();
}