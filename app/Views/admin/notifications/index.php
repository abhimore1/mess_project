<?php $pageTitle='Notifications'; ?>
<div class="row">
<div class="col-lg-8">
    <div class="panel">
        <div class="panel-header"><h6>Notification History</h6></div>
        <div class="table-responsive">
            <table class="table">
                <thead><tr><th>Date</th><th>Title</th><th>Message</th><th>Target</th><th>Type</th></tr></thead>
                <tbody>
                <?php foreach($notifs as $n): ?>
                <tr>
                    <td class="small text-muted"><?= format_date($n['created_at'],'d M H:i') ?></td>
                    <td class="fw-600 small"><?= e($n['title']) ?></td>
                    <td class="small"><?= e($n['message']) ?></td>
                    <td><span class="badge bg-secondary"><?= e($n['target_role']) ?></span></td>
                    <td><span class="badge bg-<?= $n['type']==='error'?'danger':($n['type']==='success'?'success':($n['type']==='warning'?'warning':'info')) ?>"><?= ucfirst($n['type']) ?></span></td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<div class="col-lg-4">
    <div class="panel">
        <div class="panel-header"><h6><i class="bi bi-send me-2"></i>Send Notification</h6></div>
        <div class="panel-body">
            <form method="POST" action="<?= url('admin/notifications/send') ?>">
                <?= csrf_field() ?>
                <div class="mb-3">
                    <label class="form-label">TARGET AUDIENCE</label>
                    <select name="target_role" class="form-select">
                        <option value="all">Everyone</option>
                        <option value="student">All Students</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">TYPE</label>
                    <select name="type" class="form-select">
                        <option value="info">Info</option>
                        <option value="success">Success</option>
                        <option value="warning">Warning</option>
                        <option value="error">Alert</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">TITLE</label>
                    <input type="text" name="title" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">MESSAGE</label>
                    <textarea name="message" class="form-control" rows="3" required></textarea>
                </div>
                <button type="submit" class="btn btn-primary-g w-100">Send Notification</button>
            </form>
        </div>
    </div>
</div>
</div>
