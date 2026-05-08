<?php
namespace App\Controllers\SuperAdmin;

use App\Core\Controller;
use App\Services\ModuleService;
use DB;

class TenantController extends Controller
{
    public function index(): void
    {
        $page    = (int)($this->input('page', 1));
        $search  = trim($this->input('q', ''));
        $status  = $this->input('status', '');

        $sql = "SELECT t.*, sp.name AS plan_name,
                (SELECT COUNT(*) FROM students s WHERE s.tenant_id=t.tenant_id AND s.status='active') AS student_count
                FROM tenants t LEFT JOIN subscription_plans sp ON sp.plan_id=t.plan_id
                WHERE 1=1";
        $params = [];

        if ($search) { $sql .= " AND (t.name LIKE ? OR t.slug LIKE ? OR t.contact_email LIKE ?)"; $params = array_merge($params, ["%$search%","%$search%","%$search%"]); }
        if ($status)  { $sql .= " AND t.status=?"; $params[] = $status; }

        $total     = DB::queryOne("SELECT COUNT(*) AS c FROM ($sql) x", $params)['c'] ?? 0;
        $perPage   = 15;
        $offset    = ($page - 1) * $perPage;
        $tenants   = DB::query("$sql ORDER BY t.created_at DESC LIMIT $perPage OFFSET $offset", $params);
        $plans     = DB::query("SELECT plan_id, name FROM subscription_plans WHERE is_active=1 ORDER BY price_monthly");
        $pagination = ['current_page'=>$page,'last_page'=>(int)ceil($total/$perPage),'total'=>$total,'per_page'=>$perPage];

        $this->view('super_admin/tenants/index', compact('tenants','plans','pagination','search','status'), 'app');
    }

    public function create(): void
    {
        $plans = DB::query("SELECT plan_id, name, price_monthly FROM subscription_plans WHERE is_active=1 ORDER BY price_monthly");
        $this->view('super_admin/tenants/create', ['plans'=>$plans, 'csrf'=>$this->csrfToken()], 'app');
    }

    public function store(): void
    {
        $this->verifyCsrf();

        $errors = $this->validate([
            'name'          => 'required|min:3',
            'contact_email' => 'required|email',
            'plan_id'       => 'required|numeric',
        ]);

        $slug = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', trim($this->input('name', ''))));

        $exists = DB::queryOne("SELECT tenant_id FROM tenants WHERE slug=? LIMIT 1", [$slug]);
        if ($exists) $errors['name'] = 'A mess with this name already exists.';

        if ($errors) {
            flash('error', array_values($errors)[0]);
            $this->back();
        }

        DB::beginTransaction();
        try {
            $tenantId = DB::insert('tenants', [
                'name'           => $this->input('name'),
                'owner_name'     => $this->input('owner_name'),
                'slug'           => $slug,
                'primary_color'  => $this->input('primary_color', '#6366f1'),
                'secondary_color'=> $this->input('secondary_color','#06b6d4'),
                'contact_email'  => $this->input('contact_email'),
                'contact_phone'  => $this->input('contact_phone'),
                'address'        => $this->input('address'),
                'city'           => $this->input('city'),
                'state'          => $this->input('state'),
                'pincode'        => $this->input('pincode'),
                'plan_id'        => $this->input('plan_id'),
                'status'         => 'active',
                'created_at'     => date('Y-m-d H:i:s'),
                'updated_at'     => date('Y-m-d H:i:s'),
            ]);

            // Enable core modules by default
            $coreModules = DB::query("SELECT module_id FROM feature_modules WHERE is_core=1");
            foreach ($coreModules as $m) {
                ModuleService::enableForTenant($tenantId, $m['module_id']);
            }

            // Create mess admin user
            $roleId = DB::queryOne("SELECT role_id FROM roles WHERE slug='mess_admin' LIMIT 1")['role_id'];
            $adminPass = $this->input('admin_password', 'Admin@123');
            DB::insert('users', [
                'tenant_id'     => $tenantId,
                'role_id'       => $roleId,
                'email'         => $this->input('contact_email'),
                'password_hash' => password_hash($adminPass, PASSWORD_BCRYPT, ['cost'=>12]),
                'full_name'     => $this->input('name') . ' Admin',
                'status'        => 'active',
                'created_by'    => $_SESSION['user_id'],
                'created_at'    => date('Y-m-d H:i:s'),
                'updated_at'    => date('Y-m-d H:i:s'),
            ]);

            // Default mess settings
            $defaults = [
                'mess_name'        => $this->input('name'),
                'student_login'    => '1',
                'timezone'         => 'Asia/Kolkata',
                'currency_symbol'  => '₹',
            ];
            foreach ($defaults as $k => $v) {
                DB::insert('mess_settings', ['tenant_id'=>$tenantId,'setting_key'=>$k,'setting_value'=>$v,'setting_group'=>'general','created_at'=>date('Y-m-d H:i:s'),'updated_at'=>date('Y-m-d H:i:s')]);
            }

            // Subscription
            $plan = DB::queryOne("SELECT * FROM subscription_plans WHERE plan_id=? LIMIT 1",[$this->input('plan_id')]);
            DB::insert('tenant_subscriptions',[
                'tenant_id'   => $tenantId,
                'plan_id'     => $plan['plan_id'],
                'billing_cycle'=> 'monthly',
                'starts_at'   => date('Y-m-d'),
                'expires_at'  => date('Y-m-d', strtotime('+30 days')),
                'status'      => 'active',
                'amount_paid' => $plan['price_monthly'],
                'created_at'  => date('Y-m-d H:i:s'),
                'updated_at'  => date('Y-m-d H:i:s'),
            ]);

            DB::commit();
            log_activity('tenant.created','tenants',$tenantId,[],['slug'=>$slug]);
            flash('success', "Mess '{$this->input('name')}' created successfully!");
            $this->redirect('super/tenants');
        } catch (\Throwable $e) {
            DB::rollBack();
            flash('error', 'Failed to create mess: ' . $e->getMessage());
            $this->back();
        }
    }

