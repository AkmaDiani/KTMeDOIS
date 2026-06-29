<?php
// Presentation/Public/index.php

require_once __DIR__ . '/../../Data/db.php';
require_once __DIR__ . '/../../Application/Controller/authController.php';
require_once __DIR__ . '/../../Application/Controller/invoiceController.php';

class FrontController {
    private $db;
    private $action;
    private $allowedActions = [
        // Auth routes
        'login',
        'auth_login',
        'logout',
        'dashboard',
        
        // Invoice routes - Vendor
        'invoice_submit',
        'invoice_submit_post',
        'invoice_status',
        'invoice_summary',
        'invoice_edit',
        'invoice_pdf',
        'invoice_preview',
        'get_do_details',
        
        // Invoice routes - Officer
        'invoice_pending',
        'invoice_review',
    ];

    public function __construct($db) {
        $this->db = $db;
        $this->action = $_GET['action'] ?? 'login';
    }

    public function run() {
        // Validate action
        if (!in_array($this->action, $this->allowedActions)) {
            $this->action = 'login';
        }

        // Route to appropriate controller
        switch ($this->action) {
            // Auth routes
            case 'login':
                $controller = new AuthController($this->db);
                $controller->showLogin();
                break;
                
            case 'auth_login':
                $controller = new AuthController($this->db);
                $controller->login();
                break;
                
            case 'logout':
                $controller = new AuthController($this->db);
                $controller->logout();
                break;
                
            case 'dashboard':
                $controller = new AuthController($this->db);
                $controller->dashboard();
                break;
                
            // Invoice routes - Vendor
            case 'invoice_submit':
                $controller = new InvoiceController($this->db);
                $controller->submitForm();
                break;
                
            case 'invoice_submit_post':
                $controller = new InvoiceController($this->db);
                $controller->submit();
                break;
                
            case 'invoice_status':
                $controller = new InvoiceController($this->db);
                $controller->status();
                break;
                
            case 'invoice_summary':
                $controller = new InvoiceController($this->db);
                $controller->invoiceSummary();
                break;
                
            case 'invoice_edit':
                $controller = new InvoiceController($this->db);
                $controller->editInvoice();
                break;
                
            case 'invoice_pdf':
                $controller = new InvoiceController($this->db);
                $controller->generatePdf($_GET['id'] ?? 0);
                break;
                
            case 'invoice_preview':
                $controller = new InvoiceController($this->db);
                $controller->previewPdf();
                break;
                
            case 'get_do_details':
                $controller = new InvoiceController($this->db);
                $controller->getDODetails();
                break;
                
            // Invoice routes - Officer
            case 'invoice_pending':
                $controller = new InvoiceController($this->db);
                $controller->pendingList();
                break;
                
            case 'invoice_review':
                $controller = new InvoiceController($this->db);
                $controller->reviewAction();
                break;
                
            default:
                header('Location: /KTMEDOIS/Presentation/Public/index.php?action=login');
                exit;
                
        }
    }
}

// --- EXECUTION ---
$db = Database::getInstance()->getConnection();
$frontController = new FrontController($db);
$frontController->run();