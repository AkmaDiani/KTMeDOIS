<?php
// Application/Controller/invoiceController.php

require_once __DIR__ . '/../Model/modelM3/Invoice.php';
require_once __DIR__ . '/../Model/modelM3/Item.php';
require_once __DIR__ . '/../Model/modelM3/DeliveryOrder.php';
require_once __DIR__ . '/../Model/modelM3/Supplier.php';
require_once __DIR__ . '/../Model/modelM3/Staff.php';
require_once __DIR__ . '/../Model/modelM3/Notification.php';
require_once __DIR__ . '/../Model/modelM3/AuditLog.php';

// View classes
require_once __DIR__ . '/../../Presentation/View/Module3/UploadInvoice.php';
require_once __DIR__ . '/../../Presentation/View/Module3/InvoiceStatusTracking.php';
require_once __DIR__ . '/../../Presentation/View/Module3/InvoiceSummary.php';

require_once __DIR__ . '/../Middleware/API_gateways/invoicePdf.php';

class InvoiceController {
    private $db;
    private $invoiceModel;
    private $itemModel;
    private $doModel;
    private $supplierModel;
    private $staffModel;
    private $notificationModel;
    private $auditLogModel;

    public function __construct($db) {
        $this->db = $db;
        $this->invoiceModel = new Invoice();
        $this->itemModel = new Item();
        $this->doModel = new DeliveryOrder();
        $this->supplierModel = new Supplier();
        $this->staffModel = new Staff();
        $this->notificationModel = new Notification();
        $this->auditLogModel = new AuditLog();
    }

    // GENERATE INVOICE NUMBER
    private function generateInvoiceNumber() {
        return $this->invoiceModel->generateNumber();
    }

    // GET DO DETAILS
    public function getDODetails() {
        session_start();
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Vendor') {
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }
        
        $doId = $_GET['do_id'] ?? 0;
        $vendorId = $_SESSION['vendor_id'];
        
        $do = $this->doModel->getDOWithStaff($doId);
        
        if (!$do || $do['supplier_ID'] != $vendorId || $do['Status'] !== 'Approved') {
            echo json_encode(['error' => 'DO not found or not approved']);
            exit;
        }
        
        $items = $this->doModel->getItems($doId);
        
        $subtotal = 0;
        foreach ($items as $item) {
            $subtotal += $item['quantity'] * $item['unit_price'];
        }
        
