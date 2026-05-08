<?php
namespace App\Controllers\SuperAdmin;

use App\Core\Controller;
use DB;

class PlanController extends Controller
{
    public function index(): void
    {
        $plans = DB::query("SELECT sp.*, (SELECT COUNT(*) FROM tenant_subscriptions ts WHERE ts.plan_id=sp.plan_id AND ts.status='active') AS active_tenants FROM subscription_plans sp ORDER BY sp.price_monthly");
        $pageTitle = 'Subscription Plans';
        $this->view('super_admin/plans/index', compact('plans','pageTitle'), 'app');
    }

    public function store(): void
    {
        $this->verifyCsrf();
        $errors = $this->validate(['name'=>'required','price_monthly'=>'required|numeric']);
        if ($errors) { flash('error',array_values($errors)[0]); $this->back(); }
        DB::insert('subscription_plans',[
            'name'               => $this->input('name'),
            'price_monthly'      => $this->input('price_monthly'),
            'price_yearly'       => $this->input('price_yearly',0),
            'max_students'       => $this->input('max_students',0),
            'max_coordinators'   => $this->input('max_coordinators',2),
            'storage_mb'         => $this->input('storage_mb',500),
            'is_active'          => 1,
            'created_at'         => date('Y-m-d H:i:s'),
            'updated_at'         => date('Y-m-d H:i:s'),
        ]);
        flash('success','Plan created.'); $this->redirect('super/plans');
    }

    public function edit(string $id): void
    {
        $plan = DB::queryOne("SELECT * FROM subscription_plans WHERE plan_id=?", [(int)$id]);
        if (!$plan) { flash('error', 'Plan not found.'); $this->redirect('super/plans'); }
        $pageTitle = 'Edit Plan';
        $this->view('super_admin/plans/edit', compact('plan','pageTitle'), 'app');
    }

    public function update(string $id): void
    {
        $this->verifyCsrf();
        $errors = $this->validate(['name'=>'required','price_monthly'=>'required|numeric']);
        if ($errors) { flash('error',array_values($errors)[0]); $this->back(); }
        DB::update('subscription_plans', [
            'name'               => $this->input('name'),
            'price_monthly'      => $this->input('price_monthly'),
            'max_students'       => $this->input('max_students',0),
            'storage_mb'         => $this->input('storage_mb',500),
            'updated_at'         => date('Y-m-d H:i:s'),
        ], ['plan_id' => (int)$id]);
        flash('success', 'Plan updated.'); $this->redirect('super/plans');
    }

    public function delete(string $id): void
    {
        $this->verifyCsrf();
        $inUse = DB::queryOne("SELECT COUNT(*) AS c FROM tenants WHERE plan_id=?", [(int)$id])['c'] ?? 0;
        if ($inUse > 0) {
            flash('error', 'Cannot delete plan because it is currently assigned to tenants.');
            $this->redirect('super/plans');
        }
        DB::delete('subscription_plans', ['plan_id' => (int)$id]);
        flash('success', 'Plan deleted.');
        $this->redirect('super/plans');
    }
}
