<?php
$title = 'Supplier Profile - KTM eDOIS';
$showTopbar = true;
$showSidebar = true;
include ROOT_PATH . '/Presentation/View/SharedUI/topbar.php';
include ROOT_PATH . '/Presentation/View/SharedUI/sidebarM1.php';

?>
<div class="content">
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Supplier Profile</h1>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card card-ktm">
            <div class="card-header bg-primary text-white"><i class="fas fa-user"></i> Profile Information</div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3"><label class="fw-bold text-muted">Supplier ID</label>
                        <p class="h5"><?php echo $supplier['SUPPLIERID']; ?></p>
                    </div>
                    <div class="col-md-6 mb-3"><label class="fw-bold text-muted">Status</label>
                        <p class="h5"><span class="badge bg-<?php echo $supplier['SUPPLIER_CTC_STATUS'] == 'Active' ? 'success' : 'danger'; ?>"><?php echo $supplier['SUPPLIER_CTC_STATUS']; ?></span></p>
                    </div>
                    <div class="col-md-6 mb-3"><label class="fw-bold text-muted">Company Name</label>
                        <p class="h5"><?php echo $supplier['SUPPLIER_COMP_NAME']; ?></p>
                    </div>
                    <div class="col-md-6 mb-3"><label class="fw-bold text-muted">Registration Number</label>
                        <p class="h5"><?php echo $supplier['SUPPLIER_COMP_REG_NO']; ?></p>
                    </div>
                    <div class="col-md-6 mb-3"><label class="fw-bold text-muted">Contact Person</label>
                        <p class="h5"><?php echo $supplier['SUPPLIER_CTC_PERSON']; ?></p>
                    </div>
                    <div class="col-md-6 mb-3"><label class="fw-bold text-muted">Contact Number</label>
                        <p class="h5"><?php echo $supplier['SUPPLIER_CTC_NO']; ?></p>
                    </div>
                    <div class="col-md-6 mb-3"><label class="fw-bold text-muted">Email Address</label>
                        <p class="h5"><?php echo $supplier['SUPPLIER_EMAIL_ADD']; ?></p>
                    </div>
                    <div class="col-md-6 mb-3"><label class="fw-bold text-muted">Expiry Date</label>
                        <p class="h5"><?php echo date('d/m/Y', strtotime($supplier['SUPPLIER_EXPIRED_DATE'])); ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card card-ktm">
            <div class="card-header bg-info text-white"><i class="fas fa-chart-simple"></i> Quick Stats</div>
            <div class="card-body">
                <div class="mb-2"><strong>Total DO:</strong> <?php echo $totalDO; ?></div>
                <div><strong>Member Since:</strong> <?php echo date('d/m/Y'); ?></div>
            </div>
        </div>
    </div>
</div>
</div>

<?php include __DIR__ . '/../SharedUI/footer.php'; ?>