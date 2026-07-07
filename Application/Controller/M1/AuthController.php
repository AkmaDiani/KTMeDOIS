<?php
// Application/Controller/M1/AuthController.php

class AuthController
{
    private $pdo;
    private $supplierPdo;

    public function __construct(PDO $pdo, PDO $supplierPdo = null)
    {
        $this->pdo = $pdo;
        $this->supplierPdo = $supplierPdo ?? $pdo;
    }

    public function login()
    {
        // Already logged in checks...
        if (isset($_SESSION['user_id']) && $_SESSION['user_type'] === 'staff') {
            header('Location: /KTMedOIS/Presentation/Public/indexM1.php?controller=staff&action=dashboard');
            exit;
        }
        if (isset($_SESSION['supplier_id']) && $_SESSION['user_type'] === 'supplier') {
            header('Location: /KTMedOIS/Presentation/Public/indexM1.php?controller=supplier&action=dashboard');
            exit;
        }

        $error = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // ✅ FIX: Accept BOTH 'email' AND 'username' (for cache compatibility)
            $login_input = trim($_POST['email'] ?? $_POST['username'] ?? '');
            $password = trim($_POST['password'] ?? '');
            $login_type = $_POST['login_type'] ?? '';

            if ($login_type === 'staff') {
                $role = $_POST['role'] ?? '';

                $stmt = $this->pdo->prepare("SELECT * FROM `ktm staff` WHERE Email = ? AND Status = 'Active'");
                $stmt->execute([$login_input]);
                $user = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($user && $password === $user['Password_Hash']) {
                    if ($role === $user['Role']) {
                        $_SESSION['user_id'] = $user['User_ID'];
                        $_SESSION['username'] = $user['Username'];
                        $_SESSION['role'] = $user['Role'];
                        $_SESSION['user_type'] = 'staff';

                        $update = $this->pdo->prepare("UPDATE `ktm staff` SET Last_Login = NOW() WHERE User_ID = ?");
                        $update->execute([$user['User_ID']]);

                        header('Location: /KTMedOIS/Presentation/Public/indexM1.php?controller=staff&action=dashboard');
                        exit;
                    } else {
                        $error = 'Invalid role selected.';
                    }
                } else {
                    $error = 'Invalid email or password.';
                }

            } elseif ($login_type === 'supplier') {
                $stmt = $this->supplierPdo->prepare("SELECT * FROM supplier WHERE (username = ? OR SUPPLIER_EMAIL_ADD = ?) AND SUPPLIER_CTC_STATUS = 'Active'");
                $stmt->execute([$login_input, $login_input]);
                $supplier = $stmt->fetch(PDO::FETCH_ASSOC);

                if ($supplier && md5($password) === ($supplier['password'] ?? '')) {
                    syncSupplierToMain($supplier['SUPPLIERID']);

                    $_SESSION['supplier_id'] = $supplier['SUPPLIERID'];
                    $_SESSION['supplier_name'] = $supplier['SUPPLIER_COMP_NAME'];
                    $_SESSION['supplier_email'] = $supplier['SUPPLIER_EMAIL_ADD'];
                    $_SESSION['user_type'] = 'supplier';
                    $_SESSION['role'] = 'Supplier';

                    $update = $this->supplierPdo->prepare("UPDATE supplier SET last_login = NOW() WHERE SUPPLIERID = ?");
                    $update->execute([$supplier['SUPPLIERID']]);

                    header('Location: /KTMedOIS/Presentation/Public/indexM1.php?controller=supplier&action=dashboard');
                    exit;
                } else {
                    $error = 'Invalid supplier credentials.';
                }
            }
        }

        $title = 'Login - KTM eDOIS';
        $showTopbar = false;
        $showSidebar = false;
        include ROOT_PATH . '/Presentation/View/auth/login.php';
    }

    public function logout()
    {
        $_SESSION = [];
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        session_destroy();
        header('Location: /KTMedOIS/Presentation/Public/indexM1.php?controller=auth&action=login');
        exit;
    }

    public function showLogin()
    {
        if (isset($_SESSION['user_id'])) {
            $role = $_SESSION['role'] ?? '';
            if ($role === 'Vendor' || $role === 'Supplier') {
                header('Location: /KTMedOIS/Presentation/Public/indexM1.php?controller=staff&action=invoice_status');
            } else {
                header('Location: /KTMedOIS/Presentation/Public/indexM1.php?controller=staff&action=invoice_pending');
            }
            exit;
        }
        include ROOT_PATH . '/Presentation/View/auth/login.php';
    }
}