<?php
// Application/Controller/M4/ReviewController.php

require_once ROOT_PATH . '/Application/Model/modelM3/DeliveryOrder.php';
require_once ROOT_PATH . '/Application/Model/modelM3/Invoice.php';
require_once ROOT_PATH . '/Application/Model/modelM3/Item.php';
require_once ROOT_PATH . '/Application/Model/modelM3/Supplier.php';
require_once ROOT_PATH . '/Application/Model/modelM3/Staff.php';
require_once ROOT_PATH . '/Application/Model/modelM3/AuditLog.php';
require_once ROOT_PATH . '/Application/Model/modelM3/Notification.php';

class ReviewController {
    private $pdo;
    private $doModel;
    private $invoiceModel;
    private $itemModel;
    private $supplierModel;
    private $staffModel;
    private $auditLogModel;
    private $notificationModel;

    public function __construct() {
        $this->pdo = Database::getInstance()->getConnection();
        $this->doModel = new DeliveryOrder();
        $this->invoiceModel = new Invoice();
        $this->itemModel = new Item();
        $this->supplierModel = new Supplier();
        $this->staffModel = new Staff();
        $this->auditLogModel = new AuditLog();
        $this->notificationModel = new Notification();
    }

    // ---------- DO REVIEW ----------
    public function doIndex() {
        $status = $_GET['status'] ?? '';
        $sql = "SELECT d.*, s.Supplier_name, k.Username AS reviewer_name 
                FROM `do` d
                LEFT JOIN supplier s ON s.Supplier_id = d.supplier_ID
                LEFT JOIN `ktm staff` k ON k.User_ID = d.Staff_id";
        $params = [];
        if ($status) {
            $sql .= " WHERE d.Status = ?";
            $params[] = $status;
        }
        $sql .= " ORDER BY d.created_date DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $deliveryOrders = $stmt->fetchAll();

        $statuses = ['Submitted', 'Under Review', 'Approved', 'Rejected'];
        $pageTitle = 'Delivery Orders Review';
        $activePage = 'review_document';
        include ROOT_PATH . '/Presentation/View/SharedUI/topbar.php';
        include ROOT_PATH . '/Presentation/View/SharedUI/sidebarM4.php';
        include ROOT_PATH . '/Presentation/View/Module4/do/index.php';
        include ROOT_PATH . '/Presentation/View/SharedUI/footer.php';
    }

    public function doShow($id) {
        $do = $this->getDoWithDetails($id);
        $items = $this->itemModel->getItemsByDO($id);
        $invoices = $this->invoiceModel->getByDO($id);
        $officers = $this->getOfficers();

        $pageTitle = 'DO #' . $do['DO_number'];
        $activePage = 'review_document';
        include ROOT_PATH . '/Presentation/View/SharedUI/topbar.php';
        include ROOT_PATH . '/Presentation/View/SharedUI/sidebarM4.php';
        include ROOT_PATH . '/Presentation/View/Module4/do/show.php';
        include ROOT_PATH . '/Presentation/View/SharedUI/footer.php';
    }

    public function doAssign($id) {
        $staffId = (int)($_POST['staff_id'] ?? 0);
        if (!$staffId) {
            flash('error', 'Please select a reviewer.');
            redirect("/KTMeDOIS/Presentation/Public/indexM4.php?action=do_show&id=$id");
            return;
        }
        // verify staff exists
        $stmt = $this->pdo->prepare("SELECT 1 FROM `ktm staff` WHERE User_ID = ?");
        $stmt->execute([$staffId]);
        if (!$stmt->fetch()) {
            flash('error', 'Selected officer does not exist.');
            redirect("/KTMeDOIS/Presentation/Public/indexM4.php?action=do_show&id=$id");
            return;
        }

        $do = $this->getDoWithDetails($id);
        $newStatus = ($do['Status'] === 'Submitted') ? 'Under Review' : $do['Status'];
        $upd = $this->pdo->prepare("UPDATE `do` SET Staff_id = ?, Status = ? WHERE DO_id = ?");
        $upd->execute([$staffId, $newStatus, $id]);

        $this->auditLogModel->log($staffId, "Assigned DO {$do['DO_number']} to reviewer", $do['DO_number']);
        $this->notificationModel->createForStaff($staffId, 'System', "You have been assigned to review DO {$do['DO_number']}.");

        flash('success', 'Reviewer assigned successfully.');
        redirect("/KTMeDOIS/Presentation/Public/indexM4.php?action=do_show&id=$id");
    }

