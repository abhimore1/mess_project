<?php $pageTitle='Audit Logs'; ?>
<div class="panel">
    <div class="panel-header"><h6>System Audit Logs</h6></div>
    <div class="table-responsive">
        <table class="table table-hover table-sm">
            <thead class="bg-light"><tr><th>Time</th><th>User</th><th>Mess/Tenant</th><th>Action</th><th>IP Address</th></tr></thead>
            <tbody>
            <?php foreach($logs as $log): ?>
            <tr>
                <td class="text-muted" style="font-size:.8rem"><?= $log['created_at'] ?></td>
                <td class="fw-500" style="font-size:.85rem"><?= e($log['full_name']??'System') ?></td>
                <td style="font-size:.85rem"><?= e($log['tenant_name']??'Global') ?></td>
                <td><span class="badge bg-secondary"><?= e($log['action']) ?></span></td>
                <td class="text-muted" style="font-size:.8rem"><?= e($log['ip_address']??'—') ?></td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?= paginate_links($pagination, url('super/audit')) ?>
