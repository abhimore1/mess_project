<?php $pageTitle='My Profile'; ?>
<div class="row justify-content-center">
<div class="col-lg-8">
    <div class="panel p-4 text-center">
        <?php if($student['photo_path']): ?>
            <img src="<?= url($student['photo_path']) ?>" class="rounded-circle mb-3 shadow-sm" width="100" height="100" style="object-fit:cover">
        <?php else: ?>
            <div style="width:100px;height:100px;border-radius:50%;background:linear-gradient(135deg,var(--primary),var(--accent));display:flex;align-items:center;justify-content:center;font-weight:800;font-size:2.5rem;color:#fff;margin:0 auto 1rem" class="shadow-sm">
                <?= strtoupper(substr($student['full_name'],0,1)) ?>
            </div>
        <?php endif; ?>
        <h4 class="fw-800 mb-1"><?= e($student['full_name']) ?></h4>
        <p class="text-muted mb-2">Reg No: <?= e($student['reg_number']) ?></p>
        <div class="mb-4"><?= badge($student['status']) ?></div>
        
        <div class="row text-start mt-4 pt-4 border-top" style="border-color:var(--border)!important">
            <div class="col-sm-6 mb-3">
                <label class="text-muted small fw-600">PHONE</label>
                <div><?= e($student['phone']) ?></div>
            </div>
            <div class="col-sm-6 mb-3">
                <label class="text-muted small fw-600">EMAIL</label>
                <div><?= e($student['email']??'—') ?></div>
            </div>
            <div class="col-sm-6 mb-3">
                <label class="text-muted small fw-600">ROOM NUMBER</label>
                <div><?= e($student['room_number']??'—') ?></div>
            </div>
            <div class="col-sm-6 mb-3">
                <label class="text-muted small fw-600">DATE OF JOINING</label>
                <div><?= format_date($student['joined_at']) ?></div>
            </div>
            <div class="col-12 mb-3">
                <label class="text-muted small fw-600">ADDRESS</label>
                <div><?= e($student['address']??'—') ?></div>
            </div>
            <div class="col-12">
                <label class="text-muted small fw-600">EMERGENCY CONTACT</label>
                <div><?= e($student['emergency_contact']??'—') ?></div>
            </div>
        </div>
    </div>
</div>
</div>
