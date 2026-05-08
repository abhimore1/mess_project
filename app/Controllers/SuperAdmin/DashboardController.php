<?php
namespace App\Controllers\SuperAdmin;

use App\Core\Controller;
use DB;

class DashboardController extends Controller
{
    public function index(): void
    {
        // Platform stats
        $stats = [
            'total_tenants'   => DB::queryOne("SELECT COUNT(*) AS c FROM tenants")['c'] ?? 0,
            'active_tenants'  => DB::queryOne("SELECT COUNT(*) AS c FROM tenants WHERE status='active'")['c'] ?? 0,
            'total_students'  => DB::queryOne("SELECT COUNT(*) AS c FROM students WHERE status='active'")['c'] ?? 0,
            'total_revenue'   => DB::queryOne("SELECT COALESCE(SUM(net_amount),0) AS r FROM payments WHERE status='paid'")['r'] ?? 0,
            'this_month_rev'  => DB::queryOne("SELECT COALESCE(SUM(net_amount),0) AS r FROM payments WHERE status='paid' AND MONTH(payment_date)=MONTH(NOW()) AND YEAR(payment_date)=YEAR(NOW())")['r'] ?? 0,
        ];

        // Recent tenants
        $recentTenants = DB::query("SELECT t.*, sp.name AS plan_name,
            (SELECT COUNT(*) FROM students s WHERE s.tenant_id=t.tenant_id AND s.status='active') AS student_count
            FROM tenants t LEFT JOIN subscription_plans sp ON sp.plan_id=t.plan_id
            ORDER BY t.created_at DESC LIMIT 8");

        // Monthly revenue chart data (last 6 months)
        $revenueChart = DB::query("SELECT DATE_FORMAT(payment_date,'%b %Y') AS month,
            SUM(net_amount) AS total
            FROM payments WHERE status='paid' AND payment_date >= DATE_SUB(NOW(), INTERVAL 6 MONTH)
            GROUP BY YEAR(payment_date), MONTH(payment_date)
            ORDER BY payment_date ASC");

        // Recent activity
        $recentLogs = DB::query("SELECT al.*, u.full_name FROM activity_logs al
            LEFT JOIN users u ON u.user_id=al.user_id
            ORDER BY al.created_at DESC LIMIT 10");

        $this->view('super_admin/dashboard', compact('stats','recentTenants','revenueChart','recentLogs'), 'app');
    }
}
