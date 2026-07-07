<?php
// ============================================
// EXTERNAL SUPPLIER MODEL
// ============================================

class ExternalSupplierModel
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function find($id)
    {
        $id = mysqli_real_escape_string($this->conn, $id);
        $query = "SELECT * FROM supplier WHERE SUPPLIERID = '$id'";
        $result = mysqli_query($this->conn, $query);
        return mysqli_fetch_assoc($result);
    }

    public function findByUsername($username)
    {
        $username = mysqli_real_escape_string($this->conn, $username);
        $query = "SELECT * FROM supplier WHERE username = '$username' AND SUPPLIER_CTC_STATUS = 'Active'";
        $result = mysqli_query($this->conn, $query);
        return mysqli_fetch_assoc($result);
    }

    public function findByEmail($email)
    {
        $email = mysqli_real_escape_string($this->conn, $email);
        $query = "SELECT * FROM supplier WHERE SUPPLIER_EMAIL_ADD = '$email' AND SUPPLIER_CTC_STATUS = 'Active'";
        $result = mysqli_query($this->conn, $query);
        return mysqli_fetch_assoc($result);
    }

    public function verifyPassword($supplier, $password)
    {
        return md5($password) == $supplier['password'];
    }

    public function updateLastLogin($id)
    {
        $id = mysqli_real_escape_string($this->conn, $id);
        $query = "UPDATE supplier SET last_login = NOW() WHERE SUPPLIERID = '$id'";
        return mysqli_query($this->conn, $query);
    }

    public function getActiveSuppliers()
    {
        $query = "SELECT * FROM supplier WHERE SUPPLIER_CTC_STATUS = 'Active' ORDER BY SUPPLIER_COMP_NAME";
        $result = mysqli_query($this->conn, $query);
        $data = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = $row;
        }
        return $data;
    }
}
