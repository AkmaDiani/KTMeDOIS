<?php
/**
 * Application_Layer/Backend_API/Controllers/DeliveryOrderController.php
 * Replaces: App\Http\Controllers\DeliveryOrderReviewController (Laravel)
 *
 * Eloquent → PDO mapping per method:
 *   DeliveryOrder::with(['supplier','staff'])->when()->orderByDesc()->get()
 *     → PDO SELECT with LEFT JOINs + WHERE + ORDER BY
 *
 *   DeliveryOrder::findOrFail($id)
 *     → PDO SELECT WHERE DO_id = ? + http 404 on miss
 *
 *   $deliveryOrder->save()
 *     → PDO UPDATE
 *
 *   AuditLog::record(...)   → audit_log() in lib/audit.php
 *   NotificationLog::create() → notify() in lib/audit.php
 *
 *   Pdf::loadView(...)->download()
 *     → require views/do/pdf.php  (browser print-to-PDF)
 */

// ── Shared helpers ────────────────────────────────────────────────────────────

/** Fetch a DO with its supplier + reviewer in one query. 404 if not found. */
function do_find(int $id): array {
    $stmt = db()->prepare(
        'SELECT d.*,
                s.Supplier_name, s.Contact_person, s.Vendor_Number,
                k.Username AS reviewer_name
         FROM `do` d
         LEFT JOIN supplier s ON s.Supplier_id = d.supplier_ID
         LEFT JOIN `ktm staff` k ON k.User_ID = d.Staff_id
         WHERE d.DO_id = :id
         LIMIT 1'
    );
    $stmt->execute([':id' => $id]);
    $row = $stmt->fetch();
    if (!$row) { http_response_code(404); die('<h1>404 — Delivery Order not found</h1>'); }
    return $row;
}

/** Fetch all items for a DO. */
function do_items(int $doId): array {
    $stmt = db()->prepare('SELECT * FROM item WHERE DO_id = :id ORDER BY item_no');
    $stmt->execute([':id' => $doId]);
    return $stmt->fetchAll();
}

/** Fetch invoices linked to a DO. */
function do_invoices(int $doId): array {
    $stmt = db()->prepare('SELECT * FROM invoice WHERE DO_id = :id ORDER BY Created_At');
    $stmt->execute([':id' => $doId]);
    return $stmt->fetchAll();
}

/** Active KTM Officers for the assign-reviewer dropdown. */
function do_officers(): array {
    $stmt = db()->query(
        "SELECT User_ID, Username FROM `ktm staff`
         WHERE Role = 'KTM Officer' AND Status = 'Active'
         ORDER BY Username"
    );
    return $stmt->fetchAll();
}

// ── Controllers ───────────────────────────────────────────────────────────────

/**
 * GET /delivery-orders
 * Replaces: DeliveryOrderReviewController::index()
 */
function do_index(): void {
    // Replaces: DeliveryOrder::with(['supplier','staff'])->when($status,...)->orderByDesc()->get()
    $activeStatus = $_GET['status'] ?? '';
    $params = [];

    $sql = 'SELECT d.*,
                   s.Supplier_name,
                   k.Username AS reviewer_name
            FROM `do` d
            LEFT JOIN supplier s ON s.Supplier_id = d.supplier_ID
            LEFT JOIN `ktm staff` k ON k.User_ID = d.Staff_id';

    if ($activeStatus) {
        $sql .= ' WHERE d.Status = :status';
        $params[':status'] = $activeStatus;
    }

    $sql .= ' ORDER BY d.created_date DESC';

    $stmt = db()->prepare($sql);
    $stmt->execute($params);
    $deliveryOrders = $stmt->fetchAll();

    // Replaces: array_merge(DeliveryOrder::STAGES, ['Rejected'])
    $statuses = ['Submitted', 'Under Review', 'Approved', 'Rejected'];

    require __DIR__ . '/../../../Presentation_Layer/Web_Interface/views/do/index.php';
}

/**
 * GET /delivery-orders/{id}
 * Replaces: DeliveryOrderReviewController::show()
 */
function do_show(int $id): void {
    $do       = do_find($id);
    $items    = do_items($id);
    $invoices = do_invoices($id);
    $officers = do_officers();

    require __DIR__ . '/../../../Presentation_Layer/Web_Interface/views/do/show.php';
}

/**
 * POST /delivery-orders/{id}/assign
 * Replaces: DeliveryOrderReviewController::assignReviewer()
 */
