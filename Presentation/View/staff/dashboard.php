<?php
$title = 'Staff Dashboard - KTM eDOIS';
$showTopbar = true;
$showSidebar = true;

include ROOT_PATH . '/Presentation/View/SharedUI/topbar.php';
include ROOT_PATH . '/Presentation/View/SharedUI/sidebarM1.php';
?>

<div class="content">

    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card text-white bg-success stat-card">
                <div class="card-body">
                    <h6>Active</h6>
                    <h2 class="mb-0"><?php echo $stats['active'] ?? 0; ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-warning stat-card">
                <div class="card-body">
                    <h6>Pending</h6>
                    <h2 class="mb-0"><?php echo $stats['pending'] ?? 0; ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-danger stat-card">
                <div class="card-body">
                    <h6>Inactive</h6>
                    <h2 class="mb-0"><?php echo $stats['inactive'] ?? 0; ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-info stat-card">
                <div class="card-body">
                    <h6>Total Vendors</h6>
                    <h2 class="mb-0"><?php echo $stats['total'] ?? 0; ?></h2>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card text-white bg-primary stat-card">
                <div class="card-body">
                    <h6>Total DO</h6>
                    <h2 class="mb-0"><?php echo $totalDO ?? 0; ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-warning stat-card">
                <div class="card-body">
                    <h6>Pending DO</h6>
                    <h2 class="mb-0"><?php echo $pendingDO ?? 0; ?></h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card text-white bg-info stat-card">
                <div class="card-body">
                    <h6>Total Invoice</h6>
                    <h2 class="mb-0"><?php echo $totalInvoice ?? 0; ?></h2>
                </div>
            </div>
        </div>
    </div>

</div> <!-- END .content -->

<?php include ROOT_PATH . '/Presentation/View/SharedUI/footer.php'; ?>