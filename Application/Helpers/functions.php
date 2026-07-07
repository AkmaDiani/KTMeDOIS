<?php
// ============================================
// HELPER FUNCTIONS - PDO VERSION
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

// ------------------------------------------------------------
// SUPPLIER FUNCTIONS - PDO VERSION (Uses $GLOBALS)
// ------------------------------------------------------------

function getSupplierFromExternal($supplier_id)
{
    $supplierPdo = $GLOBALS['supplier_pdo'] ?? null;
    if (!$supplierPdo) return null;
    
    try {
        $stmt = $supplierPdo->prepare("SELECT * FROM supplier WHERE SUPPLIERID = ?");
        $stmt->execute([$supplier_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return null;
    }
}

function syncSupplierToMain($supplier_id)
{
    $mainPdo = $GLOBALS['main_pdo'] ?? null;
    $supplierPdo = $GLOBALS['supplier_pdo'] ?? null;
    
    if (!$mainPdo || !$supplierPdo) return false;
    
    try {
        $stmt = $supplierPdo->prepare("SELECT * FROM supplier WHERE SUPPLIERID = ?");
        $stmt->execute([$supplier_id]);
        $external = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$external) return false;
        
        $stmt = $mainPdo->prepare("SELECT Supplier_id FROM supplier WHERE Vendor_Number = ?");
        $stmt->execute([$external['SUPPLIERID']]);
        $exists = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($exists) {
            $sql = "UPDATE supplier SET 
                        Supplier_name = ?, Contac_person = ?, phone = ?, email = ?,
                        status = ?, username = ?, password = ?
                    WHERE Supplier_id = ?";
            $stmt = $mainPdo->prepare($sql);
            return $stmt->execute([
                $external['SUPPLIER_COMP_NAME'],
                $external['SUPPLIER_CTC_PERSON'],
                $external['SUPPLIER_CTC_NO'],
                $external['SUPPLIER_EMAIL_ADD'],
                $external['SUPPLIER_CTC_STATUS'],
                $external['username'] ?? null,
                $external['password'] ?? null,
                $exists['Supplier_id']
            ]);
        } else {
            $sql = "INSERT INTO supplier 
                        (Supplier_name, Contac_person, phone, email, status, Vendor_Number, username, password) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $mainPdo->prepare($sql);
            return $stmt->execute([
                $external['SUPPLIER_COMP_NAME'],
                $external['SUPPLIER_CTC_PERSON'],
                $external['SUPPLIER_CTC_NO'],
                $external['SUPPLIER_EMAIL_ADD'],
                $external['SUPPLIER_CTC_STATUS'],
                $external['SUPPLIERID'],
                $external['username'] ?? null,
                $external['password'] ?? null
            ]);
        }
    } catch (PDOException $e) {
        return false;
    }
}

function getSupplierFromMain($supplier_id)
{
    $mainPdo = $GLOBALS['main_pdo'] ?? null;
    if (!$mainPdo) return null;
    
    try {
        $stmt = $mainPdo->prepare("SELECT * FROM supplier WHERE Supplier_id = ?");
        $stmt->execute([$supplier_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        return null;
    }
}