<?php $pageTitle = 'My Membership'; ?>

<div class="container-fluid px-0">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4 animate-fadeIn">
                <div>
                    <h4 class="fw-700 mb-1">My Membership 🎫</h4>
                    <p class="text-muted small mb-0">Manage your active meal plans and view your subscription history.</p>
                </div>
            </div>

            <?php 
            $active = null;
            foreach($memberships as $m) { if($m['status']==='active'){ $active=$m; break; } }
            ?>
            
            <?php if($active): 
                $daysLeft = (int)((strtotime($active['end_date']) - time()) / 86400);
                $totalDays = max(1, $active['duration_days']);
                // Calculate percentage based on days ELAPSED
                $daysElapsed = $totalDays - $daysLeft;
                $percent = min(100, max(0, ($daysElapsed / $totalDays) * 100));
            ?>
            <!-- Active Plan Hero Card -->
            <div class="card border-0 shadow-lg mb-5 overflow-hidden animate-fadeInUp" style="border-radius: 28px; background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);">
                <div class="card-body p-4 p-md-5 position-relative">
                    <!-- Decorative Circle -->
                    <div class="position-absolute top-0 end-0 p-5 opacity-10 d-none d-md-block">
                        <i class="bi bi-patch-check-fill" style="font-size: 10rem;"></i>
                    </div>

                    <div class="row align-items-center position-relative" style="z-index: 1;">
                        <div class="col-md-7">
                            <span class="badge rounded-pill bg-white text-primary px-3 py-2 fw-700 x-small mb-3 shadow-sm">CURRENT ACTIVE PLAN</span>
                            <h2 class="fw-800 text-white mb-2 display-6"><?= e($active['plan_name']) ?></h2>
                            <p class="text-white opacity-75 mb-4 fs-5">Valid until <span class="fw-700 text-white"><?= date('d M, Y', strtotime($active['end_date'])) ?></span></p>
                            
                            <div class="d-flex flex-wrap gap-4 text-white">
                                <div class="bg-white bg-opacity-10 p-3 rounded-4 px-4 backdrop-blur">
                                    <div class="x-small opacity-75 mb-1">Started On</div>
                                    <div class="fw-700"><?= date('d M Y', strtotime($active['start_date'])) ?></div>
                                </div>
                                <div class="bg-white bg-opacity-10 p-3 rounded-4 px-4 backdrop-blur">
                                    <div class="x-small opacity-75 mb-1">Duration</div>
                                    <div class="fw-700"><?= $active['duration_days'] ?> Days</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-5 text-md-end mt-5 mt-md-0">
                            <div class="days-remaining-circle d-inline-flex flex-column align-items-center justify-content-center bg-white bg-opacity-10 rounded-circle border border-white border-opacity-25 backdrop-blur" 
                                 style="width: 160px; height: 160px;">
                                <div class="display-4 fw-800 text-white lh-1"><?= max(0, $daysLeft) ?></div>
                                <div class="x-small text-white opacity-75 fw-700">Days Left</div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-5">
                        <div class="d-flex justify-content-between text-white x-small mb-2 fw-700">
                            <span>Usage Progress</span>
                            <span><?= round($percent) ?>%</span>
                        </div>
                        <div class="progress rounded-pill bg-white bg-opacity-20 shadow-inner" style="height: 12px;">
                            <div class="progress-bar bg-white rounded-pill shadow-sm" style="width: <?= $percent ?>%"></div>
                        </div>
                    </div>
                </div>
            </div>
            <?php else: ?>
            <!-- No Active Plan State -->
            <div class="card border-0 shadow-sm rounded-4 mb-5 animate-fadeInUp" style="border-radius: 24px; background: #fffcf0; border: 1px solid #ffecb3 !important;">
                <div class="card-body p-5 text-center">
                    <div class="mb-4 d-inline-flex align-items-center justify-content-center bg-warning bg-opacity-10 text-warning rounded-circle" style="width: 80px; height: 80px;">
                        <i class="bi bi-exclamation-triangle-fill fs-1"></i>
                    </div>
                    <h5 class="fw-800 text-dark">No Active Membership</h5>
                    <p class="text-muted mb-4 mx-auto" style="max-width: 400px;">
                        You do not currently have an active meal plan. Please visit the admin office or contact support to renew your membership.
                    </p>
                    <a href="<?= url('student/complaints') ?>" class="btn btn-warning rounded-pill px-4 fw-700">Contact Admin</a>
                </div>
            </div>
            <?php endif; ?>

            <!-- Membership History Table -->
            <div class="card border-0 shadow-sm animate-fadeInUp stagger-1" style="border-radius: 20px;">
                <div class="card-header bg-white border-0 py-3 px-4">
                    <h6 class="fw-700 mb-0">Membership History</h6>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-surface-variant">
                                <tr>
                                    <th class="ps-4 py-3 border-0 x-small">Plan Name</th>
                                    <th class="py-3 border-0 x-small">Period</th>
                                    <th class="py-3 border-0 x-small">Status</th>
                                    <th class="pe-4 py-3 border-0 text-end x-small">Price</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach($memberships as $m): ?>
                                <tr>
                                    <td class="ps-4">
                                        <div class="fw-700 text-dark"><?= e($m['plan_name']) ?></div>
                                        <div class="x-small text-muted"><?= $m['duration_days'] ?> Days</div>
                                    </td>
                                    <td class="small">
                                        <span class="text-muted"><?= date('d M Y', strtotime($m['start_date'])) ?></span>
                                        <i class="bi bi-arrow-right mx-2 text-muted opacity-50"></i>
                                        <span class="fw-600"><?= date('d M Y', strtotime($m['end_date'])) ?></span>
                                    </td>
                                    <td>
                                        <?php if($m['status']==='active'): ?>
                                            <span class="badge rounded-pill bg-success-subtle text-success px-3">Active</span>
                                        <?php elseif($m['status']==='expired'): ?>
                                            <span class="badge rounded-pill bg-danger-subtle text-danger px-3">Expired</span>
                                        <?php else: ?>
                                            <span class="badge rounded-pill bg-secondary-subtle text-secondary px-3"><?= ucfirst($m['status']) ?></span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="pe-4 text-end fw-800 text-dark">
                                        ₹<?= number_format($m['price']) ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                                <?php if(empty($memberships)): ?>
                                <tr>
                                    <td colspan="4" class="text-center py-5 text-muted opacity-50">
                                        <i class="bi bi-clock-history fs-1 mb-2"></i>
                                        <div class="small fw-600">No membership history found.</div>
                                    </td>
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
.backdrop-blur {
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
}

.shadow-inner {
    box-shadow: inset 0 2px 4px rgba(0,0,0,0.1);
}

.x-small {
    font-size: 0.65rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.bg-success-subtle { background: #e8f5e9 !important; }
.bg-danger-subtle { background: #ffebee !important; }
.bg-secondary-subtle { background: #f5f5f5 !important; }

.days-remaining-circle {
    transition: transform 0.3s;
}

.card:hover .days-remaining-circle {
    transform: scale(1.05) rotate(5deg);
}

.table th {
    font-weight: 700;
    color: var(--text-muted);
}
</style>
