<?php
// Application/Model/Supplier.php

require_once __DIR__ . '/../../../Data/db.php';

class Supplier {
    private $db;
    public $Supplier_id;
    public $Supplier_name;
    public $Vendor_Number;
    public $Contact_person;
    public $phone;
    public $email;
    public $status;
    public $Inactive_date;
    public $Billing_address;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function load($id) {
        $stmt = $this->db->prepare("SELECT * FROM supplier WHERE Supplier_id = ?");
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

    public function getByEmail($email) {
        $stmt = $this->db->prepare("SELECT * FROM supplier WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch();
    }

    public function getSupplierArray($id) {
        $stmt = $this->db->prepare("SELECT * FROM supplier WHERE Supplier_id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();  // Returns array
    }

    public function getSupplierWithInvoices($supplierId) {
        $stmt = $this->db->prepare("
            SELECT s.*, COUNT(i.Invoice_id) as invoice_count, SUM(i.Total) as total_amount
            FROM supplier s
            LEFT JOIN invoice i ON s.Supplier_id = i.supplier_ID
            WHERE s.Supplier_id = ?
            GROUP BY s.Supplier_id
        ");
        $stmt->execute([$supplierId]);
        return $stmt->fetch();
    }

    public function isActive() {
        return $this->status === 'Active';
    }

    public function isInactive() {
        return $this->status === 'Inactive';
    }

    public function getFullAddress() {
        return $this->Billing_address;
    }
}