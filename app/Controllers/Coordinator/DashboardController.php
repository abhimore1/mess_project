<?php
namespace App\Controllers\Coordinator;

use DB;

use App\Core\Controller;

class DashboardController extends Controller
{
    public function index(): void
    {
        $uid = auth_user()['user_id'];
        $coord = DB::queryOne("SELECT * FROM coordinators WHERE user_id=? LIMIT 1",[$uid]);
        $assignedTenants = json_decode($coord['assigned_tenants']??'[]',true);

        $tenants = [];
        if (!empty($assignedTenants)) {
            $in = implode(',', array_fill(0, count($assignedTenants), '?'));
            $tenants = DB::query("SELECT t.*, (SELECT COUNT(*) FROM students s WHERE s.tenant_id=t.tenant_id AND s.status='active') AS students FROM tenants t WHERE t.tenant_id IN ($in)", $assignedTenants);
        }
        $pageTitle = 'Coordinator Dashboard';
        $this->view('coordinator/dashboard', compact('tenants','pageTitle'), 'app');
    }

    public function students(): void  { $this->view('coordinator/students', ['pageTitle'=>'Students'], 'app'); }
    public function reports(): void   { $this->view('coordinator/reports',  ['pageTitle'=>'Reports'],  'app'); }
    public function complaints(): void{ $this->view('coordinator/complaints',['pageTitle'=>'Complaints'],'app'); }
}
