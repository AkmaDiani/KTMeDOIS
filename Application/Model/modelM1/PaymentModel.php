<?php
// ============================================
// PAYMENT MODEL
// ============================================

class PaymentModel
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function getBySupplier($supplierId)
    {
        $supplierId = mysqli_real_escape_string($this->conn, $supplierId);
        $query = "SELECT p.*, i.Invoice_num, i.Total as Invoice_Total 
                  FROM payment p 
                  LEFT JOIN invoice i ON p.Invoice_ID = i.Invoice_id 
                  WHERE p.Supplier_ID = '$supplierId' 
                  ORDER BY p.Created_At DESC";
        $result = mysqli_query($this->conn, $query);
        $data = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = $row;
        }
        return $data;
    }

    public function getSummary($supplierId)
    {
        $supplierId = mysqli_real_escape_string($this->conn, $supplierId);
        $totalPaid = $this->getTotalByStatus($supplierId, 'Paid');
        $totalPending = $this->getTotalByStatus($supplierId, 'Pending');
        $totalProcessing = $this->getTotalByStatus($supplierId, 'Processing');
        $totalCount = $this->getCount($supplierId);
        return [
            'total_paid' => $totalPaid,
            'total_pending' => $totalPending,
            'total_processing' => $totalProcessing,
            'total_count' => $totalCount,
            'total_amount' => $totalPaid + $totalPending + $totalProcessing
        ];
    }

    private function getTotalByStatus($supplierId, $status)
    {
        $supplierId = mysqli_real_escape_string($this->conn, $supplierId);
        $status = mysqli_real_escape_string($this->conn, $status);
        $query = "SELECT SUM(Payment_Amount) as total FROM payment 
                  WHERE Supplier_ID = '$supplierId' AND Payment_Status = '$status'";
        $result = mysqli_query($this->conn, $query);
        $row = mysqli_fetch_assoc($result);
        return $row['total'] ?? 0;
    }

    private function getCount($supplierId)
    {
        $supplierId = mysqli_real_escape_string($this->conn, $supplierId);
        $query = "SELECT COUNT(*) as count FROM payment WHERE Supplier_ID = '$supplierId'";
        $result = mysqli_query($this->conn, $query);
        $row = mysqli_fetch_assoc($result);
        return $row['count'] ?? 0;
    }
}
