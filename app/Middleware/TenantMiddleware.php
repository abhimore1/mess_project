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
    }
}
