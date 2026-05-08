<?php
namespace App\Controllers\Admin;

use DB;

use App\Core\Controller;

class PaymentController extends Controller
{
    public function index(): void
    {
        $tid    = auth_user()['tenant_id'];
        $page   = (int)$this->input('page',1);
        $status = $this->input('status','');
        $from   = $this->input('from', date('Y-m-01'));
        $to     = $this->input('to',   date('Y-m-d'));

        $sql = "SELECT p.*, s.full_name AS student_name FROM payments p
                JOIN students s ON s.student_id=p.student_id
                WHERE p.tenant_id=? AND p.payment_date BETWEEN ? AND ?";
        $params = [$tid,$from,$to];
        if ($status) { $sql .= " AND p.status=?"; $params[] = $status; }

        $total   = DB::queryOne("SELECT COUNT(*) AS c FROM ($sql) x",$params)['c']??0;
        $perPage = 20;
        $offset  = ($page-1)*$perPage;
        $payments = DB::query("$sql ORDER BY p.payment_date DESC, p.payment_id DESC LIMIT $perPage OFFSET $offset",$params);
        $pagination = ['current_page'=>$page,'last_page'=>(int)ceil($total/$perPage),'total'=>$total,'per_page'=>$perPage];

        $totals = DB::queryOne("SELECT COALESCE(SUM(net_amount),0) AS total_paid, COALESCE(SUM(discount),0) AS total_discount
            FROM payments WHERE tenant_id=? AND status='paid' AND payment_date BETWEEN ? AND ?",$params);

        $pageTitle = 'Payments';
        $this->view('admin/payments/index', compact('payments','pagination','status','from','to','totals','pageTitle'), 'app');
    }

    public function create(): void
    {
        $tid      = auth_user()['tenant_id'];
        $students = DB::query("SELECT student_id, full_name, phone FROM students WHERE tenant_id=? AND status='active' ORDER BY full_name",[$tid]);
        $plans    = DB::query("SELECT plan_id, name, price FROM membership_plans WHERE tenant_id=? AND is_active=1 ORDER BY name",[$tid]);
        $pageTitle = 'Collect Payment';
        $this->view('admin/payments/create', compact('students','plans','pageTitle'), 'app');
    }

    public function store(): void
    {
        $this->verifyCsrf();
        $this->requirePermission('payments.create');
        $tid = auth_user()['tenant_id'];

        $errors = $this->validate(['student_id'=>'required|numeric','amount'=>'required|numeric','payment_date'=>'required']);
        if ($errors) { flash('error',array_values($errors)[0]); $this->back(); }

        $amount    = (float)$this->input('amount');
        $discount  = (float)$this->input('discount', 0);
        $netAmount = $amount - $discount;

        // Auto-generate receipt number
        $lastReceipt = DB::queryOne("SELECT receipt_number FROM payments WHERE tenant_id=? ORDER BY payment_id DESC LIMIT 1",[$tid]);
        $num   = $lastReceipt ? (int)preg_replace('/\D/','',$lastReceipt['receipt_number']??'0') + 1 : 1;
        $receipt = 'RCP-' . str_pad($num, 5, '0', STR_PAD_LEFT);

        DB::beginTransaction();
        try {
            $paymentId = DB::insert('payments',[
                'tenant_id'      => $tid,
                'student_id'     => (int)$this->input('student_id'),
                'membership_id'  => $this->input('membership_id') ?: null,
                'amount'         => $amount,
                'discount'       => $discount,
                'net_amount'     => $netAmount,
                'payment_mode'   => $this->input('payment_mode','cash'),
                'transaction_ref'=> $this->input('transaction_ref'),
                'payment_date'   => $this->input('payment_date'),
                'due_date'       => $this->input('due_date') ?: null,
                'status'         => 'paid',
                'receipt_number' => $receipt,
                'notes'          => $this->input('notes'),
                'created_by'     => auth_user()['user_id'],
                'created_at'     => date('Y-m-d H:i:s'),
                'updated_at'     => date('Y-m-d H:i:s'),
            ]);

            // Log history
            DB::insert('payment_history',[
                'payment_id'  => $paymentId,
                'tenant_id'   => $tid,
                'changed_by'  => auth_user()['user_id'],
                'old_status'  => null,
                'new_status'  => 'paid',
                'note'        => 'Payment collected',
                'created_at'  => date('Y-m-d H:i:s'),
            ]);

            DB::commit();
            log_activity('payment.created','payments',$paymentId);
            flash('success',"Payment recorded. Receipt: $receipt");
            $this->redirect("admin/payments/$paymentId/receipt");
        } catch (\Throwable $e) {
            DB::rollBack();
            flash('error','Failed: '.$e->getMessage());
            $this->back();
        }
    }

    public function receipt(string $id): void
    {
        $tid     = auth_user()['tenant_id'];
        $payment = DB::queryOne("SELECT p.*, s.full_name AS student_name, s.phone, s.room_number,
            mp.name AS plan_name FROM payments p
            JOIN students s ON s.student_id=p.student_id
            LEFT JOIN memberships m ON m.membership_id=p.membership_id
            LEFT JOIN membership_plans mp ON mp.plan_id=m.plan_id
            WHERE p.payment_id=? AND p.tenant_id=? LIMIT 1",[(int)$id,$tid]);
        if (!$payment) $this->abort(404);

        $tenant  = DB::queryOne("SELECT * FROM tenants WHERE tenant_id=? LIMIT 1",[$tid]);
        $pageTitle = 'Receipt — '.$payment['receipt_number'];
        $this->view('admin/payments/receipt', compact('payment','tenant','pageTitle'), 'app');
    }

    public function downloadPdf(string $id): void
    {
        // PDF generation stub — replace with TCPDF implementation
        $this->json(['info'=>'PDF export — integrate TCPDF here']);
    }
}
