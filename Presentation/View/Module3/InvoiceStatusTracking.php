<?php
// Presentation/View/Module3/InvoiceStatusTracking.php

if (!class_exists('InvoiceStatusTracking')) {
    class InvoiceStatusTracking {
        private int $vendorId;
        private array $invoices;
        private ?array $selectedInvoice;
        private string $claimStatus;
        private string $role;
        private array $agingData;
        private array $errors;
        private array $successes;

        public function __construct(
            int $vendorId = 0,
            array $invoices = [],
            array $agingData = [],
            string $role = 'Vendor',
            array $errors = [],
            array $successes = []
        ) {
            $this->vendorId = $vendorId;
            $this->invoices = $invoices;
            $this->agingData = $agingData;
            $this->role = $role;
            $this->errors = $errors;
            $this->successes = $successes;
            $this->selectedInvoice = null;
            $this->claimStatus = '';
        }

        public function displayStatusList(): void {
            $isVendorView = ($this->role === 'Vendor');
            $invoices = $this->invoices;
            $agingData = $this->agingData;
            $errors = $this->errors;
            $successes = $this->successes;

            
            $pageTitle = $isVendorView ? 'My Invoice Claims' : 'Invoice Management & Reports';
            $activePage = $isVendorView ? 'invoice_status' : 'pending_invoices';

            
            include ROOT_PATH . '/Presentation/View/SharedUI/topbar.php';
            include ROOT_PATH . '/Presentation/View/SharedUI/sidebarM3.php';
            ?>
            <div class="content"> <!-- Changed from content-area to content -->

                <?php if ($isVendorView): ?>
                    <!-- VENDOR VIEW -->
                    <h2>My Invoice Claims</h2>
                    <?php foreach ($errors as $error): ?>
                        <div class="alert alert-danger"><?= $this->escape($error) ?></div>
                    <?php endforeach; ?>
                    <?php foreach ($successes as $success): ?>
                        <div class="alert alert-success"><?= $this->escape($success) ?></div>
                    <?php endforeach; ?>

                    <?php if (empty($invoices)): ?>
                        <div class="alert alert-info">
                            <p>No invoices found for your account.</p>
                            <p><a href="/KTMeDOIS/Presentation/Public/indexM3.php?action=invoice_submit" class="btn btn-primary">Submit Your First Invoice</a></p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>No.</th>
                                        <th>Invoice Number</th>
                                        <th>DO Number</th>
                                        <th>Description</th>
                                        <th>Submission Date</th>
                                        <th>Status</th>
                                        <th>Total Amount</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php $i = 1; foreach ($invoices as $inv): ?>
                                    <tr>
                                        <td><?= $i++ ?></td>
                                        <td><?= $this->escape($inv['Invoice_num']) ?></td>
                                        <td><?= $this->escape($inv['DO_number'] ?? 'N/A') ?></td>
                                        <td><?= $this->escape($inv['Description'] ?? '-') ?></td>
                                        <td><?= $this->escape($inv['Created_At']) ?></td>
                                        <td>
                                            <span class="status-<?= strtolower(str_replace(' ', '-', $inv['Status'])) ?>">
                                                <?= $this->escape($inv['Status']) ?>
                                            </span>
                                        </td>
                                        <td>RM <?= number_format($inv['Total'], 2) ?></td>
                                        <td>
                                            <a href="/KTMeDOIS/Presentation/Public/indexM3.php?action=invoice_summary&id=<?= $inv['Invoice_id'] ?>" class="btn btn-sm btn-summary">Summary</a>
                                            <?php if ($inv['Status'] === 'Draft'): ?>
                                                <a href="/KTMeDOIS/Presentation/Public/indexM3.php?action=invoice_edit&id=<?= $inv['Invoice_id'] ?>" class="btn btn-sm btn-edit">Edit</a>
                                            <?php endif; ?>
                                            <a href="/KTMeDOIS/Presentation/Public/indexM3.php?action=invoice_pdf&id=<?= $inv['Invoice_id'] ?>" class="btn btn-sm btn-pdf">PDF</a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>

                <?php else: ?>
                    <!-- OFFICER VIEW -->
                    <h2>Invoice Management & Reports</h2>

                    <?php foreach ($errors as $error): ?>
                        <div class="alert alert-danger"><?= $this->escape($error) ?></div>
                    <?php endforeach; ?>
                    <?php foreach ($successes as $success): ?>
                        <div class="alert alert-success"><?= $this->escape($success) ?></div>
                    <?php endforeach; ?>

                    <?php if (empty($invoices)): ?>
                        <div class="alert alert-info">
                            <p>No invoices found matching the selected filters.</p>
                        </div>
                    <?php else: ?>
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>No.</th>
                                        <th>Invoice Number</th>
                                        <th>Supplier</th>
                                        <th>DO Number</th>
                                        <th>Description</th>
                                        <th>Submission Date</th>
                                        <th>Status</th>
                                        <th>Total Amount</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php $i = 1; foreach ($invoices as $inv): ?>
                                    <tr>
                                        <td><?= $i++ ?></td>
                                        <td><?= $this->escape($inv['Invoice_num']) ?></td>
                                        <td><?= $this->escape($inv['Supplier_name'] ?? 'N/A') ?></td>
                                        <td><?= $this->escape($inv['DO_number'] ?? 'N/A') ?></td>
                                        <td><?= $this->escape($inv['Description'] ?? '-') ?></td>
                                        <td><?= $this->escape($inv['Created_At']) ?></td>
                                        <td>
                                            <span class="status-badge <?= strtolower(str_replace(' ', '-', $inv['Status'])) ?>">
                                                <?= $this->escape($inv['Status']) ?>
                                            </span>
                                        </td>
                                        <td>RM <?= number_format($inv['Total'], 2) ?></td>
                                        <td>
                                            <div class="action-buttons">
                                                <a href="/KTMeDOIS/Presentation/Public/indexM3.php?action=invoice_summary&id=<?= $inv['Invoice_id'] ?>" class="btn btn-sm btn-summary">Summary</a>
                                                <?php if (in_array($inv['Status'], ['Submitted', 'Finance Review'])): ?>
                                                <form method="post" action="/KTMeDOIS/Presentation/Public/indexM3.php?action=invoice_review" style="display:inline-block;">
                                                    <input type="hidden" name="invoice_id" value="<?= $inv['Invoice_id'] ?>">
                                                    <?php if ($_SESSION['role'] === 'KTM Officer'): ?>
                                                        <button type="submit" name="action" value="approve" class="btn btn-sm btn-approve">Approve</button>
                                                        <button type="submit" name="action" value="forward" class="btn btn-sm btn-forward">Forward</button>
                                                        <button type="submit" name="action" value="reject" class="btn btn-sm btn-reject">Reject</button>
                                                        <input type="text" name="reason" class="reason-input" placeholder="Reason">
                                                    <?php elseif ($_SESSION['role'] === 'Finance Officer'): ?>
                                                        <button type="submit" name="action" value="approve" class="btn btn-sm btn-approve">Approve Payment</button>
                                                        <button type="submit" name="action" value="reject" class="btn btn-sm btn-reject">Reject</button>
                                                        <input type="text" name="reason" class="reason-input" placeholder="Reason">
                                                    <?php endif; ?>
                                                </form>
                                                <?php endif; ?>
                                                <a href="/KTMeDOIS/Presentation/Public/indexM3.php?action=invoice_pdf&id=<?= $inv['Invoice_id'] ?>" class="btn btn-sm btn-pdf">PDF</a>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>

                    <!-- Aging Report -->
                    <?php if (!empty($agingData)): ?>
                    <div class="aging-report" id="aging-report">
                        <h3>Invoice Aging Report</h3>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Company Name</th>
                                    <th>0 - 30 Days</th>
                                    <th>31 - 60 Days</th>
                                    <th>61 - 90 Days</th>
                                    <th>Above 90 Days</th>
                                    <th>Total</th>
                                </tr>
                            </thead>
                            <tbody>
                            <?php
                            $grandTotal = 0;
                            foreach ($agingData as $company => $data):
                                $grandTotal += $data['total'];
                            ?>
                                <tr>
                                    <td><strong><?= $this->escape($company) ?></strong></td>
                                    <td>RM <?= number_format($data['0-30'], 2) ?></td>
                                    <td>RM <?= number_format($data['31-60'], 2) ?></td>
                                    <td>RM <?= number_format($data['61-90'], 2) ?></td>
                                    <td>RM <?= number_format($data['90+'], 2) ?></td>
                                    <td><strong>RM <?= number_format($data['total'], 2) ?></strong></td>
                                </tr>
                            <?php endforeach; ?>
                                <tr class="grand-total">
                                    <td><strong>GRAND TOTAL</strong></td>
                                    <td><strong>RM <?= number_format(array_sum(array_column($agingData, '0-30')), 2) ?></strong></td>
                                    <td><strong>RM <?= number_format(array_sum(array_column($agingData, '31-60')), 2) ?></strong></td>
                                    <td><strong>RM <?= number_format(array_sum(array_column($agingData, '61-90')), 2) ?></strong></td>
                                    <td><strong>RM <?= number_format(array_sum(array_column($agingData, '90+')), 2) ?></strong></td>
                                    <td><strong>RM <?= number_format($grandTotal, 2) ?></strong></td>
                                </tr>
                            </tbody>
                        </table>
                        <div class="report-actions">
                            <button class="btn btn-primary" onclick="window.print();">Print Report</button>
                        </div>
                    </div>
                    <?php endif; ?>
                <?php endif; ?>

            </div> <!-- .content -->
            <?php
            include ROOT_PATH . '/Presentation/View/SharedUI/footer.php';
        }

        public function viewInvoiceDetails(int $invoiceId): void {
            // ... (unchanged, but can also be fixed similarly)
        }

        public function trackClaimProgress(): string {
            return $this->claimStatus;
        }

        private function escape($value): string {
            return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
        }

        public function render(): void {
            $this->displayStatusList();
        }
    }
}