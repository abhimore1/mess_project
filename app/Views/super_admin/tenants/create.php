<?php $pageTitle='Add New Mess'; ?>
<div class="row justify-content-center">
<div class="col-lg-7">
<div class="panel">
    <div class="panel-header"><h6><i class="bi bi-building-add me-2"></i>Create New Mess</h6></div>
    <div class="panel-body">
        <form method="POST" action="<?= url('super/tenants/store') ?>">
            <?= csrf_field() ?>
            <div class="row g-3">
                <div class="col-md-6"><label class="form-label">MESS NAME *</label>
                    <input type="text" name="name" class="form-control" required placeholder="e.g. Green Valley Mess"></div>
                <div class="col-md-6"><label class="form-label">OWNER NAME</label>
                    <input type="text" name="owner_name" class="form-control" placeholder="e.g. John Doe"></div>
                <div class="col-md-6"><label class="form-label">CONTACT EMAIL *</label>
                    <input type="email" name="contact_email" class="form-control" required></div>
                <div class="col-md-6"><label class="form-label">CONTACT PHONE</label>
                    <input type="text" name="contact_phone" class="form-control"></div>
                <div class="col-12"><label class="form-label">ADDRESS</label>
                    <textarea name="address" class="form-control" rows="2"></textarea></div>
                <div class="col-md-4"><label class="form-label">CITY</label>
                    <input type="text" name="city" class="form-control" placeholder="e.g. Pune"></div>
                <div class="col-md-4"><label class="form-label">STATE</label>
                    <input type="text" name="state" class="form-control" placeholder="e.g. Maharashtra"></div>
                <div class="col-md-4"><label class="form-label">PINCODE</label>
                    <input type="text" name="pincode" class="form-control" placeholder="e.g. 411001"></div>
                <div class="col-md-6"><label class="form-label">SUBSCRIPTION PLAN *</label>
                    <select name="plan_id" class="form-select" required>
                        <?php foreach ($plans as $p): ?>
                        <option value="<?= $p['plan_id'] ?>"><?= e($p['name']) ?> — ₹<?= number_format($p['price_monthly']) ?>/mo</option>
                        <?php endforeach; ?>
                    </select></div>
                <div class="col-md-6"><label class="form-label">ADMIN PASSWORD</label>
                    <input type="text" name="admin_password" class="form-control" value="Admin@123" required></div>
                <div class="col-md-6"><label class="form-label">PRIMARY COLOR</label>
                    <input type="color" name="primary_color" class="form-control" value="#6366f1" style="height:38px;border-radius:10px"></div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary-g"><i class="bi bi-check-circle me-2"></i>Create Mess</button>
                    <a href="<?= url('super/tenants') ?>" class="btn btn-outline-secondary ms-2">Cancel</a>
                </div>
            </div>
        </form>
    </div>
</div>
</div></div>
