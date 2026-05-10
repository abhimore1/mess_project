<?php
namespace App\Middleware;

class TenantMiddleware
{
    public function handle(): void
    {
        $tenantId = $_SESSION['tenant_id'] ?? null;
        $role     = $_SESSION['role_slug'] ?? '';

        // Super admin bypasses tenant check
        if ($role === 'super_admin') return;

        if (!$tenantId) {
            session_destroy();
            header("Location: " . env('APP_URL') . "/login");
            exit;
        }

        // Verify tenant is still active
        $tenant = \DB::queryOne(
            "SELECT tenant_id, status FROM tenants WHERE tenant_id = ? LIMIT 1",
            [$tenantId]
        );

        if (!$tenant || $tenant['status'] !== 'active') {
            session_destroy();
            header("Location: " . env('APP_URL') . "/login?reason=tenant_inactive");
            exit;
        }

        // Dynamically refresh module access on every request for real-time toggling
        $rows = \DB::query(
            "SELECT fm.slug FROM feature_modules fm
             JOIN tenant_modules tm ON tm.module_id = fm.module_id
             WHERE tm.tenant_id = ? AND tm.is_enabled = 1",
            [$tenantId]
        );
        $core = \DB::query("SELECT slug FROM feature_modules WHERE is_core = 1");
        
        $_SESSION['modules'] = array_unique(array_merge(
            array_column($rows, 'slug'),
            array_column($core, 'slug')
        ));

        // Dynamically refresh permissions for coordinators
        if ($role === 'coordinator') {
            $permissions = ['dashboard.view'];
            $userId = $_SESSION['user_id'] ?? 0;
            $coord = \DB::queryOne("SELECT custom_permissions FROM coordinators WHERE user_id = ?", [$userId]);
            if ($coord && !empty($coord['custom_permissions'])) {
                $custom = json_decode($coord['custom_permissions'], true);
                if (is_array($custom)) {
                    $permissions = array_unique(array_merge($permissions, $custom));
                }
            }
            $_SESSION['permissions'] = $permissions;
        }
    }
}
