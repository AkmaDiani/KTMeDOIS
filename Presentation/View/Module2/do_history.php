<?php
require_once __DIR__ . '/../../../bootstrap.php';

// Check supplier login
if (!isset($_SESSION['supplier_id']) || $_SESSION['user_type'] !== 'supplier') {
    header('Location: /KTMeDOIS/Presentation/Public/indexM1.php?controller=auth&action=login');
    exit;
}

$supplier_id = (int)$_SESSION['supplier_id'];
$selected_month = $_GET['month'] ?? '';

$pdo = Database::getInstance()->getConnection();

$service = new DOService($pdo);
$dos = $service->getDOHistory($supplier_id, $selected_month);

$title = 'My Delivery Orders - KTM eDOIS';
$activePage = 'manage_do';

include ROOT_PATH . '/Presentation/View/SharedUI/topbar.php';
include ROOT_PATH . '/Presentation/View/SharedUI/sidebarM2.php';
?>

<div class="content">
    <div class="container-fluid">
        <h2>My Delivery Orders</h2>

        <form method="GET" class="filter-toolbar">
            <div class="filter-group">
                <label for="month">Filter by Month</label>
                <input type="month" name="month" id="month" value="<?= htmlspecialchars($selected_month) ?>">
            </div>
            <button type="submit" class="btn btn-primary">Filter</button>
            <a href="do_history.php" class="btn btn-outline">Clear</a>
            <a href="submit_do.php" class="btn btn-success">+ Submit New DO</a>
        </form>

        <div class="card-ktm">
            <div class="card-header"><span class="header-icon"><i class="fas fa-list"></i></span> Delivery Orders</div>
            <div class="card-body">
                <div class="table-responsive">
                    <table>
                        <thead>
                            <tr>
                                <th>DO Number</th>
                                <th>PO Number</th>
                                <th>Supplier</th>
                                <th>Status</th>
                                <th>Created Date</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($dos)): ?>
                                <?php foreach ($dos as $do): ?>
                                    <tr>
                                        <td><?= htmlspecialchars($do['DO_number'] ?? '') ?></td>
                                        <td><?= htmlspecialchars($do['PO_number'] ?? '') ?></td>
                                        <td><?= htmlspecialchars($do['Supplier_name'] ?? '') ?></td>
                                        <td>
                                            <?php
                                            $statusClass = str_replace(' ', '-', strtolower($do['Status'] ?? ''));
                                            ?>
                                            <span class="status-badge <?= $statusClass ?>">
                                                <?= htmlspecialchars($do['Status'] ?? '') ?>
                                            </span>
                                        </td>
                                        <td><?= date('d/m/Y h:i A', strtotime($do['created_date'] ?? '')) ?></td>
                                        <td>
                                            <a class="btn btn-sm btn-primary" href="view_do.php?id=<?= urlencode($do['DO_id'] ?? 0) ?>">View</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="empty-row"><i class="fas fa-info-circle"></i> No delivery orders found.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include ROOT_PATH . '/Presentation/View/SharedUI/footer.php'; ?>