<?php
// Application/Model/Invoice.php

require_once __DIR__ . '/../../Data/db.php';

class Invoice {
    private $db;
    public $Invoice_id;
    public $Invoice_num;
    public $DO_id;
    public $supplier_ID;
    public $Staff_id;
    public $issue_date;
    public $Subtotal;
    public $Tax;
    public $Total;
    public $discount;
    public $penalty;
    public $Status;
    public $Reason;
    public $Created_At;
    public $Description;
    public $Credit_note;

    const STATUS_SUBMITTED = 'Submitted';
    const STATUS_FINANCE_REVIEW = 'Finance Review';
    const STATUS_PAYMENT_PROCESSING = 'Payment Processing';
    const STATUS_PAID = 'Paid';
    const STATUS_REJECTED = 'Rejected';
    const STATUS_DRAFT = 'Draft';

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function load($id) {
        $stmt = $this->db->prepare("SELECT * FROM invoice WHERE Invoice_id = ?");
        $stmt->execute([$id]);
        $data = $stmt->fetch();
        if ($data) {
            foreach ($data as $key => $value) {
                if (property_exists($this, $key)) {
                    $this->$key = $value;
                }
            }
            return true;
        }
        return false;
    }

    public function save() {
        if ($this->Invoice_id) {
            $sql = "UPDATE invoice SET 
                    Invoice_num = ?, DO_id = ?, supplier_ID = ?, Staff_id = ?,
                    issue_date = ?, Subtotal = ?, Tax = ?, Total = ?,
                    discount = ?, penalty = ?, Status = ?, Reason = ?,
                    Description = ?, Credit_note = ?
                    WHERE Invoice_id = ?";
            $stmt = $this->db->prepare($sql);
            return $stmt->execute([
                $this->Invoice_num,
                $this->DO_id,
                $this->supplier_ID,
                $this->Staff_id,
                $this->issue_date,
                $this->Subtotal,
                $this->Tax,
                $this->Total,
                $this->discount,
                $this->penalty,
                $this->Status,
                $this->Reason,
                $this->Description,
                $this->Credit_note,
                $this->Invoice_id
            ]);
        } else {
            $sql = "INSERT INTO invoice 
                    (Invoice_num, DO_id, supplier_ID, Staff_id, issue_date,
                     Subtotal, Tax, Total, discount, penalty, Status, Reason,
                     Description, Credit_note, Created_At)
                    VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?, NOW())";
            $stmt = $this->db->prepare($sql);
            $result = $stmt->execute([
                $this->Invoice_num,
                $this->DO_id,
                $this->supplier_ID,
                $this->Staff_id,
                $this->issue_date,
                $this->Subtotal,
                $this->Tax,
                $this->Total,
                $this->discount,
                $this->penalty,
                $this->Status,
                $this->Reason,
                $this->Description,
                $this->Credit_note
            ]);
            if ($result) {
                $this->Invoice_id = $this->db->lastInsertId();
            }
            return $result;
        }
    }

    // ✅ FIXED: Get items using DO_id (not invoice_id)
    public function getItems() {
        $stmt = $this->db->prepare("SELECT * FROM item WHERE DO_id = ?");
        $stmt->execute([$this->DO_id]);
        return $stmt->fetchAll();
    }

    public function getDO() {
        $stmt = $this->db->prepare("
        SELECT d.*, s.Username as staff_name, s.Email as staff_email, s.User_ID as staff_user_id
        FROM do d
        LEFT JOIN `ktm staff` s ON d.Staff_id = s.User_ID
        WHERE d.DO_id = ?
        ");
        $stmt->execute([$this->DO_id]);
        return $stmt->fetch();
    }

    public function getSupplier() {
        $stmt = $this->db->prepare("SELECT * FROM supplier WHERE Supplier_id = ?");
        $stmt->execute([$this->supplier_ID]);
        return $stmt->fetch();
    }

    public function getStaff() {
        $stmt = $this->db->prepare("SELECT * FROM `ktm staff` WHERE User_ID = ?");
        $stmt->execute([$this->Staff_id]);
        $result = $stmt->fetch();
        return $result !== false ? $result : null;
    }

    // Get invoices by supplier
    public function getBySupplier($supplierId) {
        $stmt = $this->db->prepare("
            SELECT i.*, d.DO_number 
            FROM invoice i
            LEFT JOIN do d ON i.DO_id = d.DO_id
            WHERE i.supplier_ID = ? 
            ORDER BY i.Created_At DESC
        ");
        $stmt->execute([$supplierId]);
        return $stmt->fetchAll();
    }

    // Get all invoices except Draft (for officers)
    public function getAllExceptDraft() {
        $stmt = $this->db->prepare("
            SELECT i.*, s.Supplier_name, d.DO_number 
            FROM invoice i 
            LEFT JOIN supplier s ON i.supplier_ID = s.Supplier_id 
            LEFT JOIN do d ON i.DO_id = d.DO_id
            WHERE i.Status != 'Draft' 
            ORDER BY i.Created_At DESC
        ");
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Get aging data
    public function getAgingData() {
        $invoices = $this->getAllExceptDraft();
        $agingData = [];
        $today = new DateTime();
        
        foreach ($invoices as $invoice) {
            if (in_array($invoice['Status'], ['Paid', 'Rejected'])) {
                continue;
            }
            
            $supplierName = $invoice['Supplier_name'] ?? 'Unknown';
            $invoiceDate = new DateTime($invoice['issue_date']);
            $daysDiff = $today->diff($invoiceDate)->days;
            
            if (!isset($agingData[$supplierName])) {
                $agingData[$supplierName] = [
                    '0-30' => 0,
                    '31-60' => 0,
                    '61-90' => 0,
                    '90+' => 0,
                    'total' => 0
                ];
            }
            
            $amount = floatval($invoice['Total']);
            
            if ($daysDiff <= 30) {
                $agingData[$supplierName]['0-30'] += $amount;
            } elseif ($daysDiff <= 60) {
                $agingData[$supplierName]['31-60'] += $amount;
            } elseif ($daysDiff <= 90) {
                $agingData[$supplierName]['61-90'] += $amount;
            } else {
                $agingData[$supplierName]['90+'] += $amount;
            }
            
            $agingData[$supplierName]['total'] += $amount;
        }
        
        return $agingData;
    }

    // Generate invoice number
    public function generateNumber() {
        $year = date('Y');
        $stmt = $this->db->prepare("SELECT Invoice_num FROM invoice WHERE Invoice_num LIKE ? ORDER BY Invoice_id DESC LIMIT 1");
        $stmt->execute(["INV-$year-%"]);
        $last = $stmt->fetch();
        
        if ($last) {
            $parts = explode('-', $last['Invoice_num']);
            $num = intval($parts[2]) + 1;
        } else {
            $num = 1;
        }
        
        return sprintf("INV-%s-%04d", $year, $num);
    }

    // Status check methods
    public function isRejected(): bool {
        return $this->Status === self::STATUS_REJECTED;
    }

    public function isDraft(): bool {
        return $this->Status === self::STATUS_DRAFT;
    }

    public function isSubmitted(): bool {
        return $this->Status === self::STATUS_SUBMITTED;
    }

    public function canBeEdited(): bool {
        return $this->Status === self::STATUS_DRAFT;
    }

    public function getStatusLabel(): string {
        $labels = [
            self::STATUS_SUBMITTED => 'Submitted',
            self::STATUS_FINANCE_REVIEW => 'Finance Review',
            self::STATUS_PAYMENT_PROCESSING => 'Payment Processing',
            self::STATUS_PAID => 'Paid',
            self::STATUS_REJECTED => 'Rejected',
            self::STATUS_DRAFT => 'Draft'
        ];
        return $labels[$this->Status] ?? $this->Status;
    }
}