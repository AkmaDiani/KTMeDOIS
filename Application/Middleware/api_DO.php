<?php
require_once ROOT_PATH . '/bootstrap.php';

if (!isset($_SESSION['supplier_id']) || $_SESSION['user_type'] !== 'supplier') {
    header('Location: /KTMeDOIS/Presentation/Public/indexM1.php?controller=auth&action=login');
    exit;
}

$supplier_id = (int)$_SESSION['supplier_id'];

$pdo = Database::getInstance()->getConnection();

try {
    $service = new DOService($pdo);
    $do_id = $service->submitDO($_POST, $_FILES, $supplier_id);

    header('Location: /KTMeDOIS/Presentation/View/Module2/view_do.php?id=' . urlencode((string)$do_id));
    exit;
} catch (Exception $e) {
    echo '<script>
            alert("Error: ' . addslashes($e->getMessage()) . '");
            window.history.back();
          </script>';
    exit;
}