    public function edit(string $id): void
    {
        $tenant = DB::queryOne("SELECT * FROM tenants WHERE tenant_id=?", [(int)$id]);
        if (!$tenant) $this->abort(404);
        $plans  = DB::query("SELECT plan_id, name, price_monthly FROM subscription_plans WHERE is_active=1");
        $this->view('super_admin/tenants/edit', compact('tenant','plans'), 'app');
    }

    public function update(string $id): void
    {
        $this->verifyCsrf();
        DB::update('tenants', [
            'name'           => $this->input('name'),
            'owner_name'     => $this->input('owner_name'),
            'primary_color'  => $this->input('primary_color','#6366f1'),
            'secondary_color'=> $this->input('secondary_color','#06b6d4'),
            'contact_email'  => $this->input('contact_email'),
            'contact_phone'  => $this->input('contact_phone'),
            'address'        => $this->input('address'),
            'city'           => $this->input('city'),
            'state'          => $this->input('state'),
            'pincode'        => $this->input('pincode'),
            'plan_id'        => $this->input('plan_id'),
            'status'         => $this->input('status','active'),
            'updated_at'     => date('Y-m-d H:i:s'),
        ], ['tenant_id' => (int)$id]);
        log_activity('tenant.updated','tenants',(int)$id);
        flash('success', 'Mess updated successfully.');
        $this->redirect('super/tenants');
    }

    public function toggleStatus(string $id): void
    {
        $this->verifyCsrf();
        $tenant = DB::queryOne("SELECT status FROM tenants WHERE tenant_id=?",[(int)$id]);
        $newStatus = $tenant['status'] === 'active' ? 'inactive' : 'active';
        DB::update('tenants',['status'=>$newStatus,'updated_at'=>date('Y-m-d H:i:s')],['tenant_id'=>(int)$id]);
        $this->json(['success'=>true,'status'=>$newStatus]);
    }

    public function modules(string $id): void
    {
        $tenant  = DB::queryOne("SELECT * FROM tenants WHERE tenant_id=?",[(int)$id]);
        if (!$tenant) $this->abort(404);
        $modules = ModuleService::getTenantModules((int)$id);
        $this->view('super_admin/tenants/modules', compact('tenant','modules'), 'app');
    }

    public function saveModules(string $id): void
    {
        $this->verifyCsrf();
        $enabled  = $_POST['modules'] ?? [];
        $allMods  = DB::query("SELECT module_id, is_core FROM feature_modules");
        foreach ($allMods as $m) {
            if ($m['is_core']) continue; // Core modules cannot be disabled
            if (in_array($m['module_id'], $enabled)) {
                ModuleService::enableForTenant((int)$id, $m['module_id']);
            } else {
                ModuleService::disableForTenant((int)$id, $m['module_id']);
            }
        }
        flash('success','Module settings saved.');
        $this->redirect("super/tenants/$id/modules");
    }
}
