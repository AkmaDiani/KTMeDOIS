<?php
// ============================================
// STAFF CONTROLLER
// ============================================

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../Helpers/functions.php';
require_once __DIR__ . '/../Models/SupplierModel.php';
require_once __DIR__ . '/../Models/PaymentModel.php';

class StaffController
{
    private $conn;
    private $supplierModel;
    private $paymentModel;

    public function __construct($conn)
    {
        $this->conn = $conn;
        $this->supplierModel = new SupplierModel($conn);
        $this->paymentModel = new PaymentModel($conn);
    }

    public function dashboard()
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'staff') {
            header('Location: /SDW/KTMeDOIS/login');
            exit();
        }

        $stats = $this->supplierModel->getStats();
        $totalDO = mysqli_fetch_assoc(mysqli_query($this->conn, "SELECT COUNT(*) as count FROM do"))['count'] ?? 0;
        $pendingDO = mysqli_fetch_assoc(mysqli_query($this->conn, "SELECT COUNT(*) as count FROM do WHERE Status = 'Submitted'"))['count'] ?? 0;
        $totalInvoice = mysqli_fetch_assoc(mysqli_query($this->conn, "SELECT COUNT(*) as count FROM invoice"))['count'] ?? 0;

        $title = 'Staff Dashboard - KTM eDOIS';
        $showTopbar = true;
        $showSidebar = true;
        include __DIR__ . '/../../Presentation/View/staff/dashboard.php';
    }

    public function profile()
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'staff') {
            header('Location: /SDW/KTMeDOIS/login');
            exit();
        }

        $query = "SELECT * FROM `ktm staff` WHERE User_ID = '{$_SESSION['user_id']}'";
        $result = mysqli_query($this->conn, $query);
        $user = mysqli_fetch_assoc($result);

        $title = 'Staff Profile - KTM eDOIS';
        $showTopbar = true;
        $showSidebar = true;
        include __DIR__ . '/../../Presentation/View/staff/profile.php';
    }

    public function vendorRegistry()
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'staff') {
            header('Location: /SDW/KTMeDOIS/login');
            exit();
        }

        $suppliers = $this->supplierModel->getAll();
        $stats = $this->supplierModel->getStats();

        $title = 'Vendor Registry - KTM eDOIS';
        $showTopbar = true;
        $showSidebar = true;
        include __DIR__ . '/../../Presentation/View/staff/vendor_registry.php';
    }

    public function vendorReport()
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'staff') {
            header('Location: /SDW/KTMeDOIS/login');
            exit();
        }

        $stats = $this->supplierModel->getStats();

        $where = "1=1";
        if (isset($_GET['search']) && !empty($_GET['search'])) {
            $search = mysqli_real_escape_string($this->conn, $_GET['search']);
            $where .= " AND (Supplier_name LIKE '%$search%' OR Contac_person LIKE '%$search%' OR Vendor_Number LIKE '%$search%')";
        }
        if (isset($_GET['status']) && !empty($_GET['status'])) {
            $status = mysqli_real_escape_string($this->conn, $_GET['status']);
            $where .= " AND status = '$status'";
        }

        $query = "SELECT * FROM supplier WHERE $where ORDER BY Supplier_id DESC";
        $result = mysqli_query($this->conn, $query);
        $vendors = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $vendors[] = $row;
        }
        $filteredCount = count($vendors);

        $title = 'Vendor Activity & Status Report - KTM eDOIS';
        $showTopbar = true;
        $showSidebar = true;
        include __DIR__ . '/../../Presentation/View/staff/vendor_report.php';
    }

    public function vendorView()
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'staff') {
            header('Location: /SDW/KTMeDOIS/login');
            exit();
        }

        $id = $_GET['id'] ?? 0;
        $vendor = $this->supplierModel->getById($id);

        if (!$vendor) {
            header('Location: /SDW/KTMeDOIS/staff/vendor');
            exit();
        }

        $totalDO = mysqli_fetch_assoc(mysqli_query($this->conn, "SELECT COUNT(*) as count FROM do WHERE supplier_ID = '$id'"))['count'] ?? 0;
        $totalInvoice = mysqli_fetch_assoc(mysqli_query($this->conn, "SELECT COUNT(*) as count FROM invoice WHERE supplier_ID = '$id'"))['count'] ?? 0;

        $title = 'Vendor Profile - KTM eDOIS';
        $showTopbar = true;
        $showSidebar = true;
        include __DIR__ . '/../../Presentation/View/staff/vendor_view.php';
    }

    public function vendorCreate()
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'staff') {
            header('Location: /SDW/KTMeDOIS/login');
            exit();
        }

        $error = '';
        $success = '';

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = [
                'name' => $_POST['name'],
                'contact' => $_POST['contact'],
                'phone' => $_POST['phone'],
                'email' => $_POST['email'],
                'address' => $_POST['address'],
                'status' => $_POST['status']
            ];

            $result = $this->supplierModel->create($data);

            if ($result) {
                $success = 'Vendor added successfully!';
            } else {
                $error = 'Failed to add vendor. Please try again.';
            }
        }

        $title = 'Add Vendor - KTM eDOIS';
        $showTopbar = true;
        $showSidebar = true;
        include __DIR__ . '/../../Presentation/View/staff/vendor_create.php';
    }

    public function vendorEdit()
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'staff') {
            header('Location: /SDW/KTMeDOIS/login');
            exit();
        }

        $id = $_GET['id'] ?? 0;
        $vendor = $this->supplierModel->getById($id);

        if (!$vendor) {
            header('Location: /SDW/KTMeDOIS/staff/vendor');
            exit();
        }

        $error = '';
        $success = '';

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $data = [
                'name' => $_POST['name'],
                'contact' => $_POST['contact'],
                'phone' => $_POST['phone'],
                'email' => $_POST['email'],
                'address' => $_POST['address'],
                'status' => $_POST['status']
            ];

            $result = $this->supplierModel->update($id, $data);

            if ($result) {
                $success = 'Vendor updated successfully!';
                $vendor = $this->supplierModel->getById($id);
            } else {
                $error = 'Failed to update vendor. Please try again.';
            }
        }

        $title = 'Edit Vendor - KTM eDOIS';
        $showTopbar = true;
        $showSidebar = true;
        include __DIR__ . '/../../Presentation/View/staff/vendor_edit.php';
    }

    public function vendorDelete()
    {
        if (!isset($_SESSION['user_id']) || $_SESSION['user_type'] !== 'staff') {
            header('Location: /SDW/KTMeDOIS/login');
            exit();
        }

        $id = $_GET['id'] ?? 0;
        $result = $this->supplierModel->delete($id);

        if ($result['success']) {
            $_SESSION['success'] = $result['message'];
        } else {
            $_SESSION['error'] = $result['message'];
        }

        header('Location: /SDW/KTMeDOIS/staff/vendor');
        exit();
    }
}
