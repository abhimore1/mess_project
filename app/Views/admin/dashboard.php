<?php
/**
 * Mess Admin Dashboard view - Google Material Design 3
 * @var array $stats, $todaySlots, $recentPayments, $revenueChart, $expiringMemberships
 */
?>
<!-- Stats Cards with Animation -->
<div class="row" style="margin:-8px;margin-bottom:calc(var(--space-5) - 8px)">
    <div class="col-6 col-xl-3 animate-fadeInUp stagger-1" style="padding:8px">
        <div class="stat-card-outlined" style="height:100%">
            <div class="stat-icon stat-icon-primary">
                <i class="bi bi-people-fill"></i>
            </div>
            <div class="stat-content">
                <div class="stat-value"><?= number_format($stats['total_students']) ?></div>
                <div class="stat-label">Active Students</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-3 animate-fadeInUp stagger-2" style="padding:8px">
        <div class="stat-card-outlined" style="height:100%">
            <div class="stat-icon stat-icon-success">
                <i class="bi bi-cash-coin"></i>
            </div>
            <div class="stat-content">
                <div class="stat-value"><?= format_currency($stats['today_collection']) ?></div>
                <div class="stat-label">Today Collection</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-3 animate-fadeInUp stagger-3" style="padding:8px">
        <div class="stat-card-outlined" style="height:100%">
            <div class="stat-icon stat-icon-error">
                <i class="bi bi-exclamation-circle-fill"></i>
            </div>
            <div class="stat-content">
                <div class="stat-value"><?= format_currency($stats['pending_dues']) ?></div>
                <div class="stat-label">Pending Dues</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-xl-3 animate-fadeInUp stagger-4" style="padding:8px">
        <div class="stat-card-outlined" style="height:100%">
            <div class="stat-icon stat-icon-warning">
                <i class="bi bi-calendar-x-fill"></i>
            </div>
            <div class="stat-content">
                <div class="stat-value"><?= $stats['expiring_soon'] ?></div>
                <div class="stat-label">Expiring (7d)</div>
            </div>
        </div>
    </div>
</div>

<!-- Today Meal Slot Attendance -->
<?php if (module_enabled('attendance') && !empty($todaySlots)): ?>
<div class="card mb-5 animate-fadeInUp stagger-5">
    <div class="card-header">
        <h6 class="card-title"><i class="bi bi-calendar-check" style="color:var(--primary)"></i>Today's Attendance by Meal Slot</h6>
        <a href="<?= url('admin/attendance') ?>" class="btn btn-primary btn-sm">Mark Attendance</a>
    </div>
    <div class="card-body">
        <div class="row" style="margin:-8px">
        <?php foreach ($todaySlots as $slot): ?>
        <div class="col-sm-6 col-md-3" style="padding:8px">
            <div class="text-center p-4" style="background:var(--surface-container);border-radius:var(--radius-lg);transition:all var(--transition-fast);height:100%" onmouseover="this.style.background='var(--surface-container-high)'" onmouseout="this.style.background='var(--surface-container)'">
                <div class="font-semibold mb-1"><?= e($slot['name']) ?></div>
                <div class="text-secondary text-label mb-3"><?= e($slot['slot_time'] ?? '') ?></div>
                <div class="d-flex justify-content-center gap-4">
                    <div>
                        <div class="fw-bold fs-4" style="color:var(--success)"><?= $slot['present_count'] ?></div>
                        <div class="text-label text-tertiary">Present</div>
                    </div>
                    <div>
                        <div class="fw-bold fs-4" style="color:var(--error)"><?= $slot['absent_count'] ?></div>
                        <div class="text-label text-tertiary">Absent</div>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
        </div>
    </div>
</div>
<?php endif; ?>

