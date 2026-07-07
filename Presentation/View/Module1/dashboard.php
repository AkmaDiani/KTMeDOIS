<?php
$title = 'Supplier Dashboard - KTM eDOIS';
$showTopbar = true;
$showSidebar = true;

include ROOT_PATH . '/Presentation/View/SharedUI/topbar.php';
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Supplier Dashboard</h1>
    <div>
        <span class="badge bg-success"><?php echo $supplier['SUPPLIER_CTC_STATUS']; ?></span>
        <span class="badge bg-info">ID: <?php echo $supplier['SUPPLIERID']; ?></span>
    </div>
</div>

<div class="alert alert-info">
    <h4><i class="fas fa-wave"></i> Welcome, <?php echo $supplier['SUPPLIER_COMP_NAME']; ?>!</h4>
    <p class="mb-0">Email: <?php echo $supplier['SUPPLIER_EMAIL_ADD']; ?></p>
</div>

<div class="row mb-4">
    <div class="col-md-3">
        <div class="card text-white bg-primary stat-card">
            <div class="card-body">
                <h6>Total DO</h6>
                <h2 class="mb-0"><?php echo $totalDO; ?></h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-warning stat-card">
            <div class="card-body">
                <h6>Pending</h6>
                <h2 class="mb-0"><?php echo $pendingDO; ?></h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-success stat-card">
            <div class="card-body">
                <h6>Approved</h6>
                <h2 class="mb-0"><?php echo $approvedDO; ?></h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-danger stat-card">
            <div class="card-body">
                <h6>Rejected</h6>
                <h2 class="mb-0"><?php echo $rejectedDO; ?></h2>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-6">
        <div class="card text-white bg-success">
            <div class="card-body">
                <h6>Total Paid</h6>
                <h2 class="mb-0">RM <?php echo number_format($totalPaid, 2); ?></h2>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card text-white bg-warning">
            <div class="card-body">
                <h6>Pending Payment</h6>
                <h2 class="mb-0">RM <?php echo number_format($totalPending, 2); ?></h2>
            </div>
        </div>
    </div>
</div>

<div class="card card-ktm">
    <div class="card-header bg-primary text-white"><i class="fas fa-list"></i> Recent Delivery Orders</div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>DO Number</th>
                        <th>PO Number</th>
                        <th>Status</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($recentDO)): ?>
                        <tr>
                            <td colspan="4" class="text-center text-muted py-3"><i class="fas fa-info-circle"></i> No delivery orders found.</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($recentDO as $row): ?>
                            <tr>
                                <td><?php echo $row['DO_number']; ?></td>
                                <td><?php echo $row['PO_number']; ?></td>
                                <td><?php echo getStatusBadge($row['Status']); ?></td>
                                <td><?php echo date('d/m/Y', strtotime($row['created_date'])); ?></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include ROOT_PATH . '/Presentation/View/SharedUI/footer.php'; ?>