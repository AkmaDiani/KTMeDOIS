<?php
$title = 'Vendor Activity & Status Report - KTM eDOIS';
$showTopbar = true;
$showSidebar = true;
include ROOT_PATH . '/Presentation/View/SharedUI/topbar.php';
include ROOT_PATH . '/Presentation/View/SharedUI/sidebarM1.php';

?>
<div class="content">
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Vendor Activity & Status Report</h1>
    
</div>

<div class="card card-ktm mb-4">
    <div class="card-header bg-success text-white"><i class="fas fa-chart-pie"></i> Vendor Status Summary</div>
    <div class="card-body">
        <p class="text-muted"><i class="fas fa-info-circle"></i> A real-time overview of a supplier's eligibility within the KTM eDOIS platform, categorized as Active, Inactive and Pending Verification.</p>
        <div class="row mt-3">
            <div class="col-md-3">
                <div class="card text-white bg-success">
                    <div class="card-body text-center">
                        <h6>Active</h6>
                        <h2 class="mb-0"><?php echo $stats['active']; ?></h2><small><?php echo $stats['total'] > 0 ? round(($stats['active'] / $stats['total']) * 100, 1) : 0; ?>%</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-warning">
                    <div class="card-body text-center">
                        <h6>Pending Verification</h6>
                        <h2 class="mb-0"><?php echo $stats['pending']; ?></h2><small><?php echo $stats['total'] > 0 ? round(($stats['pending'] / $stats['total']) * 100, 1) : 0; ?>%</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-danger">
                    <div class="card-body text-center">
                        <h6>Inactive</h6>
                        <h2 class="mb-0"><?php echo $stats['inactive']; ?></h2><small><?php echo $stats['total'] > 0 ? round(($stats['inactive'] / $stats['total']) * 100, 1) : 0; ?>%</small>
                    </div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="card text-white bg-info">
                    <div class="card-body text-center">
                        <h6>Total Vendors</h6>
                        <h2 class="mb-0"><?php echo $stats['total']; ?></h2><small>100%</small>
                    </div>
                </div>
            </div>
        </div>
        <div class="row mt-4">
            <div class="col-md-12">
                <div class="progress" style="height: 30px;">
                    <div class="progress-bar bg-success" style="width: <?php echo $stats['total'] > 0 ? round(($stats['active'] / $stats['total']) * 100, 1) : 0; ?>%;">Active <?php echo $stats['total'] > 0 ? round(($stats['active'] / $stats['total']) * 100, 1) : 0; ?>%</div>
                    <div class="progress-bar bg-warning" style="width: <?php echo $stats['total'] > 0 ? round(($stats['pending'] / $stats['total']) * 100, 1) : 0; ?>%;">Pending <?php echo $stats['total'] > 0 ? round(($stats['pending'] / $stats['total']) * 100, 1) : 0; ?>%</div>
                    <div class="progress-bar bg-danger" style="width: <?php echo $stats['total'] > 0 ? round(($stats['inactive'] / $stats['total']) * 100, 1) : 0; ?>%;">Inactive <?php echo $stats['total'] > 0 ? round(($stats['inactive'] / $stats['total']) * 100, 1) : 0; ?>%</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="card card-ktm mb-4">
    <div class="card-header bg-primary text-white"><i class="fas fa-file-export"></i> Generate Report</div>
    <div class="card-body">
        <form method="GET" action="" class="row g-3">
            <input type="hidden" name="controller" value="staff">
            <input type="hidden" name="action" value="vendor_report">
            <div class="col-md-4"><label class="form-label">Vendor ID / Name</label>
                <input type="text" class="form-control" name="search" placeholder="e.g: ABCD123456 or Company Name" value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
            </div>
            <div class="col-md-4"><label class="form-label">Status</label>
                <select class="form-control" name="status">
                    <option value="">All Status</option>
                    <option value="Active" <?php echo (isset($_GET['status']) && $_GET['status'] == 'Active') ? 'selected' : ''; ?>>Active</option>
                    <option value="Pending Verification" <?php echo (isset($_GET['status']) && $_GET['status'] == 'Pending Verification') ? 'selected' : ''; ?>>Pending Verification</option>
                    <option value="Inactive" <?php echo (isset($_GET['status']) && $_GET['status'] == 'Inactive') ? 'selected' : ''; ?>>Inactive</option>
                </select>
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100"><i class="fas fa-file-pdf me-2"></i> Generate Report</button>
                <a href="/SDW/KTMeDOIS/staff/vendor/report" class="btn btn-secondary ms-2 w-50"><i class="fas fa-undo"></i> Reset</a>
            </div>
        </form>
    </div>
</div>

<div class="card card-ktm">
    <div class="card-header bg-primary text-white"><i class="fas fa-list"></i> Vendor List <span class="badge bg-light text-dark ms-2"><?php echo $filteredCount; ?> records</span></div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped">
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
                    <?php if ($filteredCount > 0): ?>
                        <?php foreach ($vendors as $row): ?>
                            <tr>
                                <td><i class="fas fa-user-circle text-primary me-1"></i> <?php echo htmlspecialchars($row['Contact_person']); ?></td>
                                <td><?php echo str_pad($row['Vendor_Number'], 6, '0', STR_PAD_LEFT); ?></td>
                                <td><?php echo htmlspecialchars($row['Supplier_name']); ?></td>
                                <td><?php
                                    $status = $row['status'] ?? 'Pending Verification';
                                    $class = '';
                                    if ($status == 'Active') $class = 'status-active';
                                    elseif ($status == 'Pending Verification') $class = 'status-pending';
                                    elseif ($status == 'Inactive') $class = 'status-inactive';
                                    ?><span class="<?php echo $class; ?>"><i class="fas fa-circle me-1" style="font-size:8px;"></i> <?php echo $status; ?></span></td>
                                <td>
                                    <a href="/SDW/KTMeDOIS/staff/vendor/view/<?php echo $row['Supplier_id']; ?>" class="btn btn-sm btn-info"><i class="fas fa-eye"></i> View</a>
                                    <a href="/SDW/KTMeDOIS/staff/vendor/edit/<?php echo $row['Supplier_id']; ?>" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i> Edit</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="5" class="text-center text-muted py-4"><i class="fas fa-search fa-2x d-block mb-2"></i> No vendors found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <div class="mt-3"><small class="text-muted"><i class="fas fa-info-circle"></i> Showing <?php echo $filteredCount; ?> record(s) from total <?php echo $stats['total']; ?> vendors</small></div>
    </div>
</div>
<div>
        <button class="btn btn-primary" onclick="window.print()"><i class="fas fa-print"></i> Print</button>
    </div>
</div>

<script>
    function saveReport() {
        alert('Report saved successfully!');
    }
</script>

<?php include __DIR__ . '/../SharedUI/footer.php'; ?>