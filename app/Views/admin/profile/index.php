<?php
/**
 * Mess Profile View - Modernized MD3 Design
 */
?>
<div class="row g-4 animate-fadeInUp">
    <!-- Profile Overview Card -->
    <div class="col-lg-4">
        <div class="card h-100">
            <div class="card-body text-center py-5">
                <!-- Avatar with refined gradient and shadow -->
                <div class="mx-auto mb-4 animate-scaleIn" style="width:120px; height:120px; border-radius: var(--radius-xl); background: linear-gradient(135deg, var(--primary), var(--tertiary)); display:flex; align-items:center; justify-content:center; color:#fff; font-size:3rem; font-weight:700; box-shadow: var(--shadow-4);">
                    <?= strtoupper(substr($tenant['name'], 0, 1)) ?>
                </div>
                
                <h3 class="mb-1"><?= e($tenant['name']) ?></h3>
                <div class="badge badge-primary mb-4"><?= e($tenant['plan_name'] ?? 'Free Plan') ?></div>
                
                <div class="d-flex justify-content-center gap-2">
                    <a href="<?= url('admin/settings') ?>" class="btn btn-primary btn-sm">
                        <i class="bi bi-pencil-square"></i> Edit Profile
                    </a>
                </div>
            </div>
            
            <div class="card-footer bg-surface-variant border-0 p-4">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <span class="text-label text-secondary">Status</span>
                    <span class="badge badge-success"><?= ucfirst($tenant['status']) ?></span>
                </div>
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <span class="text-label text-secondary">Registered On</span>
                    <span class="text-body font-medium"><?= date('M d, Y', strtotime($tenant['created_at'])) ?></span>
                </div>
                <div class="d-flex align-items-center justify-content-between">
                    <span class="text-label text-secondary">Tenant ID</span>
                    <span class="text-body font-medium">#<?= $tenant['tenant_id'] ?></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Details Column -->
    <div class="col-lg-8">
        <!-- Mess Information Card -->
        <div class="card mb-4">
            <div class="card-header">
                <h5 class="card-title"><i class="bi bi-building-check text-primary"></i> Mess Information</h5>
            </div>
            <div class="card-body">
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="text-label text-tertiary mb-1">Mess Owner</div>
                        <div class="text-body font-semibold"><?= e($tenant['owner_name'] ?? '—') ?></div>
                    </div>
                    <div class="col-md-6">
                        <div class="text-label text-tertiary mb-1">Contact Email</div>
                        <div class="text-body font-semibold"><?= e($tenant['contact_email'] ?? '—') ?></div>
                    </div>
                    <div class="col-md-6">
                        <div class="text-label text-tertiary mb-1">Contact Phone</div>
                        <div class="text-body font-semibold"><?= e($tenant['contact_phone'] ?? '—') ?></div>
                    </div>
                    <div class="col-md-6">
                        <div class="text-label text-tertiary mb-1">City / State</div>
                        <div class="text-body font-semibold"><?= e($tenant['city']) ?><?= $tenant['state'] ? ', '.e($tenant['state']) : '' ?></div>
                    </div>
                    <div class="col-12">
                        <div class="text-label text-tertiary mb-1">Business Address</div>
                        <div class="text-body font-semibold"><?= nl2br(e($tenant['address'] ?? '—')) ?></div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Subscription & Limits Card -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title"><i class="bi bi-shield-check text-primary"></i> Subscription & Limits</h5>
            </div>
            <div class="card-body">
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="text-label text-tertiary mb-1">Current Plan</div>
                        <div class="text-headline text-primary font-bold"><?= e($tenant['plan_name'] ?? 'Starter') ?></div>
                    </div>
                    <div class="col-md-6">
                        <div class="text-label text-tertiary mb-1">Expires On</div>
                        <div class="text-body font-semibold fs-5"><?= $subscription ? date('M d, Y', strtotime($subscription['expires_at'])) : 'Lifetime' ?></div>
                    </div>
                    
                    <!-- Stat Grid -->
                    <div class="col-md-4">
                        <div class="stat-card-outlined">
                            <div class="stat-icon stat-icon-primary"><i class="bi bi-people"></i></div>
                            <div class="stat-content">
                                <div class="stat-value"><?= $tenant['max_students'] == 0 ? '∞' : number_format($tenant['max_students']) ?></div>
                                <div class="stat-label">Max Students</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stat-card-outlined">
                            <div class="stat-icon stat-icon-info"><i class="bi bi-person-gear"></i></div>
                            <div class="stat-content">
                                <div class="stat-value"><?= number_format($tenant['max_coordinators']) ?></div>
                                <div class="stat-label">Coordinators</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="stat-card-outlined">
                            <div class="stat-icon stat-icon-success"><i class="bi bi-database"></i></div>
                            <div class="stat-content">
                                <div class="stat-value small"><?= number_format($tenant['storage_used_mb'], 1) ?> <span class="fs-6">/ 500 MB</span></div>
                                <div class="stat-label">Storage</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.bg-surface-variant { background-color: var(--surface-container-low); }
.font-medium { font-weight: 500; }
.font-semibold { font-weight: 600; }
.font-bold { font-weight: 700; }
</style>