    public function doApprove($id) {
        $do = $this->getDoWithDetails($id);
        $upd = $this->pdo->prepare("UPDATE `do` SET Status = 'Approved', Reason = '-' WHERE DO_id = ?");
        $upd->execute([$id]);

        $this->auditLogModel->log($_SESSION['user_id'], "Approved DO {$do['DO_number']}", $do['DO_number']);
        $this->notificationModel->createForSupplier($do['supplier_ID'], 'Email', "DO {$do['DO_number']} has been approved.");

        flash('success', 'Delivery Order approved.');
        redirect("/KTMeDOIS/Presentation/Public/indexM4.php?action=do_show&id=$id");
    }

    public function doReject($id) {
        $reason = trim($_POST['reason'] ?? '');
        if (!$reason) {
            flash('error', 'Rejection reason is required.');
            redirect("/KTMeDOIS/Presentation/Public/indexM4.php?action=do_show&id=$id");
            return;
        }
        if (strlen($reason) > 250) {
            flash('error', 'Reason may not exceed 250 characters.');
            redirect("/KTMeDOIS/Presentation/Public/indexM4.php?action=do_show&id=$id");
            return;
        }

        $do = $this->getDoWithDetails($id);
        $upd = $this->pdo->prepare("UPDATE `do` SET Status = 'Rejected', Reason = ? WHERE DO_id = ?");
        $upd->execute([$reason, $id]);

        $this->auditLogModel->log($_SESSION['user_id'], "Rejected DO {$do['DO_number']}: $reason", $do['DO_number']);
        $this->notificationModel->createForSupplier($do['supplier_ID'], 'Email', "DO {$do['DO_number']} has been rejected: $reason");

        flash('success', 'Delivery Order rejected.');
        redirect("/KTMeDOIS/Presentation/Public/indexM4.php?action=do_show&id=$id");
    }

    public function doExportPdf($id) {
        $do = $this->getDoWithDetails($id);
        $items = $this->itemModel->getItemsByDO($id);
        // Output as HTML for browser print-to-PDF
        include ROOT_PATH . '/Presentation/View/Module4/do/pdf.php';
        exit;
    }

    // ---------- INVOICE REVIEW ----------
    public function invoiceIndex() {
        $status = $_GET['status'] ?? '';
        $sql = "SELECT i.*, s.Supplier_name, d.DO_number, k.Username AS handler_name
                FROM invoice i
                LEFT JOIN supplier s ON s.Supplier_id = i.supplier_ID
                LEFT JOIN `do` d ON d.DO_id = i.DO_id
                LEFT JOIN `ktm staff` k ON k.User_ID = i.Staff_id";
        $params = [];
        if ($status) {
            $sql .= " WHERE i.Status = ?";
            $params[] = $status;
        }
        $sql .= " ORDER BY i.Created_At DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $invoices = $stmt->fetchAll();

        $statuses = ['Submitted', 'Finance Review', 'Payment Processing', 'Paid', 'Rejected'];
        $pageTitle = 'Invoices Review';
        $activePage = 'review_document';
        include ROOT_PATH . '/Presentation/View/SharedUI/topbar.php';
        include ROOT_PATH . '/Presentation/View/SharedUI/sidebarM4.php';
        include ROOT_PATH . '/Presentation/View/Module4/invoice/index.php';
        include ROOT_PATH . '/Presentation/View/SharedUI/footer.php';
    }

