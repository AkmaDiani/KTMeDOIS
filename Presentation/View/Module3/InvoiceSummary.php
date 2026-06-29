<?php
// Presentation/View/Module3/InvoiceSummary.php

if (!class_exists('InvoiceSummary')) {
    class InvoiceSummary {
        private $invoice;
        private array $items;
        private ?array $do;
        private ?array $supplier;
        private $staff;
        private ?string $proofLink;
        private string $role;

        public function __construct($invoice, array $items = [], ?array $do = null, ?array $supplier = null, $staff = null, ?string $proofLink = null, string $role = 'Vendor') {
            $this->invoice = $invoice;
            $this->items = $items;
            $this->do = $do;
            $this->supplier = $supplier;
            $this->staff = $staff;
            $this->proofLink = $proofLink;
            $this->role = $role;
        }

        public function displaySummary(): void {
            $isVendorView = ($this->role === 'Vendor');
            $backUrl = $isVendorView 
                ? '/KTMEDOIS/Presentation/Public/index.php?action=invoice_status' 
                : '/KTMEDOIS/Presentation/Public/index.php?action=invoice_pending';

            $pageTitle = 'Invoice Summary';
            $activePage = 'invoice_claims';
            $invoice = $this->invoice;
            $items = $this->items;
            $do = $this->do;
            $supplier = $this->supplier;
            $staff = $this->staff;
            $proofLink = $this->proofLink;

            include __DIR__ . '/../SharedUI/topbar.php';
            include __DIR__ . '/../SharedUI/sidebarM3.php';
            ?>
            <div class="content-area">
                <div class="summary-container">

                    <h2>Invoice Summary</h2>

                    <!-- Invoice Information -->
                    <div class="summary-box">
                        <h3>Invoice Information</h3>
                        <div class="summary-body">
                            <table class="summary-table">
                                <tr>
                                    <td class="label">Invoice Number:</td>
                                    <td class="value"><?= $this->escape($invoice->Invoice_num) ?></td>
                                </tr>
                                <tr>
                                    <td class="label">Invoice Date:</td>
                                    <td class="value"><?= $this->escape($invoice->issue_date) ?></td>
                                </tr>
                                <tr>
                                    <td class="label">Status:</td>
                                    <td class="value">
                                        <span class="status-badge <?= strtolower(str_replace(' ', '-', $invoice->Status)) ?>">
                                            <?= $this->escape($invoice->Status) ?>
                                        </span>
                                    </td>
                                </tr>
                                <tr>
                                    <td class="label">Description:</td>
                                    <td class="value"><?= $this->escape($invoice->Description ?? '') ?></td>
                                </tr>
                                <?php if ($do): ?>
                                <tr>
                                    <td class="label">DO Number:</td>
                                    <td class="value"><?= $this->escape($do['DO_number']) ?></td>
                                </tr>
                                <tr>
                                    <td class="label">PO Number:</td>
                                    <td class="value"><?= $this->escape($do['PO_number']) ?></td>
                                </tr>
                                <tr>
                                    <td class="label">Customer (KTM Staff):</td>
                                    <td class="value"><?php $staffName = $do['staff_name'] ?? $do['Username'] ?? 'N/A';echo $this->escape($staffName); ?></td>
                                </tr>
                                <?php endif; ?>
                            </table>
                        </div>
                    </div>

                    <!-- Supplier Information -->
                    <div class="summary-box">
                        <h3>Supplier Information</h3>
                        <div class="summary-body">
                            <table class="summary-table">
                                <tr>
                                    <td class="label">Supplier:</td>
                                    <td class="value"><?= $this->escape($supplier['Supplier_name'] ?? 'N/A') ?></td>
                                </tr>
                                <tr>
                                    <td class="label">Address:</td>
                                    <td class="value"><?= nl2br($this->escape($supplier['Billing_address'] ?? 'N/A')) ?></td>
                                </tr>
                                <tr>
                                    <td class="label">Vendor Number:</td>
                                    <td class="value"><?= $this->escape($supplier['Vendor_Number'] ?? 'N/A') ?></td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- Items -->
                    <div class="summary-box">
                        <h3>Items</h3>
                        <div class="summary-body">
                            <table class="summary-items-table">
                                <thead>
                                    <tr>
                                        <th>No.</th>
                                        <th>Description</th>
                                        <th class="text-right">Quantity</th>
                                        <th class="text-right">Unit Price (RM)</th>
                                        <th class="text-right">Subtotal (RM)</th>
                                    </tr>
                                </thead>
                                <tbody>
                                <?php $counter = 1; ?>
                                <?php foreach ($items as $item): ?>
                                    <?php 
                                        $desc = $item['item_description'] ?? $item['description'] ?? '';
                                        $subtotal = $item['quantity'] * $item['unit_price'];
                                    ?>
                                    <tr>
                                        <td><?= $counter++ ?></td>
                                        <td><?= $this->escape($desc) ?></td>
                                        <td class="text-right"><?= $item['quantity'] ?></td>
                                        <td class="text-right"><?= number_format($item['unit_price'], 2) ?></td>
                                        <td class="text-right"><?= number_format($subtotal, 2) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Totals -->
                    <div class="summary-box">
                        <h3>Totals</h3>
                        <div class="summary-body">
                            <table class="summary-totals">
                                <tr>
                                    <td class="label">Subtotal:</td>
                                    <td class="value">RM <?= number_format($invoice->Subtotal, 2) ?></td>
                                </tr>
                                <tr>
                                    <td class="label">Tax (6%):</td>
                                    <td class="value">RM <?= number_format($invoice->Tax, 2) ?></td>
                                </tr>
                                <?php if ($invoice->discount > 0): ?>
                                <tr>
                                    <td class="label discount">Discount:</td>
                                    <td class="value discount">- RM <?= number_format($invoice->discount, 2) ?></td>
                                </tr>
                                <?php endif; ?>
                                <?php if ($invoice->penalty > 0): ?>
                                <tr>
                                    <td class="label penalty">Penalty:</td>
                                    <td class="value penalty">+ RM <?= number_format($invoice->penalty, 2) ?></td>
                                </tr>
                                <?php endif; ?>
                                <tr class="total-row">
                                    <td class="label">Total:</td>
                                    <td class="value">RM <?= number_format($invoice->Total, 2) ?></td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <!-- Proof of Delivery -->
                    <?php if ($proofLink): ?>
                    <div class="summary-box">
                        <h3>Proof of Delivery</h3>
                        <div class="summary-body">
                            <a href="/KTMEDOIS/Data/<?= $proofLink ?>" target="_blank" class="proof-link">View Proof of Delivery</a>
                        </div>
                    </div>
                    <?php endif; ?>

                    <div class="summary-actions">
                        <a href="<?= $backUrl ?>" class="btn">← Back</a>
                        <a href="/KTMEDOIS/Presentation/Public/index.php?action=invoice_pdf&id=<?= $invoice->Invoice_id ?>" class="btn btn-primary">Download PDF</a>
                    </div>

                </div>
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

        private function escape($value): string {
            return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
        }

        public function render(): void {
            $this->displaySummary();
        }
    }
}