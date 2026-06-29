<?php
// Application/Controller/authController.php

require_once __DIR__ . '/../../Data/db.php';

class AuthController {
    private $db;

    public function __construct($db) {
        $this->db = $db;
    }

    public function showLogin() {
        session_start();
        if (isset($_SESSION['user_id'])) {
            if ($_SESSION['role'] === 'Vendor') {
                header('Location: /KTMEDOIS/Presentation/Public/index.php?action=invoice_status');
            } else {
                header('Location: /KTMEDOIS/Presentation/Public/index.php?action=invoice_pending');
            }
            exit;
        }
        include __DIR__ . '/../../Presentation/View/auth/login.php';
    }

    public function login() {
        session_start();
        
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';

        if (empty($email) || empty($password)) {
            $_SESSION['error'] = 'Please enter email and password.';
            header('Location: /KTMEDOIS/Presentation/Public/index.php?action=login');
            exit;
        }

        try {
            // Check ktm staff table
            $stmt = $this->db->prepare("SELECT * FROM `ktm staff` WHERE Email = ?");
            $stmt->execute([$email]);
            $staff = $stmt->fetch();

            if ($staff && $staff['Password_Hash'] === $password) {
                $_SESSION['user_id'] = $staff['User_ID'];
                $_SESSION['username'] = $staff['Username'];
                $_SESSION['role'] = $staff['Role'];
                $_SESSION['email'] = $staff['Email'];
                $_SESSION['vendor_id'] = null;
                
                header('Location: /KTMEDOIS/Presentation/Public/index.php?action=invoice_pending');
                exit;
            }

            // Check supplier table
            $stmt = $this->db->prepare("SELECT * FROM supplier WHERE email = ?");
            $stmt->execute([$email]);
            $supplier = $stmt->fetch();

            if ($supplier && $supplier['status'] === 'Active') {
                $_SESSION['user_id'] = $supplier['Supplier_id'];
                $_SESSION['username'] = $supplier['Supplier_name'];
                $_SESSION['role'] = 'Vendor';
                $_SESSION['email'] = $supplier['email'];
                $_SESSION['vendor_id'] = $supplier['Supplier_id'];
                
                header('Location: /KTMEDOIS/Presentation/Public/index.php?action=invoice_status');
                exit;
            }

            $_SESSION['error'] = 'Invalid email or password.';
            header('Location: /KTMEDOIS/Presentation/Public/index.php?action=login');
            exit;

        } catch (Exception $e) {
            $_SESSION['error'] = 'Login error: ' . $e->getMessage();
            header('Location: /KTMEDOIS/Presentation/Public/index.php?action=login');
            exit;
        }
    }

    public function logout() {
        session_start();
        session_destroy();
        header('Location: /KTMEDOIS/Presentation/Public/index.php?action=login');
        exit;
    }

    public function dashboard() {
        session_start();
        if (!isset($_SESSION['user_id'])) {
            header('Location: /KTMEDOIS/Presentation/Public/index.php?action=login');
            exit;
        }
        // Redirect if someone manually goes to dashboard
        if ($_SESSION['role'] === 'Vendor') {
            header('Location: /KTMEDOIS/Presentation/Public/index.php?action=invoice_status');
            exit;
        } elseif (in_array($_SESSION['role'], ['KTM Officer', 'Finance Officer'])) {
            header('Location: /KTMEDOIS/Presentation/Public/index.php?action=invoice_pending');
            exit;
        }
        include __DIR__ . '/../../Presentation/View/auth/dashboard.php';
    }
}