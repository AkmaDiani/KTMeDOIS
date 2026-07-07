<?php
// Application/Controller/M1/SupplierController.php

class SupplierController
{
    private $pdo;
    private $supplierPdo;
    private $supplierModel;
    private $paymentModel;

    public function __construct(PDO $pdo, PDO $supplierPdo = null)
    {
        $this->pdo = $pdo;
        $this->supplierPdo = $supplierPdo ?? $pdo;
        $this->supplierModel = new SupplierModel($pdo);
        $this->paymentModel = new PaymentModel($pdo);
    }

    private function checkAuth()
    {
        if (!isset($_SESSION['supplier_id'])) {
            header('Location: ' . ROOT_PATH . '/login');
            exit;
        }
    }

    public function dashboard()
    {
        $this->checkAuth();

        $supplier_id = $_SESSION['supplier_id'];
        $supplier = getSupplierFromExternal($supplier_id);

        $stmt = $this->pdo->prepare("SELECT COUNT(*) as count FROM do WHERE supplier_ID = ?");
        $stmt->execute([$supplier_id]);
        $totalDO = $stmt->fetchColumn();

        $stmt = $this->pdo->prepare("SELECT COUNT(*) as count FROM do WHERE supplier_ID = ? AND Status = 'Submitted'");
        $stmt->execute([$supplier_id]);
        $pendingDO = $stmt->fetchColumn();

        $stmt = $this->pdo->prepare("SELECT COUNT(*) as count FROM do WHERE supplier_ID = ? AND Status = 'Approved'");
        $stmt->execute([$supplier_id]);
        $approvedDO = $stmt->fetchColumn();

        $stmt = $this->pdo->prepare("SELECT COUNT(*) as count FROM do WHERE supplier_ID = ? AND Status = 'Rejected'");
        $stmt->execute([$supplier_id]);
        $rejectedDO = $stmt->fetchColumn();

        $stmt = $this->pdo->prepare("SELECT SUM(Payment_Amount) as total FROM payment WHERE Supplier_ID = ? AND Payment_Status = 'Paid'");
        $stmt->execute([$supplier_id]);
        $totalPaid = $stmt->fetchColumn() ?: 0;

        $stmt = $this->pdo->prepare("SELECT SUM(Payment_Amount) as total FROM payment WHERE Supplier_ID = ? AND Payment_Status = 'Pending'");
        $stmt->execute([$supplier_id]);
        $totalPending = $stmt->fetchColumn() ?: 0;

        $stmt = $this->pdo->prepare("SELECT * FROM do WHERE supplier_ID = ? ORDER BY created_date DESC LIMIT 5");
        $stmt->execute([$supplier_id]);
        $recentDO = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $title = 'Supplier Dashboard - KTM eDOIS';
        $showTopbar = true;
        $showSidebar = true;
        include ROOT_PATH . '/Presentation/View/Module1/dashboard.php';
    }

    public function profile()
    {
        $this->checkAuth();

        $supplier = getSupplierFromExternal($_SESSION['supplier_id']);

        $stmt = $this->pdo->prepare("SELECT COUNT(*) as count FROM do WHERE supplier_ID = ?");
        $stmt->execute([$_SESSION['supplier_id']]);
        $totalDO = $stmt->fetchColumn();

        $title = 'Supplier Profile - KTM eDOIS';
        $showTopbar = true;
        $showSidebar = true;
        include ROOT_PATH . '/Presentation/View/Module1/profile.php';
    }

    public function doList()
    {
        $this->checkAuth();

        $supplier_id = $_SESSION['supplier_id'];
        $doList = $this->supplierModel->getDOBySupplier($supplier_id);

        $title = 'My Delivery Orders - KTM eDOIS';
        $showTopbar = true;
        $showSidebar = true;
        include ROOT_PATH . '/Presentation/View/Module1/do.php';
    }

    public function payment()
    {
        $this->checkAuth();

        $supplier_id = $_SESSION['supplier_id'];
        $payments = $this->paymentModel->getBySupplier($supplier_id);
        $summary = $this->paymentModel->getSummary($supplier_id);

        $title = 'Payment Status - KTM eDOIS';
        $showTopbar = true;
        $showSidebar = true;
        include ROOT_PATH . '/Presentation/View/Module1/payment.php';
    }
}