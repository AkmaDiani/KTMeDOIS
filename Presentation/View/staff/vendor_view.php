<?php
$title = 'Vendor Profile - KTM eDOIS';
$showTopbar = true;
$showSidebar = true;
include ROOT_PATH . '/Presentation/View/SharedUI/topbar.php';
include ROOT_PATH . '/Presentation/View/SharedUI/sidebarM1.php';

?>
<div class="content">
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Vendor Profile</h1>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card card-ktm mb-4">
            <div class="card-header bg-primary text-white"><i class="fas fa-user"></i> Vendor Details</div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3"><label class="fw-bold text-muted">Full Name</label>
                        <p class="h5"><?php echo htmlspecialchars($vendor['Contact_person']); ?></p>
                    </div>
                    <div class="col-md-6 mb-3"><label class="fw-bold text-muted">Phone Number</label>
                        <p class="h5"><?php echo htmlspecialchars($vendor['phone']); ?></p>
                    </div>
                    <div class="col-md-6 mb-3"><label class="fw-bold text-muted">Email</label>
                        <p class="h5"><?php echo htmlspecialchars($vendor['email']); ?></p>
                    </div>
                    <div class="col-md-6 mb-3"><label class="fw-bold text-muted">Vendor ID</label>
                        <p class="h5">KTM<?php echo str_pad($vendor['Vendor_Number'], 6, '0', STR_PAD_LEFT); ?></p>
                    </div>
                    <div class="col-md-6 mb-3"><label class="fw-bold text-muted">Status</label>
                        <p class="h5"><?php
                                        $status = $vendor['status'] ?? 'Pending Verification';
                                        $class = '';
                                        if ($status == 'Active') $class = 'status-active';
                                        elseif ($status == 'Pending Verification') $class = 'status-pending';
                                        elseif ($status == 'Inactive') $class = 'status-inactive';
                                        ?><span class="<?php echo $class; ?>"><?php echo $status; ?></span></p>
                    </div>
                    <div class="col-md-6 mb-3"><label class="fw-bold text-muted">Vendor ID Activation Date</label>
                        <p class="h5">11/1/2023</p>
                    </div>
                </div>
            </div>
        </div>

        <div class="card card-ktm">
            <div class="card-header bg-primary text-white"><i class="fas fa-building"></i> Company Details</div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3"><label class="fw-bold text-muted">Company Name</label>
                        <p class="h5"><?php echo htmlspecialchars($vendor['Supplier_name']); ?></p>
                    </div>
                    <div class="col-md-6 mb-3"><label class="fw-bold text-muted">Reference Number</label>
                        <p class="h5"><?php echo htmlspecialchars($vendor['Supplier_name']); ?></p>
                    </div>
                    <div class="col-md-6 mb-3"><label class="fw-bold text-muted">Billing Address</label>
                        <p class="h5"><?php echo htmlspecialchars($vendor['Billing_address']) ?: '-'; ?></p>
                    </div>
                    <div class="col-md-6 mb-3"><label class="fw-bold text-muted">Vendor ID Expiry Date</label>
                        <p class="h5">11/1/2027</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card card-ktm">
            <div class="card-header bg-info text-white"><i class="fas fa-chart-simple"></i> Summary</div>
            <div class="card-body">
                <div class="mb-2"><strong>Total DO:</strong> <?php echo $totalDO; ?></div>
                <div class="mb-2"><strong>Total Invoice:</strong> <?php echo $totalInvoice; ?></div>
                <div><strong>User Role:</strong> <?php echo $_SESSION['role']; ?></div>
            </div>
        </div>

        <div class="card card-ktm mt-3">
            <div class="card-header bg-success text-white"><i class="fas fa-cog"></i> Quick Actions</div>
            <div class="card-body">
                <div class="d-grid gap-2">
                    <a href="/KTMeDOIS/Presentation/Public/indexM1.php?controller=staff&action=vendor_edit&id=<?php echo $id; ?>" class="btn btn-warning"><i class="fas fa-edit"></i> Edit Vendor</a>
                    <a href="/KTMeDOIS/Presentation/Public/indexM1.php?controller=staff&action=vendor_delete&id=<?php echo $id; ?>" class="btn btn-danger" onclick="return confirm('Are you sure?')"><i class="fas fa-trash"></i> Delete Vendor</a>
                    <a href="/KTMeDOIS/Presentation/Public/indexM1.php?controller=staff&action=vendor" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back to List</a>
                </div>
            </div>
        </div>
    </div>
</div>
</div>

<?php include __DIR__ . '/../SharedUI/footer.php'; ?>