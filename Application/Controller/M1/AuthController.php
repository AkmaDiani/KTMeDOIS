<?php
//M1
// AUTH CONTROLLER - Staff + Supplier Login

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . '/../../Helpers/functions.php';

class AuthController
{
    private $conn;
    private $conn_supplier;

    public function __construct($conn, $conn_supplier = null)
    {
        $this->conn = $conn;
        $this->conn_supplier = $conn_supplier ?? $conn;
    }

    public function login()
    {
        // If already logged in as staff
        if (isset($_SESSION['user_id']) && $_SESSION['user_type'] === 'staff') {
            header('Location: /KTMeDOIS/Presentation/View/Staff/dashboard');
            exit();
        }

        // If already logged in as supplier
        if (isset($_SESSION['supplier_id']) && $_SESSION['user_type'] === 'supplier') {
            header('Location: /KTMeDOIS/Presentation/View/Module1/dashboard');
            exit();
        }

        $error = '';

        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            $username = mysqli_real_escape_string($this->conn, $_POST['username']);
            $password = mysqli_real_escape_string($this->conn, $_POST['password']);
            $login_type = mysqli_real_escape_string($this->conn, $_POST['login_type']);

            if ($login_type == 'staff') {
                // STAFF LOGIN
                $role = mysqli_real_escape_string($this->conn, $_POST['role']);

                $query = "SELECT * FROM `ktm staff` WHERE Username = '$username' AND Status = 'Active'";
                $result = mysqli_query($this->conn, $query);

                if (mysqli_num_rows($result) == 1) {
                    $user = mysqli_fetch_assoc($result);

                    if ($password == $user['Password_Hash']) {
                        if ($role == $user['Role']) {
                            $_SESSION['user_id'] = $user['User_ID'];
                            $_SESSION['username'] = $user['Username'];
                            $_SESSION['role'] = $user['Role'];
                            $_SESSION['user_type'] = 'staff';

                            $updateQuery = "UPDATE `ktm staff` SET Last_Login = NOW() WHERE User_ID = " . $user['User_ID'];
                            mysqli_query($this->conn, $updateQuery);

                            header('Location: /KTMeDOIS/Presentation/View/Staff/dashboard');
                            exit();
                        } else {
                            $error = 'Invalid role selected. Please choose the correct role.';
                        }
                    } else {
                        $error = 'Invalid password. Please try again.';
                    }
                } else {
                    $error = 'Invalid username. Please check your credentials.';
                }
            } elseif ($login_type == 'supplier') {
                // SUPPLIER LOGIN - MODULE 1
                $query = "SELECT * FROM supplier WHERE (username = '$username' OR SUPPLIER_EMAIL_ADD = '$username') AND SUPPLIER_CTC_STATUS = 'Active'";
                $result = mysqli_query($this->conn_supplier, $query);

                if (mysqli_num_rows($result) == 1) {
                    $supplier = mysqli_fetch_assoc($result);
                    $hashedPassword = md5($password);

                    if ($hashedPassword == $supplier['password']) {
                        syncSupplierToMain($supplier['SUPPLIERID']);

                        $_SESSION['supplier_id'] = $supplier['SUPPLIERID'];
                        $_SESSION['supplier_name'] = $supplier['SUPPLIER_COMP_NAME'];
                        $_SESSION['supplier_email'] = $supplier['SUPPLIER_EMAIL_ADD'];
                        $_SESSION['user_type'] = 'supplier';
                        $_SESSION['role'] = 'Supplier';

                        $updateQuery = "UPDATE supplier SET last_login = NOW() WHERE SUPPLIERID = '{$supplier['SUPPLIERID']}'";
                        mysqli_query($this->conn_supplier, $updateQuery);

                        header('Location: /KTMeDOIS/Presentation/View/Module1/dashboard');
                        exit();
                    } else {
                        $error = 'Invalid password. Please try again.';
                    }
                } else {
                    $error = 'Invalid username. Please check your credentials.';
                }
            }
        }

        $title = 'Login - KTM eDOIS';
        $showTopbar = false;
        $showSidebar = false;
        include __DIR__ . '/../../../Presentation/View/auth/login.php';
    }

    public function logout()
    {
        $_SESSION = array();

        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }

        session_destroy();
        header('Location: /KTMeDOIS/login');
        exit();
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
}
