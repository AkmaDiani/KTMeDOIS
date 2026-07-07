<?php
require_once __DIR__ . '/../../bootstrap.php';

session_start();

$do_id = $_GET["id"] ?? null;
$type = $_GET["type"] ?? "do";

/*
 * TEMPORARY TESTING:
 * Later after login, make sure login sets:
 * $_SESSION["supplier_id"] = supplier Supplier_id;
 */
$supplier_id = $_SESSION['supplier_id'];

$service = new DOService($pdo);
$file = $service->getFile($do_id, $type, (int)$supplier_id);

if (!$file) {
    die("File not found or access denied.");
}

$finfo = new finfo(FILEINFO_MIME_TYPE);
$mimeType = $finfo->buffer($file);

if (!$mimeType) {
    $mimeType = "application/octet-stream";
}

$filename = ($type === "proof") ? "proof_file" : "delivery_order_file";

header("Content-Type: " . $mimeType);
header("Content-Disposition: inline; filename=\"" . $filename . "\"");
header("Content-Length: " . strlen($file));

echo $file;
exit;
