<?php $pageTitle='Complaints'; ?>
<div class="panel">
    <div class="panel-header"><h6>Complaints</h6></div>
    <div class="table-responsive">
        <table class="table">
            <thead><tr><th>Date</th><th>Student</th><th>Subject</th><th>Priority</th><th>Status</th><th>Action</th></tr></thead>
            <tbody>
            <?php foreach($complaints as $c): ?>
            <tr>
                <td class="small text-muted"><?= format_date($c['created_at']) ?></td>
                <td class="fw-500"><a href="<?= url('admin/students/'.$c['student_id']) ?>"><?= e($c['student_name']) ?></a></td>
                <td>
                    <div class="fw-600 small"><?= e($c['subject']) ?></div>
                    <div class="text-muted text-truncate" style="font-size:.73rem;max-width:250px"><?= e($c['description']) ?></div>
                </td>
                <td>
                    <?php if($c['priority']==='high'): ?><span class="badge bg-danger">High</span>
                    <?php elseif($c['priority']==='low'): ?><span class="badge bg-secondary">Low</span>
                    <?php else: ?><span class="badge bg-warning">Medium</span><?php endif; ?>
                </td>
                <td><?= badge($c['status']) ?></td>
                <td>
                    <?php if($c['status']==='open'): ?>
                    <form method="POST" action="<?= url('admin/complaints/'.$c['complaint_id'].'/status') ?>">
                        <?= csrf_field() ?>
                        <input type="hidden" name="status" value="resolved">
                        <button type="submit" class="btn btn-sm btn-outline-success" style="border-radius:8px">Mark Resolved</button>
                    </form>
                    <?php endif; ?>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php if(empty($complaints)): ?><tr><td colspan="6" class="text-center text-muted py-4">No complaints.</td></tr><?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
