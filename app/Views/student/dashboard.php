<?php $pageTitle = 'My Dashboard'; ?>

<div class="container-fluid px-0">
    <!-- Welcome Header -->
    <div class="welcome-banner mb-4 animate-fadeIn">
        <div class="banner-content p-4 p-md-5">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="badge rounded-pill bg-white bg-opacity-20 backdrop-blur text-white px-3 py-1 x-small shadow-sm">
                            <i class="bi bi-stars me-1"></i> Student Portal
                        </div>
                    </div>
                    <h1 class="fw-800 mb-2 text-white display-6">Hello, <?= e(explode(' ', $student['full_name'])[0]) ?>! 👋</h1>
                    <p class="text-white opacity-90 mb-0 fs-5 fw-500">Welcome back to your mess portal. Here's what's happening today.</p>
                </div>
                <div class="col-md-4 text-md-end mt-4 mt-md-0">
                    <div class="d-flex align-items-center justify-content-md-end gap-4">
                        <div class="text-end text-white d-none d-lg-block">
                            <div class="fs-5 fw-700"><?= date('l') ?></div>
                            <div class="opacity-75"><?= date('d M, Y') ?></div>
                        </div>
                        <div class="avatar-banner-wrapper">
                            <div class="avatar-banner shadow-lg">
                                <?php if ($student['photo_path']): ?>
                                    <img src="<?= url($student['photo_path']) ?>" class="w-100 h-100 object-fit-cover">
                                <?php else: ?>
                                    <span class="fs-2 fw-bold text-primary"><?= strtoupper(substr($student['full_name'], 0, 1)) ?></span>
                                <?php endif; ?>
                            </div>
                            <div class="avatar-ring"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <!-- Membership Status -->
        <div class="col-lg-4 col-md-6">
            <div class="card h-100 border-0 shadow-sm overflow-hidden animate-fadeInUp" style="border-radius: 20px;">
                <div class="card-header bg-white border-0 py-3 px-4">
                    <h6 class="fw-700 mb-0 d-flex align-items-center">
                        <i class="bi bi-card-checklist me-2 text-primary"></i>My Membership
                    </h6>
                </div>
                <div class="card-body p-4 pt-0">
                    <?php if ($activeMembership): ?>
                        <div class="membership-card p-3 rounded-4 mb-3" style="background: var(--primary-container);">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <span class="badge rounded-pill bg-primary px-3"><?= e($activeMembership['plan_name']) ?></span>
                                <span class="fw-700 text-primary h5 mb-0">₹<?= number_format($activeMembership['price']) ?></span>
                            </div>
                            <div class="small text-primary fw-600 mb-1">Expires on: <?= date('d M Y', strtotime($activeMembership['end_date'])) ?></div>
                            <div class="progress mb-2" style="height: 6px; background: rgba(0,0,0,0.05);">
                                <?php 
                                    $daysLeft = $activeMembership['days_left'];
                                    $progress = max(0, min(100, ($daysLeft / 30) * 100)); // Simple 30-day base
                                ?>
                                <div class="progress-bar bg-primary" style="width: <?= $progress ?>%"></div>
                            </div>
                            <div class="d-flex justify-content-between x-small text-primary opacity-75">
                                <span><?= $daysLeft ?> days remaining</span>
                                <span>Active</span>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-4 bg-surface-variant rounded-4 mb-3">
                            <i class="bi bi-exclamation-triangle fs-1 text-warning mb-2"></i>
                            <div class="fw-700 small">No Active Plan</div>
                            <div class="x-small text-muted">Visit office to renew</div>
                        </div>
                    <?php endif; ?>
                    
                    <div class="d-grid gap-2">
                        <a href="<?= url('student/membership') ?>" class="btn btn-outline-primary border-2 rounded-3 py-2 fw-600 small">
                            View Plan Details
                        </a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Today's Menu -->
        <div class="col-lg-4 col-md-6">
            <div class="card h-100 border-0 shadow-sm animate-fadeInUp stagger-1" style="border-radius: 20px;">
                <div class="card-header bg-white border-0 py-3 px-4">
                    <h6 class="fw-700 mb-0 d-flex align-items-center">
                        <i class="bi bi-journal-text me-2 text-success"></i>Today's Menu
                    </h6>
                </div>
                <div class="card-body p-4 pt-0">
                    <?php if ($todayMenu): ?>
                        <div class="menu-list">
                            <?php foreach ($todayMenu as $item): ?>
                                <div class="menu-item d-flex gap-3 mb-3 p-3 bg-surface-variant rounded-4">
                                    <div class="menu-icon bg-white shadow-sm rounded-circle d-flex align-items-center justify-content-center" style="width:40px;height:40px;flex-shrink:0;">
                                        <i class="bi bi-egg-fried text-success"></i>
                                    </div>
                                    <div>
                                        <div class="fw-700 small"><?= e($item['slot_name']) ?></div>
                                        <div class="x-small text-muted mb-1"><?= e($item['slot_time']) ?></div>
                                        <div class="small fw-500 text-dark"><?= e($item['items']) ?></div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-5 opacity-50">
                            <i class="bi bi-calendar-x fs-1 mb-2"></i>
                            <div class="small fw-600">No menu published for today</div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Attendance Summary -->
        <div class="col-lg-4">
            <div class="card h-100 border-0 shadow-sm animate-fadeInUp stagger-2" style="border-radius: 20px;">
                <div class="card-header bg-white border-0 py-3 px-4">
                    <h6 class="fw-700 mb-0 d-flex align-items-center">
                        <i class="bi bi-calendar-check me-2 text-info"></i>Attendance (Last 7 Days)
                    </h6>
                </div>
                <div class="card-body p-4 pt-0">
                    <?php if ($attendanceSummary): ?>
                        <div class="timeline-compact">
                            <?php 
                                $currentDate = '';
                                foreach ($attendanceSummary as $att): 
                            ?>
                                <?php if ($currentDate !== $att['date']): ?>
                                    <div class="timeline-date small fw-700 text-muted mt-3 mb-2"><?= date('D, d M', strtotime($att['date'])) ?></div>
                                    <?php $currentDate = $att['date']; ?>
                                <?php endif; ?>
                                <div class="d-flex align-items-center justify-content-between mb-2 ps-2 border-start border-2 border-surface-variant">
                                    <div class="small"><?= e($att['slot_name']) ?></div>
                                    <span class="badge rounded-pill <?= $att['status']==='present'?'bg-success-subtle text-success':'bg-danger-subtle text-danger' ?> x-small">
                                        <?= ucfirst($att['status']) ?>
                                    </span>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-5 opacity-50">
                            <i class="bi bi-clock-history fs-1 mb-2"></i>
                            <div class="small fw-600">No attendance records yet</div>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>

        <!-- Recent Payments -->
        <div class="col-12">
            <div class="card border-0 shadow-sm animate-fadeInUp stagger-3" style="border-radius: 20px;">
                <div class="card-header bg-white border-0 py-3 px-4 d-flex justify-content-between align-items-center">
                    <h6 class="fw-700 mb-0 d-flex align-items-center">
                        <i class="bi bi-credit-card me-2 text-primary"></i>Recent Payments
                    </h6>
                    <a href="<?= url('student/payments') ?>" class="btn btn-sm btn-light rounded-pill px-3 x-small fw-700">View All</a>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-surface-variant">
                                <tr>
                                    <th class="ps-4 py-3 border-0 x-small">Date</th>
                                    <th class="py-3 border-0 x-small">Receipt No</th>
                                    <th class="py-3 border-0 x-small">Amount</th>
                                    <th class="py-3 border-0 x-small">Status</th>
                                    <th class="pe-4 py-3 border-0 text-end x-small">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($recentPayments as $p): ?>
                                    <tr>
                                        <td class="ps-4 small"><?= date('d M Y', strtotime($p['payment_date'])) ?></td>
                                        <td class="small fw-600"><?= e($p['receipt_number']) ?></td>
                                        <td class="small fw-700">₹<?= number_format($p['net_amount']) ?></td>
                                        <td>
                                            <span class="badge rounded-pill bg-success-subtle text-success x-small"><?= ucfirst($p['status']) ?></span>
                                        </td>
                                        <td class="pe-4 text-end">
                                            <a href="<?= url("student/payments/{$p['payment_id']}/receipt") ?>" class="btn btn-icon btn-icon-sm btn-light">
                                                <i class="bi bi-file-earmark-pdf"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                                <?php if (empty($recentPayments)): ?>
                                    <tr>
                                        <td colspan="5" class="text-center py-4 text-muted small">No payment history found.</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.welcome-banner {
    background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
    border-radius: 32px;
    position: relative;
    overflow: hidden;
    box-shadow: 0 20px 40px rgba(0,0,0,0.1);
}

