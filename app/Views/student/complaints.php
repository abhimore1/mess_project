<?php $pageTitle='My Complaints'; ?>
<div class="row g-4">
<div class="col-lg-8">
    <div class="panel">
        <div class="panel-header"><h6>Complaint History</h6></div>
        <div class="table-responsive">
            <table class="table">
                <thead><tr><th>Date</th><th>Subject</th><th>Status</th></tr></thead>
                <tbody>
                <?php foreach($complaints as $c): ?>
                <tr>
                    <td class="small text-muted"><?= format_date($c['created_at']) ?></td>
                    <td>
                        <div class="fw-600 small"><?= e($c['subject']) ?></div>
                        <div class="text-muted text-truncate" style="font-size:.73rem;max-width:250px"><?= e($c['description']) ?></div>
                    </td>
                    <td><?= badge($c['status']) ?></td>
                </tr>
                <?php endforeach; ?>
                <?php if(empty($complaints)): ?><tr><td colspan="3" class="text-center text-muted py-4">No complaints submitted.</td></tr><?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<div class="col-lg-4">
    <div class="panel">
        <div class="panel-header"><h6><i class="bi bi-chat-left-text me-2"></i>Submit Complaint</h6></div>
        <div class="panel-body">
            <form method="POST" action="<?= url('student/complaints/submit') ?>">
                <?= csrf_field() ?>
                <div class="mb-3">
                    <label class="form-label">SUBJECT</label>
                    <input type="text" name="subject" class="form-control" required placeholder="Brief title">
                </div>
                <div class="mb-3">
                    <label class="form-label">DESCRIPTION</label>
                    <textarea name="description" class="form-control" rows="4" required placeholder="Explain the issue..."></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">PRIORITY</label>
                    <select name="priority" class="form-select">
                        <option value="low">Low</option>
                        <option value="medium" selected>Medium</option>
                        <option value="high">High</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary-g w-100">Submit Complaint</button>
            </form>
        </div>
    </div>
</div>
</div>
