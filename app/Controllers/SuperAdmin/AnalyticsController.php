<?php
namespace App\Controllers\SuperAdmin;

use App\Core\Controller;
use DB;

class AnalyticsController extends Controller
{
    public function index(): void
    {
        $monthly = DB::query("SELECT DATE_FORMAT(payment_date,'%b %Y') AS month, SUM(net_amount) AS total, COUNT(*) AS count FROM payments WHERE status='paid' GROUP BY YEAR(payment_date),MONTH(payment_date) ORDER BY payment_date DESC LIMIT 12");
        $tenantStats = DB::query("SELECT t.name, COUNT(s.student_id) AS students, COALESCE(SUM(p.net_amount),0) AS revenue FROM tenants t LEFT JOIN students s ON s.tenant_id=t.tenant_id AND s.status='active' LEFT JOIN payments p ON p.tenant_id=t.tenant_id AND p.status='paid' GROUP BY t.tenant_id ORDER BY revenue DESC LIMIT 10");
        $pageTitle = 'Analytics';
        $this->view('super_admin/analytics', compact('monthly','tenantStats','pageTitle'), 'app');
    }
}
