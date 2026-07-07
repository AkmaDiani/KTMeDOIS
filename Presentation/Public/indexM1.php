<?php
// Presentation/Public/indexM1.php

require_once __DIR__ . '/../../bootstrap.php';

// ------------------------------------------------------------
// DATABASE CONNECTIONS (PDO)
// ------------------------------------------------------------
$host = 'localhost';
$port = '3306';
$user = 'root';
$pass = '';

try {
    $mainPdo = new PDO("mysql:host=$host;port=$port;dbname=ktm_edois;charset=utf8mb4", $user, $pass);
    $mainPdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $supplierPdo = new PDO("mysql:host=$host;port=$port;dbname=supplier;charset=utf8mb4", $user, $pass);
    $supplierPdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Store in globals so helper functions can use them
$GLOBALS['main_pdo'] = $mainPdo;
$GLOBALS['supplier_pdo'] = $supplierPdo;

// ------------------------------------------------------------
// ROUTING PARAMETERS
// ------------------------------------------------------------
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

// ------------------------------------------------------------
// API REQUEST
// ------------------------------------------------------------
if (isset($_GET['api'])) {
    // The SupplierAPI class will be autoloaded
    $api = new SupplierAPI($mainPdo, $supplierPdo);
    // You may route to methods based on $_GET['method'] or similar
    exit;
}

// ------------------------------------------------------------
// ROUTE TO CONTROLLERS
// ------------------------------------------------------------
if ($controller === 'auth') {
    $auth = new AuthController($mainPdo, $supplierPdo);
    switch ($action) {
        case 'login':  $auth->login(); break;
        case 'logout': $auth->logout(); break;
        default:       $auth->login();
    }
} elseif ($controller === 'staff') {
    $staff = new StaffController($mainPdo);
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
    $supplier = new SupplierController($mainPdo, $supplierPdo);
    switch ($action) {
        case 'dashboard':  $supplier->dashboard(); break;
        case 'do':         $supplier->doList(); break;
        case 'payment':    $supplier->payment(); break;
        case 'profile':    $supplier->profile(); break;
        default:           $supplier->dashboard();
    }

    } elseif ($controller === 'invoice') {
    require_once ROOT_PATH . '/Application/Controller/M3/invoiceController.php';
    $invoice = new InvoiceController($mainPdo, $supplierPdo);
    switch ($action) {
        case 'invoice_status':
            $invoice->status();
            break;
        case 'invoice_submit':
            $invoice->submitForm();
            break;
        case 'invoice_submit_post':
            $invoice->submit();
            break;
        case 'invoice_summary':
            $_GET['id'] = $id;
            $invoice->invoiceSummary();
            break;
        case 'invoice_edit':
            $_GET['id'] = $id;
            $invoice->editInvoice();
            break;
        case 'invoice_pdf':
            $invoice->generatePdf($id);
            break;
        case 'invoice_preview':
            $invoice->previewPdf();
            break;
        case 'get_do_details':
            $invoice->getDODetails();
            break;
        case 'invoice_pending':
            $invoice->pendingList();
            break;
        case 'invoice_review':
            $invoice->reviewAction();
            break;
        default:
            $invoice->status();
    }

} else {
    // fallback to login
    $auth = new AuthController($mainPdo, $supplierPdo);
    $auth->login();
}