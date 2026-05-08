<?php $pageTitle='Coordinators'; ?>
<div class="row g-4">
<div class="col-lg-8">
    <div class="panel">
        <div class="panel-header"><h6>Coordinators</h6></div>
        <div class="table-responsive">
            <table class="table">
                <thead><tr><th>Name</th><th>Email</th><th>Phone</th><th>Status</th><th class="text-end">Actions</th></tr></thead>
                <tbody>
                <?php foreach($coordinators as $c): ?>
                <tr>
                    <td class="fw-600"><?= e($c['full_name']) ?></td>
                    <td><?= e($c['email']) ?></td>
                    <td><?= e($c['phone']??'—') ?></td>
                    <td><?= badge($c['status']) ?></td>
                    <td class="text-end">
                        <a href="<?= url('super/coordinators/' . $c['user_id'] . '/edit') ?>" class="btn btn-sm btn-light border text-muted" title="Edit">
                            <i class="bi bi-pencil"></i>
                        </a>
                        <form method="POST" action="<?= url('super/coordinators/' . $c['user_id'] . '/delete') ?>" class="d-inline">
                            <?= csrf_field() ?>
                            <button type="button" class="btn btn-sm btn-light border text-danger" onclick="confirmDelete(this)" title="Delete">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
                <?php if(empty($coordinators)): ?><tr><td colspan="5" class="text-center text-muted py-4">No coordinators.</td></tr><?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<div class="col-lg-4">
    <div class="panel">
        <div class="panel-header"><h6><i class="bi bi-person-plus me-2"></i>Add Coordinator</h6></div>
        <div class="panel-body">
            <form method="POST" action="<?= url('super/coordinators/store') ?>">
                <?= csrf_field() ?>
                <div class="mb-3">
                    <label class="form-label">ASSIGN TO MESS *</label>
                    <select name="tenant_id" class="form-select" required>
                        <option value="">Select Mess...</option>
                        <?php foreach($tenants as $t): ?>
                        <option value="<?= $t['tenant_id'] ?>"><?= e($t['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">FULL NAME *</label>
                    <input type="text" name="full_name" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">EMAIL *</label>
                    <input type="email" name="email" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">PHONE</label>
                    <input type="text" name="phone" class="form-control">
                </div>
                <div class="mb-3">
                    <label class="form-label">TEMPORARY PASSWORD *</label>
                    <input type="text" name="password" class="form-control" value="Coord@123" required>
                </div>
                <button type="submit" class="btn btn-primary-g w-100">Create Coordinator</button>
            </form>
        </div>
    </div>
</div>
</div>
