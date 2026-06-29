<?php
// Application/Model/DeliveryOrder.php

require_once __DIR__ . '/../../../Data/db.php';

class DeliveryOrder {
    private $db;
    public $DO_id;
    public $DO_number;
    public $PO_number;
    public $supplier_ID;
    public $Staff_id;
    public $DO_link;
    public $Proof_link;
    public $Status;
    public $Reason;
    public $created_by;
    public $created_date;

    const STATUS_SUBMITTED = 'Submitted';
    const STATUS_UNDER_REVIEW = 'Under Review';
    const STATUS_APPROVED = 'Approved';
    const STATUS_REJECTED = 'Rejected';

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function load($id) {
        $stmt = $this->db->prepare("SELECT * FROM do WHERE DO_id = ?");
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

    public function getApprovedDOsBySupplier($supplierId) {
        $stmt = $this->db->prepare("SELECT * FROM do WHERE supplier_ID = ? AND Status = 'Approved'");
        $stmt->execute([$supplierId]);
        return $stmt->fetchAll();
    }

    public function getDOWithStaff($doId) {
        $stmt = $this->db->prepare("
            SELECT 
                d.*, 
                s.Username as staff_name, 
                s.Email as staff_email
            FROM do d
            LEFT JOIN `ktm staff` s ON d.Staff_id = s.User_ID
            WHERE d.DO_id = ?
        ");
        $stmt->execute([$doId]);
        return $stmt->fetch();
    }

    public function getDOWithItems($doId) {
        $stmt = $this->db->prepare("
            SELECT 
                d.*, 
                s.Username as staff_name, 
                s.Email as staff_email,
                i.*
            FROM do d
            LEFT JOIN `ktm staff` s ON d.Staff_id = s.User_ID
            LEFT JOIN item i ON d.DO_id = i.DO_id
            WHERE d.DO_id = ?
        ");
        $stmt->execute([$doId]);
        return $stmt->fetchAll();
    }

    public function updateProofLink($doId, $proofPath) {
        $stmt = $this->db->prepare("UPDATE do SET Proof_link = ? WHERE DO_id = ?");
        return $stmt->execute([$proofPath, $doId]);
    }

    public function getItems($doId) {
        $stmt = $this->db->prepare("SELECT * FROM item WHERE DO_id = ?");
        $stmt->execute([$doId]);
        return $stmt->fetchAll();
    }

    public function getSupplier($doId) {
        $stmt = $this->db->prepare("
            SELECT s.* 
            FROM do d
            LEFT JOIN supplier s ON d.supplier_ID = s.Supplier_id
            WHERE d.DO_id = ?
        ");
        $stmt->execute([$doId]);
        return $stmt->fetch();
    }

    public function getStaff($doId) {
        $stmt = $this->db->prepare("
            SELECT s.* 
            FROM do d
            LEFT JOIN `ktm staff` s ON d.Staff_id = s.User_ID
            WHERE d.DO_id = ?
        ");
        $stmt->execute([$doId]);
        return $stmt->fetch();
    }

    public function isApproved() {
        return $this->Status === self::STATUS_APPROVED;
    }

    public function isRejected() {
        return $this->Status === self::STATUS_REJECTED;
    }

    public function isSubmitted() {
        return $this->Status === self::STATUS_SUBMITTED;
    }

    public function getStatusLabel() {
        $labels = [
            self::STATUS_SUBMITTED => 'Submitted',
            self::STATUS_UNDER_REVIEW => 'Under Review',
            self::STATUS_APPROVED => 'Approved',
            self::STATUS_REJECTED => 'Rejected'
        ];
        return $labels[$this->Status] ?? $this->Status;
    }
}