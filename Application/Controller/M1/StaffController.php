<?php
// Application/Controller/M1/StaffController.php

class StaffController
{
    private $pdo;
    private $supplierModel;
    private $paymentModel;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
        $this->supplierModel = new SupplierModel($pdo);
        $this->paymentModel = new PaymentModel($pdo);
    }

    private function checkAuth()
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'staff') {
            header('Location: ' . ROOT_PATH . '/login');
            exit;
        }
    }

    public function dashboard()
    {
        $this->checkAuth();

        $stats = $this->supplierModel->getStats();
        $stmt = $this->pdo->query("SELECT COUNT(*) as count FROM do");
        $totalDO = $stmt->fetchColumn();
        $stmt = $this->pdo->query("SELECT COUNT(*) as count FROM do WHERE Status = 'Submitted'");
        $pendingDO = $stmt->fetchColumn();
        $stmt = $this->pdo->query("SELECT COUNT(*) as count FROM invoice");
        $totalInvoice = $stmt->fetchColumn();

        $title = 'Staff Dashboard - KTM eDOIS';
        $showTopbar = true;
        $showSidebar = true;
        include ROOT_PATH . '/Presentation/View/staff/dashboard.php';

        $activePage = 'dashboard';
        include ROOT_PATH . '/Presentation/View/SharedUI/sidebarM1.php';
    }

    public function profile()
    {
        $this->checkAuth();

        $stmt = $this->pdo->prepare("SELECT * FROM `ktm staff` WHERE User_ID = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        $title = 'Staff Profile - KTM eDOIS';
        $showTopbar = true;
        $showSidebar = true;
        include ROOT_PATH . '/Presentation/View/staff/profile.php';
    }

    public function vendorRegistry()
    {
        $this->checkAuth();

        $suppliers = $this->supplierModel->getAll();
        $stats = $this->supplierModel->getStats();

        $title = 'Vendor Registry - KTM eDOIS';
        $showTopbar = true;
        $showSidebar = true;
        include ROOT_PATH . '/Presentation/View/staff/vendor_registry.php';
    }

    public function vendorReport()
    {
        $this->checkAuth();

        $stats = $this->supplierModel->getStats();

        $where = "1=1";
        $params = [];
        if (!empty($_GET['search'])) {
            $where .= " AND (Supplier_name LIKE ? OR Contac_person LIKE ? OR Vendor_Number LIKE ?)";
            $search = '%' . $_GET['search'] . '%';
            $params = array_merge($params, [$search, $search, $search]);
        }
        if (!empty($_GET['status'])) {
            $where .= " AND status = ?";
            $params[] = $_GET['status'];
        }

        $sql = "SELECT * FROM supplier WHERE $where ORDER BY Supplier_id DESC";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        $vendors = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $filteredCount = count($vendors);

        $title = 'Vendor Activity & Status Report - KTM eDOIS';
        $showTopbar = true;
        $showSidebar = true;
        include ROOT_PATH . '/Presentation/View/staff/vendor_report.php';
    }

    public function vendorView()
    {
        $this->checkAuth();

        $id = $_GET['id'] ?? 0;
        $vendor = $this->supplierModel->getById($id);
        if (!$vendor) {
            header('Location: ' . ROOT_PATH . '/staff/vendor');
            exit;
        }

        $stmt = $this->pdo->prepare("SELECT COUNT(*) as count FROM do WHERE supplier_ID = ?");
        $stmt->execute([$id]);
        $totalDO = $stmt->fetchColumn();
        $stmt = $this->pdo->prepare("SELECT COUNT(*) as count FROM invoice WHERE supplier_ID = ?");
        $stmt->execute([$id]);
        $totalInvoice = $stmt->fetchColumn();

        $title = 'Vendor Profile - KTM eDOIS';
        $showTopbar = true;
        $showSidebar = true;
        include ROOT_PATH . '/Presentation/View/staff/vendor_view.php';
    }

    public function vendorCreate()
    {
        $this->checkAuth();

        $error = '';
        $success = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'name' => $_POST['name'] ?? '',
                'contact' => $_POST['contact'] ?? '',
                'phone' => $_POST['phone'] ?? '',
                'email' => $_POST['email'] ?? '',
                'address' => $_POST['address'] ?? '',
                'status' => $_POST['status'] ?? 'Active'
            ];

            if ($this->supplierModel->create($data)) {
                $success = 'Vendor added successfully!';
            } else {
                $error = 'Failed to add vendor.';
            }
        }

        $title = 'Add Vendor - KTM eDOIS';
        $showTopbar = true;
        $showSidebar = true;
        include ROOT_PATH . '/Presentation/View/staff/vendor_create.php';
    }

    public function vendorEdit()
    {
        $this->checkAuth();

        $id = $_GET['id'] ?? 0;
        $vendor = $this->supplierModel->getById($id);
        if (!$vendor) {
            header('Location: ' . ROOT_PATH . '/staff/vendor');
            exit;
        }

        $error = '';
        $success = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'name' => $_POST['name'] ?? '',
                'contact' => $_POST['contact'] ?? '',
                'phone' => $_POST['phone'] ?? '',
                'email' => $_POST['email'] ?? '',
                'address' => $_POST['address'] ?? '',
                'status' => $_POST['status'] ?? 'Active'
            ];

            if ($this->supplierModel->update($id, $data)) {
                $success = 'Vendor updated successfully!';
                $vendor = $this->supplierModel->getById($id);
            } else {
                $error = 'Failed to update vendor.';
            }
        }

        $title = 'Edit Vendor - KTM eDOIS';
        $showTopbar = true;
        $showSidebar = true;
        include ROOT_PATH . '/Presentation/View/staff/vendor_edit.php';
    }

    public function vendorDelete()
    {
        $this->checkAuth();

        $id = $_GET['id'] ?? 0;
        $result = $this->supplierModel->delete($id);

        if ($result['success']) {
            $_SESSION['success'] = $result['message'];
        } else {
            $_SESSION['error'] = $result['message'];
        }

        header('Location: ' . ROOT_PATH . '/staff/vendor');
        exit;
    }
}