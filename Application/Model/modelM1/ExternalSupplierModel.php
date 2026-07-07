<?php
// Application/Model/modelM1/ExternalSupplierModel.php

class ExternalSupplierModel
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function find($id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM supplier WHERE SUPPLIERID = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function findByUsername($username)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM supplier WHERE username = ? AND SUPPLIER_CTC_STATUS = 'Active'");
        $stmt->execute([$username]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function findByEmail($email)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM supplier WHERE SUPPLIER_EMAIL_ADD = ? AND SUPPLIER_CTC_STATUS = 'Active'");
        $stmt->execute([$email]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function findByUsernameOrEmail($input)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM supplier WHERE (username = ? OR SUPPLIER_EMAIL_ADD = ?) AND SUPPLIER_CTC_STATUS = 'Active'");
        $stmt->execute([$input, $input]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function verifyPassword($supplier, $password)
    {
        return md5($password) == ($supplier['password'] ?? '');
    }

    public function updateLastLogin($id)
    {
        $stmt = $this->pdo->prepare("UPDATE supplier SET last_login = NOW() WHERE SUPPLIERID = ?");
        return $stmt->execute([$id]);
    }

    public function getActiveSuppliers()
    {
        $stmt = $this->pdo->query("SELECT * FROM supplier WHERE SUPPLIER_CTC_STATUS = 'Active' ORDER BY SUPPLIER_COMP_NAME");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}