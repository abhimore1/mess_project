<?php
namespace App\Services;

class ModuleService
{
    public static function isEnabled(string $slug): bool
    {
        $modules = $_SESSION['modules'] ?? [];
        return in_array('*', $modules) || in_array($slug, $modules);
    }

    public static function enableForTenant(int $tenantId, int $moduleId): void
    {
        $exists = \DB::queryOne(
            "SELECT id FROM tenant_modules WHERE tenant_id=? AND module_id=?",
            [$tenantId, $moduleId]
        );
        if ($exists) {
            \DB::update('tenant_modules', ['is_enabled' => 1, 'enabled_at' => date('Y-m-d H:i:s')], ['id' => $exists['id']]);
        } else {
            \DB::insert('tenant_modules', [
                'tenant_id'  => $tenantId,
                'module_id'  => $moduleId,
                'is_enabled' => 1,
                'enabled_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
            ]);
        }
    }

    public static function disableForTenant(int $tenantId, int $moduleId): void
    {
        \DB::update('tenant_modules', ['is_enabled' => 0], ['tenant_id' => $tenantId, 'module_id' => $moduleId]);
    }

    public static function getTenantModules(int $tenantId): array
    {
        return \DB::query(
            "SELECT fm.*, COALESCE(tm.is_enabled,0) AS is_enabled
             FROM feature_modules fm
             LEFT JOIN tenant_modules tm ON tm.module_id=fm.module_id AND tm.tenant_id=?
             ORDER BY fm.is_core DESC, fm.name ASC",
            [$tenantId]
        );
    }
}