    public function invoiceShow($id) {
        $inv = $this->getInvoiceWithDetails($id);
        $financeOfficers = $this->getFinanceOfficers();
        $pageTitle = 'Invoice #' . $inv['Invoice_num'];
        $activePage = 'review_document';
        include ROOT_PATH . '/Presentation/View/SharedUI/topbar.php';
        include ROOT_PATH . '/Presentation/View/SharedUI/sidebarM4.php';
        include ROOT_PATH . '/Presentation/View/Module4/invoice/show.php';
        include ROOT_PATH . '/Presentation/View/SharedUI/footer.php';
    }

    public function invoiceForward($id) {
        $staffId = (int)($_POST['staff_id'] ?? 0);
        if (!$staffId) {
            flash('error', 'Please select a Finance Officer.');
            redirect("/KTMeDOIS/Presentation/Public/indexM4.php?action=invoice_show&id=$id");
            return;
        }
        $stmt = $this->pdo->prepare("SELECT 1 FROM `ktm staff` WHERE User_ID = ?");
        $stmt->execute([$staffId]);
        if (!$stmt->fetch()) {
            flash('error', 'Selected officer does not exist.');
            redirect("/KTMeDOIS/Presentation/Public/indexM4.php?action=invoice_show&id=$id");
            return;
        }

        $inv = $this->getInvoiceWithDetails($id);
        $upd = $this->pdo->prepare("UPDATE invoice SET Staff_id = ?, Status = 'Finance Review' WHERE Invoice_id = ?");
        $upd->execute([$staffId, $id]);

        $this->auditLogModel->log($_SESSION['user_id'], "Forwarded Invoice {$inv['Invoice_num']} to Finance", $inv['Invoice_num']);
        $this->notificationModel->createForStaff($staffId, 'System', "Invoice {$inv['Invoice_num']} forwarded to you.");

        flash('success', 'Invoice forwarded to Finance.');
        redirect("/KTMeDOIS/Presentation/Public/indexM4.php?action=invoice_show&id=$id");
    }

    public function invoiceUpdateStatus($id) {
        $status = $_POST['status'] ?? '';
        $allowed = ['Finance Review', 'Payment Processing', 'Paid'];
        if (!in_array($status, $allowed, true)) {
            flash('error', 'Invalid status.');
            redirect("/KTMeDOIS/Presentation/Public/indexM4.php?action=invoice_show&id=$id");
            return;
        }
        $inv = $this->getInvoiceWithDetails($id);
        $upd = $this->pdo->prepare("UPDATE invoice SET Status = ? WHERE Invoice_id = ?");
        $upd->execute([$status, $id]);

        $this->auditLogModel->log($_SESSION['user_id'], "Updated Invoice {$inv['Invoice_num']} to $status", $inv['Invoice_num']);
        $this->notificationModel->createForSupplier($inv['supplier_ID'], 'Email', "Invoice {$inv['Invoice_num']} status changed to $status.");

        flash('success', 'Invoice status updated.');
        redirect("/KTMeDOIS/Presentation/Public/indexM4.php?action=invoice_show&id=$id");
    }

    public function invoiceReject($id) {
        $reason = trim($_POST['reason'] ?? '');
        if (!$reason) {
            flash('error', 'Rejection reason is required.');
            redirect("/KTMeDOIS/Presentation/Public/indexM4.php?action=invoice_show&id=$id");
            return;
        }
        if (strlen($reason) > 250) {
            flash('error', 'Reason may not exceed 250 characters.');
            redirect("/KTMeDOIS/Presentation/Public/indexM4.php?action=invoice_show&id=$id");
            return;
        }

        $inv = $this->getInvoiceWithDetails($id);
        $upd = $this->pdo->prepare("UPDATE invoice SET Status = 'Rejected', Reason = ? WHERE Invoice_id = ?");
        $upd->execute([$reason, $id]);

        $this->auditLogModel->log($_SESSION['user_id'], "Rejected Invoice {$inv['Invoice_num']}: $reason", $inv['Invoice_num']);
        $this->notificationModel->createForSupplier($inv['supplier_ID'], 'Email', "Invoice {$inv['Invoice_num']} rejected: $reason");

        flash('success', 'Invoice rejected.');
        redirect("/KTMeDOIS/Presentation/Public/indexM4.php?action=invoice_show&id=$id");
    }