.welcome-banner::before {
    content: '';
    position: absolute;
    top: -20%;
    right: -10%;
    width: 400px;
    height: 400px;
    background: radial-gradient(circle, rgba(255,255,255,0.15) 0%, transparent 70%);
    border-radius: 50%;
}

.backdrop-blur {
    backdrop-filter: blur(8px);
    -webkit-backdrop-filter: blur(8px);
}

.avatar-banner-wrapper {
    position: relative;
    padding: 10px;
}

.avatar-banner {
    width: 100px;
    height: 100px;
    background: white;
    border-radius: 24px;
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    position: relative;
    z-index: 2;
    transform: rotate(-3deg);
    transition: transform 0.3s ease;
}

.avatar-banner:hover {
    transform: rotate(0deg) scale(1.05);
}

.avatar-ring {
    position: absolute;
    top: 0;
    right: 0;
    width: 110px;
    height: 110px;
    border: 2px dashed rgba(255,255,255,0.3);
    border-radius: 28px;
    z-index: 1;
    animation: rotate 20s linear infinite;
}

@keyframes rotate {
    from { transform: rotate(0deg); }
    to { transform: rotate(360deg); }
}

.bg-success-subtle { background: #e8f5e9; }
.bg-danger-subtle { background: #ffebee; }

.x-small {
    font-size: 0.7rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.progress {
    overflow: visible;
}

.timeline-compact {
    max-height: 300px;
    overflow-y: auto;
    padding-right: 5px;
}

/* Custom Scrollbar */
.timeline-compact::-webkit-scrollbar {
    width: 4px;
}
.timeline-compact::-webkit-scrollbar-thumb {
    background: var(--outline-variant);
    border-radius: 10px;
}
</style>
