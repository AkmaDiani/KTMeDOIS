<?php
// Presentation/View/Module3/UploadInvoice.php

if (!class_exists('UploadInvoice')) {
class UploadInvoice {
    private bool $isEdit;
    private $vendor;
    private array $approvedDOs;
    private $editInvoice;
    private ?array $selectedDO;
    private array $invoiceItems;
    private array $errors;
    private array $successes;

    public function __construct(
        bool $isEdit = false,
        $vendor = null,
        array $approvedDOs = [],
        $editInvoice = null,
        ?array $selectedDO = null,
        array $invoiceItems = [],
        array $errors = [],
        array $successes = []
    ) {
        $this->isEdit = $isEdit;
        $this->vendor = $vendor;
        $this->approvedDOs = $approvedDOs;
        $this->editInvoice = $editInvoice;
        $this->selectedDO = $selectedDO;
        $this->invoiceItems = $invoiceItems;
        $this->errors = $errors;
        $this->successes = $successes;
    }

    public function displayForm(): void {
        $isEdit = $this->isEdit;
        $vendor = $this->vendor;
        $approvedDOs = $this->approvedDOs;
        $editInvoice = $this->editInvoice;
        $selectedDO = $this->selectedDO;
        $invoiceItems = $this->invoiceItems;
        $errors = $this->errors;
        $successes = $this->successes;

        $pageTitle = $isEdit ? 'Edit Invoice Draft' : 'Submit Invoice';
        $activePage = 'submit_invoice';

        include ROOT_PATH . '/Presentation/View/SharedUI/topbar.php';
        include ROOT_PATH . '/Presentation/View/SharedUI/sidebarM3.php';
        ?>
        <div class="content-area">

            <h2><?= $isEdit ? 'Edit Invoice Draft' : 'Invoice Creation / Editing' ?></h2>

            <?php foreach ($errors as $error): ?>
                <div class="alert alert-danger"><?= $this->escape($error) ?></div>
            <?php endforeach; ?>
            <?php foreach ($successes as $success): ?>
                <div class="alert alert-success"><?= $this->escape($success) ?></div>
            <?php endforeach; ?>

            <form id="invoiceForm" action="/KTMEDOIS/Presentation/Public/indexM3.php?action=invoice_submit_post" method="post" enctype="multipart/form-data">

                <fieldset>
                    <legend>Invoice Header</legend>
                    
                    <div>
                        <label>Billing Address:</label>
                        <span>Keretapi Tanah Melayu Berhad<br>KTMB Headquarters<br>Jalan Sultan Hishamuddin<br>50621 Kuala Lumpur</span>
                    </div>
                    
                    <div>
                        <label>Supplier Name:</label>
                        <span><?= $this->escape(is_array($vendor) ? ($vendor['Supplier_name'] ?? '') : '') ?></span>
                    </div>
                    
                    <div>
                        <label>Supplier Address:</label>
                        <span><?= $this->escape(is_array($vendor) ? ($vendor['Billing_address'] ?? '') : '') ?></span>
                    </div>
                    
                    <div>
                        <label>Customer Information:</label>
                        <?php if ($isEdit): ?>
                            <input type="hidden" name="do_id" value="<?= $editInvoice->DO_id ?>">
                            <span><?= $this->escape($selectedDO['DO_number'] ?? 'N/A') ?> - <?= $this->escape($selectedDO['PO_number'] ?? 'N/A') ?></span>
                            <div style="font-size:12px; color:#666;">DO cannot be changed for draft edit.</div>
                        <?php else: ?>
                            <select name="do_id" id="do_id" required onchange="fetchDODetails(this.value)">
                                <option value="">-- Select Delivery Order --</option>
                                <?php foreach ($approvedDOs as $do): ?>
                                    <option value="<?= $do['DO_id'] ?>">
                                        <?= $this->escape($do['DO_number']) ?> - <?= $this->escape($do['PO_number']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        <?php endif; ?>
                    </div>
                    
                    <div id="customer-details" style="<?= $isEdit ? 'display:block;' : 'display:none;' ?> margin-top:10px; padding:10px; background:#f8f9fa; border-radius:4px;">
                        <div><strong>Customer (KTM Staff):</strong> <span id="staff-name"><?= $isEdit ? $this->escape($selectedDO['staff_name'] ?? 'N/A') : 'N/A' ?></span></div>
                        <div><strong>Staff Email:</strong> <span id="staff-email"><?= $isEdit ? $this->escape($selectedDO['staff_email'] ?? 'N/A') : 'N/A' ?></span></div>
                        <div><strong>PO Number:</strong> <span id="po-number"><?= $isEdit ? $this->escape($selectedDO['PO_number'] ?? 'N/A') : 'N/A' ?></span></div>
                        <div><strong>DO Number:</strong> <span id="do-number"><?= $isEdit ? $this->escape($selectedDO['DO_number'] ?? 'N/A') : 'N/A' ?></span></div>
                        <?php if ($isEdit): ?>
                            <input type="hidden" name="edit_invoice_id" value="<?= $editInvoice->Invoice_id ?>">
                        <?php endif; ?>
                    </div>
                </fieldset>

                <fieldset>
                    <legend>Invoice Description</legend>
                    <div>
                        <label for="description">Description:</label>
                        <textarea name="description" id="description" rows="3" placeholder="Enter invoice description..." style="width:100%; max-width:500px; padding:8px; border:1px solid #ccc; border-radius:4px; font-size:13px;"><?= $isEdit ? $this->escape($editInvoice->Description ?? '') : '' ?></textarea>
                    </div>
                </fieldset>

                <fieldset>
                    <legend>Items</legend>
                    <table id="items-table">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>Description</th>
                                <th>Quantity</th>
                                <th>Unit Price (RM)</th>
                                <th>Subtotal (RM)</th>
                            </tr>
                        </thead>
                        <tbody id="items-body">
                            <?php if ($isEdit && !empty($invoiceItems)): ?>
                                <?php $counter = 1; foreach ($invoiceItems as $item): ?>
                                    <tr>
                                        <td><?= $counter++ ?></td>
                                        <td>
                                            <input type="hidden" name="items[<?= $counter-1 ?>][description]" value="<?= $this->escape($item['item_description']) ?>">
                                            <?= $this->escape($item['item_description']) ?>
                                        </td>
                                        <td>
                                            <input type="hidden" name="items[<?= $counter-1 ?>][quantity]" value="<?= $item['quantity'] ?>">
                                            <?= $item['quantity'] ?>
                                        </td>
                                        <td>
                                            <input type="hidden" name="items[<?= $counter-1 ?>][unit_price]" value="<?= $item['unit_price'] ?>">
                                            <?= number_format($item['unit_price'], 2) ?>
                                        </td>
                                        <td class="subtotal"><?= number_format($item['quantity'] * $item['unit_price'], 2) ?></td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr id="no-items-row">
                                    <td colspan="5" style="text-align:center; color:#999;">
                                        Please select a Delivery Order to load items
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </fieldset>

                <fieldset>
                    <legend>Total Amount Calculator</legend>
                    
                    <div>
                        <label>Subtotal:</label>
                        <span id="display-subtotal"><?= $isEdit ? number_format($editInvoice->Subtotal, 2) : '0.00' ?></span>
                        <input type="hidden" name="subtotal" id="subtotal" value="<?= $isEdit ? $editInvoice->Subtotal : 0 ?>">
                    </div>
                    
                    <div>
                        <label>Tax (6%):</label>
                        <span id="display-tax"><?= $isEdit ? number_format($editInvoice->Tax, 2) : '0.00' ?></span>
                        <input type="hidden" name="tax" id="tax" value="<?= $isEdit ? $editInvoice->Tax : 0 ?>">
                    </div>
                    
                    <div>
                        <label for="discount">Discount (Credit Note):</label>
                        <input type="number" step="0.01" name="discount" id="discount" value="<?= $isEdit ? $editInvoice->discount : 0 ?>" onchange="updateTotals()">
                        <small>Enter credit note amount if applicable</small>
                    </div>
                    
                    <div>
                        <label>Penalty:</label>
                        <span id="display-penalty">0.00</span>
                        <input type="hidden" name="penalty" id="penalty" value="0">
                        <small>Will be added by officer if delivery is late</small>
                    </div>
                    
                    <div>
                        <label>Total:</label>
                        <span id="display-total"><?= $isEdit ? number_format($editInvoice->Total, 2) : '0.00' ?></span>
                        <input type="hidden" name="total" id="total" value="<?= $isEdit ? $editInvoice->Total : 0 ?>">
                    </div>
                </fieldset>

                <!-- Proof of Delivery Upload -->
                <fieldset>
                    <legend>Proof of Delivery</legend>
                    <div>
                        <label for="proof_link">Upload Proof of Delivery:</label>
                        <input type="file" name="proof_link" id="proof_link" accept=".pdf,.jpg,.jpeg,.png,.gif">
                        <small>Upload delivery slip or acknowledgement receipt (PDF, JPG, PNG, GIF - max 5MB)</small>
                    </div>
                </fieldset>

                <fieldset>
                    <legend>Declaration</legend>
                    <div>
                        <input type="checkbox" name="declaration" id="declaration" required>
                        <label for="declaration" style="width:auto;">I confirm that the information provided is accurate and related to the approved Delivery Order (DO).</label>
                    </div>
                </fieldset>

                <div>
                    <?php if ($isEdit): ?>
                        <button type="submit" name="action" value="draft" class="btn">Update Draft</button>
                        <button type="submit" name="action" value="submit" class="btn btn-primary">Submit Draft</button>
                    <?php else: ?>
                        <button type="submit" name="action" value="draft" class="btn">Save as Draft</button>
                        <button type="submit" name="action" value="submit" class="btn btn-primary">Submit</button>
                    <?php endif; ?>
                    <button type="button" class="btn" onclick="previewInvoice()">Preview PDF</button>
                </div>
            </form>

        </div>

        <script>
        // Fetch DO Details when selected
        function fetchDODetails(doId) {
            if (!doId) {
                document.getElementById('customer-details').style.display = 'none';
                document.getElementById('items-body').innerHTML = `
                    <tr id="no-items-row">
                        <td colspan="5" style="text-align:center; color:#999;">
                            Please select a Delivery Order to load items
                        </td>
                    </tr>
                `;
                document.getElementById('display-subtotal').innerText = '0.00';
                document.getElementById('display-tax').innerText = '0.00';
                document.getElementById('display-total').innerText = '0.00';
                document.getElementById('subtotal').value = 0;
                return;
            }
            
            fetch('/KTMEDOIS/Presentation/Public/index.php?action=get_do_details&do_id=' + doId)
                .then(response => response.json())
                .then(data => {
                    if (data.error) {
                        alert(data.error);
                        return;
                    }
                    
                    document.getElementById('customer-details').style.display = 'block';
                    document.getElementById('staff-name').innerText = data.staff_name || 'N/A';
                    document.getElementById('staff-email').innerText = data.staff_email || 'N/A';
                    document.getElementById('po-number').innerText = data.po_number || 'N/A';
                    document.getElementById('do-number').innerText = data.do_number || 'N/A';
                    
                    renderItems(data.items || []);
                    
                    if (data.subtotal) {
                        var rawSubtotal = parseFloat(data.subtotal.toString().replace(/,/g, ''));
                        document.getElementById('display-subtotal').innerText = data.subtotal;
                        document.getElementById('subtotal').value = rawSubtotal;
                    }
                    
                    updateTotals();
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Failed to load DO details');
                });
        }

        // Render items in table
        function renderItems(items) {
            var tbody = document.getElementById('items-body');
            tbody.innerHTML = '';
            
            if (items.length === 0) {
                tbody.innerHTML = `
                    <tr id="no-items-row">
                        <td colspan="5" style="text-align:center; color:#999;">
                            No items found for this Delivery Order
                        </td>
                    </tr>
                `;
                return;
            }
            
            var html = '';
            items.forEach(function(item, index) {
                var subtotal = item.quantity * item.unit_price;
                html += `
                    <tr>
                        <td>${index + 1}</td>
                        <td>
                            <input type="hidden" name="items[${index}][description]" value="${item.item_description}">
                            ${item.item_description}
                        </td>
                        <td>
                            <input type="hidden" name="items[${index}][quantity]" value="${item.quantity}">
                            ${item.quantity}
                        </td>
                        <td>
                            <input type="hidden" name="items[${index}][unit_price]" value="${item.unit_price}">
                            ${parseFloat(item.unit_price).toFixed(2)}
                        </td>
                        <td class="subtotal">${subtotal.toFixed(2)}</td>
                    </tr>
                `;
            });
            tbody.innerHTML = html;
        }

        // Update totals
        function updateTotals() {
            var subtotal = parseFloat(document.getElementById('subtotal').value) || 0;
            var tax = subtotal * 0.06;
            var discount = parseFloat(document.getElementById('discount').value) || 0;
            var penalty = 0;
            var total = subtotal + tax - discount + penalty;
            
            document.getElementById('display-tax').innerText = tax.toFixed(2);
            document.getElementById('display-total').innerText = total.toFixed(2);
            document.getElementById('display-penalty').innerText = '0.00';
            document.getElementById('tax').value = tax.toFixed(2);
            document.getElementById('total').value = total.toFixed(2);
        }

        // Preview PDF
        function previewInvoice() {
            var form = document.getElementById('invoiceForm');
            form.action = '/KTMEDOIS/Presentation/Public/index.php?action=invoice_preview';
            form.target = '_blank';
            form.submit();
            form.action = '/KTMEDOIS/Presentation/Public/index.php?action=invoice_submit_post';
            form.target = '';
        }

        document.addEventListener('DOMContentLoaded', function() {
            updateTotals();
        });
        </script>
        <?php
        echo '</body></html>';
    }

    private function escape($value): string {
        return htmlspecialchars($value, ENT_QUOTES, 'UTF-8');
    }

    public function render(): void {
        $this->displayForm();
    }
}
}