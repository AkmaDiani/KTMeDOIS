<?php
// Application/Model/Notification.php

require_once __DIR__ . '/../../Data/db.php';

class Notification {
    private $db;
    public $Notification_ID;
    public $User_ID;
    public $Supplier_id;
    public $Type;
    public $Content;
    public $Status;
    public $Creates_At;

    const TYPE_SYSTEM = 'System';
    const TYPE_EMAIL = 'Email';
    const STATUS_SENT = 'Sent';
    const STATUS_PENDING = 'Pending';
    const STATUS_FAILED = 'Failed';

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function load($id) {
        $stmt = $this->db->prepare("SELECT * FROM notification WHERE Notification_ID = ?");
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

    public function create($userId, $supplierId, $type, $content) {
        $stmt = $this->db->prepare("
            INSERT INTO notification (User_ID, Supplier_id, Type, Content, Status, Creates_At) 
            VALUES (?, ?, ?, ?, 'Sent', NOW())
        ");
        return $stmt->execute([$userId, $supplierId, $type, $content]);
    }

    public function createForStaff($userId, $type, $content) {
        return $this->create($userId, null, $type, $content);
    }

    public function createForSupplier($supplierId, $type, $content) {
        return $this->create(null, $supplierId, $type, $content);
    }

    public function getByUser($userId) {
        $stmt = $this->db->prepare("SELECT * FROM notification WHERE User_ID = ? ORDER BY Creates_At DESC");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public function getBySupplier($supplierId) {
        $stmt = $this->db->prepare("SELECT * FROM notification WHERE Supplier_id = ? ORDER BY Creates_At DESC");
        $stmt->execute([$supplierId]);
        return $stmt->fetchAll();
    }

    public function getUnread($userId) {
        $stmt = $this->db->prepare("SELECT * FROM notification WHERE User_ID = ? AND Status = 'Sent' ORDER BY Creates_At DESC");
        $stmt->execute([$userId]);
        return $stmt->fetchAll();
    }

    public function markAsRead($id) {
        $stmt = $this->db->prepare("UPDATE notification SET Status = 'Read' WHERE Notification_ID = ?");
        return $stmt->execute([$id]);
    }
}