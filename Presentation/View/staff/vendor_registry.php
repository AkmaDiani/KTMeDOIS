<?php
$title = 'Vendor Registry - KTM eDOIS';
$showTopbar = true;
$showSidebar = true;
include __DIR__ . '/../SharedUI/topbar.php';
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Vendor Registry</h1>
    <div class="btn-toolbar mb-2 mb-md-0">
        <a href="/SDW/KTMeDOIS/staff/vendor/add" class="btn btn-primary me-2"><i class="fas fa-plus"></i> Add Vendor</a>
        <button class="btn btn-success me-2" onclick="syncVendors()"><i class="fas fa-sync"></i> Sync</button>
        <button class="btn btn-secondary" onclick="filterVendors()"><i class="fas fa-filter"></i> Filter</button>
    </div>
</div>

<?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success"><?php echo $_SESSION['success'];
                                        unset($_SESSION['success']); ?></div>
<?php endif; ?>
<?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger"><?php echo $_SESSION['error'];
                                    unset($_SESSION['error']); ?></div>
<?php endif; ?>

<div class="row mb-4">
    <div class="col-md-3">
        <div class="card text-white bg-primary stat-card">
            <div class="card-body">
                <h6>Total Vendors</h6>
                <h2 class="mb-0"><?php echo $stats['total']; ?></h2>
            </div>
        </div>
    </div>
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
</div>

<div class="card card-ktm">
    <div class="card-header bg-primary text-white"><i class="fas fa-list"></i> Vendor Registry</div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Vendor ID</th>
                        <th>Company Name</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($suppliers) > 0): ?>
                        <?php foreach ($suppliers as $row): ?>
                            <tr>
                                <td><i class="fas fa-user-circle text-primary me-1"></i> <?php echo htmlspecialchars($row['Contac_person']); ?></td>
                                <td><strong>KTM<?php echo str_pad($row['Vendor_Number'], 6, '0', STR_PAD_LEFT); ?></strong></td>
                                <td><?php echo htmlspecialchars($row['Supplier_name']); ?></td>
                                <td><?php
                                    $status = $row['status'] ?? 'Pending Verification';
                                    $class = '';
                                    if ($status == 'Active') $class = 'status-active';
                                    elseif ($status == 'Pending Verification') $class = 'status-pending';
                                    elseif ($status == 'Inactive') $class = 'status-inactive';
                                    ?><span class="<?php echo $class; ?>"><i class="fas fa-circle me-1" style="font-size:8px;"></i> <?php echo $status; ?></span></td>
                                <td>
                                    <a href="/SDW/KTMeDOIS/staff/vendor/view/<?php echo $row['Supplier_id']; ?>" class="btn btn-sm btn-info"><i class="fas fa-eye"></i></a>
                                    <a href="/SDW/KTMeDOIS/staff/vendor/edit/<?php echo $row['Supplier_id']; ?>" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                                    <a href="/SDW/KTMeDOIS/staff/vendor/delete/<?php echo $row['Supplier_id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')"><i class="fas fa-trash"></i></a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4"><i class="fas fa-building fa-2x d-block mb-2"></i> No vendors found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    function syncVendors() {
        alert('Vendor data synchronized successfully!');
    }

    function filterVendors() {
        alert('Filter functionality coming soon!');
    }
</script>

<?php include __DIR__ . '/../SharedUI/footer.php'; ?>