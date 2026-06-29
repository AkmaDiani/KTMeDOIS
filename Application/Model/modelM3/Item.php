<?php
// Application/Model/Item.php

require_once __DIR__ . '/../../Data/db.php';

class Item {
    private $db;

    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }

    public function getItemsByDO($doId) {
        $stmt = $this->db->prepare("SELECT * FROM item WHERE DO_id = ?");
        $stmt->execute([$doId]);
        return $stmt->fetchAll();
    }

    public function getItemsByInvoice($invoiceId) {
        $stmt = $this->db->prepare("SELECT * FROM item WHERE invoice_id = ?");
        $stmt->execute([$invoiceId]);
        return $stmt->fetchAll();
    }
}