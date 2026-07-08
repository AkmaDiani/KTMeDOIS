<?php
// Presentation/Public/indexM4.php

require_once __DIR__ . '/../../bootstrap.php';

// Only staff with officer roles can access
$allowedRoles = ['KTM Officer', 'Finance Officer', 'Audit Officer', 'System Admin'];
if (!isset($_SESSION['user_id']) || !in_array($_SESSION['role'], $allowedRoles)) {
    header('Location: /KTMeDOIS/Presentation/Public/indexM1.php?controller=auth&action=login');
    exit;
}

// Get action
$action = $_GET['action'] ?? 'do_index';
$id = $_GET['id'] ?? 0;

// Include the ReviewController (M4)
require_once ROOT_PATH . '/Application/Controller/M4/ReviewController.php';

$controller = new ReviewController();

switch ($action) {
    // DO routes
    case 'do_index':
        $controller->doIndex();
        break;
    case 'do_show':
        $controller->doShow($id);
        break;
    case 'do_assign':
        $controller->doAssign($id);
        break;
    case 'do_approve':
        $controller->doApprove($id);
        break;
    case 'do_reject':
        $controller->doReject($id);
        break;
    case 'do_export':
        $controller->doExportPdf($id);
        break;
    
    // Invoice routes
    case 'invoice_index':
        $controller->invoiceIndex();
        break;
    case 'invoice_show':
        $controller->invoiceShow($id);
        break;
    case 'invoice_forward':
        $controller->invoiceForward($id);
        break;
    case 'invoice_status':
        $controller->invoiceUpdateStatus($id);
        break;
    case 'invoice_reject':
        $controller->invoiceReject($id);
        break;
    case 'invoice_export':
        $controller->invoiceExportPdf($id);
        break;
    
    // Audit log routes
    case 'auditlog_index':
        $controller->auditlogIndex();
        break;
    case 'auditlog_export':
        $controller->auditlogExport();
        break;
    
    default:
        header('Location: /KTMeDOIS/Presentation/Public/indexM4.php?action=do_index');
        exit;
}