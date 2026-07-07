<?php
$title = 'My Delivery Orders - KTM eDOIS';
$showTopbar = true;
$showSidebar = true;
include ROOT_PATH . '/Presentation/View/SharedUI/topbar.php';
include ROOT_PATH . '/Presentation/View/SharedUI/sidebarM1.php';

?>
<div class="content">
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">My Delivery Orders</h1>
</div>

<div class="card card-ktm">
    <div class="card-header bg-primary text-white"><i class="fas fa-list"></i> Delivery Orders</div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>DO Number</th>
                        <th>PO Number</th>
                        <th>Status</th>
                        <th>Created By</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($doList)): ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4"><i class="fas fa-truck fa-2x d-block mb-2"></i> No delivery orders found.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($doList as $row): ?>
                            <tr>
                                <td><?php echo $row['DO_number']; ?></td>
                                <td><?php echo $row['PO_number']; ?></td>
                                <td><?php echo getStatusBadge($row['Status']); ?></td>
                                <td><?php echo $row['created_by']; ?></td>
                                <td><?php echo date('d/m/Y', strtotime($row['created_date'])); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</div>

<?php include __DIR__ . '/../SharedUI/footer.php'; ?>