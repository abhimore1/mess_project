<?php
/**
 * @var array $stats
 * @var array $recentTenants
 * @var array $revenueChart
 * @var array $recentLogs
 */
$pageTitle = 'Platform Dashboard';
?>
<!-- Stats Row -->
<div class="row g-3 mb-4">
    <?php
    $cards = [
        ['label'=>'Total Messes',    'value'=>$stats['total_tenants'],                    'icon'=>'bi-building',           'color'=>'#6366f1', 'bg'=>'rgba(99,102,241,.15)'],
        ['label'=>'Active Messes',   'value'=>$stats['active_tenants'],                   'icon'=>'bi-check-circle',       'color'=>'#10b981', 'bg'=>'rgba(16,185,129,.15)'],
        ['label'=>'Total Students',  'value'=>number_format($stats['total_students']),     'icon'=>'bi-people-fill',        'color'=>'#06b6d4', 'bg'=>'rgba(6,182,212,.15)'],
        ['label'=>'Monthly Revenue', 'value'=>'₹'.number_format($stats['this_month_rev']),'icon'=>'bi-currency-rupee',     'color'=>'#f59e0b', 'bg'=>'rgba(245,158,11,.15)'],
    ];
    foreach ($cards as $c): ?>
    <div class="col-6 col-xl-3">
        <div class="stat-card">
            <div class="d-flex align-items-start justify-content-between mb-3">
                <div class="stat-icon" style="background:<?= $c['bg'] ?>;color:<?= $c['color'] ?>">
                    <i class="bi <?= $c['icon'] ?>"></i>
                </div>
            </div>
            <div class="stat-value"><?= $c['value'] ?></div>
            <div class="stat-label"><?= $c['label'] ?></div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<div class="row g-3 mb-4">
    <!-- Revenue Chart -->
    <div class="col-lg-8">
        <div class="panel">
            <div class="panel-header">
                <h6><i class="bi bi-bar-chart-line me-2 text-primary"></i>Revenue — Last 6 Months</h6>
            </div>
            <div class="panel-body">
                <div id="revenueChart" style="min-height:280px"></div>
            </div>
        </div>
    </div>
    <!-- Quick stats -->
    <div class="col-lg-4">
        <div class="panel h-100">
            <div class="panel-header"><h6><i class="bi bi-activity me-2 text-accent"></i>Platform Health</h6></div>
            <div class="panel-body">
                <?php
                $health = [
                    ['label'=>'Subscriptions expiring (7d)', 'val'=> DB::queryOne("SELECT COUNT(*) AS c FROM tenant_subscriptions WHERE status='active' AND expires_at BETWEEN NOW() AND DATE_ADD(NOW(),INTERVAL 7 DAY)")['c']??0, 'color'=>'warning'],
                    ['label'=>'Dues pending',  'val'=> DB::queryOne("SELECT COUNT(*) AS c FROM payments WHERE status='pending'")['c']??0, 'color'=>'danger'],
                    ['label'=>'Open complaints','val'=> DB::queryOne("SELECT COUNT(*) AS c FROM complaints WHERE status='open'")['c']??0, 'color'=>'info'],
                    ['label'=>'Students active','val'=> number_format($stats['total_students']), 'color'=>'success'],
                ];
                foreach ($health as $h): ?>
                <div class="d-flex align-items-center justify-content-between py-2 border-bottom" style="border-color:var(--border)!important">
                    <span class="small text-muted"><?= $h['label'] ?></span>
                    <span class="badge bg-<?= $h['color'] ?>"><?= $h['val'] ?></span>
                </div>
                <?php endforeach; ?>
                <div class="mt-3 d-grid gap-2">
                    <a href="<?= url('super/tenants/create') ?>" class="btn btn-sm btn-primary-g">
                        <i class="bi bi-plus-circle me-1"></i> Add New Mess
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tenant Table + Activity Log -->
<div class="row g-3">
    <div class="col-lg-8">
        <div class="panel">
            <div class="panel-header">
                <h6><i class="bi bi-building me-2"></i>Recent Messes</h6>
                <a href="<?= url('super/tenants') ?>" class="btn btn-sm btn-primary-g">View All</a>
            </div>
            <div class="table-responsive">
                <table class="table">
                    <thead><tr>
                        <th>Mess Name</th><th>Plan</th><th>Students</th><th>Status</th><th>Actions</th>
                    </tr></thead>
                    <tbody>
                    <?php foreach ($recentTenants as $t): ?>
                    <tr>
                        <td>
                            <div class="fw-600 small"><?= e($t['name']) ?></div>
                            <div class="text-muted" style="font-size:.75rem"><?= e($t['slug']) ?></div>
                        </td>
                        <td><span class="small"><?= e($t['plan_name'] ?? '—') ?></span></td>
                        <td><?= number_format($t['student_count']) ?></td>
                        <td><?= badge($t['status']) ?></td>
                        <td>
                            <a href="<?= url('super/tenants/'.$t['tenant_id'].'/edit') ?>" class="btn btn-sm btn-outline-secondary" style="border-radius:8px;font-size:.75rem">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <a href="<?= url('super/tenants/'.$t['tenant_id'].'/modules') ?>" class="btn btn-sm btn-outline-primary ms-1" style="border-radius:8px;font-size:.75rem">
                                <i class="bi bi-puzzle"></i>
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <!-- Activity log -->
    <div class="col-lg-4">
        <div class="panel">
            <div class="panel-header"><h6><i class="bi bi-journal-text me-2"></i>Recent Activity</h6></div>
            <div class="panel-body p-0">
                <ul class="list-unstyled mb-0">
                <?php foreach ($recentLogs as $log): ?>
                <li class="d-flex gap-3 p-3 border-bottom" style="border-color:var(--border)!important">
                    <div class="flex-shrink-0 mt-1">
                        <div style="width:8px;height:8px;border-radius:50%;background:var(--primary);margin-top:4px"></div>
                    </div>
                    <div style="min-width:0">
                        <div class="small fw-500"><?= e($log['action']) ?></div>
                        <div class="text-muted" style="font-size:.73rem"><?= e($log['full_name'] ?? 'System') ?> &bull; <?= format_date($log['created_at'],'d M H:i') ?></div>
                    </div>
                </li>
                <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>
</div>

<script>
const chartData = <?= json_encode(array_values($revenueChart)) ?>;
const opts = {
    series: [{ name: 'Revenue (₹)', data: chartData.map(d => parseFloat(d.total)) }],
    chart: { type: 'area', height: 280, toolbar: { show: false }, foreColor: 'var(--muted)', background: 'transparent' },
    stroke: { curve: 'smooth', width: 2 },
    fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.4, opacityTo: 0.05 } },
    colors: ['#6366f1'],
    xaxis: { categories: chartData.map(d => d.month), axisBorder: { show: false } },
    yaxis: { labels: { formatter: v => '₹'+v.toLocaleString('en-IN') } },
    tooltip: { theme: 'dark' },
    grid: { borderColor: 'var(--border)' },
};
new ApexCharts(document.getElementById('revenueChart'), opts).render();
</script>
