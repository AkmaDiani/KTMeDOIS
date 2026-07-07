<?php
require_once __DIR__ . '/../../../bootstrap.php';

// Check supplier login
if (!isset($_SESSION['supplier_id']) || $_SESSION['user_type'] !== 'supplier') {
    header('Location: /KTMeDOIS/Presentation/Public/indexM1.php?controller=auth&action=login');
    exit;
}

$supplier_id = (int)$_SESSION['supplier_id'];

$pdo = Database::getInstance()->getConnection();


$service = new DOService($pdo);
$supplier = $service->getSupplierById($supplier_id);
if (!$supplier) {
    die('Supplier not found.');
}

$title = 'Submit Delivery Order - KTM eDOIS';
$activePage = 'manage_do';

include ROOT_PATH . '/Presentation/View/SharedUI/topbar.php';
include ROOT_PATH . '/Presentation/View/SharedUI/sidebarM2.php';
?>

<div class="content">
    <div class="container-fluid">
        <h1>Submit Delivery Order (DO)</h1>
        <p class="text-muted">Please complete the form below and upload supporting documents.</p>

        <form action="/KTMeDOIS/Application/Middleware/API_gateways/api_DO.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="supplier_id" value="<?= htmlspecialchars($supplier_id) ?>">

            <div class="card-ktm">
                <div class="card-header"><span class="header-icon"><i class="fas fa-file-upload"></i></span> 1. Upload Delivery Order Document</div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="do_file">Delivery Order File <span class="text-danger">*</span></label>
                        <input type="file" name="do_file" id="do_file" accept=".pdf,.jpg,.jpeg,.png" class="form-control" required>
                        <small class="text-muted">Accepted file types: PDF, JPG, JPEG, PNG. Max 10MB.</small>
                    </div>
                </div>
            </div>

            <div class="card-ktm">
                <div class="card-header"><span class="header-icon"><i class="fas fa-info-circle"></i></span> 2. Delivery Order Information</div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Supplier</label>
                                <input type="text" class="form-control" value="<?= htmlspecialchars($supplier['Supplier_name'] ?? '') ?>" readonly>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="po_number">PO Number <span class="text-danger">*</span></label>
                                <input type="text" name="po_number" id="po_number" class="form-control" placeholder="PO-2026-001" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Status</label>
                                <input type="text" class="form-control" value="Submitted after final submit" readonly>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-ktm">
                <div class="card-header"><span class="header-icon"><i class="fas fa-boxes"></i></span> 3. Item Details</div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table id="itemTable" class="table">
                            <thead>
                                <tr>
                                    <th>Item No.</th>
                                    <th>Description</th>
                                    <th>Quantity</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                    <td><input type="number" name="item_no[]" class="form-control" required></td>
                                    <td><input type="text" name="item_description[]" class="form-control" required></td>
                                    <td><input type="number" name="quantity[]" class="form-control" min="1" required></td>
                                    <td>
                                        <button type="button" class="btn btn-danger btn-sm" onclick="removeRow(this)">Delete</button>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <button type="button" class="btn btn-outline" onclick="addRow()">+ Add Item</button>
                </div>
            </div>

            <div class="card-ktm">
                <div class="card-header"><span class="header-icon"><i class="fas fa-check-circle"></i></span> 4. Proof of Delivery</div>
                <div class="card-body">
                    <div class="form-group">
                        <label for="proof_file">Proof File <span class="text-danger">*</span></label>
                        <input type="file" name="proof_file" id="proof_file" accept=".pdf,.jpg,.jpeg,.png" class="form-control" required>
                        <small class="text-muted">Upload signed Delivery Order, delivery slip, acknowledgement receipt, or receiver signature.</small>
                    </div>
                </div>
            </div>

            <div class="card-ktm">
                <div class="card-header"><span class="header-icon"><i class="fas fa-edit"></i></span> 5. Review & Submit</div>
                <div class="card-body">
                    <p>Please review all information before submitting.</p>
                    <div id="draftPreview" style="display: none;">
                        <h4>Saved Draft Preview</h4>
                        <div id="previewContent"></div>
                    </div>
                    <div class="action-buttons">
                        <button type="button" onclick="saveDraft()" class="btn btn-outline">Save as Draft</button>
                        <button type="button" onclick="clearForm()" class="btn btn-danger">Clear Form</button>
                        <button type="submit" name="action" value="submit" class="btn btn-success">Submit Delivery Order</button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    function addRow() {
        const table = document.querySelector("#itemTable tbody");
        table.insertAdjacentHTML("beforeend", `
            <tr>
                <td><input type="number" name="item_no[]" class="form-control" required></td>
                <td><input type="text" name="item_description[]" class="form-control" required></td>
                <td><input type="number" name="quantity[]" class="form-control" min="1" required></td>
                <td><button type="button" class="btn btn-danger btn-sm" onclick="removeRow(this)">Delete</button></td>
            </tr>
        `);
    }

    function removeRow(button) {
        const rows = document.querySelectorAll("#itemTable tbody tr");
        if (rows.length > 1) {
            button.closest("tr").remove();
        } else {
            alert("At least one item is required.");
        }
    }

    function saveDraft() {
        const form = document.querySelector("form");
        const data = {
            supplier_name: "<?= htmlspecialchars($supplier['Supplier_name'] ?? '') ?>",
            po_number: form.po_number.value,
            do_file_name: form.do_file.files[0] ? form.do_file.files[0].name : "",
            proof_file_name: form.proof_file.files[0] ? form.proof_file.files[0].name : "",
            item_no: Array.from(document.getElementsByName("item_no[]")).map(i => i.value),
            item_description: Array.from(document.getElementsByName("item_description[]")).map(i => i.value),
            quantity: Array.from(document.getElementsByName("quantity[]")).map(i => i.value)
        };
        localStorage.setItem("doDraft", JSON.stringify(data));
        showDraftPreview(data);
        alert("Draft saved on this page.");
    }

    function showDraftPreview(data) {
        let items = "";
        for (let i = 0; i < data.item_no.length; i++) {
            items += `
                <tr>
                    <td>${escapeHtml(data.item_no[i])}</td>
                    <td>${escapeHtml(data.item_description[i])}</td>
                    <td>${escapeHtml(data.quantity[i])}</td>
                </tr>
            `;
        }
        document.getElementById("draftPreview").style.display = "block";
        document.getElementById("previewContent").innerHTML = `
            <p><b>Supplier:</b> ${escapeHtml(data.supplier_name)}</p>
            <p><b>PO Number:</b> ${escapeHtml(data.po_number)}</p>
            <p><b>DO File:</b> ${escapeHtml(data.do_file_name || "Not selected")}</p>
            <p><b>Proof File:</b> ${escapeHtml(data.proof_file_name || "Not selected")}</p>
            <table class="table table-bordered">
                <thead><tr><th>Item No</th><th>Description</th><th>Quantity</th></tr></thead>
                <tbody>${items}</tbody>
            </table>
        `;
    }

    window.onload = function() {
        const savedDraft = localStorage.getItem("doDraft");
        if (savedDraft) {
            const data = JSON.parse(savedDraft);
            const form = document.querySelector("form");
            form.po_number.value = data.po_number || "";
            const table = document.querySelector("#itemTable tbody");
            table.innerHTML = "";
            for (let i = 0; i < data.item_no.length; i++) {
                table.insertAdjacentHTML("beforeend", `
                    <tr>
                        <td><input type="number" name="item_no[]" class="form-control" value="${escapeAttr(data.item_no[i])}" required></td>
                        <td><input type="text" name="item_description[]" class="form-control" value="${escapeAttr(data.item_description[i])}" required></td>
                        <td><input type="number" name="quantity[]" class="form-control" value="${escapeAttr(data.quantity[i])}" min="1" required></td>
                        <td><button type="button" class="btn btn-danger btn-sm" onclick="removeRow(this)">Delete</button></td>
                    </tr>
                `);
            }
            showDraftPreview(data);
        }
    };

    function clearForm() {
        if (confirm("Are you sure you want to clear all form data?")) {
            localStorage.removeItem("doDraft");
            document.querySelector("form").reset();
            document.getElementById("draftPreview").style.display = "none";
            document.getElementById("previewContent").innerHTML = "";
            document.querySelector("#itemTable tbody").innerHTML = `
                <tr>
                    <td><input type="number" name="item_no[]" class="form-control" required></td>
                    <td><input type="text" name="item_description[]" class="form-control" required></td>
                    <td><input type="number" name="quantity[]" class="form-control" min="1" required></td>
                    <td><button type="button" class="btn btn-danger btn-sm" onclick="removeRow(this)">Delete</button></td>
                </tr>
            `;
        }
    }

    function escapeHtml(value) {
        return String(value).replace(/[&<>"']/g, function(m) {
            return ({
                '&': '&amp;',
                '<': '&lt;',
                '>': '&gt;',
                '"': '&quot;',
                "'": '&#039;'
            })[m];
        });
    }

    function escapeAttr(value) {
        return escapeHtml(value).replace(/"/g, '&quot;');
    }
</script>

<?php include ROOT_PATH . '/Presentation/View/SharedUI/footer.php'; ?>