<?php $pageTitle='Edit — '.e($tenant['name']); ?>
<div class="row justify-content-center">
<div class="col-lg-7">
<div class="panel">
    <div class="panel-header"><h6><i class="bi bi-pencil me-2"></i>Edit Mess</h6></div>
    <div class="panel-body">
        <form method="POST" action="<?= url('super/tenants/'.$tenant['tenant_id'].'/update') ?>">
            <?= csrf_field() ?>
            <div class="row g-3">
                <div class="col-md-6"><label class="form-label">MESS NAME</label>
                    <input type="text" name="name" class="form-control" value="<?= e($tenant['name']) ?>" required></div>
                <div class="col-md-6"><label class="form-label">OWNER NAME</label>
                    <input type="text" name="owner_name" class="form-control" value="<?= e($tenant['owner_name']??'') ?>" placeholder="e.g. John Doe"></div>
                <div class="col-md-6"><label class="form-label">CONTACT EMAIL</label>
                    <input type="email" name="contact_email" class="form-control" value="<?= e($tenant['contact_email']) ?>"></div>
                <div class="col-md-6"><label class="form-label">CONTACT PHONE</label>
                    <input type="text" name="contact_phone" class="form-control" value="<?= e($tenant['contact_phone']) ?>"></div>
                <div class="col-12"><label class="form-label">ADDRESS</label>
                    <textarea name="address" class="form-control" rows="2"><?= e($tenant['address']) ?></textarea></div>
                <div class="col-md-4"><label class="form-label">CITY</label>
                    <input type="text" name="city" class="form-control" value="<?= e($tenant['city']) ?>"></div>
                <div class="col-md-4"><label class="form-label">STATE</label>
                    <input type="text" name="state" class="form-control" value="<?= e($tenant['state']) ?>"></div>
                <div class="col-md-4"><label class="form-label">PINCODE</label>
                    <input type="text" name="pincode" class="form-control" value="<?= e($tenant['pincode']) ?>"></div>
                <div class="col-md-6"><label class="form-label">STATUS</label>
                    <select name="status" class="form-select">
                        <option value="active" <?= $tenant['status']==='active'?'selected':'' ?>>Active</option>
                        <option value="inactive" <?= $tenant['status']==='inactive'?'selected':'' ?>>Inactive</option>
                        <option value="suspended" <?= $tenant['status']==='suspended'?'selected':'' ?>>Suspended</option>
                    </select></div>
                <div class="col-md-6"><label class="form-label">PLAN</label>
                    <select name="plan_id" class="form-select">
                        <?php foreach($plans as $p): ?>
                        <option value="<?= $p['plan_id'] ?>" <?= $p['plan_id']==$tenant['plan_id']?'selected':'' ?>><?= e($p['name']) ?></option>
                        <?php endforeach; ?>
                    </select></div>
                <div class="col-md-6"><label class="form-label">PRIMARY COLOR</label>
                    <input type="color" name="primary_color" class="form-control" value="<?= e($tenant['primary_color']??'#6366f1') ?>" style="height:38px;border-radius:10px"></div>
                <div class="col-md-6"><label class="form-label">SECONDARY COLOR</label>
                    <input type="color" name="secondary_color" class="form-control" value="<?= e($tenant['secondary_color']??'#06b6d4') ?>" style="height:38px;border-radius:10px"></div>
                <div class="col-12 mt-4">
                    <button type="submit" class="btn btn-primary-g"><i class="bi bi-save me-2"></i>Update Mess</button>
                    <a href="<?= url('super/tenants') ?>" class="btn btn-outline-secondary ms-2">Cancel</a>
                </div>
            </div>
        </form>
    </div>
</div>
</div></div>
