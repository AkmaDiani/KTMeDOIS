<?php
require_once __DIR__ . "/../Model/db.php";
require_once __DIR__ . "/../Controllers/DOService.php";

session_start();

try {
    /*
     * TEMPORARY TESTING:
     * Later after login, make sure login sets:
     * $_SESSION["supplier_id"] = supplier Supplier_id;
     */
    $supplier_id = $_SESSION['supplier_id'];

    $service = new DOService($pdo);
    $do_id = $service->submitDO($_POST, $_FILES, (int)$supplier_id);

    header("Location: ../../Presentation/Module2/view_do.php?id=" . urlencode((string)$do_id));
    exit;
} catch (Exception $e) {
    echo "<script>
            alert('Error: " . addslashes($e->getMessage()) . "');
            window.history.back();
          </script>";
    exit;
}
