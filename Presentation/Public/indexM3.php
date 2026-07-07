<?php
// Presentation/Public/indexM3.php

require_once __DIR__ . '/../../bootstrap.php';

// Get PDO connection
$pdo = Database::getInstance()->getConnection();

// Route parameters
$action = $_GET['action'] ?? 'login';
$id = $_GET['id'] ?? null;

// Instantiate InvoiceController (autoloaded)
$controller = new InvoiceController($pdo);

switch ($action) {

    // Vendor invoice routes
    case 'invoice_submit':
        $controller->submitForm();
        break;
    case 'invoice_submit_post':
        $controller->submit();
        break;
    case 'invoice_status':
        $controller->status();
        break;
    case 'invoice_summary':
        $_GET['id'] = $id;
        $controller->invoiceSummary();
        break;
    case 'invoice_edit':
        $_GET['id'] = $id;
        $controller->editInvoice();
        break;
    case 'invoice_pdf':
        $controller->generatePdf($id);
        break;
    case 'invoice_preview':
        $controller->previewPdf();
        break;
    case 'get_do_details':
        $controller->getDODetails();
        break;

    // Officer routes
    case 'invoice_pending':
        $controller->pendingList();
        break;
    case 'invoice_review':
        $controller->reviewAction();
        break;

    default:
        header('Location: /KTMeDOIS/Presentation/Public/indexM3.php?action=login');
        exit;
}