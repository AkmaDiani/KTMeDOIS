<?php
// ============================================
// MODULE 1 - SUPPLIER API GATEWAY
// ============================================

require_once __DIR__ . '/../../Helpers/functions.php';

class SupplierAPI
{
    private $conn;
    private $conn_supplier;

    public function __construct($conn, $conn_supplier)
    {
        $this->conn = $conn;
        $this->conn_supplier = $conn_supplier;
        header('Content-Type: application/json');
    }

    public function getSupplier()
    {
        $id = $_GET['id'] ?? $_SESSION['supplier_id'] ?? null;
        if (!$id) {
            $this->sendResponse(false, 'Supplier ID required', null, 400);
            return;
        }
        $supplier = getSupplierFromExternal($id);
        if (!$supplier) {
            $this->sendResponse(false, 'Supplier not found', null, 404);
            return;
        }
        $this->sendResponse(true, 'Supplier found', $supplier);
    }

    public function getDO()
    {
        $id = $_GET['id'] ?? $_SESSION['supplier_id'] ?? null;
        if (!$id) {
            $this->sendResponse(false, 'Supplier ID required', null, 400);
            return;
        }
        $query = "SELECT * FROM do WHERE supplier_ID = '$id' ORDER BY created_date DESC";
        $result = mysqli_query($this->conn, $query);
        $list = [];
        while ($row = mysqli_fetch_assoc($result)) $list[] = $row;
        $this->sendResponse(true, 'DO list retrieved', $list);
    }

    public function getPayment()
    {
        $id = $_GET['id'] ?? $_SESSION['supplier_id'] ?? null;
        if (!$id) {
            $this->sendResponse(false, 'Supplier ID required', null, 400);
            return;
        }
        $query = "SELECT p.*, i.Invoice_num, i.Total as Invoice_Total 
                  FROM payment p 
                  LEFT JOIN invoice i ON p.Invoice_ID = i.Invoice_id 
                  WHERE p.Supplier_ID = '$id' 
                  ORDER BY p.Created_At DESC";
        $result = mysqli_query($this->conn, $query);
        $list = [];
        while ($row = mysqli_fetch_assoc($result)) $list[] = $row;
        $this->sendResponse(true, 'Payment list retrieved', $list);
    }

    public function syncSupplier()
    {
        $id = $_GET['id'] ?? $_POST['id'] ?? null;
        if (!$id) {
            $this->sendResponse(false, 'Supplier ID required', null, 400);
            return;
        }
        $result = syncSupplierToMain($id);
        $this->sendResponse($result, $result ? 'Supplier synced' : 'Sync failed');
    }

    public function getAllSuppliers()
    {
        $query = "SELECT * FROM supplier ORDER BY Supplier_name";
        $result = mysqli_query($this->conn, $query);
        $list = [];
        while ($row = mysqli_fetch_assoc($result)) $list[] = $row;
        $this->sendResponse(true, 'Suppliers retrieved', $list);
    }

    public function getSupplierStatus()
    {
        $id = $_GET['id'] ?? $_SESSION['supplier_id'] ?? null;
        if (!$id) {
            $this->sendResponse(false, 'Supplier ID required', null, 400);
            return;
        }
        $supplier = getSupplierFromExternal($id);
        if (!$supplier) {
            $this->sendResponse(false, 'Supplier not found', null, 404);
            return;
        }
        $this->sendResponse(true, 'Supplier status', [
            'id' => $supplier['SUPPLIERID'],
            'name' => $supplier['SUPPLIER_COMP_NAME'],
            'status' => $supplier['SUPPLIER_CTC_STATUS'],
            'active' => $supplier['SUPPLIER_CTC_STATUS'] === 'Active',
            'expiry' => $supplier['SUPPLIER_EXPIRED_DATE']
        ]);
    }

    private function sendResponse($success, $message, $data = null, $statusCode = 200)
    {
        http_response_code($statusCode);
        echo json_encode([
            'success' => $success,
            'message' => $message,
            'data' => $data,
            'timestamp' => date('Y-m-d H:i:s')
        ]);
        exit();
    }
}
