<?php
class Supplier
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function getSupplierById($supplier_id)
    {
        $stmt = $this->pdo->prepare("
            SELECT *
            FROM supplier
            WHERE Supplier_id = ?
        ");
        $stmt->execute([$supplier_id]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getActiveSuppliers()
    {
        $stmt = $this->pdo->query("
            SELECT Supplier_id, Supplier_name
            FROM supplier
            WHERE status = 'Active'
            ORDER BY Supplier_name ASC
        ");

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
