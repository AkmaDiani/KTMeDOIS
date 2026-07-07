<?php
require_once __DIR__ . '/../../bootstrap.php';

session_start();

// Get database connection
$pdo = Database::getInstance()->getConnection();

// Check if user is logged in and is a vendor
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Vendor') {
    header('Location: /KTMEDOIS/Presentation/Public/index.php?action=login');
    exit;
}

$supplier_id = $_SESSION['vendor_id'] ?? null;

if (!$supplier_id) {
    die('Supplier ID not found. Please login again.');
}

$service = new DOService($pdo);
$supplier = $service->getSupplierById($supplier_id);

if (!$supplier) {
    die("Supplier not found.");
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Submit Delivery Order</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: #f5f7fb;
            margin: 0;
            padding: 30px;
        }

        .container {
            max-width: 1050px;
            margin: auto;
        }

        h1 {
            margin-bottom: 5px;
        }

        .subtitle {
            color: #666;
            margin-bottom: 25px;
        }

        .card {
            background: white;
            padding: 25px;
            margin-bottom: 18px;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
        }

        .section-title {
            color: #0066cc;
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 15px;
        }

        label {
            font-weight: bold;
            display: block;
            margin-bottom: 6px;
        }

        input,
        textarea {
            width: 100%;
            padding: 11px;
            border: 1px solid #ccc;
            border-radius: 7px;
            box-sizing: border-box;
        }

        .grid-3 {
            display: grid;
            grid-template-columns: 1fr 1fr 1fr;
            gap: 18px;
        }

        .upload-box {
            border: 2px dashed #b8c4d6;
            padding: 25px;
            text-align: center;
            border-radius: 10px;
            background: #fafcff;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th {
            background: #f1f4f9;
            text-align: left;
            padding: 10px;
        }

        td {
            padding: 8px;
        }

        .btn {
            padding: 12px 20px;
            border-radius: 7px;
            border: none;
            cursor: pointer;
            font-weight: bold;
            text-decoration: none;
            display: inline-block;
        }

        .btn-add {
            background: white;
            color: #0066cc;
            border: 1px solid #0066cc;
            margin-top: 12px;
        }

        .btn-submit {
            background: #0066cc;
            color: white;
        }

        .btn-draft {
            background: white;
            color: #0066cc;
            border: 1px solid #0066cc;
        }

        .btn-clear {
            background: #dc3545;
            color: white;
        }

        .actions {
            text-align: right;
            margin-top: 20px;
        }

        .remove-btn {
            background: #ffeded;
            color: red;
            border: none;
            padding: 8px 10px;
            border-radius: 6px;
            cursor: pointer;
        }

        .required {
            color: red;
        }

        #draftPreview {
            background: #f1f7ff;
            padding: 15px;
            border-radius: 8px;
            margin-top: 15px;
            display: none;
        }
    </style>
</head>

<body>

    <?php 
    include __DIR__ . '/../SharedUI/topbar.php';
    include __DIR__ . '/../SharedUI/sidebarM2.php'; ?>

    <div class="content"></div>

    <div class="container">
        <h1>Submit Delivery Order (DO)</h1>
        <p class="subtitle">Please complete the form below and upload supporting documents.</p>

        <form action="/KTMEDOIS/Application/Middleware/API_gateways/api_DO.php" method="POST" enctype="multipart/form-data">

            <input type="hidden" name="staff_id" value="<?= htmlspecialchars($_SESSION['staff_id'] ?? '') ?>">

            <div class="card">
                <div class="section-title">1. Upload Delivery Order Document</div>

                <div class="upload-box">
                    <label>Delivery Order File <span class="required">*</span></label>
                    <input type="file" name="do_file" accept=".pdf,.jpg,.jpeg,.png" required>
                    <p>Accepted file types: PDF, JPG, JPEG, PNG. Max 10MB.</p>
                </div>
            </div>

            <div class="card">
                <div class="section-title">2. Delivery Order Information</div>

                <div class="grid-3">
                    <div>
                        <label>Supplier</label>
                        <input type="text" value="<?= htmlspecialchars($supplier['Supplier_name']) ?>" readonly>
                    </div>

                    <div>
                        <label>PO Number <span class="required">*</span></label>
                        <input type="text" name="po_number" placeholder="PO-2026-001" required>
                    </div>

                    <div>
                        <label>Status</label>
                        <input type="text" value="Submitted after final submit" readonly>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="section-title">3. Item Details</div>

                <table id="itemTable">
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
                            <td><input type="number" name="item_no[]" required></td>
                            <td><input type="text" name="item_description[]" required></td>
                            <td><input type="number" name="quantity[]" min="1" required></td>
                            <td>
                                <button type="button" class="remove-btn" onclick="removeRow(this)">Delete</button>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <button type="button" class="btn btn-add" onclick="addRow()">+ Add Item</button>
            </div>

            <div class="card">
                <div class="section-title">4. Proof of Delivery</div>

                <div class="upload-box">
                    <label>Proof File <span class="required">*</span></label>
                    <input type="file" name="proof_file" accept=".pdf,.jpg,.jpeg,.png" required>
                    <p>Upload signed Delivery Order, delivery slip, acknowledgement receipt, or receiver signature.</p>
                </div>
            </div>

            <div class="card">
                <div class="section-title">5. Review & Submit</div>
                <p>Please review all information before submitting.</p>

                <div id="draftPreview">
                    <h4>Saved Draft Preview</h4>
                    <div id="previewContent"></div>
                </div>

                <div class="actions">
                    <button type="button" onclick="saveDraft()" class="btn btn-draft">Save as Draft</button>
                    <button type="button" onclick="clearForm()" class="btn btn-clear">Clear Form</button>
                    <button type="submit" name="action" value="submit" class="btn btn-submit">Submit Delivery Order</button>
                </div>
            </div>

        </form>
    </div>

    <script>
        function addRow() {
            const table = document.querySelector("#itemTable tbody");

            table.insertAdjacentHTML("beforeend", `
        <tr>
            <td><input type="number" name="item_no[]" required></td>
            <td><input type="text" name="item_description[]" required></td>
            <td><input type="number" name="quantity[]" min="1" required></td>
            <td><button type="button" class="remove-btn" onclick="removeRow(this)">Delete</button></td>
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
                supplier_name: "<?= htmlspecialchars($supplier['Supplier_name']) ?>",
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

        <table border="1" width="100%" cellpadding="8" cellspacing="0">
            <tr>
                <th>Item No</th>
                <th>Description</th>
                <th>Quantity</th>
            </tr>
            ${items}
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
                    <td><input type="number" name="item_no[]" value="${escapeAttr(data.item_no[i])}" required></td>
                    <td><input type="text" name="item_description[]" value="${escapeAttr(data.item_description[i])}" required></td>
                    <td><input type="number" name="quantity[]" value="${escapeAttr(data.quantity[i])}" min="1" required></td>
                    <td><button type="button" class="remove-btn" onclick="removeRow(this)">Delete</button></td>
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
                <td><input type="number" name="item_no[]" required></td>
                <td><input type="text" name="item_description[]" required></td>
                <td><input type="number" name="quantity[]" min="1" required></td>
                <td><button type="button" class="remove-btn" onclick="removeRow(this)">Delete</button></td>
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

    </div>

</body>

</html>