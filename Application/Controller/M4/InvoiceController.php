<?php
/**
 * Application_Layer/Backend_API/Controllers/InvoiceController.php
 * Replaces: App\Http\Controllers\InvoiceReviewController (Laravel)
 *
 * Same Eloquent → PDO pattern as DeliveryOrderController.
 * Key queries:
 *   Invoice::with(['supplier','deliveryOrder','staff'])->...
 *     → PDO SELECT with LEFT JOINs
 *   Invoice::findOrFail($id)
 *     → PDO SELECT + 404 on miss
 *   $invoice->save()
 *     → PDO UPDATE
 */

// ── Shared helpers ────────────────────────────────────────────────────────────

/** Fetch an invoice with supplier, linked DO, and handler name. 404 if missing. */
function inv_find(int $id): array {
    $stmt = db()->prepare(
        'SELECT i.*,
                s.Supplier_name,
                d.DO_number, d.DO_id AS linked_do_id,
                k.Username AS handler_name
         FROM invoice i
         LEFT JOIN supplier s ON s.Supplier_id = i.supplier_ID
         LEFT JOIN `do` d ON d.DO_id = i.DO_id
         LEFT JOIN `ktm staff` k ON k.User_ID = i.Staff_id
         WHERE i.Invoice_id = :id
         LIMIT 1'
    );
    $stmt->execute([':id' => $id]);
    $row = $stmt->fetch();
    if (!$row) { http_response_code(404); die('<h1>404 — Invoice not found</h1>'); }
    return $row;
}

/** Active Finance Officers for the forward-to-finance dropdown. */
function inv_finance_officers(): array {
    $stmt = db()->query(
        "SELECT User_ID, Username FROM `ktm staff`
         WHERE Role = 'Finance Officer' AND Status = 'Active'
         ORDER BY Username"
    );
    return $stmt->fetchAll();
}

// ── Controllers ───────────────────────────────────────────────────────────────

/**
 * GET /invoices
 * Replaces: InvoiceReviewController::index()
 */
function invoice_index(): void {
    $activeStatus = $_GET['status'] ?? '';
    $params = [];

    $sql = 'SELECT i.*,
                   s.Supplier_name,
                   d.DO_number,
                   k.Username AS handler_name
            FROM invoice i
            LEFT JOIN supplier s ON s.Supplier_id = i.supplier_ID
            LEFT JOIN `do` d ON d.DO_id = i.DO_id
            LEFT JOIN `ktm staff` k ON k.User_ID = i.Staff_id';

    if ($activeStatus) {
        $sql .= ' WHERE i.Status = :status';
        $params[':status'] = $activeStatus;
    }

    $sql .= ' ORDER BY i.Created_At DESC';

    $stmt = db()->prepare($sql);
    $stmt->execute($params);
    $invoices = $stmt->fetchAll();

    // Replaces: array_merge(Invoice::STAGES, ['Rejected'])
    $statuses = ['Submitted', 'Finance Review', 'Payment Processing', 'Paid', 'Rejected'];

    require __DIR__ . '/../../../Presentation_Layer/Web_Interface/views/invoice/index.php';
}

/**
 * GET /invoices/{id}
 * Replaces: InvoiceReviewController::show()
 */
function invoice_show(int $id): void {
    $inv            = inv_find($id);
    $financeOfficers = inv_finance_officers();
    require __DIR__ . '/../../../Presentation_Layer/Web_Interface/views/invoice/show.php';
}

/**
 * POST /invoices/{id}/forward
 * Replaces: InvoiceReviewController::forwardToFinance()
 */
function invoice_forward_to_finance(int $id): void {
    $staffId = (int)($_POST['staff_id'] ?? 0);

    // Replaces: 'required|exists:ktm staff,User_ID'
    if (!$staffId) {
        flash('error', 'Please select a Finance Officer.');
        redirect("invoices/$id");
    }
    $chk = db()->prepare("SELECT 1 FROM `ktm staff` WHERE User_ID = :id LIMIT 1");
    $chk->execute([':id' => $staffId]);
    if (!$chk->fetch()) {
        flash('error', 'Selected officer does not exist.');
        redirect("invoices/$id");
    }

    $inv = inv_find($id);

    // Replaces: $invoice->Staff_id = ...; $invoice->Status = 'Finance Review'; save()
    $upd = db()->prepare(
        "UPDATE invoice SET Staff_id = :sid, Status = 'Finance Review' WHERE Invoice_id = :id"
    );
    $upd->execute([':sid' => $staffId, ':id' => $id]);

    audit_log(
        "Forwarded Invoice {$inv['Invoice_num']} to Finance (Staff #$staffId)",
        $inv['Invoice_num']
    );

    notify($staffId, $inv['supplier_ID'] ? (int)$inv['supplier_ID'] : null, 'System',
        "Invoice {$inv['Invoice_num']} has been forwarded to you for finance review.");

    flash('success', 'Invoice forwarded to Finance.');
    redirect("invoices/$id");
}

/**
 * POST /invoices/{id}/status
 * Replaces: InvoiceReviewController::updateStatus()
 */
function invoice_update_status(int $id): void {
    $status = $_POST['status'] ?? '';

    // Replaces: 'required|in:Finance Review,Payment Processing,Paid'
    $allowed = ['Finance Review', 'Payment Processing', 'Paid'];
    if (!in_array($status, $allowed, true)) {
        flash('error', 'Invalid status value.');
        redirect("invoices/$id");
    }

    $inv = inv_find($id);

    $upd = db()->prepare('UPDATE invoice SET Status = :status WHERE Invoice_id = :id');
    $upd->execute([':status' => $status, ':id' => $id]);

    audit_log(
        "Updated Invoice {$inv['Invoice_num']} status to $status",
        $inv['Invoice_num']
    );

    notify(
        (int)$_SESSION['staff_id'],
        $inv['supplier_ID'] ? (int)$inv['supplier_ID'] : null,
        'Email',
        "Invoice {$inv['Invoice_num']} status changed to $status."
    );

    flash('success', 'Invoice status updated.');
    redirect("invoices/$id");
}

/**
 * POST /invoices/{id}/reject
 * Replaces: InvoiceReviewController::reject()
 */
function invoice_reject(int $id): void {
    $reason = trim($_POST['reason'] ?? '');

    if ($reason === '') {
        flash('error', 'Rejection reason is required.');
        redirect("invoices/$id");
    }
    if (mb_strlen($reason) > 250) {
        flash('error', 'Rejection reason may not exceed 250 characters.');
        redirect("invoices/$id");
    }

    $inv = inv_find($id);

    $upd = db()->prepare(
        "UPDATE invoice SET Status = 'Rejected', Reason = :reason WHERE Invoice_id = :id"
    );
    $upd->execute([':reason' => $reason, ':id' => $id]);

    audit_log(
        "Rejected Invoice {$inv['Invoice_num']}: $reason",
        $inv['Invoice_num']
    );

    notify(
        (int)$_SESSION['staff_id'],
        $inv['supplier_ID'] ? (int)$inv['supplier_ID'] : null,
        'Email',
        "Invoice {$inv['Invoice_num']} has been rejected: $reason"
    );

    flash('success', 'Invoice rejected.');
    redirect("invoices/$id");
}

/**
 * GET /invoices/{id}/export
 * Replaces: InvoiceReviewController::exportPdf()
 */
function invoice_export_pdf(int $id): void {
    $inv = inv_find($id);
    require __DIR__ . '/../../../Presentation_Layer/Web_Interface/views/invoice/pdf.php';
}
