<?php
require_once __DIR__ . '/../../../bootstrap.php';

if (!isset($_SESSION['supplier_id']) || $_SESSION['user_type'] !== 'supplier') {
    header('Location: /KTMeDOIS/Presentation/Public/indexM1.php?controller=auth&action=login');
    exit;
}

$do_id = $_GET['id'] ?? null;
if (!$do_id) {
    die('Invalid DO ID.');
}

$supplier_id = (int)$_SESSION['supplier_id'];

$pdo = Database::getInstance()->getConnection();

$service = new DOService($pdo);
$result = $service->getDODetails($do_id, $supplier_id);

if (!$result) {
    die('Delivery Order not found or access denied.');
}

$do = $result['do'];
$items = $result['items'];

$title = 'View Delivery Order - KTM eDOIS';
$activePage = 'manage_do';

include ROOT_PATH . '/Presentation/View/SharedUI/topbar.php';
include ROOT_PATH . '/Presentation/View/SharedUI/sidebarM2.php';
?>

<div class="content">
    <div class="container-fluid">
        <h2>Delivery Order Submission Detail</h2>

        <div class="card-ktm">
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-6"><strong>DO Number:</strong> <?= htmlspecialchars($do['DO_number']) ?></div>
                    <div class="col-md-6"><strong>PO Number:</strong> <?= htmlspecialchars($do['PO_number']) ?></div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6"><strong>Supplier:</strong> <?= htmlspecialchars($do['Supplier_name']) ?></div>
                    <div class="col-md-6">
                        <strong>Status:</strong>
                        <?php
                        $statusClass = str_replace(' ', '-', strtolower($do['Status'] ?? ''));
                        ?>
                        <span class="status-badge <?= $statusClass ?>"><?= htmlspecialchars($do['Status']) ?></span>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6"><strong>Created Date:</strong> <?= htmlspecialchars($do['created_date']) ?></div>
                </div>

                <h4>Submitted Files</h4>
                <div class="mb-3">
                    <a class="btn btn-primary btn-sm" target="_blank" href="view_file.php?id=<?= urlencode($do_id) ?>&type=do">View DO File</a>
                    <a class="btn btn-primary btn-sm" target="_blank" href="view_file.php?id=<?= urlencode($do_id) ?>&type=proof">View Proof File</a>
                </div>

                <h4>Item Details</h4>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Item No</th>
                                <th>Description</th>
                                <th>Quantity</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($items)): ?>
                                <?php foreach ($items as $item): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($item['item_no']) ?></td>
                                        <td><?= htmlspecialchars($item['item_description']) ?></td>
                                        <td><?= htmlspecialchars($item['quantity']) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="3" class="empty-row">No items found.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    <a class="btn btn-primary" href="submit_do.php">Submit Another DO</a>
                    <a class="btn btn-outline" href="do_history.php">Back to My DOs</a>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include ROOT_PATH . '/Presentation/View/SharedUI/footer.php'; ?>