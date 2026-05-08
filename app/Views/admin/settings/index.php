<?php
/**
 * Mess Settings view — dynamic meal slots + mess configuration
 */
?>
<div class="row g-4">
<div class="col-lg-8 mx-auto">
<div class="panel">
    <div class="panel-header"><h6><i class="bi bi-gear me-2"></i>Mess Configuration</h6></div>
    <div class="panel-body">
        <form method="POST" action="<?= url('admin/settings/save') ?>">
            <?= csrf_field() ?>
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">MESS NAME</label>
                    <input type="text" name="mess_name" class="form-control" value="<?= e($settings['mess_name']??'') ?>" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">OWNER NAME</label>
                    <input type="text" name="owner_name" class="form-control" value="<?= e($tenant['owner_name']??'') ?>" placeholder="Owner name">
                </div>
                <div class="col-md-6">
                    <label class="form-label">CONTACT PHONE</label>
                    <input type="text" name="mess_phone" class="form-control" value="<?= e($settings['mess_phone']??'') ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label">CONTACT EMAIL</label>
                    <input type="email" name="mess_email" class="form-control" value="<?= e($settings['mess_email']??'') ?>">
                </div>
                <div class="col-12">
                    <label class="form-label">ADDRESS</label>
                    <textarea name="mess_address" class="form-control" rows="2"><?= e($settings['mess_address']??'') ?></textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label">CURRENCY SYMBOL</label>
                    <input type="text" name="currency_symbol" class="form-control" value="<?= e($settings['currency_symbol']??'₹') ?>" maxlength="5">
                </div>
                <div class="col-md-6">
                    <label class="form-label">BRAND COLOR</label>
                    <div class="d-flex gap-2 align-items-center">
                        <input type="color" name="primary_color" class="form-control" value="<?= e($tenant['primary_color']??'#6366f1') ?>" style="width:50px;height:38px;padding:.2rem;border-radius:10px">
                        <span class="text-muted small">Primary theme color</span>
                    </div>
                </div>
                <div class="col-12">
                    <div class="d-flex align-items-center justify-content-between p-3" style="background:var(--card2);border-radius:12px;border:1px solid var(--border)">
                        <div>
                            <div class="fw-600 small">Student Login</div>
                            <div class="text-muted" style="font-size:.78rem">Allow students to login to the portal</div>
                        </div>
                        <div class="form-check form-switch mb-0">
                            <input class="form-check-input" type="checkbox" name="student_login" value="1"
                                   <?= ($settings['student_login']??'0')==='1'?'checked':'' ?>
                                   style="width:44px;height:24px;cursor:pointer">
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary-g w-100 py-2">
                        <i class="bi bi-save me-2"></i>Save Configuration
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>
</div>
</div>

