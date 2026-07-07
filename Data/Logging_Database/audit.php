<?php
/**
 * Data_Layer/Logging_Database/audit.php
 * Replaces the AuditLog Eloquent model's static record() helper.
 * Usage:  audit_log("Approved DO DO-2026-0001", "DO-2026-0001");
 */

require_once __DIR__ . '/../Relational_Database/db.php';

function audit_log(string $action, string $affectedRecord): void {
    $userId = $_SESSION['staff_id'] ?? null;
    if (!$userId) return;

    $stmt = db()->prepare(
        'INSERT INTO auditlog (User_ID, Action, Affected_Record, Timestamp)
         VALUES (:uid, :action, :record, :ts)'
    );
    $stmt->execute([
        ':uid'    => $userId,
        ':action' => $action,
        ':record' => $affectedRecord,
        ':ts'     => now_str(),
    ]);
}

/**
 * Replaces NotificationLog::create().
 * $type = 'System' | 'Email'
 */
function notify(int $userId, ?int $supplierId, string $type, string $content): void {
    $stmt = db()->prepare(
        'INSERT INTO notification
            (User_ID, Supplier_id, Type, Content, Status, Creates_At)
         VALUES
            (:uid, :sid, :type, :content, \'Sent\', :ts)'
    );
    $stmt->execute([
        ':uid'     => $userId,
        ':sid'     => $supplierId,
        ':type'    => $type,
        ':content' => $content,
        ':ts'      => now_str(),
    ]);
}
