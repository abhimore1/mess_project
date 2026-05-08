<?php $pageTitle = 'Settings'; ?>

<div class="page-header d-flex align-items-center justify-content-between mb-4 animate-fadeInUp">
    <div>
        <h4 class="fw-700 mb-1">Mess Settings</h4>
        <p class="text-muted small mb-0">Configure your mess identity, branding, and system preferences.</p>
    </div>
</div>

<form method="POST" action="<?= url('admin/settings/save') ?>" class="animate-fadeInUp stagger-1">
    <?= csrf_field() ?>
    
    <div class="row g-4">
        <!-- Main Settings Column -->
        <div class="col-lg-8">
            <!-- General Information -->
            <div class="card border-0 shadow-sm mb-4" style="border-radius: 20px;">
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="fw-700 mb-0"><i class="bi bi-shop me-2 text-primary"></i>General Information</h6>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label font-medium small text-muted text-uppercase tracking-wider">Mess Name</label>
                            <div class="input-group">
                                <span class="input-group-text bg-surface-variant border-0 text-muted"><i class="bi bi-building"></i></span>
                                <input type="text" name="mess_name" class="form-control border-0 bg-surface-variant" 
                                       value="<?= e($settings['mess_name']??'') ?>" required style="border-radius: 0 10px 10px 0;">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label font-medium small text-muted text-uppercase tracking-wider">Owner Name</label>
                            <div class="input-group">
                                <span class="input-group-text bg-surface-variant border-0 text-muted"><i class="bi bi-person-badge"></i></span>
                                <input type="text" name="owner_name" class="form-control border-0 bg-surface-variant" 
                                       value="<?= e($tenant['owner_name']??'') ?>" required style="border-radius: 0 10px 10px 0;">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label font-medium small text-muted text-uppercase tracking-wider">Contact Phone</label>
                            <div class="input-group">
                                <span class="input-group-text bg-surface-variant border-0 text-muted"><i class="bi bi-telephone"></i></span>
                                <input type="text" name="mess_phone" class="form-control border-0 bg-surface-variant" 
                                       value="<?= e($settings['mess_phone']??'') ?>" style="border-radius: 0 10px 10px 0;">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label font-medium small text-muted text-uppercase tracking-wider">Contact Email</label>
                            <div class="input-group">
                                <span class="input-group-text bg-surface-variant border-0 text-muted"><i class="bi bi-envelope"></i></span>
                                <input type="email" name="mess_email" class="form-control border-0 bg-surface-variant" 
                                       value="<?= e($settings['mess_email']??'') ?>" style="border-radius: 0 10px 10px 0;">
                            </div>
                        </div>
                        <div class="col-12">
                            <label class="form-label font-medium small text-muted text-uppercase tracking-wider">Physical Address</label>
                            <textarea name="mess_address" class="form-control border-0 bg-surface-variant" rows="2" 
                                      placeholder="Full mess address..." style="border-radius: 12px;"><?= e($settings['mess_address']??'') ?></textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- System Preferences -->
            <div class="card border-0 shadow-sm" style="border-radius: 20px;">
                <div class="card-header bg-white border-bottom py-3">
                    <h6 class="fw-700 mb-0"><i class="bi bi-cpu me-2 text-primary"></i>System Preferences</h6>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label font-medium small text-muted text-uppercase tracking-wider">Currency Symbol</label>
                            <div class="input-group">
                                <span class="input-group-text bg-surface-variant border-0 text-muted"><i class="bi bi-cash-stack"></i></span>
                                <input type="text" name="currency_symbol" class="form-control border-0 bg-surface-variant" 
                                       value="<?= e($settings['currency_symbol']??'₹') ?>" maxlength="5" style="border-radius: 0 10px 10px 0;">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="p-3 bg-surface-variant h-100 d-flex align-items-center justify-content-between" style="border-radius: 12px;">
                                <div>
                                    <div class="fw-700 small mb-0">Student Portal</div>
                                    <div class="text-muted x-small">Allow student logins</div>
                                </div>
                                <div class="form-check form-switch mb-0">
                                    <input class="form-check-input custom-switch" type="checkbox" name="student_login" value="1"
                                           <?= ($settings['student_login']??'0')==='1'?'checked':'' ?>>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar Column (Branding & Save) -->
        <div class="col-lg-4">
            <div class="sticky-top" style="top: 1.5rem;">
                <!-- Branding Card -->
                <div class="card border-0 shadow-sm mb-4 overflow-hidden" style="border-radius: 20px;">
                    <div class="card-header bg-white border-bottom py-3">
                        <h6 class="fw-700 mb-0"><i class="bi bi-palette me-2 text-primary"></i>Visual Branding</h6>
                    </div>
                    <div class="card-body p-4 text-center">
                        <div class="mb-4">
                            <label class="form-label font-medium small text-muted text-uppercase tracking-wider d-block mb-3">Primary Brand Color</label>
                            <div class="d-flex justify-content-center">
                                <div class="color-picker-wrapper shadow-sm">
                                    <input type="color" name="primary_color" value="<?= e($tenant['primary_color']??'#6366f1') ?>">
                                </div>
                            </div>
                            <div class="mt-3 small text-muted">This color will be used for buttons, links, and highlights in your portal.</div>
                        </div>
                    </div>
                </div>

                <!-- Save Action Card -->
                <div class="card border-0 shadow-lg bg-dark text-white" style="border-radius: 20px;">
                    <div class="card-body p-4">
                        <h6 class="fw-700 mb-3"><i class="bi bi-cloud-arrow-up me-2"></i>Save Changes</h6>
                        <p class="small opacity-75 mb-4">Ensure all your configuration details are correct before saving. Branding changes apply instantly.</p>
                        <button type="submit" class="btn btn-primary-g w-100 py-3 fw-700 shadow-sm border-0">
                            Update Settings
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>

<style>
.color-picker-wrapper {
    width: 80px;
    height: 80px;
    border-radius: 20px;
    overflow: hidden;
    border: 4px solid white;
    cursor: pointer;
}
.color-picker-wrapper input[type="color"] {
    width: 140%;
    height: 140%;
    margin: -20%;
    padding: 0;
    border: none;
    cursor: pointer;
}

.custom-switch {
    width: 44px !important;
    height: 22px !important;
    cursor: pointer;
}

.x-small {
    font-size: 0.65rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.tracking-wider {
    letter-spacing: 0.05em;
}
</style>

<script>
// Show toast on form submission if needed, but since it's a normal redirect-post
// the controller's flash() will handle it. We just add a loading state.
document.querySelector('form').addEventListener('submit', function() {
    const btn = this.querySelector('button[type="submit"]');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Saving...';
});
</script>
