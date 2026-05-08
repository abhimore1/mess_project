<?php $pageTitle='Edit Coordinator'; ?>
<div class="row">
    <div class="col-lg-6 offset-lg-3">
        <div class="panel">
            <div class="panel-header">
                <h6><i class="bi bi-pencil-square me-2"></i>Edit Coordinator: <?= e($coordinator['full_name']) ?></h6>
            </div>
            <div class="panel-body">
                <form method="POST" action="<?= url('super/coordinators/' . $coordinator['user_id'] . '/update') ?>">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label class="form-label">ASSIGN TO MESS *</label>
                        <select name="tenant_id" class="form-select" required>
                            <option value="">Select Mess...</option>
                            <?php foreach($tenants as $t): ?>
                            <option value="<?= $t['tenant_id'] ?>" <?= $t['tenant_id'] == $coordinator['tenant_id'] ? 'selected' : '' ?>>
                                <?= e($t['name']) ?>
                            </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">FULL NAME *</label>
                        <input type="text" name="full_name" class="form-control" value="<?= e($coordinator['full_name']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">EMAIL *</label>
                        <input type="email" name="email" class="form-control" value="<?= e($coordinator['email']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">PHONE</label>
                        <input type="text" name="phone" class="form-control" value="<?= e($coordinator['phone'] ?? '') ?>">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">NEW PASSWORD <small class="text-muted">(Leave blank to keep current)</small></label>
                        <input type="password" name="password" class="form-control">
                    </div>
                    <div class="mb-4">
                        <label class="form-label">STATUS</label>
                        <select name="status" class="form-select">
                            <option value="active" <?= $coordinator['status'] === 'active' ? 'selected' : '' ?>>Active</option>
                            <option value="inactive" <?= $coordinator['status'] === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                        </select>
                    </div>
                    <div class="d-flex justify-content-between">
                        <a href="<?= url('super/coordinators') ?>" class="btn btn-light border">Cancel</a>
                        <button type="submit" class="btn btn-primary-g">Update Coordinator</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
