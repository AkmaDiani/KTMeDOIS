<?php
/**
 * Application_Layer/Backend_API/Helpers/helpers.php
 * Utility functions that replace Laravel helpers:
 *   redirect()   →  redirect()->route() / back()
 *   e()          →  {{ }}  Blade escaping
 *   flash()      →  session()->flash()
 *   old()        →  old()
 *   url()        →  route() / asset()
 *   csrf_*       →  @csrf  Blade directive
 */

// ── Redirect ──────────────────────────────────────────────────────────────────
function redirect(string $path, array $query = []): never {
    require_once __DIR__ . '/../../../Data_Layer/Relational_Database/database_config.php';
    $url = BASE_URL . '/' . ltrim($path, '/');
    if ($query) {
        $url .= '?' . http_build_query($query);
    }
    header('Location: ' . $url);
    exit;
}

function redirect_back(): never {
    $ref = $_SERVER['HTTP_REFERER'] ?? null;
    if ($ref) {
        header('Location: ' . $ref);
        exit;
    }
    redirect('delivery-orders');
}

// ── Output escaping (replaces Blade {{ }}) ────────────────────────────────────
function e(mixed $value): string {
    return htmlspecialchars((string)($value ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

// ── Flash messages ────────────────────────────────────────────────────────────
function flash(string $key, string $message): void {
    $_SESSION['_flash'][$key] = $message;
}

function get_flash(string $key): ?string {
    $msg = $_SESSION['_flash'][$key] ?? null;
    unset($_SESSION['_flash'][$key]);
    return $msg;
}

// ── "Old" input (replaces old() helper) ──────────────────────────────────────
function old(string $key, string $default = ''): string {
    return e($_SESSION['_old'][$key] ?? $_POST[$key] ?? $default);
}

function store_old(array $data): void {
    $_SESSION['_old'] = $data;
}

function clear_old(): void {
    unset($_SESSION['_old']);
}

// ── URL / Asset helpers ───────────────────────────────────────────────────────
function url(string $path = ''): string {
    require_once __DIR__ . '/../../../Data_Layer/Relational_Database/database_config.php';
    return BASE_URL . '/' . ltrim($path, '/');
}

function asset(string $path): string {
    return url('css/' . ltrim($path, '/'));
}

// ── CSRF ──────────────────────────────────────────────────────────────────────
function csrf_token(): string {
    if (empty($_SESSION['_csrf'])) {
        $_SESSION['_csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['_csrf'];
}

function csrf_field(): string {
    return '<input type="hidden" name="_csrf" value="' . e(csrf_token()) . '">';
}

function csrf_verify(): void {
    $token = $_POST['_csrf'] ?? '';
    if (!hash_equals(csrf_token(), $token)) {
        http_response_code(419);
        die('<h1>CSRF token mismatch</h1>');
    }
}

// ── Date formatting ───────────────────────────────────────────────────────────
function fmt_date(?string $date, string $format = 'd M Y'): string {
    if (!$date) return '—';
    try {
        return (new DateTime($date))->format($format);
    } catch (Exception) {
        return '—';
    }
}

function fmt_datetime(?string $date): string {
    return fmt_date($date, 'd M Y, h:i A');
}

function now_str(): string {
    return (new DateTime())->format('Y-m-d H:i:s');
}

// ── Status badge (replaces partials/status-badge.blade.php) ──────────────────
function status_badge(string $status): string {
    $slug = match($status) {
        'Submitted'         => 'submitted',
        'Under Review'      => 'review',
        'Approved'          => 'approved',
        'Rejected'          => 'rejected',
        'Finance Review'    => 'finance',
        'Payment Processing'=> 'processing',
        'Paid'              => 'paid',
        default             => 'submitted',
    };
    return '<span class="badge badge-' . $slug . '">' . e($status) . '</span>';
}

// ── Number formatting ─────────────────────────────────────────────────────────
function money(?string $amount): string {
    return 'RM ' . number_format((float)($amount ?? 0), 2);
}

// ── Pagination helper (simple prev/next) ─────────────────────────────────────
function paginate_query(string $sql, array $params, int $page, int $perPage = 20): array {
    $offset = ($page - 1) * $perPage;

    // Count
    $countSql = preg_replace('/SELECT .+? FROM /is', 'SELECT COUNT(*) AS total FROM ', $sql, 1);
    $countSql = preg_replace('/ORDER BY .+$/is', '', $countSql);
    $stmt = db()->prepare($countSql);
    $stmt->execute($params);
    $total = (int)$stmt->fetchColumn();

    // Data
    $stmt = db()->prepare($sql . " LIMIT $perPage OFFSET $offset");
    $stmt->execute($params);
    $rows = $stmt->fetchAll();

    return [
        'rows'       => $rows,
        'total'      => $total,
        'page'       => $page,
        'per_page'   => $perPage,
        'last_page'  => (int)ceil($total / $perPage),
    ];
}

function pagination_links(array $paginator, string $baseUrl): string {
    if ($paginator['last_page'] <= 1) return '';
    $html = '<div class="pagination">';
    for ($i = 1; $i <= $paginator['last_page']; $i++) {
        $active = $i === $paginator['page'] ? ' style="font-weight:bold;"' : '';
        $html .= '<a href="' . e($baseUrl . (str_contains($baseUrl, '?') ? '&' : '?') . 'page=' . $i) . '"' . $active . '>' . $i . '</a>';
    }
    $html .= '</div>';
    return $html;
}
