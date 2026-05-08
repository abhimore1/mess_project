<?php
namespace App\Controllers\Admin;

use DB;

use App\Core\Controller;

class DashboardController extends Controller
{
    public function index(): void
    {
        $tid = auth_user()['tenant_id'];

        $stats = [
            'total_students'   => DB::queryOne("SELECT COUNT(*) AS c FROM students WHERE tenant_id=? AND status='active'",[$tid])['c']??0,
            'today_collection' => DB::queryOne("SELECT COALESCE(SUM(net_amount),0) AS r FROM payments WHERE tenant_id=? AND status='paid' AND DATE(payment_date)=CURDATE()",[$tid])['r']??0,
            'pending_dues'     => DB::queryOne("SELECT COALESCE(SUM(net_amount),0) AS r FROM payments WHERE tenant_id=? AND status='pending'",[$tid])['r']??0,
            'expiring_soon'    => DB::queryOne("SELECT COUNT(*) AS c FROM memberships WHERE tenant_id=? AND status='active' AND end_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(),INTERVAL 7 DAY)",[$tid])['c']??0,
        ];

        // Attendance today per slot
        $todaySlots = DB::query("SELECT ms.name, ms.slot_time, ms.meal_type,
            SUM(CASE WHEN sa.status='present' THEN 1 ELSE 0 END) AS present_count,
            SUM(CASE WHEN sa.status='absent'  THEN 1 ELSE 0 END) AS absent_count
            FROM meal_slots ms
            LEFT JOIN student_attendance sa ON sa.slot_id=ms.slot_id AND sa.date=CURDATE() AND sa.tenant_id=?
            WHERE ms.tenant_id=? AND ms.is_active=1
            GROUP BY ms.slot_id ORDER BY ms.sort_order", [$tid,$tid]);

        // Recent payments
        $recentPayments = DB::query("SELECT p.*, s.full_name AS student_name
            FROM payments p JOIN students s ON s.student_id=p.student_id
            WHERE p.tenant_id=? ORDER BY p.created_at DESC LIMIT 8", [$tid]);

        // Monthly revenue (last 6 months)
        $revenueChart = DB::query("SELECT DATE_FORMAT(payment_date,'%b') AS month, SUM(net_amount) AS total
            FROM payments WHERE tenant_id=? AND status='paid' AND payment_date >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
            GROUP BY YEAR(payment_date), MONTH(payment_date) ORDER BY payment_date ASC", [$tid]);

        // Expiring memberships
        $expiringMemberships = DB::query("SELECT m.*, s.full_name AS student_name, mp.name AS plan_name
            FROM memberships m JOIN students s ON s.student_id=m.student_id
            JOIN membership_plans mp ON mp.plan_id=m.plan_id
            WHERE m.tenant_id=? AND m.status='active' AND m.end_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(),INTERVAL 7 DAY)
            ORDER BY m.end_date ASC LIMIT 5", [$tid]);

        $pageTitle = 'Dashboard';
        $this->view('admin/dashboard', compact('stats','todaySlots','recentPayments','revenueChart','expiringMemberships','pageTitle'), 'app');
    }
}
