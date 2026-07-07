<?php
require_once __DIR__ . '/../../../bootstrap.php';

if (!isset($_SESSION['supplier_id']) || $_SESSION['user_type'] !== 'supplier') {
    header('Location: /KTMeDOIS/Presentation/Public/indexM1.php?controller=auth&action=login');
    exit;
}

$do_id = $_GET['id'] ?? null;
$type = $_GET['type'] ?? 'do';
if (!$do_id) {
    die('Invalid file request.');
}

$supplier_id = (int)$_SESSION['supplier_id'];

$pdo = Database::getInstance()->getConnection();

$service = new DOService($pdo);
$file = $service->getFile($do_id, $type, $supplier_id);

if (!$file) {
    die('File not found or access denied.');
}

// Determine MIME type
$finfo = new finfo(FILEINFO_MIME_TYPE);
$mimeType = $finfo->buffer($file);
if (!$mimeType) {
    $mimeType = 'application/octet-stream';
}

$filename = ($type === 'proof') ? 'proof_file' : 'delivery_order_file';
header('Content-Type: ' . $mimeType);
header('Content-Disposition: inline; filename="' . $filename . '"');
header('Content-Length: ' . strlen($file));

echo $file;
exit;