<?php
$title = 'Staff Profile - KTM eDOIS';
$showTopbar = true;
$showSidebar = true;
include ROOT_PATH . '/Presentation/View/SharedUI/topbar.php';
include ROOT_PATH . '/Presentation/View/SharedUI/sidebarM1.php';

?>
<div class="content">
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Staff Profile</h1>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card card-ktm">
            <div class="card-header bg-primary text-white"><i class="fas fa-user"></i> Profile Information</div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3"><label class="fw-bold text-muted">User ID</label>
                        <p class="h5"><?php echo $user['User_ID']; ?></p>
                    </div>
                    <div class="col-md-6 mb-3"><label class="fw-bold text-muted">Username</label>
                        <p class="h5"><?php echo $user['Username']; ?></p>
                    </div>
                    <div class="col-md-6 mb-3"><label class="fw-bold text-muted">Email</label>
                        <p class="h5"><?php echo $user['Email']; ?></p>
                    </div>
                    <div class="col-md-6 mb-3"><label class="fw-bold text-muted">Role</label>
                        <p class="h5"><span class="badge bg-primary"><?php echo $user['Role']; ?></span></p>
                    </div>
                    <div class="col-md-6 mb-3"><label class="fw-bold text-muted">Status</label>
                        <p class="h5"><span class="badge bg-success"><?php echo $user['Status']; ?></span></p>
                    </div>
                    <div class="col-md-6 mb-3"><label class="fw-bold text-muted">Last Login</label>
                        <p class="h5"><?php echo $user['Last_Login'] ? date('d/m/Y H:i', strtotime($user['Last_Login'])) : '-'; ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card card-ktm">
            <div class="card-header bg-info text-white"><i class="fas fa-chart-simple"></i> Quick Stats</div>
            <div class="card-body">
                <div class="mb-2"><strong>Role:</strong> <?php echo $user['Role']; ?></div>
                <div class="mb-2"><strong>Status:</strong> <?php echo $user['Status']; ?></div>
                <div><strong>Member Since:</strong> <?php echo date('d/m/Y'); ?></div>
            </div>
        </div>
    </div>
</div>
</div>

<?php include __DIR__ . '/../SharedUI/footer.php'; ?>