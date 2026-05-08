<?php
namespace App\Controllers\SuperAdmin;

use App\Core\Controller;
use DB;

class CoordinatorController extends Controller
{
    public function index(): void
    {
        $coordinators = DB::query("SELECT c.*, u.full_name, u.email, u.phone FROM coordinators c JOIN users u ON u.user_id=c.user_id ORDER BY c.created_at DESC");
        $tenants = DB::query("SELECT tenant_id, name FROM tenants WHERE status='active' ORDER BY name");
        $pageTitle = 'Coordinators';
        $this->view('super_admin/coordinators', compact('coordinators','tenants','pageTitle'), 'app');
    }

    public function store(): void
    {
        $this->verifyCsrf();
        $errors = $this->validate(['full_name'=>'required','email'=>'required|email','password'=>'required|min:8']);
        if ($errors) { flash('error',array_values($errors)[0]); $this->back(); }

        $roleId = DB::queryOne("SELECT role_id FROM roles WHERE slug='coordinator' LIMIT 1")['role_id'];
        $userId = DB::insert('users',[
            'tenant_id'     => (int)$this->input('tenant_id'),
            'role_id'       => $roleId,
            'email'         => $this->input('email'),
            'password_hash' => password_hash($this->input('password'), PASSWORD_BCRYPT,['cost'=>12]),
            'full_name'     => $this->input('full_name'),
            'phone'         => $this->input('phone'),
            'status'        => 'active',
            'created_by'    => auth_user()['user_id'],
            'created_at'    => date('Y-m-d H:i:s'),
            'updated_at'    => date('Y-m-d H:i:s'),
        ]);
        DB::insert('coordinators',[
            'tenant_id'       => (int)$this->input('tenant_id'),
            'user_id'         => $userId,
            'assigned_tenants'=> json_encode([$this->input('tenant_id')]),
            'status'          => 'active',
            'created_by'      => auth_user()['user_id'],
            'created_at'      => date('Y-m-d H:i:s'),
            'updated_at'      => date('Y-m-d H:i:s'),
        ]);
        flash('success','Coordinator created.'); $this->redirect('super/coordinators');
    }

    public function edit(string $id): void
    {
        $coordinator = DB::queryOne("SELECT c.*, u.full_name, u.email, u.phone FROM coordinators c JOIN users u ON u.user_id=c.user_id WHERE c.user_id=?", [(int)$id]);
        if (!$coordinator) { flash('error', 'Coordinator not found.'); $this->redirect('super/coordinators'); }
        $tenants = DB::query("SELECT tenant_id, name FROM tenants WHERE status='active' ORDER BY name");
        $pageTitle = 'Edit Coordinator';
        $this->view('super_admin/coordinators_edit', compact('coordinator','tenants','pageTitle'), 'app');
    }

    public function update(string $id): void
    {
        $this->verifyCsrf();
        $errors = $this->validate(['full_name'=>'required','email'=>'required|email']);
        if ($errors) { flash('error',array_values($errors)[0]); $this->back(); }

        $coordinator = DB::queryOne("SELECT * FROM coordinators WHERE user_id=?", [(int)$id]);
        if (!$coordinator) { flash('error', 'Coordinator not found.'); $this->redirect('super/coordinators'); }

        DB::update('users', [
            'email'      => $this->input('email'),
            'full_name'  => $this->input('full_name'),
            'phone'      => $this->input('phone'),
            'status'     => $this->input('status', 'active'),
            'updated_at' => date('Y-m-d H:i:s'),
        ], ['user_id' => (int)$id]);

        if ($this->input('password')) {
            DB::update('users', [
                'password_hash' => password_hash($this->input('password'), PASSWORD_BCRYPT, ['cost' => 12])
            ], ['user_id' => (int)$id]);
        }

        DB::update('coordinators', [
            'tenant_id'        => (int)$this->input('tenant_id'),
            'assigned_tenants' => json_encode([$this->input('tenant_id')]),
            'status'           => $this->input('status', 'active'),
            'updated_at'       => date('Y-m-d H:i:s'),
        ], ['user_id' => (int)$id]);

        flash('success', 'Coordinator updated.'); $this->redirect('super/coordinators');
    }

    public function delete(string $id): void
    {
        $this->verifyCsrf();
        // Soft delete the user by marking them inactive to preserve logs
        DB::update('users', ['status' => 'inactive', 'updated_at' => date('Y-m-d H:i:s')], ['user_id' => (int)$id]);
        DB::update('coordinators', ['status' => 'inactive', 'updated_at' => date('Y-m-d H:i:s')], ['user_id' => (int)$id]);
        flash('success', 'Coordinator marked as inactive.');
        $this->redirect('super/coordinators');
    }
}
