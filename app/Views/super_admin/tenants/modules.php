<?php
/**
 * Module toggle UI for a tenant
 * @var array $tenant
 * @var array $modules
 */
?>
<div class="d-flex align-items-center gap-3 mb-4">
    <a href="<?= url('super/tenants') ?>" class="btn btn-outline-secondary btn-sm" style="border-radius:8px">
        <i class="bi bi-arrow-left me-1"></i>Back
    </a>
    <h5 class="mb-0 fw-700">Modules — <?= e($tenant['name']) ?></h5>
</div>

<form method="POST" action="<?= url('super/tenants/'.$tenant['tenant_id'].'/modules') ?>">
    <?= csrf_field() ?>
    <div class="row g-3">
    <?php foreach ($modules as $m): ?>
    <div class="col-sm-6 col-lg-4">
        <div class="stat-card" style="cursor:default">
            <div class="d-flex align-items-start gap-3">
                <div style="width:44px;height:44px;border-radius:12px;background:<?= $m['is_enabled']?'rgba(99,102,241,.2)':'rgba(51,65,85,.5)' ?>;
                     color:<?= $m['is_enabled']?'var(--primary)':'var(--muted)' ?>;
                     display:flex;align-items:center;justify-content:center;font-size:1.3rem;flex-shrink:0">
                    <i class="bi <?= e($m['icon']) ?>"></i>
                </div>
                <div style="flex:1">
                    <div class="fw-700 small"><?= e($m['name']) ?></div>
                    <div class="text-muted" style="font-size:.73rem"><?= e($m['description']) ?></div>
                    <div class="text-muted mt-1" style="font-size:.68rem">v<?= e($m['version']) ?></div>
                </div>
                <?php if ($m['is_core']): ?>
                <span class="badge bg-primary" style="font-size:.65rem">Core</span>
                <?php else: ?>
                <div class="form-check form-switch mb-0">
                    <input class="form-check-input" type="checkbox" name="modules[]"
                           value="<?= $m['module_id'] ?>"
                           <?= $m['is_enabled']?'checked':'' ?>
                           style="width:40px;height:22px;cursor:pointer">
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
    </div>
    <div class="mt-4">
        <button type="submit" class="btn btn-primary-g">
            <i class="bi bi-save me-2"></i>Save Module Settings
        </button>
    </div>
</form>
