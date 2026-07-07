<?php
// Application/Model/modelM1/SupplierModel.php

class SupplierModel
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function getAll()
    {
        $stmt = $this->pdo->query("SELECT * FROM supplier ORDER BY Supplier_id DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM supplier WHERE Supplier_id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getByVendorNumber($vendorNumber)
    {
        $stmt = $this->pdo->prepare("SELECT * FROM supplier WHERE Vendor_Number = ?");
        $stmt->execute([$vendorNumber]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getDOBySupplier($supplierId, $limit = null)
    {
        $sql = "SELECT * FROM do WHERE supplier_ID = ? ORDER BY created_date DESC";
        if ($limit) {
            $sql .= " LIMIT " . intval($limit);
        }
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([$supplierId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function countDO($supplierId, $status = null)
    {
        $sql = "SELECT COUNT(*) as count FROM do WHERE supplier_ID = ?";
        $params = [$supplierId];
        if ($status) {
            $sql .= " AND Status = ?";
            $params[] = $status;
        }
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] ?? 0;
    }

    public function create($data)
    {
        $vendorNum = rand(10001, 99999);
        $stmt = $this->pdo->prepare("SELECT Vendor_Number FROM supplier WHERE Vendor_Number = ?");
        $stmt->execute([$vendorNum]);
        while ($stmt->fetch()) {
            $vendorNum = rand(10001, 99999);
            $stmt->execute([$vendorNum]);
        }

        $sql = "INSERT INTO supplier 
                    (Supplier_name, Contac_person, phone, email, status, Billing_address, Vendor_Number) 
                VALUES (?, ?, ?, ?, ?, ?, ?)";
        $stmt = $this->pdo->prepare($sql);
        if ($stmt->execute([
            $data['name'],
            $data['contact'],
            $data['phone'],
            $data['email'],
            $data['status'],
            $data['address'],
            $vendorNum
        ])) {
            return $this->pdo->lastInsertId();
        }
        return false;
    }

    public function update($id, $data)
    {
        $sql = "UPDATE supplier SET 
                    Supplier_name = ?, Contac_person = ?, phone = ?, email = ?,
                    Billing_address = ?, status = ?
                WHERE Supplier_id = ?";
        $stmt = $this->pdo->prepare($sql);
        return $stmt->execute([
            $data['name'],
            $data['contact'],
            $data['phone'],
            $data['email'],
            $data['address'],
            $data['status'],
            $id
        ]);
    }

    public function delete($id)
    {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) as count FROM do WHERE supplier_ID = ?");
        $stmt->execute([$id]);
        $doCount = $stmt->fetchColumn();

        if ($doCount > 0) {
            return ['success' => false, 'message' => "Cannot delete supplier with $doCount DO records."];
        }

        $stmt = $this->pdo->prepare("DELETE FROM supplier WHERE Supplier_id = ?");
        if ($stmt->execute([$id]) && $stmt->rowCount() > 0) {
            return ['success' => true, 'message' => 'Supplier deleted successfully.'];
        }
        return ['success' => false, 'message' => 'Error deleting supplier.'];
    }

    public function getStats()
    {
        $stats = [];
        $stmt = $this->pdo->query("SELECT COUNT(*) as count FROM supplier");
        $stats['total'] = $stmt->fetchColumn();
        $stmt = $this->pdo->query("SELECT COUNT(*) as count FROM supplier WHERE status = 'Active'");
        $stats['active'] = $stmt->fetchColumn();
        $stmt = $this->pdo->query("SELECT COUNT(*) as count FROM supplier WHERE status = 'Pending Verification'");
        $stats['pending'] = $stmt->fetchColumn();
        $stmt = $this->pdo->query("SELECT COUNT(*) as count FROM supplier WHERE status = 'Inactive'");
        $stats['inactive'] = $stmt->fetchColumn();
        return $stats;
    }
}