<?php
/**
 * Global helper functions — available everywhere via Autoloader.
 */

function e(mixed $val): string
{
    return htmlspecialchars((string)$val, ENT_QUOTES | ENT_HTML5, 'UTF-8');
}

function csrf(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="_token" value="' . csrf() . '">';
}

function method_field(string $method): string
{
    return '<input type="hidden" name="_method" value="' . strtoupper($method) . '">';
}

function url(string $path = ''): string
{
    return rtrim(env('APP_URL', ''), '/') . '/' . ltrim($path, '/');
}

function asset(string $path): string
{
    return url('assets/' . ltrim($path, '/'));
}

function redirect_to(string $path): never
{
    header('Location: ' . url($path));
    exit;
}

function old(string $key, mixed $default = ''): mixed
{
    return $_SESSION['_old'][$key] ?? $default;
}

function flash(string $key, mixed $value = null): mixed
{
    if ($value !== null) {
        $_SESSION['_flash'][$key] = $value;
        return null;
    }
    $val = $_SESSION['_flash'][$key] ?? null;
    unset($_SESSION['_flash'][$key]);
    return $val;
}

function can(string $permission): bool
{
    $perms = $_SESSION['permissions'] ?? [];
    return in_array('*', $perms) || in_array($permission, $perms);
}

function module_enabled(string $slug): bool
{
    $modules = $_SESSION['modules'] ?? [];
    return in_array('*', $modules) || in_array($slug, $modules);
}

function auth_user(): array
{
    return [
        'user_id'   => $_SESSION['user_id']   ?? null,
        'full_name' => $_SESSION['full_name']  ?? '',
        'email'     => $_SESSION['email']      ?? '',
        'role'      => $_SESSION['role_slug']  ?? '',
        'tenant_id' => $_SESSION['tenant_id']  ?? null,
        'avatar'    => $_SESSION['avatar']     ?? null,
    ];
}

function format_currency(float $amount, string $symbol = '₹'): string
{
    return $symbol . number_format($amount, 2);
}

function format_date(?string $date, string $format = 'd M Y'): string
{
    return $date ? date($format, strtotime($date)) : '—';
}

function days_until(string $date): int
{
    return (int)ceil((strtotime($date) - time()) / 86400);
}

function badge(string $status): string
{
    $map = [
        'active'      => 'success',
        'inactive'    => 'secondary',
        'pending'     => 'warning',
        'expired'     => 'danger',
        'paid'        => 'success',
        'partial'     => 'warning',
        'failed'      => 'danger',
        'present'     => 'success',
        'absent'      => 'danger',
        'leave'       => 'info',
        'open'        => 'danger',
        'resolved'    => 'success',
        'in_progress' => 'warning',
        'suspended'   => 'danger',
    ];
    $color = $map[strtolower($status)] ?? 'secondary';
    return "<span class=\"badge bg-{$color}\">" . ucfirst($status) . "</span>";
}

function paginate_links(array $pagination, string $baseUrl): string
{
    $html        = '<nav><ul class="pagination pagination-sm">';
    $current     = $pagination['current_page'];
    $last        = $pagination['last_page'];
    $window      = 2;

    // Prev
    $html .= '<li class="page-item ' . ($current <= 1 ? 'disabled' : '') . '">';
    $html .= '<a class="page-link" href="' . $baseUrl . '?page=' . ($current - 1) . '">‹</a></li>';

    for ($i = 1; $i <= $last; $i++) {
        if ($i === 1 || $i === $last || abs($i - $current) <= $window) {
            $active = $i === $current ? 'active' : '';
            $html  .= "<li class=\"page-item $active\"><a class=\"page-link\" href=\"{$baseUrl}?page={$i}\">{$i}</a></li>";
        } elseif (abs($i - $current) === $window + 1) {
            $html .= '<li class="page-item disabled"><span class="page-link">…</span></li>';
        }
    }

    // Next
    $html .= '<li class="page-item ' . ($current >= $last ? 'disabled' : '') . '">';
    $html .= '<a class="page-link" href="' . $baseUrl . '?page=' . ($current + 1) . '">›</a></li>';
    $html .= '</ul></nav>';

    return $html;
}

function log_activity(string $action, string $entity = '', int $entityId = 0, array $old = [], array $new = []): void
{
    try {
        DB::insert('activity_logs', [
            'tenant_id'   => $_SESSION['tenant_id'] ?? null,
            'user_id'     => $_SESSION['user_id']   ?? null,
            'action'      => $action,
            'entity_type' => $entity,
            'entity_id'   => $entityId,
            'old_values'  => $old ? json_encode($old) : null,
            'new_values'  => $new ? json_encode($new) : null,
            'ip_address'  => $_SERVER['REMOTE_ADDR'] ?? '',
            'user_agent'  => substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 255),
            'created_at'  => date('Y-m-d H:i:s'),
        ]);
    } catch (\Throwable) { /* never crash on logging */ }
}

function get_setting(string $key, mixed $default = null): mixed
{
    $tenantId = $_SESSION['tenant_id'] ?? 0;
    if (!$tenantId) return $default;
    $row = DB::queryOne(
        "SELECT setting_value FROM mess_settings WHERE tenant_id=? AND setting_key=? LIMIT 1",
        [$tenantId, $key]
    );
    return $row ? $row['setting_value'] : $default;
}

function mess_name(): string
{
    return get_setting('mess_name', 'My Mess');
}
