<?php
/**
 * Presentation_Layer/Web_Interface/public/index.php
 * ─────────────────────────────────────────────────────────────────────────────
 * Front controller + router.
 *
 * Folder structure (matches the 3-tier architecture slide):
 *
 *   Presentation_Layer/
 *     Web_Interface/
 *       public/        ← this file lives here (web root)
 *       views/
 *
 *   Application_Layer/
 *     Backend_API/
 *       Controllers/    ← DO, Invoice, AuditLog controllers
 *       Helpers/         ← helpers.php (redirect, e(), flash, etc.)
 *     Authentication_Service/
 *       LoginController.php
 *       StaffAuth.php    ← require_login() / require_role()
 *     Notification_Service/  (reserved for future notification-specific code)
 *
 *   Data_Layer/
 *     Relational_Database/
 *       db.php               ← PDO connection
 *       database_config.php  ← DB_HOST / DB_NAME / etc.
 *     Logging_Database/
 *       audit.php            ← audit_log() / notify()
 *     File_Storage/  (reserved for uploaded documents/images)
 *
 * URL scheme is unchanged from the previous version — only file locations moved.
 */

session_start();

// ── Path roots ────────────────────────────────────────────────────────────────
$webInterface = dirname(__DIR__);                       // .../Presentation_Layer/Web_Interface
$presentation = dirname($webInterface);                  // .../Presentation_Layer
$projectRoot  = dirname($presentation);                  // project root
$applicationLayer = $projectRoot . '/Application_Layer';
$dataLayer         = $projectRoot . '/Data_Layer';

// ── Bootstrap: Data Layer → Application Layer (load order matters) ───────────
require_once $dataLayer . '/Relational_Database/db.php';
require_once $dataLayer . '/Logging_Database/audit.php';
require_once $applicationLayer . '/Backend_API/Helpers/helpers.php';
require_once $applicationLayer . '/Authentication_Service/StaffAuth.php';

// ── Parse request ─────────────────────────────────────────────────────────────
$method = $_SERVER['REQUEST_METHOD'];

// Strip base path from REQUEST_URI so this works in a subdirectory.
$scriptDir = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
$requestUri = $_SERVER['REQUEST_URI'];
$path = parse_url($requestUri, PHP_URL_PATH);
if ($scriptDir !== '' && str_starts_with($path, $scriptDir)) {
    $path = substr($path, strlen($scriptDir));
}
$path = '/' . trim($path, '/');
if ($path === '') $path = '/';

// ── Controller loader ─────────────────────────────────────────────────────────
function load_controller(string $name): void {
    global $applicationLayer;

    // LoginController lives in Authentication_Service; the rest live in Backend_API/Controllers
    if ($name === 'LoginController') {
        require_once $applicationLayer . '/Authentication_Service/LoginController.php';
        return;
    }
    require_once $applicationLayer . '/Backend_API/Controllers/' . $name . '.php';
}

// ── Route matching ────────────────────────────────────────────────────────────
function match_route(string $pattern, string $path): array|false {
    $regex = preg_replace('/\{([a-z_]+)\}/', '(?P<$1>[^/]+)', $pattern);
    if (preg_match('#^' . $regex . '$#', $path, $m)) {
        return array_filter($m, 'is_string', ARRAY_FILTER_USE_KEY);
    }
    return false;
}

// ── Router ────────────────────────────────────────────────────────────────────

// ── Auth routes (no session guard) ───────────────────────────────────────────
if ($path === '/login') {
    load_controller('LoginController');
    if ($method === 'GET')  { login_show_form(); exit; }
    if ($method === 'POST') { csrf_verify(); login_process(); exit; }
}

if ($path === '/logout' && $method === 'POST') {
    csrf_verify();
    load_controller('LoginController');
    login_logout();
    exit;
}

// ── Root → redirect ───────────────────────────────────────────────────────────
if ($path === '/') {
    require_login();
    redirect('delivery-orders');
}

// ── Delivery Order routes ─────────────────────────────────────────────────────
if ($path === '/delivery-orders' && $method === 'GET') {
    require_login();
    load_controller('DeliveryOrderController');
    do_index();
    exit;
}

if (($p = match_route('/delivery-orders/{id}/export', $path)) !== false && $method === 'GET') {
    require_login();
    load_controller('DeliveryOrderController');
    do_export_pdf((int)$p['id']);
    exit;
}

if (($p = match_route('/delivery-orders/{id}/assign', $path)) !== false && $method === 'POST') {
    require_login();
    csrf_verify();
    load_controller('DeliveryOrderController');
    do_assign_reviewer((int)$p['id']);
    exit;
}

if (($p = match_route('/delivery-orders/{id}/approve', $path)) !== false && $method === 'POST') {
    require_login();
    csrf_verify();
    load_controller('DeliveryOrderController');
    do_approve((int)$p['id']);
    exit;
}

if (($p = match_route('/delivery-orders/{id}/reject', $path)) !== false && $method === 'POST') {
    require_login();
    csrf_verify();
    load_controller('DeliveryOrderController');
    do_reject((int)$p['id']);
    exit;
}

if (($p = match_route('/delivery-orders/{id}', $path)) !== false && $method === 'GET') {
    require_login();
    load_controller('DeliveryOrderController');
    do_show((int)$p['id']);
    exit;
}

// ── Invoice routes ────────────────────────────────────────────────────────────
if ($path === '/invoices' && $method === 'GET') {
    require_login();
    load_controller('InvoiceController');
    invoice_index();
    exit;
}

if (($p = match_route('/invoices/{id}/export', $path)) !== false && $method === 'GET') {
    require_login();
    load_controller('InvoiceController');
    invoice_export_pdf((int)$p['id']);
    exit;
}

if (($p = match_route('/invoices/{id}/forward', $path)) !== false && $method === 'POST') {
    require_login();
    csrf_verify();
    load_controller('InvoiceController');
    invoice_forward_to_finance((int)$p['id']);
    exit;
}

if (($p = match_route('/invoices/{id}/status', $path)) !== false && $method === 'POST') {
    require_login();
    csrf_verify();
    load_controller('InvoiceController');
    invoice_update_status((int)$p['id']);
    exit;
}

if (($p = match_route('/invoices/{id}/reject', $path)) !== false && $method === 'POST') {
    require_login();
    csrf_verify();
    load_controller('InvoiceController');
    invoice_reject((int)$p['id']);
    exit;
}

if (($p = match_route('/invoices/{id}', $path)) !== false && $method === 'GET') {
    require_login();
    load_controller('InvoiceController');
    invoice_show((int)$p['id']);
    exit;
}

// ── Audit log routes ──────────────────────────────────────────────────────────
if ($path === '/audit-log/export' && $method === 'GET') {
    require_login();
    load_controller('AuditLogController');
    auditlog_export_pdf();
    exit;
}

if ($path === '/audit-log' && $method === 'GET') {
    require_login();
    load_controller('AuditLogController');
    auditlog_index();
    exit;
}

// ── 404 ───────────────────────────────────────────────────────────────────────
http_response_code(404);
echo '<h1>404 — Page Not Found</h1><p><a href="' . url('delivery-orders') . '">Go home</a></p>';
