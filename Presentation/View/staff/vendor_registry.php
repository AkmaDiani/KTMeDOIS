<?php
$title = 'Vendor Registry - KTM eDOIS';
$showTopbar = true;
$showSidebar = true;
include ROOT_PATH . '/Presentation/View/SharedUI/topbar.php';
include ROOT_PATH . '/Presentation/View/SharedUI/sidebarM1.php';

?>
<div class="content">
<div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items- pt-3 pb-2 mb-3 border-bottom">
    <h1 class="h2">Vendor Registry</h1>
</div>

<?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success"><?php echo $_SESSION['success'];
                                        unset($_SESSION['success']); ?></div>
<?php endif; ?>
<?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger"><?php echo $_SESSION['error'];
                                    unset($_SESSION['error']); ?></div>
<?php endif; ?>

<div class="card card-ktm">
    <div class="card-header bg-primary text-white"><i class="fas fa-list"></i> Vendor Registry</div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Vendor NUMBER</th>
                        <th>Company Name</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($suppliers) > 0): ?>
                        <?php foreach ($suppliers as $row): ?>
                            <tr>
                                <td><i class="fas fa-user-circle text-primary me-1"></i> <?php echo htmlspecialchars($row['Contact_person']); ?></td>
                                <td><strong><?php echo str_pad($row['Vendor_Number'], 6, '0', STR_PAD_LEFT); ?></strong></td>
                                <td><?php echo htmlspecialchars($row['Supplier_name']); ?></td>
                                <td><?php
                                    $status = $row['status'] ?? 'Pending Verification';
                                    $class = '';
                                    if ($status == 'Active') $class = 'status-active';
                                    elseif ($status == 'Pending Verification') $class = 'status-pending';
                                    elseif ($status == 'Inactive') $class = 'status-inactive';
                                    ?><span class="<?php echo $class; ?>"><i class="fas fa-circle me-1" style="font-size:8px;"></i> <?php echo $status; ?></span></td>
                                <td>
                                    <a href="/KTMeDOIS/Presentation/Public/indexM1.php?controller=staff&action=vendor_view&id=<?php echo $row['Supplier_id']; ?>" class="btn btn-sm btn-info"><i class="fas fa-eye"></i> View</a>
                                    <a href="/KTMeDOIS/Presentation/Public/indexM1.php?controller=staff&action=vendor_edit&id=<?php echo $row['Supplier_id']; ?>" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i> Edit</a>
                                    <a href="/KTMeDOIS/Presentation/Public/indexM1.php?controller=staff&action=vendor_delete&id=<?php echo $row['Supplier_id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')"><i class="fas fa-trash"></i> Delete</a>
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