<?php
// ============================================
// SUPPLIER MODEL
// ============================================

class SupplierModel
{
    private $conn;

    public function __construct($conn)
    {
        $this->conn = $conn;
    }

    public function getAll()
    {
        $query = "SELECT * FROM supplier ORDER BY Supplier_id DESC";
        $result = mysqli_query($this->conn, $query);
        $data = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = $row;
        }
        return $data;
    }

    public function getById($id)
    {
        $id = mysqli_real_escape_string($this->conn, $id);
        $query = "SELECT * FROM supplier WHERE Supplier_id = '$id'";
        $result = mysqli_query($this->conn, $query);
        return mysqli_fetch_assoc($result);
    }

    public function getByVendorNumber($vendorNumber)
    {
        $vendorNumber = mysqli_real_escape_string($this->conn, $vendorNumber);
        $query = "SELECT * FROM supplier WHERE Vendor_Number = '$vendorNumber'";
        $result = mysqli_query($this->conn, $query);
        return mysqli_fetch_assoc($result);
    }

    public function getDOBySupplier($supplierId, $limit = null)
    {
        $supplierId = mysqli_real_escape_string($this->conn, $supplierId);
        $query = "SELECT * FROM do WHERE supplier_ID = '$supplierId' ORDER BY created_date DESC";
        if ($limit) $query .= " LIMIT $limit";
        $result = mysqli_query($this->conn, $query);
        $data = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = $row;
        }
        return $data;
    }

    public function countDO($supplierId, $status = null)
    {
        $supplierId = mysqli_real_escape_string($this->conn, $supplierId);
        $where = "supplier_ID = '$supplierId'";
        if ($status) {
            $status = mysqli_real_escape_string($this->conn, $status);
            $where .= " AND Status = '$status'";
        }
        $query = "SELECT COUNT(*) as count FROM do WHERE $where";
        $result = mysqli_query($this->conn, $query);
        $row = mysqli_fetch_assoc($result);
        return $row['count'] ?? 0;
    }

    public function create($data)
    {
        $name = mysqli_real_escape_string($this->conn, $data['name']);
        $contact = mysqli_real_escape_string($this->conn, $data['contact']);
        $phone = mysqli_real_escape_string($this->conn, $data['phone']);
        $email = mysqli_real_escape_string($this->conn, $data['email']);
        $address = mysqli_real_escape_string($this->conn, $data['address']);
        $status = mysqli_real_escape_string($this->conn, $data['status']);

        $vendorNum = rand(10001, 99999);

        $checkQuery = "SELECT Vendor_Number FROM supplier WHERE Vendor_Number = '$vendorNum'";
        $checkResult = mysqli_query($this->conn, $checkQuery);
        while (mysqli_num_rows($checkResult) > 0) {
            $vendorNum = rand(10001, 99999);
            $checkResult = mysqli_query($this->conn, "SELECT Vendor_Number FROM supplier WHERE Vendor_Number = '$vendorNum'");
        }

        $query = "INSERT INTO supplier (Supplier_name, Contac_person, phone, email, status, Billing_address, Vendor_Number) 
                  VALUES ('$name', '$contact', '$phone', '$email', '$status', '$address', '$vendorNum')";

        if (mysqli_query($this->conn, $query)) {
            return mysqli_insert_id($this->conn);
        }
        return false;
    }

    public function update($id, $data)
    {
        $id = mysqli_real_escape_string($this->conn, $id);
        $name = mysqli_real_escape_string($this->conn, $data['name']);
        $contact = mysqli_real_escape_string($this->conn, $data['contact']);
        $phone = mysqli_real_escape_string($this->conn, $data['phone']);
        $email = mysqli_real_escape_string($this->conn, $data['email']);
        $address = mysqli_real_escape_string($this->conn, $data['address']);
        $status = mysqli_real_escape_string($this->conn, $data['status']);

        $query = "UPDATE supplier SET 
                   Supplier_name = '$name',
                   Contac_person = '$contact',
                   phone = '$phone',
                   email = '$email',
                   Billing_address = '$address',
                   status = '$status'
                   WHERE Supplier_id = '$id'";

        return mysqli_query($this->conn, $query);
    }

    public function delete($id)
    {
        $id = mysqli_real_escape_string($this->conn, $id);

        $checkDO = mysqli_query($this->conn, "SELECT COUNT(*) as count FROM do WHERE supplier_ID = '$id'");
        $doCount = mysqli_fetch_assoc($checkDO)['count'];

        if ($doCount > 0) {
            return ['success' => false, 'message' => "Cannot delete supplier with $doCount DO records."];
        }

        $query = "DELETE FROM supplier WHERE Supplier_id = '$id'";
        if (mysqli_query($this->conn, $query)) {
            return ['success' => true, 'message' => 'Supplier deleted successfully.'];
        }
        return ['success' => false, 'message' => 'Error deleting supplier.'];
    }

    public function getStats()
    {
        $total = mysqli_fetch_assoc(mysqli_query($this->conn, "SELECT COUNT(*) as count FROM supplier"))['count'];
        $active = mysqli_fetch_assoc(mysqli_query($this->conn, "SELECT COUNT(*) as count FROM supplier WHERE status = 'Active'"))['count'];
        $pending = mysqli_fetch_assoc(mysqli_query($this->conn, "SELECT COUNT(*) as count FROM supplier WHERE status = 'Pending Verification'"))['count'];
        $inactive = mysqli_fetch_assoc(mysqli_query($this->conn, "SELECT COUNT(*) as count FROM supplier WHERE status = 'Inactive'"))['count'];

        return [
            'total' => $total,
            'active' => $active,
            'pending' => $pending,
            'inactive' => $inactive
        ];
    }
}
