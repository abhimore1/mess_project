<?php $pageTitle='Subscription Plans'; ?>
<div class="row g-4">
<div class="col-lg-8">
    <div class="panel">
        <div class="panel-header"><h6>Platform Subscription Plans</h6></div>
        <div class="table-responsive">
            <table class="table">
                <thead><tr><th>Name</th><th>Monthly Price</th><th>Students</th><th>Storage</th><th>Active Messes</th><th class="text-end">Actions</th></tr></thead>
                <tbody>
                <?php foreach($plans as $p): ?>
                <tr>
                    <td class="fw-700 text-primary"><?= e($p['name']) ?></td>
                    <td>₹<?= number_format($p['price_monthly'],2) ?></td>
                    <td><?= $p['max_students']==0 ? 'Unlimited' : $p['max_students'] ?></td>
                    <td><?= $p['storage_mb'] ?> MB</td>
                    <td><span class="badge bg-secondary"><?= $p['active_tenants'] ?></span></td>
                    <td class="text-end">
                        <a href="<?= url('super/plans/' . $p['plan_id'] . '/edit') ?>" class="btn btn-sm btn-light border text-muted" title="Edit">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <form method="POST" action="<?= url('super/plans/' . $p['plan_id'] . '/delete') ?>" class="d-inline">
                            <?= csrf_field() ?>
                            <button type="button" class="btn btn-sm btn-light border text-danger" onclick="confirmDelete(this)" title="Delete">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<div class="col-lg-4">
    <div class="panel">
        <div class="panel-header"><h6><i class="bi bi-plus-circle me-2"></i>Create Plan</h6></div>
        <div class="panel-body">
            <form method="POST" action="<?= url('super/plans/store') ?>">
                <?= csrf_field() ?>
                <div class="mb-3">
                    <label class="form-label">PLAN NAME</label>
                    <input type="text" name="name" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">MONTHLY PRICE (₹)</label>
                    <input type="number" step="0.01" name="price_monthly" class="form-control" required>
                </div>
                <div class="row g-2 mb-3">
                    <div class="col-6">
                        <label class="form-label text-truncate">MAX STUDENTS</label>
                        <input type="number" name="max_students" class="form-control" value="0">
                        <small class="text-muted" style="font-size:10px">0 = Unlimited</small>
                    </div>
                    <div class="col-6">
                        <label class="form-label text-truncate">STORAGE (MB)</label>
                        <input type="number" name="storage_mb" class="form-control" value="500">
                    </div>
                </div>
                <button type="submit" class="btn btn-primary-g w-100">Save Plan</button>
            </form>
        </div>
    </div>
</div>
</div>
