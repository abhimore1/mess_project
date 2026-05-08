<?php $pageTitle='My Membership'; ?>
<div class="row justify-content-center">
<div class="col-lg-9">
    
    <?php 
    $active = null;
    foreach($memberships as $m) { if($m['status']==='active'){ $active=$m; break; } }
    ?>
    
    <?php if($active): 
        $daysLeft = (int)((strtotime($active['end_date']) - time()) / 86400);
        $percent = min(100, max(0, 100 - (($daysLeft / max(1, $active['duration_days'])) * 100)));
    ?>
    <div class="panel bg-gradient mb-4 border-0 shadow" style="background:linear-gradient(135deg,var(--primary),var(--accent));color:#fff">
        <div class="panel-body p-4 p-md-5">
            <div class="row align-items-center">
                <div class="col-md-7">
                    <div class="badge bg-white text-primary mb-2">Active Plan</div>
                    <h3 class="fw-800 text-white mb-1"><?= e($active['plan_name']) ?></h3>
                    <p class="opacity-75 mb-3">Valid until <?= format_date($active['end_date']) ?></p>
                    <div class="d-flex gap-4">
                        <div>
                            <div class="small opacity-75">Started On</div>
                            <div class="fw-600"><?= format_date($active['start_date']) ?></div>
                        </div>
                        <div>
                            <div class="small opacity-75">Duration</div>
                            <div class="fw-600"><?= $active['duration_days'] ?> Days</div>
                        </div>
                    </div>
                </div>
                <div class="col-md-5 text-md-end mt-4 mt-md-0">
                    <div class="d-inline-block text-center p-3 rounded" style="background:rgba(255,255,255,0.1);backdrop-filter:blur(10px)">
                        <div class="fs-1 fw-800 lh-1"><?= max(0,$daysLeft) ?></div>
                        <div class="small opacity-75">Days Remaining</div>
                    </div>
                </div>
            </div>
            <div class="progress mt-4" style="height:6px;background:rgba(255,255,255,0.2)">
                <div class="progress-bar bg-white" style="width:<?= $percent ?>%"></div>
            </div>
        </div>
    </div>
    <?php else: ?>
    <div class="alert alert-warning shadow-sm border-0 d-flex align-items-center mb-4">
        <i class="bi bi-exclamation-triangle-fill fs-3 me-3"></i>
        <div>
            <h6 class="alert-heading fw-700 mb-1">No Active Membership</h6>
            <p class="mb-0 small">You do not currently have an active meal plan. Please contact the admin to renew your membership.</p>
        </div>
    </div>
    <?php endif; ?>

    <div class="panel">
        <div class="panel-header"><h6>Membership History</h6></div>
        <div class="table-responsive">
            <table class="table">
                <thead><tr><th>Plan</th><th>Start Date</th><th>End Date</th><th>Status</th></tr></thead>
                <tbody>
                <?php foreach($memberships as $m): ?>
                <tr>
                    <td class="fw-600"><?= e($m['plan_name']) ?></td>
                    <td><?= format_date($m['start_date']) ?></td>
                    <td><?= format_date($m['end_date']) ?></td>
                    <td><?= badge($m['status']) ?></td>
                </tr>
                <?php endforeach; ?>
                <?php if(empty($memberships)): ?><tr><td colspan="4" class="text-center text-muted py-4">No membership history.</td></tr><?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</div>
</div>
