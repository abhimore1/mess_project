<?php $pageTitle='Payment History'; ?>
<div class="panel">
    <div class="panel-header"><h6>Payment History</h6></div>
    <div class="table-responsive">
        <table class="table">
            <thead><tr><th>Receipt No.</th><th>Date</th><th>Amount</th><th>Status</th><th>Action</th></tr></thead>
            <tbody>
            <?php foreach($payments as $p): ?>
            <tr>
                <td class="fw-600 small"><?= e($p['receipt_number']) ?></td>
                <td><?= format_date($p['payment_date']) ?></td>
                <td class="fw-700"><?= format_currency($p['net_amount']) ?></td>
                <td><?= badge($p['status']) ?></td>
                <td>
                    <a href="<?= url('student/payments/'.$p['payment_id'].'/receipt') ?>" class="btn btn-sm btn-outline-primary" style="border-radius:8px"><i class="bi bi-download me-1"></i>Receipt</a>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php if(empty($payments)): ?><tr><td colspan="5" class="text-center text-muted py-4">No payments found.</td></tr><?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
