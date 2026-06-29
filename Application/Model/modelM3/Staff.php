<?php
// Application/Model/Staff.php

require_once __DIR__ . '/../../../Data/db.php';

class Staff {
    private $db;
    public $User_ID;
    public $Username;
    public $Password_Hash;
    public $Role;
    public $Last_Login;
    public $Email;
    public $Status;

    const ROLE_OFFICER = 'KTM Officer';
    const ROLE_FINANCE = 'Finance Officer';
    const ROLE_ADMIN = 'System Admin';
    const ROLE_AUDIT = 'Audit Officer';

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function load($id) {
        $stmt = $this->db->prepare("SELECT * FROM `ktm staff` WHERE User_ID = ?");
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
        $stmt = $this->db->prepare("SELECT * FROM `ktm staff` WHERE Email = ?");
        $stmt->execute([$email]);
        return $stmt->fetch();
    }

    public function getByRole($role) {
        $stmt = $this->db->prepare("SELECT * FROM `ktm staff` WHERE Role = ? AND Status = 'Active'");
        $stmt->execute([$role]);
        return $stmt->fetchAll();
    }

    public function getFinanceOfficers() {
        return $this->getByRole(self::ROLE_FINANCE);
    }

    public function getKTMOfficers() {
        return $this->getByRole(self::ROLE_OFFICER);
    }

    public function getStaffWithInvoices($staffId) {
        $stmt = $this->db->prepare("
            SELECT s.*, COUNT(i.Invoice_id) as invoice_count
            FROM `ktm staff` s
            LEFT JOIN invoice i ON s.User_ID = i.Staff_id
            WHERE s.User_ID = ?
            GROUP BY s.User_ID
        ");
        $stmt->execute([$staffId]);
        return $stmt->fetch();
    }

    public function isActive() {
        return $this->Status === 'Active';
    }

    public function isOfficer() {
        return $this->Role === self::ROLE_OFFICER;
    }

    public function isFinance() {
        return $this->Role === self::ROLE_FINANCE;
    }

    public function getFullName() {
        return $this->Username;
    }
}