        echo json_encode([
            'success' => true,
            'staff_name' => $do['staff_name'] ?? 'N/A',
            'staff_email' => $do['staff_email'] ?? 'N/A',
            'po_number' => $do['PO_number'] ?? 'N/A',
            'do_number' => $do['DO_number'] ?? 'N/A',
            'subtotal' => $subtotal,
            'items' => $items
        ]);
        exit;
    }

    // VENDOR - SUBMIT FORM
    public function submitForm() {
        session_start();
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Vendor') {
            header('Location: /KTMEDOIS/Presentation/Public/index.php?action=login');
            exit;
        }
        $vendorId = $_SESSION['vendor_id'];

        $approvedDOs = $this->doModel->getApprovedDOsBySupplier($vendorId);
        
        // Get supplier as array
        $stmt = $this->db->prepare("SELECT * FROM supplier WHERE Supplier_id = ?");
        $stmt->execute([$vendorId]);
        $vendor = $stmt->fetch();

        $view = new UploadInvoice(
            false,
            $vendor,
            $approvedDOs,
            null,
            null,
            [],
            isset($_SESSION['error']) ? [$_SESSION['error']] : [],
            isset($_SESSION['success']) ? [$_SESSION['success']] : []
        );
        $view->render();
        exit;
    }

    // VENDOR - SUBMIT (WITH DRAFT SUPPORT)
    public function submit() {
        session_start();
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Vendor') {
            header('Location: /KTMEDOIS/Presentation/Public/index.php?action=login');
            exit;
        }
        
        $vendorId = $_SESSION['vendor_id'];
        $action = $_POST['action'] ?? 'submit';
        
        $doId = $_POST['do_id'] ?? null;
        $description = $_POST['description'] ?? '';
        $discount = floatval($_POST['discount'] ?? 0);

        $items = [];
        if (isset($_POST['items'])) {
            foreach ($_POST['items'] as $item) {
                $desc = trim($item['description'] ?? '');
                $qty = intval($item['quantity'] ?? 0);
                $price = floatval($item['unit_price'] ?? 0);
                if ($desc && $qty > 0 && $price >= 0) {
                    $items[] = [
                        'description' => $desc,
                        'quantity' => $qty,
                        'unit_price' => $price
                    ];
                }
            }
        }

        $proofFile = $_FILES['proof_link'] ?? null;
        $proofPath = null;
        
        if ($proofFile && $proofFile['error'] === UPLOAD_ERR_OK) {
            $allowedExt = ['pdf', 'jpg', 'jpeg', 'png', 'gif'];
            $ext = strtolower(pathinfo($proofFile['name'], PATHINFO_EXTENSION));
            if (in_array($ext, $allowedExt) && $proofFile['size'] <= 5 * 1024 * 1024) {
                $uploadDir = __DIR__ . '/../../Data/uploads/proofs/';
                if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);
                $fileName = 'proof_' . uniqid() . '.' . $ext;
                if (move_uploaded_file($proofFile['tmp_name'], $uploadDir . $fileName)) {
                    $proofPath = 'uploads/proofs/' . $fileName;
                }
            }
        }

        try {
            $this->db->beginTransaction();

            $do = $this->doModel->getDOWithStaff($doId);
            if (!$do || $do['supplier_ID'] != $vendorId || $do['Status'] !== 'Approved') {
                throw new Exception('Invalid Delivery Order. Only approved DOs can be invoiced.');
            }

            if ($proofPath) {
                $this->doModel->updateProofLink($doId, $proofPath);
            }

            $stmt = $this->db->prepare("SELECT * FROM invoice WHERE DO_id = ? AND Status != 'Draft'");
            $stmt->execute([$doId]);
            if ($stmt->fetch()) {
                throw new Exception('An invoice already exists for this Delivery Order.');
            }

            $stmt = $this->db->prepare("SELECT * FROM invoice WHERE DO_id = ? AND Status = 'Draft'");
            $stmt->execute([$doId]);
            $existingDraft = $stmt->fetch();

            $subtotal = 0;
            foreach ($items as $item) {
                $subtotal += $item['quantity'] * $item['unit_price'];
            }
            $tax = $subtotal * 0.06;
            $penalty = 0;
            $total = $subtotal + $tax - $discount + $penalty;

            $invoiceNum = $this->generateInvoiceNumber();
            $status = ($action === 'draft') ? Invoice::STATUS_DRAFT : Invoice::STATUS_SUBMITTED;

            if ($existingDraft) {
                $invoice = new Invoice();
                if (!$invoice->load($existingDraft['Invoice_id'])) {
                    throw new Exception('Failed to load existing draft.');
                }
                
                $invoice->Subtotal = $subtotal;
                $invoice->Tax = $tax;
                $invoice->Total = $total;
                $invoice->discount = $discount;
                $invoice->penalty = 0;
                $invoice->Status = $status;
                $invoice->Description = $description;
                $invoice->issue_date = date('Y-m-d');
                
                if (!$invoice->save()) {
                    throw new Exception('Failed to update draft invoice.');
                }
                
                $invoiceId = $invoice->Invoice_id;
                $invoiceNum = $invoice->Invoice_num;
                
                $message = ($action === 'draft') ? 'Draft updated successfully.' : 'Draft submitted successfully.';
                
            } else {
                $invoice = new Invoice();
                $invoice->Invoice_num = $invoiceNum;
                $invoice->DO_id = $doId;
                $invoice->supplier_ID = $vendorId;
                $invoice->Staff_id = null;
                $invoice->issue_date = date('Y-m-d');
                $invoice->Subtotal = $subtotal;
                $invoice->Tax = $tax;
                $invoice->Total = $total;
                $invoice->discount = $discount;
                $invoice->penalty = 0;
                $invoice->Status = $status;
                $invoice->Reason = '';
                $invoice->Description = $description;
                $invoice->Credit_note = 0;

                if (!$invoice->save()) {
                    throw new Exception('Failed to save invoice.');
                }
                
                $invoiceId = $invoice->Invoice_id;
                
                $message = ($action === 'draft') ? 'Invoice saved as draft.' : 'Invoice submitted successfully.';
            }

            $staffId = $do['Staff_id'] ?? 20001;
            if ($action === 'submit') {
                $this->auditLogModel->log($staffId, 'Submitted invoice', 'Invoice ' . $invoiceNum);
            }

            if ($action === 'submit') {
                $this->notificationModel->createForSupplier($vendorId, 'System', 'Invoice ' . $invoiceNum . ' submitted successfully.');
            }

            $this->db->commit();
            
            if ($action === 'draft') {
                $_SESSION['success'] = 'Invoice saved as draft.';
            } else {
                $_SESSION['success'] = 'Invoice submitted successfully.';
            }
            header('Location: /KTMEDOIS/Presentation/Public/index.php?action=invoice_status');
            exit;
            
        } catch (Exception $e) {
            $this->db->rollBack();
            $_SESSION['error'] = $e->getMessage();
            header('Location: /KTMEDOIS/Presentation/Public/index.php?action=invoice_submit');
            exit;
        }
    }

    // VENDOR - STATUS
    public function status() {
        session_start();
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Vendor') {
            header('Location: /KTMEDOIS/Presentation/Public/index.php?action=login');
            exit;
        }
        $vendorId = $_SESSION['vendor_id'];
        
        $invoices = $this->invoiceModel->getBySupplier($vendorId);
        
        $view = new InvoiceStatusTracking(
            $vendorId,
            $invoices,
            [],
            'Vendor',
            isset($_SESSION['error']) ? [$_SESSION['error']] : [],
            isset($_SESSION['success']) ? [$_SESSION['success']] : []
        );
        $view->render();
        exit;
    }

    // OFFICER - ALL INVOICES
    public function pendingList() {
        session_start();
        if (!in_array($_SESSION['role'], ['KTM Officer', 'Finance Officer'])) {
            die('Unauthorized');
        }
        
        $invoices = $this->invoiceModel->getAllExceptDraft();
        $agingData = $this->invoiceModel->getAgingData();
        
        $view = new InvoiceStatusTracking(
            0,
            $invoices,
            $agingData,
            $_SESSION['role'],
            isset($_SESSION['error']) ? [$_SESSION['error']] : [],
            isset($_SESSION['success']) ? [$_SESSION['success']] : []
        );
        $view->render();
        exit;
    }

    // OFFICER - REVIEW ACTION
    public function reviewAction() {
        session_start();
        if (!in_array($_SESSION['role'], ['KTM Officer', 'Finance Officer'])) {
            die('Unauthorized');
        }
        
        $userId = $_SESSION['user_id'];
        $invoiceId = $_POST['invoice_id'] ?? 0;
        $action = $_POST['action'] ?? '';
        $reason = $_POST['reason'] ?? '';
        
        $isLate = isset($_POST['is_late']) ? 1 : 0;
        $penalty = floatval($_POST['penalty'] ?? 0);

        $invoice = new Invoice();
        if (!$invoice->load($invoiceId)) {
            die('Invoice not found.');
        }

        switch ($action) {
            case 'approve':
                if ($isLate && $penalty > 0) {
                    $invoice->penalty = $penalty;
                    $invoice->Total = $invoice->Subtotal + $invoice->Tax - $invoice->discount + $penalty;
                }
                
                if ($_SESSION['role'] === 'KTM Officer') {
                    $invoice->Status = Invoice::STATUS_FINANCE_REVIEW;
                } else {
                    $invoice->Status = Invoice::STATUS_PAYMENT_PROCESSING;
                }
                break;
                
            case 'reject':
                $invoice->Status = Invoice::STATUS_REJECTED;
                $invoice->Reason = $reason;
                break;
                
            case 'forward':
                if ($_SESSION['role'] === 'KTM Officer') {
                    $invoice->Status = Invoice::STATUS_FINANCE_REVIEW;
                } else {
                    die('Only KTM Officer can forward to Finance.');
                }
                break;
                
            default:
                die('Invalid action.');
        }

        if ($invoice->save()) {
            $this->auditLogModel->log($userId, $action . ' invoice', 'Invoice ' . $invoice->Invoice_num);
            $_SESSION['success'] = 'Invoice status updated.';
        } else {
            $_SESSION['error'] = 'Failed to update invoice.';
        }
        header('Location: /KTMEDOIS/Presentation/Public/index.php?action=invoice_pending');
        exit;
    }

    // INVOICE SUMMARY (Vendor & Officer)
    public function invoiceSummary() {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            header('Location: /KTMEDOIS/Presentation/Public/index.php?action=login');
            exit;
        }
        
        $invoiceId = $_GET['id'] ?? 0;
        $invoice = new Invoice();
        if (!$invoice->load($invoiceId)) {
            die('Invoice not found.');
        }
        
        if ($_SESSION['role'] === 'Vendor') {
            if ($invoice->supplier_ID != $_SESSION['vendor_id']) {
                die('Unauthorized');
            }
        } elseif (!in_array($_SESSION['role'], ['KTM Officer', 'Finance Officer'])) {
            die('Unauthorized');
        }
        
        $items = $invoice->getItems();
        $do = $invoice->getDO();
        $supplier = $invoice->getSupplier();
        $staff = $invoice->getStaff();
        $proofLink = $do['Proof_link'] ?? null;
        
        $view = new InvoiceSummary(
            $invoice,
            $items,
            $do,
            $supplier,
            $staff,
            $proofLink,
            $_SESSION['role']
        );

        $view->render();
        exit;
    }

    // ------------------------------------------------------------
