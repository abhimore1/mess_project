<?php
/**
 * Academic Years Management View
 */
?>
<div class="row g-4 animate-fadeInUp">
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-header">
                <h5 class="card-title"><i class="bi bi-calendar-plus text-primary"></i> Add Academic Year</h5>
            </div>
            <div class="card-body">
                <form action="<?= url('admin/years/store') ?>" method="POST">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label class="form-label">Academic Year Name</label>
                        <input type="text" name="year_name" class="form-control" placeholder="e.g. 2024-25 or 2025" required>
                        <small class="text-tertiary">This year will be available when adding students.</small>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-plus-lg me-2"></i> Create Year
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h5 class="card-title"><i class="bi bi-calendar3 text-primary"></i> Academic Years</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table mb-0">
                        <thead>
                            <tr>
                                <th>Year Name</th>
                                <th>Status</th>
                                <th>Created</th>
                                <th class="text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(empty($years)): ?>
                            <tr><td colspan="4" class="text-center text-muted py-4">No academic years found. Add one to get started.</td></tr>
                            <?php else: foreach($years as $yr): ?>
                            <tr>
                                <td class="font-semibold"><?= e($yr['year_name']) ?></td>
                                <td>
                                    <?php if($yr['is_active']): ?>
                                        <span class="badge badge-success">Active</span>
                                    <?php else: ?>
                                        <span class="badge badge-secondary">Inactive</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-tertiary small"><?= date('d M Y', strtotime($yr['created_at'])) ?></td>
                                <td class="text-end">
                                    <form action="<?= url("admin/years/{$yr['year_id']}/toggle") ?>" method="POST" class="d-inline">
                                        <?= csrf_field() ?>
                                        <button type="submit" class="btn btn-sm btn-outline-secondary" title="Toggle Status">
                                            <i class="bi <?= $yr['is_active'] ? 'bi-eye-slash' : 'bi-eye' ?>"></i>
                                        </button>
                                    </form>
                                    <form action="<?= url("admin/years/{$yr['year_id']}/delete") ?>" method="POST" class="d-inline ms-1">
                                        <?= csrf_field() ?>
                                        <button type="button" class="btn btn-sm btn-outline-secondary text-error" onclick="confirmDelete(this)" title="Delete Year">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