<div class="row" style="margin:-8px">
    <!-- Revenue chart -->
    <div class="col-lg-8 animate-fadeInUp stagger-5" style="padding:8px">
        <div class="card">
            <div class="card-header">
                <h6 class="card-title"><i class="bi bi-bar-chart-line"></i>Monthly Collection</h6>
            </div>
            <div class="card-body">
                <div id="revChart" style="min-height:280px"></div>
            </div>
        </div>
    </div>
    <!-- Expiring memberships -->
    <div class="col-lg-4 animate-fadeInUp stagger-6" style="padding:8px">
        <div class="card">
            <div class="card-header">
                <h6 class="card-title"><i class="bi bi-clock" style="color:var(--warning)"></i>Expiring Memberships</h6>
            </div>
            <div class="card-body p-0">
                <?php if (empty($expiringMemberships)): ?>
                <div class="text-center py-5">
                    <i class="bi bi-check-circle-fill fs-1" style="color:var(--success);opacity:0.5"></i>
                    <p class="text-secondary mt-3 text-label">No memberships expiring soon.</p>
                </div>
                <?php else: ?>
                <div class="list-group">
                <?php foreach ($expiringMemberships as $m): ?>
                <div class="list-item">
                    <div class="user-avatar" style="width:36px;height:36px;font-size:var(--font-size-sm)"><?= strtoupper(substr($m['student_name'],0,1)) ?></div>
                    <div class="list-item-content">
                        <div class="list-item-title"><?= e($m['student_name']) ?></div>
                        <div class="list-item-subtitle"><?= e($m['plan_name']) ?> • Expires <?= format_date($m['end_date']) ?></div>
                    </div>
                    <span class="badge badge-warning"><?= days_until($m['end_date']) ?>d</span>
                </div>
                <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Recent Payments -->
<div class="card animate-fadeIn" style="margin-top:16px">
    <div class="card-header">
        <h6 class="card-title"><i class="bi bi-receipt"></i>Recent Payments</h6>
        <a href="<?= url('admin/payments/create') ?>" class="btn btn-primary btn-sm"><i class="bi bi-plus-lg"></i>Collect Payment</a>
    </div>
    <div class="table-responsive">
        <table class="table">
            <thead>
                <tr>
                    <th>Student</th>
                    <th>Amount</th>
                    <th>Mode</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Receipt</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($recentPayments as $p): ?>
            <tr>
                <td class="font-medium"><?= e($p['student_name']) ?></td>
                <td class="font-semibold"><?= format_currency($p['net_amount']) ?></td>
                <td><span class="chip chip-assist"><?= ucfirst($p['payment_mode']) ?></span></td>
                <td class="text-secondary"><?= format_date($p['payment_date']) ?></td>
                <td>
                    <?php if($p['status'] === 'completed'): ?>
                        <span class="badge badge-success"><?= ucfirst($p['status']) ?></span>
                    <?php elseif($p['status'] === 'pending'): ?>
                        <span class="badge badge-warning"><?= ucfirst($p['status']) ?></span>
                    <?php else: ?>
                        <span class="badge badge-secondary"><?= ucfirst($p['status']) ?></span>
                    <?php endif; ?>
                </td>
                <td>
                    <a href="<?= url('admin/payments/'.$p['payment_id'].'/receipt') ?>" class="btn btn-icon btn-icon-sm" style="color:var(--primary)">
                        <i class="bi bi-printer"></i>
                    </a>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script>
const rv = <?= json_encode(array_values($revenueChart)) ?>;
new ApexCharts(document.getElementById('revChart'),{
    series:[{name:'Collection (₹)',data:rv.map(d=>parseFloat(d.total))}],
    chart:{
        type:'bar',
        height:280,
        toolbar:{show:false},
        fontFamily:'Google Sans, sans-serif',
        background:'transparent'
    },
    colors:['var(--primary)'],
    xaxis:{
        categories:rv.map(d=>d.month),
        labels:{style:{colors:'var(--text-secondary)',fontSize:'12px'}}
    },
    yaxis:{
        labels:{
            formatter:v=>'₹'+v.toLocaleString('en-IN'),
            style:{colors:'var(--text-secondary)',fontSize:'12px'}
        }
    },
    tooltip:{
        theme:'light',
        style:{fontFamily:'Google Sans, sans-serif'},
        y:{formatter:v=>'₹'+v.toLocaleString('en-IN')}
    },
    grid:{
        borderColor:'var(--outline-variant)',
        strokeDashArray:4
    },
    plotOptions:{
        bar:{
            borderRadius:8,
            columnWidth:'60%',
            dataLabels:{position:'top'}
        }
    },
    dataLabels:{
        enabled:false
    },
    fill:{
        type:'gradient',
        gradient:{
            shade:'light',
            type:'vertical',
            shadeIntensity:0.5,
            gradientToColors:['var(--tertiary)'],
            inverseColors:false,
            opacityFrom:1,
            opacityTo:0.8,
            stops:[0,100]
        }
    }
}).render();
</script>
