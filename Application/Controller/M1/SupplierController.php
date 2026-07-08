<?php
// Application/Controller/M1/SupplierController.php

class SupplierController
{
    private $pdo;
    private $supplierPdo;
    private $supplierModel;

    public function __construct(PDO $pdo, PDO $supplierPdo = null)
    {
        $this->pdo = $pdo;
        $this->supplierPdo = $supplierPdo ?? $pdo;
        $this->supplierModel = new SupplierModel($pdo);
    }

    private function checkAuth()
    {
        if (!isset($_SESSION['supplier_id'])) {
            header('Location: /KTMeDOIS/Presentation/Public/indexM1.php?controller=auth&action=login');
            exit;
        }
    }

    public function dashboard()
    {
        $this->checkAuth();

        $supplier_id = $_SESSION['supplier_id'];
        $supplier = getSupplierFromExternal($supplier_id);

        // Counts for DO statuses
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

        $totalPaid = 0;
        $totalPending = 0;

        // Recent DOs
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

    // Payment page – table does not exist, so redirect
    public function payment()
    {
        $_SESSION['error'] = 'Payment module is not available.';
        header('Location: /KTMeDOIS/Presentation/Public/indexM1.php?controller=supplier&action=dashboard');
        exit;
    }
}