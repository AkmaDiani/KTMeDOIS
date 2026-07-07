<?php
$title = 'Payment Status - KTM eDOIS';
$showTopbar = true;
$showSidebar = true;
include ROOT_PATH . '/Presentation/View/SharedUI/topbar.php';
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Payment Status</h1>
</div>

<div class="row mb-4">
    <div class="col-md-3">
        <div class="card text-white bg-success">
            <div class="card-body">
                <h6>Total Paid</h6>
                <h2 class="mb-0">RM <?php echo number_format($summary['total_paid'], 2); ?></h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-warning">
            <div class="card-body">
                <h6>Pending</h6>
                <h2 class="mb-0">RM <?php echo number_format($summary['total_pending'], 2); ?></h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-info">
            <div class="card-body">
                <h6>Processing</h6>
                <h2 class="mb-0">RM <?php echo number_format($summary['total_processing'], 2); ?></h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-primary">
            <div class="card-body">
                <h6>Total Payments</h6>
                <h2 class="mb-0"><?php echo $summary['total_count']; ?></h2>
            </div>
        </div>
    </div>
</div>

<div class="card card-ktm">
    <div class="card-header bg-primary text-white"><i class="fas fa-list"></i> Payment History</div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Payment ID</th>
                        <th>Invoice</th>
                        <th>Amount</th>
                        <th>Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($payments)): ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4"><i class="fas fa-credit-card fa-2x d-block mb-2"></i> No payment records found.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($payments as $row): ?>
                            <tr>
                                <td><?php echo str_pad($row['Payment_ID'], 6, '0', STR_PAD_LEFT); ?></td>
                                <td><?php echo $row['Invoice_num'] ?? '-'; ?></td>
                                <td>RM <?php echo number_format($row['Payment_Amount'], 2); ?></td>
                                <td><?php echo $row['Payment_Date'] ? date('d/m/Y', strtotime($row['Payment_Date'])) : '-'; ?></td>
                                <td><?php echo getStatusBadge($row['Payment_Status']); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../SharedUI/footer.php'; ?>