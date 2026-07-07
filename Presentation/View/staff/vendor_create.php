<?php
$title = 'Add Vendor - KTM eDOIS';
$showTopbar = true;
$showSidebar = true;
include __DIR__ . '/../SharedUI/topbar.php';
?>

<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Add New Vendor</h1>
    <a href="/SDW/KTMeDOIS/staff/vendor" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Back</a>
</div>

<?php if (isset($success) && $success): ?>
    <div class="alert alert-success"><?php echo $success; ?></div>
<?php endif; ?>
<?php if (isset($error) && $error): ?>
    <div class="alert alert-danger"><?php echo $error; ?></div>
<?php endif; ?>

<div class="card card-ktm">
    <div class="card-header bg-primary text-white"><i class="fas fa-plus"></i> Vendor Registration Form</div>
    <div class="card-body">
        <form method="POST" action="">
            <div class="row">
                <div class="col-md-6 mb-3"><label class="form-label fw-bold">Company Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" placeholder="Enter company name" required>
                </div>
                <div class="col-md-6 mb-3"><label class="form-label fw-bold">Contact Person <span class="text-danger">*</span></label>
                    <input type="text" name="contact" class="form-control" placeholder="Enter contact person" required>
                </div>
                <div class="col-md-6 mb-3"><label class="form-label fw-bold">Phone Number <span class="text-danger">*</span></label>
                    <input type="text" name="phone" class="form-control" placeholder="e.g: 012-3456789" required>
                </div>
                <div class="col-md-6 mb-3"><label class="form-label fw-bold">Email Address <span class="text-danger">*</span></label>
                    <input type="email" name="email" class="form-control" placeholder="Enter email address" required>
                </div>
                <div class="col-md-12 mb-3"><label class="form-label fw-bold">Billing Address</label>
                    <textarea name="address" class="form-control" rows="3" placeholder="Enter billing address"></textarea>
                </div>
                <div class="col-md-12 mb-3"><label class="form-label fw-bold">Status <span class="text-danger">*</span></label>
                    <select name="status" class="form-control" required>
                        <option value="Active">Active</option>
                        <option value="Pending Verification" selected>Pending Verification</option>
                        <option value="Inactive">Inactive</option>
                    </select>
                </div>
            </div>
            <div class="text-end">
                <a href="/SDW/KTMeDOIS/staff/vendor" class="btn btn-secondary"><i class="fas fa-times"></i> Cancel</a>
                <button type="submit" class="btn btn-primary"><i class="fas fa-save"></i> Register Vendor</button>
            </div>
        </form>
    </div>
</div>

<?php include __DIR__ . '/../SharedUI/footer.php'; ?>