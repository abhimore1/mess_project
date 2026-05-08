<?php
namespace App\Controllers\Admin;

use DB;
use App\Core\Controller;

class ReportController extends Controller
{
    public function __construct() { $this->requireModule('reports'); }

    public function index(): void
    {
        $tid = auth_user()['tenant_id'];
        $this->view('admin/reports/index', ['pageTitle'=>'Reports','tid'=>$tid], 'app');
    }

    public function export(): void
    {
        $type = $this->input('type','payments');
        $tid  = auth_user()['tenant_id'];
        $from = $this->input('from', date('Y-m-01'));
        $to   = $this->input('to',   date('Y-m-d'));

        if ($type === 'payments') {
            $data = DB::query("SELECT p.receipt_number, s.full_name AS student, p.amount, p.discount, p.net_amount, p.payment_mode, p.payment_date, p.status FROM payments p JOIN students s ON s.student_id=p.student_id WHERE p.tenant_id=? AND p.payment_date BETWEEN ? AND ? ORDER BY p.payment_date DESC",[$tid,$from,$to]);
            $this->exportCsv($data, "payments_$from\_$to.csv");
        } elseif ($type === 'students') {
            $data = DB::query("SELECT reg_number, full_name, phone, email, room_number, status, joined_at FROM students WHERE tenant_id=? ORDER BY full_name",[$tid]);
            $this->exportCsv($data, "students.csv");
        } elseif ($type === 'attendance') {
            $data = DB::query("SELECT s.full_name, ms.name AS slot, sa.date, sa.status FROM student_attendance sa JOIN students s ON s.student_id=sa.student_id JOIN meal_slots ms ON ms.slot_id=sa.slot_id WHERE sa.tenant_id=? AND sa.date BETWEEN ? AND ? ORDER BY sa.date, s.full_name",[$tid,$from,$to]);
            $this->exportCsv($data, "attendance_$from\_$to.csv");
        }
    }

    private function exportCsv(array $data, string $filename): never
    {
        header('Content-Type: text/csv; charset=utf-8');
        header("Content-Disposition: attachment; filename=\"$filename\"");
        $out = fopen('php://output','w');
        if (!empty($data)) fputcsv($out, array_keys($data[0]));
        foreach ($data as $row) fputcsv($out, $row);
        fclose($out);
        exit;
    }
}
