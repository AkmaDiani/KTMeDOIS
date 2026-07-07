<?php
/**
 * Application_Layer/Backend_API/Controllers/AuditLogController.php
 * Replaces: App\Http\Controllers\AuditLogController (Laravel)
 *
 * Eloquent → PDO:
 *   AuditLog::with('staff')->orderByDesc('Timestamp')->paginate(20)
 *     → paginate_query() helper with LEFT JOIN
 *   $logs->count()  → count($logs)
 */

/**
 * GET /audit-log
 * Replaces: AuditLogController::index()
 */
function auditlog_index(): void {
    $page = max(1, (int)($_GET['page'] ?? 1));

    // Replaces: AuditLog::with('staff')->orderByDesc('Timestamp')->paginate(20)
    $paginator = paginate_query(
        'SELECT a.*, k.Username AS staff_name
         FROM auditlog a
         LEFT JOIN `ktm staff` k ON k.User_ID = a.User_ID
         ORDER BY a.Timestamp DESC',
        [],
        $page,
        20
    );

    $logs = $paginator['rows'];
    require __DIR__ . '/../../../Presentation_Layer/Web_Interface/views/auditlog/index.php';
}

/**
 * GET /audit-log/export
 * Replaces: AuditLogController::exportPdf()
 *
 * Laravel produced a binary PDF download with dompdf.
 * Plain PHP outputs the same HTML; the user prints to PDF from the browser.
 */
function auditlog_export_pdf(): void {
    // Replaces: AuditLog::with('staff')->orderByDesc('Timestamp')->get()
    $stmt = db()->query(
        'SELECT a.*, k.Username AS staff_name
         FROM auditlog a
         LEFT JOIN `ktm staff` k ON k.User_ID = a.User_ID
         ORDER BY a.Timestamp DESC'
    );
    $logs = $stmt->fetchAll();
    require __DIR__ . '/../../../Presentation_Layer/Web_Interface/views/auditlog/pdf.php';
}
