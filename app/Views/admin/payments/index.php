<?php $pageTitle='Payments'; ?>
<div class="d-flex justify-content-between align-items-center mb-3 flex-wrap gap-2">
    <h5 class="mb-0 fw-700">Payments</h5>
    <a href="<?= url('admin/payments/create') ?>" class="btn btn-primary-g btn-sm">
        <i class="bi bi-plus-circle me-1"></i>Collect Payment
    </a>
</div>
<div class="panel">
    <div class="panel-header gap-2 flex-wrap">
        <form class="d-flex gap-2 flex-wrap" method="GET">
            <input type="date" name="from" class="form-control form-control-sm" value="<?= e($from) ?>" style="width:130px">
            <span class="mt-1 small text-muted">to</span>
            <input type="date" name="to" class="form-control form-control-sm" value="<?= e($to) ?>" style="width:130px">
            <select name="status" class="form-select form-select-sm" style="width:120px" onchange="this.form.submit()">
                <option value="">All Status</option>
                <option value="paid" <?= $status==='paid'?'selected':'' ?>>Paid</option>
                <option value="pending" <?= $status==='pending'?'selected':'' ?>>Pending</option>
            </select>
            <button class="btn btn-outline-secondary btn-sm" style="border-radius:8px">Filter</button>
            <a href="<?= url('admin/reports/export?type=payments&from='.$from.'&to='.$to) ?>" class="btn btn-outline-success btn-sm ms-auto" style="border-radius:8px">
                <i class="bi bi-file-earmark-excel me-1"></i>Export
            </a>
        </form>
    </div>
    
    <div class="p-3 bg-light border-bottom d-flex gap-4">
        <div><span class="text-muted small">Total Paid:</span> <strong class="text-success fs-6"><?= format_currency($totals['total_paid']??0) ?></strong></div>
        <div><span class="text-muted small">Total Discount:</span> <strong class="text-danger fs-6"><?= format_currency($totals['total_discount']??0) ?></strong></div>
    </div>

    <div class="table-responsive">
        <table class="table">
            <thead><tr><th>Receipt No.</th><th>Student</th><th>Amount</th><th>Discount</th><th>Net Total</th><th>Mode</th><th>Date</th><th>Status</th><th>Action</th></tr></thead>
            <tbody>
            <?php foreach($payments as $p): ?>
            <tr>
                <td class="fw-600 small"><?= e($p['receipt_number']) ?></td>
                <td><a href="<?= url('admin/students/'.$p['student_id']) ?>"><?= e($p['student_name']) ?></a></td>
                <td><?= format_currency($p['amount']) ?></td>
                <td class="text-danger"><?= format_currency($p['discount']) ?></td>
                <td class="fw-700"><?= format_currency($p['net_amount']) ?></td>
                <td><span class="badge bg-secondary"><?= ucfirst($p['payment_mode']) ?></span></td>
                <td class="small"><?= format_date($p['payment_date']) ?></td>
                <td><?= badge($p['status']) ?></td>
                <td>
                    <a href="<?= url('admin/payments/'.$p['payment_id'].'/receipt') ?>" class="btn btn-sm btn-outline-primary" style="border-radius:8px"><i class="bi bi-receipt"></i></a>
                </td>
            </tr>
            <?php endforeach; ?>
            <?php if(empty($payments)): ?><tr><td colspan="9" class="text-center text-muted py-4">No payments found in this period.</td></tr><?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<?= paginate_links($pagination, url('admin/payments').'?from='.$from.'&to='.$to.'&status='.$status) ?>
