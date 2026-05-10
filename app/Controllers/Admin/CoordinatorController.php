<?php
namespace App\Controllers\Admin;

use App\Core\Controller;
use DB;

class CoordinatorController extends Controller
{
    public function index(): void
    {
        $tenantId = auth_user()['tenant_id'];

        $coordinators = DB::query("
            SELECT c.*, u.full_name, u.email, u.phone, u.status as user_status
            FROM coordinators c
            JOIN users u ON u.user_id = c.user_id
            WHERE c.tenant_id = ? AND u.status != 'banned'
            ORDER BY c.created_at DESC
        ", [$tenantId]);

        $pageTitle = 'Manage Coordinators';
        $this->view('admin/coordinators/index', compact('coordinators', 'pageTitle'), 'app');
    }

    public function create(): void
    {
        $tenantId = auth_user()['tenant_id'];
        
        // Check limits from subscription
        $tenant = DB::queryOne("SELECT p.max_coordinators FROM tenants t JOIN subscription_plans p ON p.plan_id = t.plan_id WHERE t.tenant_id = ?", [$tenantId]);
        $currentCount = DB::queryOne("SELECT COUNT(*) as c FROM coordinators WHERE tenant_id = ? AND status = 'active'", [$tenantId])['c'] ?? 0;
        
        $max = $tenant['max_coordinators'] ?? 2;
        if ($max > 0 && $currentCount >= $max) {
            flash('error', "You have reached your subscription limit of {$max} coordinators.");
            $this->redirect('admin/coordinators');
        }

        $enabledModules = DB::query("
            SELECT fm.name, fm.slug 
            FROM feature_modules fm
            JOIN tenant_modules tm ON tm.module_id = fm.module_id
            WHERE tm.tenant_id = ? AND tm.is_enabled = 1
        ", [$tenantId]);
        
        $coreModules = DB::query("SELECT name, slug FROM feature_modules WHERE is_core = 1");
        $allEnabled = array_unique(array_merge($enabledModules, $coreModules), SORT_REGULAR);

        $pageTitle = 'Add Coordinator';
        $this->view('admin/coordinators/create', compact('pageTitle', 'allEnabled'), 'app');
    }

    public function store(): void
    {
        $this->verifyCsrf();
        $tenantId = auth_user()['tenant_id'];

        // Re-check limits
        $tenant = DB::queryOne("SELECT p.max_coordinators FROM tenants t JOIN subscription_plans p ON p.plan_id = t.plan_id WHERE t.tenant_id = ?", [$tenantId]);
        $currentCount = DB::queryOne("SELECT COUNT(*) as c FROM coordinators WHERE tenant_id = ? AND status = 'active'", [$tenantId])['c'] ?? 0;
        $max = $tenant['max_coordinators'] ?? 2;
        if ($max > 0 && $currentCount >= $max) {
            flash('error', "You have reached your subscription limit of {$max} coordinators.");
            $this->redirect('admin/coordinators');
        }

        $errors = $this->validate([
            'full_name' => 'required',
            'email'     => 'required|email',
            'password'  => 'required|min:8'
        ]);

        if ($errors) {
            flash('error', array_values($errors)[0]);
            $this->back();
        }

        // Check if email already exists in users table across system
        $exists = DB::queryOne("SELECT user_id FROM users WHERE email = ?", [$this->input('email')]);
        if ($exists) {
            flash('error', 'Email is already in use by another account.');
            $this->back();
        }

        $roleId = DB::queryOne("SELECT role_id FROM roles WHERE slug='coordinator' LIMIT 1")['role_id'];
        
        // Process permissions
        $perms = $_POST['permissions'] ?? [];
        if (!is_array($perms)) $perms = [];
        
        $expandedPerms = [];
        $permissionGroups = [
            'students.view'    => ['students.view', 'students.create', 'students.edit'],
            'payments.view'    => ['payments.view', 'payments.create'],
            'attendance.view'  => ['attendance.view', 'attendance.mark'],
            'membership.view'  => ['membership.view', 'membership.create'],
            'food_menu.view'   => ['food_menu.view', 'food_menu.manage'],
            'complaints.view'  => ['complaints.view', 'complaints.manage'],
            'reports.view'     => ['reports.view', 'reports.export'],
            'settings.manage'  => ['settings.manage'],
        ];

        foreach ($perms as $p) {
            if (isset($permissionGroups[$p])) {
                $expandedPerms = array_merge($expandedPerms, $permissionGroups[$p]);
            } else {
                $expandedPerms[] = htmlspecialchars($p);
            }
        }
        $expandedPerms = array_unique($expandedPerms);

        try {
            DB::beginTransaction();

            $userId = DB::insert('users', [
                'tenant_id'     => $tenantId,
                'role_id'       => $roleId,
                'email'         => $this->input('email'),
                'password_hash' => password_hash($this->input('password'), PASSWORD_BCRYPT, ['cost' => 12]),
                'full_name'     => $this->input('full_name'),
                'phone'         => $this->input('phone'),
                'status'        => 'active',
                'created_by'    => auth_user()['user_id'],
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ]);

            DB::insert('coordinators', [
                'tenant_id'        => $tenantId,
                'user_id'          => $userId,
                'assigned_tenants' => json_encode([$tenantId]),
                'custom_permissions' => json_encode(array_values($expandedPerms)),
                'status'           => 'active',
                'created_by'       => auth_user()['user_id'],
                'created_at'       => date('Y-m-d H:i:s'),
                'updated_at'       => date('Y-m-d H:i:s'),
            ]);

            DB::commit();
            log_activity('coordinator_created', 'users', $userId);

            flash('success', 'Coordinator created successfully.');
            $this->redirect('admin/coordinators');
        } catch (\Exception $e) {
            DB::rollBack();
            flash('error', 'Error creating coordinator: ' . $e->getMessage());
            $this->back();
        }
    }

    public function edit(string $id): void
    {
        $tenantId = auth_user()['tenant_id'];
        
        $coordinator = DB::queryOne("
            SELECT c.*, u.full_name, u.email, u.phone, u.status as user_status 
            FROM coordinators c 
            JOIN users u ON u.user_id = c.user_id 
            WHERE c.user_id = ? AND c.tenant_id = ?
        ", [(int)$id, $tenantId]);

        if (!$coordinator) {
            flash('error', 'Coordinator not found.');
            $this->redirect('admin/coordinators');
        }

        $coordinator['custom_permissions'] = json_decode($coordinator['custom_permissions'] ?? '[]', true);
        if (!is_array($coordinator['custom_permissions'])) {
            $coordinator['custom_permissions'] = [];
        }

        $enabledModules = DB::query("
            SELECT fm.name, fm.slug 
            FROM feature_modules fm
            JOIN tenant_modules tm ON tm.module_id = fm.module_id
            WHERE tm.tenant_id = ? AND tm.is_enabled = 1
        ", [$tenantId]);
        
        $coreModules = DB::query("SELECT name, slug FROM feature_modules WHERE is_core = 1");
        $allEnabled = array_unique(array_merge($enabledModules, $coreModules), SORT_REGULAR);

        $pageTitle = 'Edit Coordinator';
        $this->view('admin/coordinators/edit', compact('coordinator', 'pageTitle', 'allEnabled'), 'app');
    }

    public function update(string $id): void
    {
        $this->verifyCsrf();
        $tenantId = auth_user()['tenant_id'];

        $coordinator = DB::queryOne("SELECT * FROM coordinators WHERE user_id = ? AND tenant_id = ?", [(int)$id, $tenantId]);
        if (!$coordinator) {
            flash('error', 'Coordinator not found.');
            $this->redirect('admin/coordinators');
        }

        $errors = $this->validate([
            'full_name' => 'required',
            'email'     => 'required|email'
        ]);

        if ($errors) {
            flash('error', array_values($errors)[0]);
            $this->back();
        }

        // Process permissions
        $perms = $_POST['permissions'] ?? [];
        if (!is_array($perms)) $perms = [];
        
        $expandedPerms = [];
        $permissionGroups = [
            'students.view'    => ['students.view', 'students.create', 'students.edit'],
            'payments.view'    => ['payments.view', 'payments.create'],
            'attendance.view'  => ['attendance.view', 'attendance.mark'],
            'membership.view'  => ['membership.view', 'membership.create'],
            'food_menu.view'   => ['food_menu.view', 'food_menu.manage'],
            'complaints.view'  => ['complaints.view', 'complaints.manage'],
            'reports.view'     => ['reports.view', 'reports.export'],
            'settings.manage'  => ['settings.manage'],
        ];

        foreach ($perms as $p) {
            if (isset($permissionGroups[$p])) {
                $expandedPerms = array_merge($expandedPerms, $permissionGroups[$p]);
            } else {
                $expandedPerms[] = htmlspecialchars($p);
            }
        }
        $expandedPerms = array_unique($expandedPerms);

        try {
            DB::beginTransaction();

            $updateUser = [
                'email'      => $this->input('email'),
                'full_name'  => $this->input('full_name'),
                'phone'      => $this->input('phone'),
                'status'     => $this->input('status', 'active'),
                'updated_at' => date('Y-m-d H:i:s'),
            ];

            if ($this->input('password')) {
                $updateUser['password_hash'] = password_hash($this->input('password'), PASSWORD_BCRYPT, ['cost' => 12]);
            }

            DB::update('users', $updateUser, ['user_id' => (int)$id]);

            DB::update('coordinators', [
                'custom_permissions' => json_encode(array_values($expandedPerms)),
                'status'             => $this->input('status', 'active'),
                'updated_at'         => date('Y-m-d H:i:s'),
            ], ['user_id' => (int)$id]);

            DB::commit();
            log_activity('coordinator_updated', 'users', (int)$id);

            flash('success', 'Coordinator updated successfully.');
            $this->redirect('admin/coordinators');
        } catch (\Exception $e) {
            DB::rollBack();
            flash('error', 'Error updating coordinator: ' . $e->getMessage());
            $this->back();
        }
    }

    public function delete(string $id): void
    {
        $this->verifyCsrf();
        $tenantId = auth_user()['tenant_id'];
        
        $coordinator = DB::queryOne("SELECT * FROM coordinators WHERE user_id = ? AND tenant_id = ?", [(int)$id, $tenantId]);
        if (!$coordinator) {
            flash('error', 'Coordinator not found.');
            $this->redirect('admin/coordinators');
        }

        DB::update('users', ['status' => 'inactive', 'updated_at' => date('Y-m-d H:i:s')], ['user_id' => (int)$id]);
        DB::update('coordinators', ['status' => 'inactive', 'updated_at' => date('Y-m-d H:i:s')], ['user_id' => (int)$id]);
        
        log_activity('coordinator_deleted', 'users', (int)$id);
        flash('success', 'Coordinator deactivated successfully.');
        $this->redirect('admin/coordinators');
    }
}