// PDF GENERATION
// ------------------------------------------------------------
public function generatePdf($invoiceId) {
    session_start();
    if (!isset($_SESSION['user_id'])) {
        header('Location: /KTMEDOIS/Presentation/Public/index.php?action=login');
        exit;
    }
    
    $pdfApi = new InvoicePdf();
    try {
        // ✅ Correct parameter order
        $pdfApi->generateAndDownload(
            $invoiceId,                 // 1st: Invoice ID
            $_SESSION['role'],          // 2nd: User Role (Vendor/KTM Officer/Finance Officer)
            $_SESSION['vendor_id'] ?? null  // 3rd: Vendor ID (only needed for vendors)
        );
    } catch (Exception $e) {
        die('Error generating PDF: ' . $e->getMessage());
    }
}

    // ------------------------------------------------------------
    // PREVIEW PDF
    // ------------------------------------------------------------
    public function previewPdf() {
        session_start();
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Vendor') {
            header('Location: /KTMEDOIS/Presentation/Public/index.php?action=login');
            exit;
        }
        
        $doId = $_POST['do_id'] ?? 0;
        $discount = floatval($_POST['discount'] ?? 0);
        
        $do = $this->doModel->getDOWithStaff($doId);
        
        if (!$do || $do['supplier_ID'] != $_SESSION['vendor_id'] || $do['Status'] !== 'Approved') {
            die('DO not found or not approved');
        }
        
        $items = $this->doModel->getItems($doId);
        
        $subtotal = 0;
        foreach ($items as $item) {
            $subtotal += $item['quantity'] * $item['unit_price'];
        }
        $tax = $subtotal * 0.06;
        $total = $subtotal + $tax - $discount;
        
        $supplier = $this->supplierModel->load($_SESSION['vendor_id']) ? $this->supplierModel : null;
        
        $pdfApi = new InvoicePdf();
        $pdfApi->generatePreview($do, $items, $supplier, $subtotal, $tax, $discount, $total);
    }

    // ------------------------------------------------------------
    // VENDOR - EDIT DRAFT INVOICE
    // ------------------------------------------------------------
    public function editInvoice() {
        session_start();
        if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Vendor') {
            header('Location: /KTMEDOIS/Presentation/Public/index.php?action=login');
            exit;
        }
        
        $invoiceId = $_GET['id'] ?? 0;
        $vendorId = $_SESSION['vendor_id'];
        
        $invoice = new Invoice();
        if (!$invoice->load($invoiceId)) {
            die('Invoice not found.');
        }
        
        if ($invoice->supplier_ID != $vendorId) {
            die('Unauthorized');
        }
        
        if ($invoice->Status !== Invoice::STATUS_DRAFT) {
            $_SESSION['error'] = 'Only draft invoices can be edited.';
            header('Location: /KTMEDOIS/Presentation/Public/index.php?action=invoice_status');
            exit;
        }
        
        $do = $this->doModel->getDOWithStaff($invoice->DO_id);
        $items = $this->doModel->getItems($invoice->DO_id);
        
        // Get supplier as array
        $stmt = $this->db->prepare("SELECT * FROM supplier WHERE Supplier_id = ?");
        $stmt->execute([$vendorId]);
        $vendor = $stmt->fetch();
        
        $approvedDOs = $this->doModel->getApprovedDOsBySupplier($vendorId);
        
        $view = new UploadInvoice(
            true,  // isEdit
            $vendor,
            $approvedDOs,
            $invoice,  // editInvoice
            $do,       // selectedDO
            $items,    // invoiceItems
            isset($_SESSION['error']) ? [$_SESSION['error']] : [],
            isset($_SESSION['success']) ? [$_SESSION['success']] : []
        );
        $view->render();
        exit;
    }
}