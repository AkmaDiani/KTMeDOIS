<?php
$title = 'Edit Vendor - KTM eDOIS';
$showTopbar = true;
$showSidebar = true;
include ROOT_PATH . '/Presentation/View/SharedUI/topbar.php';
include ROOT_PATH . '/Presentation/View/SharedUI/sidebarM1.php';

?>
<div class="content">
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Edit Vendor</h1>
</div>

<?php if (isset($success) && $success): ?>
    <div class="alert alert-success"><?php echo $success; ?></div>
<?php endif; ?>
<?php if (isset($error) && $error): ?>
    <div class="alert alert-danger"><?php echo $error; ?></div>
<?php endif; ?>

<div class="card card-ktm">
    <div class="card-header bg-warning text-dark"><i class="fas fa-edit"></i> Edit Vendor Information</div>
    <div class="card-body">
        <form method="POST" action="">
            <div class="row">
                <div class="col-md-6 mb-3"><label class="form-label fw-bold">Company Name <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($vendor['Supplier_name']); ?>" required>
                </div>
                <div class="col-md-6 mb-3"><label class="form-label fw-bold">Contact Person <span class="text-danger">*</span></label>
                    <input type="text" name="contact" class="form-control" value="<?php echo htmlspecialchars($vendor['Contact_person']); ?>" required>
                </div>
                <div class="col-md-6 mb-3"><label class="form-label fw-bold">Phone Number <span class="text-danger">*</span></label>
                    <input type="text" name="phone" class="form-control" value="<?php echo htmlspecialchars($vendor['phone']); ?>" required>
                </div>
                <div class="col-md-6 mb-3"><label class="form-label fw-bold">Email Address <span class="text-danger">*</span></label>
                    <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($vendor['email']); ?>" required>
                </div>
                <div class="col-md-12 mb-3"><label class="form-label fw-bold">Billing Address</label>
                    <textarea name="address" class="form-control" rows="3"><?php echo htmlspecialchars($vendor['Billing_address']); ?></textarea>
                </div>
                <div class="col-md-12 mb-3"><label class="form-label fw-bold">Status <span class="text-danger">*</span></label>
                    <select name="status" class="form-control" required>
                        <option value="Active" <?php echo $vendor['status'] == 'Active' ? 'selected' : ''; ?>>Active</option>
                        <option value="Pending Verification" <?php echo $vendor['status'] == 'Pending Verification' ? 'selected' : ''; ?>>Pending Verification</option>
                        <option value="Inactive" <?php echo $vendor['status'] == 'Inactive' ? 'selected' : ''; ?>>Inactive</option>
                    </select>
                </div>
            </div>
            <div class="text-end">
                <a href="/KTMeDOIS/Presentation/Public/indexM1.php?controller=staff&action=vendor" class="btn btn-secondary"><i class="fas fa-times"></i> Cancel</a>
                <button type="submit" class="btn btn-warning"><i class="fas fa-save"></i> Update Vendor</button>
            </div>
        </form>
    </div>
</div>
</div>

<?php include __DIR__ . '/../SharedUI/footer.php'; ?>