function do_assign_reviewer(int $id): void {
    $staffId = (int)($_POST['staff_id'] ?? 0);

    // Replaces: 'required|exists:ktm staff,User_ID'
    if (!$staffId) {
        flash('error', 'Please select a reviewer.');
        redirect("delivery-orders/$id");
    }
    $chk = db()->prepare("SELECT 1 FROM `ktm staff` WHERE User_ID = :id LIMIT 1");
    $chk->execute([':id' => $staffId]);
    if (!$chk->fetch()) {
        flash('error', 'Selected officer does not exist.');
        redirect("delivery-orders/$id");
    }

    $do = do_find($id);

    // Replaces: $deliveryOrder->Staff_id = ...; if(Status==='Submitted') Status='Under Review'; save()
    $newStatus = ($do['Status'] === 'Submitted') ? 'Under Review' : $do['Status'];
    $upd = db()->prepare(
        'UPDATE `do` SET Staff_id = :sid, Status = :status WHERE DO_id = :id'
    );
    $upd->execute([':sid' => $staffId, ':status' => $newStatus, ':id' => $id]);

    // Replaces: AuditLog::record(...)
    audit_log(
        "Assigned Delivery Order {$do['DO_number']} to reviewer (Staff #$staffId)",
        $do['DO_number']
    );

    // Replaces: NotificationLog::create([...])
    notify($staffId, null, 'System',
        "You have been assigned to review Delivery Order {$do['DO_number']}.");

    flash('success', 'Reviewer assigned successfully.');
    redirect("delivery-orders/$id");
}

/**
 * POST /delivery-orders/{id}/approve
 * Replaces: DeliveryOrderReviewController::approve()
 */
function do_approve(int $id): void {
    $do = do_find($id);

    // Replaces: $deliveryOrder->Status = 'Approved'; $deliveryOrder->Reason = '-'; save()
    $upd = db()->prepare(
        "UPDATE `do` SET Status = 'Approved', Reason = '-' WHERE DO_id = :id"
    );
    $upd->execute([':id' => $id]);

    audit_log("Approved Delivery Order {$do['DO_number']}", $do['DO_number']);

    notify(
        (int)$_SESSION['staff_id'],
        $do['supplier_ID'] ? (int)$do['supplier_ID'] : null,
        'Email',
        "Delivery Order {$do['DO_number']} has been approved."
    );

    flash('success', 'Delivery Order approved.');
    redirect("delivery-orders/$id");
}

/**
 * POST /delivery-orders/{id}/reject
 * Replaces: DeliveryOrderReviewController::reject()
 */
function do_reject(int $id): void {
    $reason = trim($_POST['reason'] ?? '');

    // Replaces: 'required|string|max:250'
    if ($reason === '') {
        flash('error', 'Rejection reason is required.');
        redirect("delivery-orders/$id");
    }
    if (mb_strlen($reason) > 250) {
        flash('error', 'Rejection reason may not exceed 250 characters.');
        redirect("delivery-orders/$id");
    }

    $do = do_find($id);

    // Replaces: $deliveryOrder->Status = 'Rejected'; $deliveryOrder->Reason = $reason; save()
    $upd = db()->prepare(
        "UPDATE `do` SET Status = 'Rejected', Reason = :reason WHERE DO_id = :id"
    );
    $upd->execute([':reason' => $reason, ':id' => $id]);

    audit_log(
        "Rejected Delivery Order {$do['DO_number']}: $reason",
        $do['DO_number']
    );

    notify(
        (int)$_SESSION['staff_id'],
        $do['supplier_ID'] ? (int)$do['supplier_ID'] : null,
        'Email',
        "Delivery Order {$do['DO_number']} has been rejected: $reason"
    );

    flash('success', 'Delivery Order rejected.');
    redirect("delivery-orders/$id");
}

/**
 * GET /delivery-orders/{id}/export
 * Replaces: DeliveryOrderReviewController::exportPdf()
 *
 * Laravel used barryvdh/laravel-dompdf to produce a binary PDF.
 * Plain PHP outputs the same HTML template; window.print() lets the user
 * save it as PDF from the browser without any extra library.
 */
function do_export_pdf(int $id): void {
    $do    = do_find($id);
    $items = do_items($id);
    require __DIR__ . '/../../../Presentation_Layer/Web_Interface/views/do/pdf.php';
}
