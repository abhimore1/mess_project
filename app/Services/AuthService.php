<?php
namespace App\Services;

class AuthService
{
    public static function attempt(string $email, string $password): array
    {
        // Find user by email
        $user = \DB::queryOne(
            "SELECT u.*, r.slug AS role_slug FROM users u
             JOIN roles r ON r.role_id = u.role_id
             WHERE u.email = ? LIMIT 1",
            [$email]
        );

        if (!$user) {
            return ['success' => false, 'message' => 'Invalid email or password.'];
        }

        // This login portal is not for super admins
        if ($user['role_slug'] === 'super_admin') {
            return ['success' => false, 'message' => 'Super Admins must use the dedicated portal.'];
        }

        // Verify tenant status
        $tenant = null;
        if ($user['tenant_id']) {
            $tenant = \DB::queryOne("SELECT tenant_id, status, logo, primary_color FROM tenants WHERE tenant_id = ?", [$user['tenant_id']]);
            if (!$tenant || $tenant['status'] !== 'active') {
                return ['success' => false, 'message' => 'Mess not found or inactive.'];
            }
        }

        // Check lockout
        if ($user['locked_until'] && strtotime($user['locked_until']) > time()) {
            return ['success' => false, 'message' => 'Account locked. Try after ' . $user['locked_until']];
        }

        if (!password_verify($password, $user['password_hash'])) {
            // Increment failed attempts
            $attempts = (int)$user['login_attempts'] + 1;
            $lock = $attempts >= 5 ? date('Y-m-d H:i:s', strtotime('+15 minutes')) : null;
            \DB::update('users', ['login_attempts' => $attempts, 'locked_until' => $lock], ['user_id' => $user['user_id']]);
            return ['success' => false, 'message' => 'Invalid email or password.'];
        }

        if ($user['status'] !== 'active') {
            return ['success' => false, 'message' => 'Account is ' . $user['status'] . '.'];
        }

        // Load permissions
        if ($user['role_slug'] === 'coordinator') {
            // Coordinators start with only dashboard access; other permissions are assigned by the Mess Owner
            $permissions = ['dashboard.view'];
            $coord = \DB::queryOne("SELECT custom_permissions FROM coordinators WHERE user_id = ?", [$user['user_id']]);
            if ($coord && !empty($coord['custom_permissions'])) {
                $custom = json_decode($coord['custom_permissions'], true);
                if (is_array($custom)) {
                    $permissions = array_unique(array_merge($permissions, $custom));
                }
            }
        } else {
            $permissions = self::loadPermissions($user['role_id']);
        }
        
        $modules = $user['tenant_id'] ? self::loadModules($user['tenant_id']) : ['*'];

        // Set session
        session_regenerate_id(true);
        $_SESSION['user_id']     = $user['user_id'];
        $_SESSION['full_name']   = $user['full_name'];
        $_SESSION['email']       = $user['email'];
        $_SESSION['role_id']     = $user['role_id'];
        $_SESSION['role_slug']   = $user['role_slug'];
        $_SESSION['tenant_id']   = $user['tenant_id'];
        $_SESSION['tenant_logo'] = $tenant['logo'] ?? null;
        $_SESSION['primary_color'] = $tenant['primary_color'] ?? '#6750A4';
        $_SESSION['permissions'] = $permissions;
        $_SESSION['modules']     = $modules;
        $_SESSION['avatar']      = $user['avatar'];

        // Update last login + reset attempts
        \DB::update('users', [
            'last_login_at'  => date('Y-m-d H:i:s'),
            'login_attempts' => 0,
            'locked_until'   => null,
        ], ['user_id' => $user['user_id']]);

        // Log activity
        self::logActivity($user['user_id'], $user['tenant_id'], 'login', 'users', $user['user_id']);

        return ['success' => true, 'role' => $user['role_slug']];
    }

    public static function logout(): void
    {
        if (!empty($_SESSION['user_id'])) {
            self::logActivity($_SESSION['user_id'], $_SESSION['tenant_id'] ?? null, 'logout', 'users', $_SESSION['user_id']);
        }
        session_destroy();
    }

    private static function loadPermissions(int $roleId): array
    {
        $rows = \DB::query(
            "SELECT p.slug FROM permissions p
             JOIN role_permissions rp ON rp.permission_id = p.permission_id
             WHERE rp.role_id = ?",
            [$roleId]
        );
        return array_column($rows, 'slug');
    }

    private static function loadModules(int $tenantId): array
    {
        if (!$tenantId) return ['*'];
        $rows = \DB::query(
            "SELECT fm.slug FROM feature_modules fm
             JOIN tenant_modules tm ON tm.module_id = fm.module_id
             WHERE tm.tenant_id = ? AND tm.is_enabled = 1",
            [$tenantId]
        );
        // Core modules always included
        $core = \DB::query("SELECT slug FROM feature_modules WHERE is_core = 1");
        return array_unique(array_merge(array_column($rows, 'slug'), array_column($core, 'slug')));
    }

    private static function logActivity(int $userId, ?int $tenantId, string $action, string $entity, int $entityId): void
    {
        \DB::insert('activity_logs', [
            'tenant_id'   => $tenantId,
            'user_id'     => $userId,
            'action'      => $action,
            'entity_type' => $entity,
            'entity_id'   => $entityId,
            'ip_address'  => $_SERVER['REMOTE_ADDR'] ?? '',
            'user_agent'  => substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 255),
            'created_at'  => date('Y-m-d H:i:s'),
        ]);
    }

    public static function check(): bool   { return !empty($_SESSION['user_id']); }
    public static function user(): array   { return $_SESSION ?? []; }
    public static function role(): string  { return $_SESSION['role_slug'] ?? ''; }
    public static function tenantId(): int { return (int)($_SESSION['tenant_id'] ?? 0); }
}
