<?php
namespace App\Controllers\Admin;

use DB;
use App\Core\Controller;

class ProfileController extends Controller
{
    public function index(): void
    {
        $tid = auth_user()['tenant_id'];
        
        // Fetch tenant details including plan info
        $tenant = DB::queryOne("
            SELECT t.*, sp.name AS plan_name, sp.max_students, sp.max_coordinators
            FROM tenants t
            LEFT JOIN subscription_plans sp ON t.plan_id = sp.plan_id
            WHERE t.tenant_id = ?
        ", [$tid]);

        if (!$tenant) {
            $this->abort(404);
        }

        // Fetch subscription info
        $subscription = DB::queryOne("
            SELECT * FROM tenant_subscriptions 
            WHERE tenant_id = ? AND status = 'active' 
            ORDER BY expires_at DESC LIMIT 1
        ", [$tid]);

        $pageTitle = 'Mess Profile';
        $this->view('admin/profile/index', compact('tenant', 'subscription', 'pageTitle'), 'app');
    }
}
