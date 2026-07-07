<?php
// ============================================
// HELPER FUNCTIONS
// ============================================

function getStatusBadge($status)
{
    $colors = [
        'Active' => 'success',
        'Inactive' => 'danger',
        'Pending Verification' => 'warning',
        'Submitted' => 'info',
        'Under Review' => 'warning',
        'Approved' => 'success',
        'Rejected' => 'danger',
        'Paid' => 'success',
        'Processing' => 'info',
        'Pending' => 'warning'
    ];
    $color = $colors[$status] ?? 'secondary';
    return "<span class='badge bg-$color'>$status</span>";
}

function formatDate($date)
{
    if (!$date) return '-';
    return date('d/m/Y H:i', strtotime($date));
}

function getSupplierFromExternal($supplier_id)
{
    global $conn_supplier;
    $supplier_id = mysqli_real_escape_string($conn_supplier, $supplier_id);
    $query = "SELECT * FROM supplier WHERE SUPPLIERID = '$supplier_id'";
    $result = mysqli_query($conn_supplier, $query);
    return mysqli_fetch_assoc($result);
}

function syncSupplierToMain($supplier_id)
{
    global $conn_main, $conn_supplier;

    $external = getSupplierFromExternal($supplier_id);
    if (!$external) return false;

    $checkQuery = "SELECT Supplier_id FROM supplier WHERE Vendor_Number = '" . mysqli_real_escape_string($conn_main, $external['SUPPLIERID']) . "'";
    $checkResult = mysqli_query($conn_main, $checkQuery);

    if (mysqli_num_rows($checkResult) > 0) {
        $row = mysqli_fetch_assoc($checkResult);
        $updateQuery = "UPDATE supplier SET 
                         Supplier_name = '" . mysqli_real_escape_string($conn_main, $external['SUPPLIER_COMP_NAME']) . "',
                         Contac_person = '" . mysqli_real_escape_string($conn_main, $external['SUPPLIER_CTC_PERSON']) . "',
                         phone = '" . mysqli_real_escape_string($conn_main, $external['SUPPLIER_CTC_NO']) . "',
                         email = '" . mysqli_real_escape_string($conn_main, $external['SUPPLIER_EMAIL_ADD']) . "',
                         status = '" . mysqli_real_escape_string($conn_main, $external['SUPPLIER_CTC_STATUS']) . "',
                         username = '" . mysqli_real_escape_string($conn_main, $external['username']) . "',
                         password = '" . mysqli_real_escape_string($conn_main, $external['password']) . "'
                         WHERE Supplier_id = " . $row['Supplier_id'];
        return mysqli_query($conn_main, $updateQuery);
    } else {
        $insertQuery = "INSERT INTO supplier (Supplier_name, Contac_person, phone, email, status, Vendor_Number, username, password) 
                        VALUES (
                            '" . mysqli_real_escape_string($conn_main, $external['SUPPLIER_COMP_NAME']) . "',
                            '" . mysqli_real_escape_string($conn_main, $external['SUPPLIER_CTC_PERSON']) . "',
                            '" . mysqli_real_escape_string($conn_main, $external['SUPPLIER_CTC_NO']) . "',
                            '" . mysqli_real_escape_string($conn_main, $external['SUPPLIER_EMAIL_ADD']) . "',
                            '" . mysqli_real_escape_string($conn_main, $external['SUPPLIER_CTC_STATUS']) . "',
                            '" . mysqli_real_escape_string($conn_main, $external['SUPPLIERID']) . "',
                            '" . mysqli_real_escape_string($conn_main, $external['username']) . "',
                            '" . mysqli_real_escape_string($conn_main, $external['password']) . "'
                        )";
        return mysqli_query($conn_main, $insertQuery);
    }
}
