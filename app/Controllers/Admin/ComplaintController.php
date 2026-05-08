<?php
namespace App\Controllers\Admin;

use DB;
use App\Core\Controller;

class ComplaintController extends Controller
{
    public function __construct() { $this->requireModule('complaints'); }

    public function index(): void
    {
        $tid  = auth_user()['tenant_id'];
        $complaints = DB::query("SELECT c.*, s.full_name AS student_name FROM complaints c JOIN students s ON s.student_id=c.student_id WHERE c.tenant_id=? ORDER BY c.created_at DESC",[$tid]);
        $pageTitle = 'Complaints';
        $this->view('admin/complaints/index', compact('complaints','pageTitle'), 'app');
    }

    public function updateStatus(string $id): void
    {
        $this->verifyCsrf();
        $tid    = auth_user()['tenant_id'];
        $status = $this->input('status','open');
        DB::update('complaints',['status'=>$status,'resolved_by'=>auth_user()['user_id'],'resolved_at'=>date('Y-m-d H:i:s'),'updated_at'=>date('Y-m-d H:i:s')],['complaint_id'=>(int)$id,'tenant_id'=>$tid]);
        if ($this->isAjax()) { $this->json(['success'=>true]); }
        flash('success','Status updated.'); $this->back();
    }
}
