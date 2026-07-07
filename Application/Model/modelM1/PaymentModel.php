<?php
// Application/Model/modelM1/PaymentModel.php

class PaymentModel
{
    private $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    public function getBySupplier($supplierId)
    {
        $stmt = $this->pdo->prepare("SELECT p.*, i.Invoice_num, i.Total as Invoice_Total 
                                      FROM payment p 
                                      LEFT JOIN invoice i ON p.Invoice_ID = i.Invoice_id 
                                      WHERE p.Supplier_ID = ? 
                                      ORDER BY p.Created_At DESC");
        $stmt->execute([$supplierId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getSummary($supplierId)
    {
        return [
            'total_paid' => $this->getTotalByStatus($supplierId, 'Paid'),
            'total_pending' => $this->getTotalByStatus($supplierId, 'Pending'),
            'total_processing' => $this->getTotalByStatus($supplierId, 'Processing'),
            'total_count' => $this->getCount($supplierId),
            'total_amount' => $this->getTotalByStatus($supplierId, 'Paid') 
                            + $this->getTotalByStatus($supplierId, 'Pending')
                            + $this->getTotalByStatus($supplierId, 'Processing')
        ];
    }

    private function getTotalByStatus($supplierId, $status)
    {
        $stmt = $this->pdo->prepare("SELECT SUM(Payment_Amount) as total FROM payment 
                                      WHERE Supplier_ID = ? AND Payment_Status = ?");
        $stmt->execute([$supplierId, $status]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['total'] ?? 0;
    }

    private function getCount($supplierId)
    {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) as count FROM payment WHERE Supplier_ID = ?");
        $stmt->execute([$supplierId]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] ?? 0;
    }
}