<?php $pageTitle='Membership Plans'; ?>
<div class="row g-4">
<div class="col-lg-8">
    <div class="panel">
        <div class="panel-header"><h6>Membership Plans</h6></div>
        <div class="table-responsive">
            <table class="table">
                <thead><tr><th>Name</th><th>Duration</th><th>Price</th><th>Active Members</th></tr></thead>
                <tbody>
                <?php foreach($plans as $p): ?>
                <tr>
                    <td class="fw-600"><?= e($p['name']) ?></td>
                    <td><?= $p['duration_days'] ?> days</td>
                    <td><?= format_currency($p['price']) ?></td>
                    <td><span class="badge bg-secondary"><?= $p['active_count'] ?> active</span></td>
                </tr>
                <?php endforeach; ?>
                <?php if(empty($plans)): ?><tr><td colspan="4" class="text-center text-muted">No plans configured.</td></tr><?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<div class="col-lg-4">
    <div class="panel">
        <div class="panel-header"><h6><i class="bi bi-plus-circle me-2"></i>Add Plan</h6></div>
        <div class="panel-body">
            <form method="POST" action="<?= url('admin/memberships/plans/store') ?>">
                <?= csrf_field() ?>
                <div class="mb-3">
                    <label class="form-label">PLAN NAME</label>
                    <input type="text" name="name" class="form-control" required placeholder="e.g. Monthly Standard">
                </div>
                <div class="mb-3">
                    <label class="form-label">DURATION (DAYS)</label>
                    <input type="number" name="duration_days" class="form-control" value="30" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">PRICE (<?= get_setting('currency_symbol','₹') ?>)</label>
                    <input type="number" step="0.01" name="price" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">INCLUDED MEALS</label>
                    <?php foreach($slots as $s): ?>
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="slot_ids[]" value="<?= $s['slot_id'] ?>" id="slot<?= $s['slot_id'] ?>" checked>
                        <label class="form-check-label" for="slot<?= $s['slot_id'] ?>"><?= e($s['name']) ?></label>
                    </div>
                    <?php endforeach; ?>
                </div>
                <div class="mb-3">
                    <label class="form-label">DESCRIPTION</label>
                    <textarea name="description" class="form-control" rows="2"></textarea>
                </div>
                <button type="submit" class="btn btn-primary-g w-100">Create Plan</button>
            </form>
        </div>
    </div>
</div>
</div>
