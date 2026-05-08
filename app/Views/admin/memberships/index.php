<?php $pageTitle='Memberships'; ?>
<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <h5 class="mb-0 fw-700">Memberships</h5>
    <a href="<?= url('admin/memberships/plans') ?>" class="btn btn-outline-primary btn-sm">Manage Plans</a>
</div>
<div class="panel">
    <div class="table-responsive">
        <table class="table" id="memTable">
            <thead><tr><th>Student</th><th>Plan</th><th>Duration</th><th>Ends On</th><th>Status</th><th>Actions</th></tr></thead>
            <tbody>
            <?php foreach($mems as $m): ?>
            <tr>
                <td class="fw-500"><a href="<?= url('admin/students/'.$m['student_id']) ?>"><?= e($m['student_name']) ?></a></td>
                <td><?= e($m['plan_name']) ?></td>
                <td><?= format_date($m['start_date']) ?> - <?= format_date($m['end_date']) ?></td>
                <td>
                    <?php if($m['status']==='active'): ?>
                        <span class="badge <?= $m['days_left']<7?'bg-warning':'bg-success' ?>"><?= $m['days_left'] ?> days left</span>
                    <?php else: ?>
                        <span class="text-muted small">Expired</span>
                    <?php endif; ?>
                </td>
                <td><?= badge($m['status']) ?></td>
                <td>
                    <?php if($m['status']==='active'): ?>
                    <form method="POST" action="<?= url('admin/memberships/'.$m['membership_id'].'/renew') ?>" style="display:inline">
                        <?= csrf_field() ?>
                        <button type="submit" class="btn btn-sm btn-outline-primary" style="border-radius:8px" onclick="return confirm('Renew this membership?')">Renew</button>
                    </form>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<script>new DataTable('#memTable',{pageLength:15});</script>
