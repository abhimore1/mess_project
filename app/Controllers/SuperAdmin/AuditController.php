<?php
namespace App\Controllers\SuperAdmin;

use App\Core\Controller;
use DB;

class AuditController extends Controller
{
    public function index(): void
    {
        $page = (int)$this->input('page',1);
        $perPage = 30;
        $total = DB::queryOne("SELECT COUNT(*) AS c FROM activity_logs")['c']??0;
        $logs  = DB::query("SELECT al.*, u.full_name, t.name AS tenant_name
            FROM activity_logs al
            LEFT JOIN users u ON u.user_id=al.user_id
            LEFT JOIN tenants t ON t.tenant_id=al.tenant_id
            ORDER BY al.created_at DESC LIMIT $perPage OFFSET " . (($page-1)*$perPage));
        $pagination = ['current_page'=>$page,'last_page'=>(int)ceil($total/$perPage),'total'=>$total,'per_page'=>$perPage];
        $pageTitle = 'Audit Logs';
        $this->view('super_admin/audit_logs', compact('logs','pagination','pageTitle'), 'app');
    }
}
