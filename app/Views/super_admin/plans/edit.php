<?php $pageTitle='Edit Subscription Plan'; ?>
<div class="row">
    <div class="col-lg-6 offset-lg-3">
        <div class="panel">
            <div class="panel-header">
                <h6><i class="bi bi-pencil-square me-2"></i>Edit Plan: <?= e($plan['name']) ?></h6>
            </div>
            <div class="panel-body">
                <form method="POST" action="<?= url('super/plans/' . $plan['plan_id'] . '/update') ?>">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label class="form-label">PLAN NAME</label>
                        <input type="text" name="name" class="form-control" value="<?= e($plan['name']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">MONTHLY PRICE (₹)</label>
                        <input type="number" step="0.01" name="price_monthly" class="form-control" value="<?= e($plan['price_monthly']) ?>" required>
                    </div>
                    <div class="row g-2 mb-4">
                        <div class="col-6">
                            <label class="form-label text-truncate">MAX STUDENTS</label>
                            <input type="number" name="max_students" class="form-control" value="<?= e($plan['max_students']) ?>">
                            <small class="text-muted" style="font-size:10px">0 = Unlimited</small>
                        </div>
                        <div class="col-6">
                            <label class="form-label text-truncate">STORAGE (MB)</label>
                            <input type="number" name="storage_mb" class="form-control" value="<?= e($plan['storage_mb']) ?>">
                        </div>
                    </div>
                    <div class="d-flex justify-content-between">
                        <a href="<?= url('super/plans') ?>" class="btn btn-light border">Cancel</a>
                        <button type="submit" class="btn btn-primary-g">Update Plan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
