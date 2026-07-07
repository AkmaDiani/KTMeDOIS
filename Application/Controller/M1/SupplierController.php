<?php
// ============================================
// MODULE 1 - SUPPLIER CONTROLLER
// ============================================

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../Helpers/functions.php';
require_once __DIR__ . '/../Middleware/API_gateways/SupplierAPI.php';
require_once __DIR__ . '/../Models/SupplierModel.php';
require_once __DIR__ . '/../Models/PaymentModel.php';

class SupplierController
{
    private $conn;
    private $conn_supplier;
    private $api;
    private $supplierModel;
    private $paymentModel;

    public function __construct($conn, $conn_supplier)
    {
        $this->conn = $conn;
        $this->conn_supplier = $conn_supplier;
        $this->api = new SupplierAPI($conn, $conn_supplier);
        $this->supplierModel = new SupplierModel($conn);
        $this->paymentModel = new PaymentModel($conn);
    }

    public function dashboard()
    {
        if (!isset($_SESSION['supplier_id'])) {
            header('Location: /SDW/KTMeDOIS/login');
            exit();
        }

        $supplier_id = $_SESSION['supplier_id'];
        $supplier = getSupplierFromExternal($supplier_id);

        $totalDO = mysqli_fetch_assoc(mysqli_query($this->conn, "SELECT COUNT(*) as count FROM do WHERE supplier_ID = '$supplier_id'"))['count'] ?? 0;
        $pendingDO = mysqli_fetch_assoc(mysqli_query($this->conn, "SELECT COUNT(*) as count FROM do WHERE supplier_ID = '$supplier_id' AND Status = 'Submitted'"))['count'] ?? 0;
        $approvedDO = mysqli_fetch_assoc(mysqli_query($this->conn, "SELECT COUNT(*) as count FROM do WHERE supplier_ID = '$supplier_id' AND Status = 'Approved'"))['count'] ?? 0;
        $rejectedDO = mysqli_fetch_assoc(mysqli_query($this->conn, "SELECT COUNT(*) as count FROM do WHERE supplier_ID = '$supplier_id' AND Status = 'Rejected'"))['count'] ?? 0;

        $totalPaid = mysqli_fetch_assoc(mysqli_query($this->conn, "SELECT SUM(Payment_Amount) as total FROM payment WHERE Supplier_ID = '$supplier_id' AND Payment_Status = 'Paid'"))['total'] ?? 0;
        $totalPending = mysqli_fetch_assoc(mysqli_query($this->conn, "SELECT SUM(Payment_Amount) as total FROM payment WHERE Supplier_ID = '$supplier_id' AND Payment_Status = 'Pending'"))['total'] ?? 0;

        $recentDO = mysqli_query($this->conn, "SELECT * FROM do WHERE supplier_ID = '$supplier_id' ORDER BY created_date DESC LIMIT 5");

        $title = 'Supplier Dashboard - KTM eDOIS';
        $showTopbar = true;
        $showSidebar = true;
        include __DIR__ . '/../../Presentation/View/Module1/dashboard.php';
    }

    public function profile()
    {
        if (!isset($_SESSION['supplier_id'])) {
            header('Location: /SDW/KTMeDOIS/login');
            exit();
        }

        $supplier = getSupplierFromExternal($_SESSION['supplier_id']);
        $totalDO = mysqli_fetch_assoc(mysqli_query($this->conn, "SELECT COUNT(*) as count FROM do WHERE supplier_ID = '" . $_SESSION['supplier_id'] . "'"))['count'] ?? 0;

        $title = 'Supplier Profile - KTM eDOIS';
        $showTopbar = true;
        $showSidebar = true;
        include __DIR__ . '/../../Presentation/View/Module1/profile.php';
    }

    public function doList()
    {
        if (!isset($_SESSION['supplier_id'])) {
            header('Location: /SDW/KTMeDOIS/login');
            exit();
        }

        $supplier_id = $_SESSION['supplier_id'];
        $doList = $this->supplierModel->getDOBySupplier($supplier_id);

        $title = 'My Delivery Orders - KTM eDOIS';
        $showTopbar = true;
        $showSidebar = true;
        include __DIR__ . '/../../Presentation/View/Module1/do.php';
    }

    public function payment()
    {
        if (!isset($_SESSION['supplier_id'])) {
            header('Location: /SDW/KTMeDOIS/login');
            exit();
        }

        $supplier_id = $_SESSION['supplier_id'];
        $payments = $this->paymentModel->getBySupplier($supplier_id);
        $summary = $this->paymentModel->getSummary($supplier_id);

        $title = 'Payment Status - KTM eDOIS';
        $showTopbar = true;
        $showSidebar = true;
        include __DIR__ . '/../../Presentation/View/Module1/payment.php';
    }

    // ============================================
    // API METHODS
    // ============================================

    public function apiGetSupplier()
    {
        $this->api->getSupplier();
    }

    public function apiGetDO()
    {
        $this->api->getDO();
    }

    public function apiGetPayment()
    {
        $this->api->getPayment();
    }

    public function apiSyncSupplier()
    {
        $this->api->syncSupplier();
    }
}
