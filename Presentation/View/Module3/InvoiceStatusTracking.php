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
            $activePage = $isVendorView ? 'invoice_claims' : 'pending_invoices';

            // Include topbar and sidebar
            include __DIR__ . '/../SharedUI/topbar.php';
            include __DIR__ . '/../SharedUI/sidebarM3.php';
            ?>
            <div class="content-area">

                <?php if ($isVendorView): ?>
                    <!-- ============================================== -->
                    <!-- VENDOR VIEW                                    -->
                    <!-- ============================================== -->
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
                            <p><a href="/KTMEDOIS/Presentation/Public/index.php?action=invoice_submit" class="btn btn-primary">Submit Your First Invoice</a></p>
                        </div>
                    <?php else: ?>
                        <table>
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
                                        <a href="/KTMEDOIS/Presentation/Public/index.php?action=invoice_summary&id=<?= $inv['Invoice_id'] ?>" class="btn" style="padding:2px 10px; font-size:12px;">Summary</a>
                                        <?php if ($inv['Status'] === 'Draft'): ?>
                                            <a href="/KTMEDOIS/Presentation/Public/index.php?action=invoice_edit&id=<?= $inv['Invoice_id'] ?>" class="btn" style="padding:2px 10px; font-size:12px;">Edit</a>
                                        <?php endif; ?>
                                        <a href="/KTMEDOIS/Presentation/Public/index.php?action=invoice_pdf&id=<?= $inv['Invoice_id'] ?>" class="btn btn-primary" style="padding:2px 10px; font-size:12px;">PDF</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php endif; ?>

                <?php else: ?>
                    <!-- ============================================== -->
                    <!-- OFFICER VIEW - WITH AGING REPORT               -->
                    <!-- ============================================== -->
                    <h2>Invoice Management & Reports</h2>

                    <?php foreach ($errors as $error): ?>
                        <div class="alert alert-danger"><?= $this->escape($error) ?></div>
                    <?php endforeach; ?>
                    <?php foreach ($successes as $success): ?>
                        <div class="alert alert-success"><?= $this->escape($success) ?></div>
                    <?php endforeach; ?>

                    <!-- Filters -->
                    <div class="filter-container">
                        <form method="get" action="/KTMEDOIS/Presentation/Public/index.php">
                            <input type="hidden" name="action" value="invoice_pending">

                            <select name="status">
                                <option value="all">All Status</option>
                                <option value="Submitted" <?= ($_GET['status'] ?? '') === 'Submitted' ? 'selected' : '' ?>>Submitted</option>
                                <option value="Finance Review" <?= ($_GET['status'] ?? '') === 'Finance Review' ? 'selected' : '' ?>>Finance Review</option>
                                <option value="Payment Processing" <?= ($_GET['status'] ?? '') === 'Payment Processing' ? 'selected' : '' ?>>Payment Processing</option>
                                <option value="Paid" <?= ($_GET['status'] ?? '') === 'Paid' ? 'selected' : '' ?>>Paid</option>
                                <option value="Rejected" <?= ($_GET['status'] ?? '') === 'Rejected' ? 'selected' : '' ?>>Rejected</option>
                            </select>

                            <input type="date" name="date_from" value="<?= $this->escape($_GET['date_from'] ?? '') ?>">
                            <input type="date" name="date_to" value="<?= $this->escape($_GET['date_to'] ?? '') ?>">

                            <input type="text" name="search" placeholder="Search Invoice/Supplier/DO" value="<?= $this->escape($_GET['search'] ?? '') ?>">

                            <button type="submit" class="btn btn-primary">Apply Filters</button>
                            <a href="/KTMEDOIS/Presentation/Public/index.php?action=invoice_pending" class="btn">Reset</a>
                        </form>
                    </div>

                    <!-- Invoice List -->
                    <?php if (empty($invoices)): ?>
                        <div class="alert alert-info">
                            <p>No invoices found matching the selected filters.</p>
                        </div>
                    <?php else: ?>
                        <div class="invoice-list">
                            <table>
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
                                                <a href="/KTMEDOIS/Presentation/Public/index.php?action=invoice_summary&id=<?= $inv['Invoice_id'] ?>" class="btn btn-sm btn-summary">Summary</a>
                                                <?php if (in_array($inv['Status'], ['Submitted', 'Finance Review'])): ?>
                                                <form method="post" action="/KTMEDOIS/Presentation/Public/index.php?action=invoice_review" style="display:inline-block;">
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
                                                <a href="/KTMEDOIS/Presentation/Public/index.php?action=invoice_pdf&id=<?= $inv['Invoice_id'] ?>" class="btn btn-sm btn-pdf">PDF</a>
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
                        <table>
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

                    <!-- Print Styles -->
                    <style media="print">
                        /* Hide everything except the aging report */
                        body * {
                            visibility: hidden;
                        }

                        #aging-report, #aging-report * {
                            visibility: visible;
                        }

                        #aging-report {
                            position: absolute;
                            left: 0;
                            top: 0;
                            width: 100%;
                            padding: 20px;
                            background: #fff;
                        }

                        #aging-report .report-actions {
                            display: none !important;
                        }

                        .topbar, .sidebar, .sidebar-overlay, .filter-container, .invoice-list, .action-buttons {
                            display: none !important;
                        }

                        .content-area {
                            margin: 0 !important;
                            padding: 0 !important;
                            width: 100% !important;
                        }

                        .aging-report table {
                            width: 100%;
                            border-collapse: collapse;
                            font-size: 12px;
                        }

                        .aging-report table thead th {
                            background: #003366 !important;
                            color: #fff !important;
                            padding: 8px 12px;
                            text-align: left;
                            -webkit-print-color-adjust: exact !important;
                            print-color-adjust: exact !important;
                        }

                        .aging-report table tbody td {
                            padding: 6px 12px;
                            border: 1px solid #ccc;
                        }

                        .aging-report .grand-total {
                            background: #f0f4f8 !important;
                            font-weight: bold;
                            -webkit-print-color-adjust: exact !important;
                            print-color-adjust: exact !important;
                        }

                        .aging-report h3 {
                            color: #003366;
                            font-size: 18px;
                            border-bottom: 2px solid #003366;
                            padding-bottom: 10px;
                            text-align: center;
                        }
                    </style>

                    <script>

                    function toggleSidebar() {
                        document.getElementById('sidebar').classList.toggle('open');
                        document.getElementById('sidebarOverlay').classList.toggle('active');
                    }
                    </script>
                    <?php endif; ?>

                <?php endif; ?>

            </div>
            <?php
            echo '</body></html>';
        }

        public function viewInvoiceDetails(int $invoiceId): void {
            foreach ($this->invoices as $invoice) {
                if ($invoice['Invoice_id'] == $invoiceId) {
                    $this->selectedInvoice = $invoice;
                    break;
                }
            }

            if (!$this->selectedInvoice) {
                echo 'Invoice not found.';
                return;
            }

            $pageTitle = 'Invoice Details';
            $activePage = 'invoice_claims';
            $invoice = $this->selectedInvoice;

            include __DIR__ . '/../SharedUI/topbar.php';
            include __DIR__ . '/../SharedUI/sidebarM3.php';
            ?>
            <div class="content-area">
                <h2>Invoice Details</h2>
                <div class="summary-box">
                    <p><strong>Invoice Number:</strong> <?= $this->escape($invoice['Invoice_num']) ?></p>
                    <p><strong>DO Number:</strong> <?= $this->escape($invoice['DO_number'] ?? 'N/A') ?></p>
                    <p><strong>Status:</strong> <?= $this->escape($invoice['Status']) ?></p>
                    <p><strong>Total:</strong> RM <?= number_format($invoice['Total'], 2) ?></p>
                </div>
                <a href="/KTMEDOIS/Presentation/Public/index.php?action=invoice_status" class="btn">← Back</a>
            </div>
            <script>
            function toggleSidebar() {
                document.getElementById('sidebar').classList.toggle('open');
                document.getElementById('sidebarOverlay').classList.toggle('active');
            }
            </script>
            <?php
            echo '</body></html>';
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