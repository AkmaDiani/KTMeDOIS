<?php
$title = 'Staff Dashboard - KTM eDOIS';
$showTopbar = true;
$showSidebar = true;
include __DIR__ . '/../SharedUI/topbar.php';
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Staff Dashboard</h1>
    <div>
        <span class="badge bg-primary"><?php echo $_SESSION['role']; ?></span>
        <span class="badge bg-info"><?php echo $_SESSION['username']; ?></span>
    </div>
</div>

<div class="alert alert-info">
    <h4><i class="fas fa-wave"></i> Welcome, <?php echo $_SESSION['username']; ?>!</h4>
    <p class="mb-0">Role: <?php echo $_SESSION['role']; ?></p>
</div>

<div class="row mb-4">
    <div class="col-md-3">
        <div class="card text-white bg-success stat-card">
            <div class="card-body">
                <h6>Active</h6>
                <h2 class="mb-0"><?php echo $stats['active']; ?></h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-warning stat-card">
            <div class="card-body">
                <h6>Pending</h6>
                <h2 class="mb-0"><?php echo $stats['pending']; ?></h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-danger stat-card">
            <div class="card-body">
                <h6>Inactive</h6>
                <h2 class="mb-0"><?php echo $stats['inactive']; ?></h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-white bg-info stat-card">
            <div class="card-body">
                <h6>Total Vendors</h6>
                <h2 class="mb-0"><?php echo $stats['total']; ?></h2>
            </div>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-4">
        <div class="card text-white bg-primary stat-card">
            <div class="card-body">
                <h6>Total DO</h6>
                <h2 class="mb-0"><?php echo $totalDO; ?></h2>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-white bg-warning stat-card">
            <div class="card-body">
                <h6>Pending DO</h6>
                <h2 class="mb-0"><?php echo $pendingDO; ?></h2>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card text-white bg-info stat-card">
            <div class="card-body">
                <h6>Total Invoice</h6>
                <h2 class="mb-0"><?php echo $totalInvoice; ?></h2>
            </div>
        </div>
    </div>
</div>

<div class="card card-ktm">
    <div class="card-header bg-primary text-white"><i class="fas fa-cog"></i> Quick Actions</div>
    <div class="card-body">
        <div class="row text-center">
            <div class="col-md-3"><a href="/SDW/KTMeDOIS/staff/vendor" class="text-decoration-none">
                    <div class="p-3 border rounded"><i class="fas fa-building fa-2x text-primary"></i>
                        <h6 class="mt-2">Vendor Registry</h6>
                    </div>
                </a></div>
            <div class="col-md-3"><a href="/SDW/KTMeDOIS/staff/vendor/add" class="text-decoration-none">
                    <div class="p-3 border rounded"><i class="fas fa-plus fa-2x text-success"></i>
                        <h6 class="mt-2">Add Vendor</h6>
                    </div>
                </a></div>
            <div class="col-md-3"><a href="/SDW/KTMeDOIS/staff/vendor/report" class="text-decoration-none">
                    <div class="p-3 border rounded"><i class="fas fa-chart-bar fa-2x text-warning"></i>
                        <h6 class="mt-2">Reports</h6>
                    </div>
                </a></div>
            <div class="col-md-3"><a href="/SDW/KTMeDOIS/staff/profile" class="text-decoration-none">
                    <div class="p-3 border rounded"><i class="fas fa-user fa-2x text-info"></i>
                        <h6 class="mt-2">My Profile</h6>
                    </div>
                </a></div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../SharedUI/footer.php'; ?>