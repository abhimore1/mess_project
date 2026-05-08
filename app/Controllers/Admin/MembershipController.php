<?php
namespace App\Controllers\Admin;

use DB;
use App\Core\Controller;

class MembershipController extends Controller
{
    public function __construct() { $this->requireModule('membership'); }

    public function index(): void
    {
        $tid  = auth_user()['tenant_id'];
        $mems = DB::query("SELECT m.*, s.full_name AS student_name, mp.name AS plan_name, mp.price,
            DATEDIFF(m.end_date, CURDATE()) AS days_left
            FROM memberships m JOIN students s ON s.student_id=m.student_id
            JOIN membership_plans mp ON mp.plan_id=m.plan_id
            WHERE m.tenant_id=? ORDER BY m.end_date ASC",[$tid]);
        $pageTitle = 'Memberships';
        $this->view('admin/memberships/index', compact('mems','pageTitle'), 'app');
    }

    public function plans(): void
    {
        $tid   = auth_user()['tenant_id'];
        $plans = DB::query("SELECT mp.*, (SELECT COUNT(*) FROM memberships m WHERE m.plan_id=mp.plan_id AND m.status='active') AS active_count FROM membership_plans mp WHERE mp.tenant_id=? ORDER BY mp.price",[$tid]);
        $slots = DB::query("SELECT * FROM meal_slots WHERE tenant_id=? AND is_active=1 ORDER BY sort_order",[$tid]);
        $pageTitle = 'Membership Plans';
        $this->view('admin/memberships/plans', compact('plans','slots','pageTitle'), 'app');
    }

    public function storePlan(): void
    {
        $this->verifyCsrf();
        $tid    = auth_user()['tenant_id'];
        $errors = $this->validate(['name'=>'required','price'=>'required|numeric','duration_days'=>'required|numeric']);
        if ($errors) { flash('error',array_values($errors)[0]); $this->back(); }

        $slotIds = $_POST['slot_ids'] ?? [];
        DB::insert('membership_plans',[
            'tenant_id'    => $tid,
            'name'         => $this->input('name'),
            'duration_days'=> (int)$this->input('duration_days'),
            'price'        => (float)$this->input('price'),
            'meal_slots'   => json_encode(array_map('intval',$slotIds)),
            'description'  => $this->input('description'),
            'is_active'    => 1,
            'created_by'   => auth_user()['user_id'],
            'created_at'   => date('Y-m-d H:i:s'),
            'updated_at'   => date('Y-m-d H:i:s'),
        ]);
        flash('success','Plan created.'); $this->redirect('admin/memberships/plans');
    }

    public function assign(): void
    {
        $this->verifyCsrf();
        $this->requirePermission('membership.create');
        $tid = auth_user()['tenant_id'];
        $errors = $this->validate(['student_id'=>'required|numeric','plan_id'=>'required|numeric','start_date'=>'required']);
        if ($errors) { flash('error',array_values($errors)[0]); $this->back(); }

        $plan      = DB::queryOne("SELECT * FROM membership_plans WHERE plan_id=? AND tenant_id=? LIMIT 1",[(int)$this->input('plan_id'),$tid]);
        if (!$plan) { flash('error','Invalid plan.'); $this->back(); }

        $startDate = $this->input('start_date');
        $endDate   = date('Y-m-d', strtotime($startDate . ' + ' . $plan['duration_days'] . ' days'));

        // Deactivate existing active membership
        DB::execute("UPDATE memberships SET status='expired', updated_at=NOW() WHERE student_id=? AND tenant_id=? AND status='active'",
            [(int)$this->input('student_id'),$tid]);

        $mId = DB::insert('memberships',[
            'tenant_id'  => $tid,
            'student_id' => (int)$this->input('student_id'),
            'plan_id'    => (int)$this->input('plan_id'),
            'start_date' => $startDate,
            'end_date'   => $endDate,
            'status'     => 'active',
            'created_by' => auth_user()['user_id'],
            'created_at' => date('Y-m-d H:i:s'),
            'updated_at' => date('Y-m-d H:i:s'),
        ]);
        log_activity('membership.assigned','memberships',$mId);
        flash('success',"Membership assigned. Valid until $endDate.");
        $this->redirect('admin/memberships');
    }

    public function renew(string $id): void
    {
        $this->verifyCsrf();
        $tid = auth_user()['tenant_id'];
        $mem = DB::queryOne("SELECT m.*, mp.duration_days FROM memberships m JOIN membership_plans mp ON mp.plan_id=m.plan_id WHERE m.membership_id=? AND m.tenant_id=? LIMIT 1",[(int)$id,$tid]);
        if (!$mem) { flash('error','Not found.'); $this->back(); }

        $startDate = date('Y-m-d');
        $endDate   = date('Y-m-d', strtotime($startDate . ' + ' . $mem['duration_days'] . ' days'));

        DB::update('memberships',[
            'start_date'    => $startDate,
            'end_date'      => $endDate,
            'status'        => 'active',
            'renewal_count' => $mem['renewal_count'] + 1,
            'updated_at'    => date('Y-m-d H:i:s'),
        ],['membership_id'=>(int)$id,'tenant_id'=>$tid]);
        flash('success',"Membership renewed. New expiry: $endDate.");
        $this->redirect('admin/memberships');
    }
}
