<?php
// Application/Model/AuditLog.php

require_once __DIR__ . '/../../Data/db.php';

class AuditLog {
    private $db;
    public $Log_ID;
    public $User_ID;
    public $Action;
    public $Affected_Record;
    public $Timestamp;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function load($id) {
        $stmt = $this->db->prepare("SELECT * FROM auditlog WHERE Log_ID = ?");
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

    public function log($userId, $action, $affectedRecord) {
        $stmt = $this->db->prepare("
            INSERT INTO auditlog (User_ID, Action, Affected_Record, Timestamp) 
            VALUES (?, ?, ?, NOW())
        ");
        return $stmt->execute([$userId, $action, $affectedRecord]);
    }

    public function logVendorAction($vendorId, $action, $affectedRecord) {
        // Use a default system user (e.g., 20001) since vendor is not in staff table
        return $this->log(20001, $action, $affectedRecord);
    }

    public function getByUser($userId) {
        $stmt = $this->db->prepare("SELECT * FROM auditlog WHERE User_ID = ? ORDER BY Timestamp DESC");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public function getByRecord($record) {
        $stmt = $this->db->prepare("SELECT * FROM auditlog WHERE Affected_Record LIKE ? ORDER BY Timestamp DESC");
        $stmt->execute(["%$record%"]);
        return $stmt->fetchAll();
    }

    public function getRecent($limit = 50) {
        $stmt = $this->db->prepare("SELECT * FROM auditlog ORDER BY Timestamp DESC LIMIT ?");
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }

    public function getByDateRange($from, $to) {
        $stmt = $this->db->prepare("
            SELECT * FROM auditlog 
            WHERE DATE(Timestamp) >= ? AND DATE(Timestamp) <= ? 
            ORDER BY Timestamp DESC
        ");
        $stmt->execute([$from, $to]);
        return $stmt->fetchAll();
    }
}