    public function invoiceExportPdf($id) {
        $inv = $this->getInvoiceWithDetails($id);
        include ROOT_PATH . '/Presentation/View/Module4/invoice/pdf.php';
        exit;
    }

    // ---------- AUDIT LOG ----------
    public function auditlogIndex() {
        $page = max(1, (int)($_GET['page'] ?? 1));
        $perPage = 20;
        $offset = ($page - 1) * $perPage;

        $sql = "SELECT a.*, k.Username AS staff_name
                FROM auditlog a
                LEFT JOIN `ktm staff` k ON k.User_ID = a.User_ID
                ORDER BY a.Timestamp DESC
                LIMIT $perPage OFFSET $offset";
        $stmt = $this->pdo->query($sql);
        $logs = $stmt->fetchAll();

        $countSql = "SELECT COUNT(*) FROM auditlog";
        $countStmt = $this->pdo->query($countSql);
        $total = $countStmt->fetchColumn();
        $lastPage = ceil($total / $perPage);

        $pageTitle = 'Audit Log';
        $activePage = 'review_document';
        include ROOT_PATH . '/Presentation/View/SharedUI/topbar.php';
        include ROOT_PATH . '/Presentation/View/SharedUI/sidebarM4.php';
        include ROOT_PATH . '/Presentation/View/Module4/auditlog/index.php';
        include ROOT_PATH . '/Presentation/View/SharedUI/footer.php';
    }

    public function auditlogExport() {
        $stmt = $this->pdo->query("SELECT a.*, k.Username AS staff_name
                                    FROM auditlog a
                                    LEFT JOIN `ktm staff` k ON k.User_ID = a.User_ID
                                    ORDER BY a.Timestamp DESC");
        $logs = $stmt->fetchAll();
        include ROOT_PATH . '/Presentation/View/Module4/auditlog/pdf.php';
        exit;
    }

    // ---------- HELPERS ----------
    private function getDoWithDetails($id) {
        $stmt = $this->pdo->prepare("SELECT d.*, s.Supplier_name, k.Username AS reviewer_name
                                     FROM `do` d
                                     LEFT JOIN supplier s ON s.Supplier_id = d.supplier_ID
                                     LEFT JOIN `ktm staff` k ON k.User_ID = d.Staff_id
                                     WHERE d.DO_id = ?");
        $stmt->execute([$id]);
        $do = $stmt->fetch();
        if (!$do) {
            http_response_code(404);
            die('<h1>404 — Delivery Order not found</h1>');
        }
        return $do;
    }

    private function getInvoiceWithDetails($id) {
        $stmt = $this->pdo->prepare("SELECT i.*, s.Supplier_name, d.DO_number, k.Username AS handler_name
                                     FROM invoice i
                                     LEFT JOIN supplier s ON s.Supplier_id = i.supplier_ID
                                     LEFT JOIN `do` d ON d.DO_id = i.DO_id
                                     LEFT JOIN `ktm staff` k ON k.User_ID = i.Staff_id
                                     WHERE i.Invoice_id = ?");
        $stmt->execute([$id]);
        $inv = $stmt->fetch();
        if (!$inv) {
            http_response_code(404);
            die('<h1>404 — Invoice not found</h1>');
        }
        return $inv;
    }

    private function getOfficers() {
        $stmt = $this->pdo->query("SELECT User_ID, Username FROM `ktm staff` WHERE Role = 'KTM Officer' AND Status = 'Active' ORDER BY Username");
        return $stmt->fetchAll();
    }

    private function getFinanceOfficers() {
        $stmt = $this->pdo->query("SELECT User_ID, Username FROM `ktm staff` WHERE Role = 'Finance Officer' AND Status = 'Active' ORDER BY Username");
        return $stmt->fetchAll();
    }
}