<?php $pageTitle='Edit Student'; ?>

<div class="d-flex align-items-center gap-3 mb-4">
    <a href="<?= url('admin/students/'.$student['student_id']) ?>" class="btn btn-icon btn-outline-secondary" title="Back to Profile">
        <i class="bi bi-arrow-left"></i>
    </a>
    <div>
        <h4 class="mb-0 fw-700 text-primary">Edit Student</h4>
        <div class="text-tertiary small">Modify student details for <strong><?= e($student['full_name']) ?></strong></div>
    </div>
</div>

<div class="row justify-content-center">
<div class="col-lg-9">

<form method="POST" action="<?= url('admin/students/'.$student['student_id'].'/update') ?>" enctype="multipart/form-data">
    <?= csrf_field() ?>

    <!-- ① Basic Info -->
    <div class="card mb-4">
        <div class="card-header">
            <h6 class="card-title mb-0">
                <i class="bi bi-person-badge text-primary me-2"></i>Basic Information
                <span class="badge badge-error ms-2" style="font-size:0.65rem">* Required</span>
            </h6>
        </div>
        <div class="card-body">
            <div class="row g-3">

                <!-- Academic Year -->
                <div class="col-md-6">
                    <label class="form-label font-medium">Academic Year</label>
                    <select name="year_id" class="form-select">
                        <option value="">-- No Year --</option>
                        <?php foreach($years as $yr): ?>
                            <option value="<?= $yr['year_id'] ?>" <?= $student['year_id'] == $yr['year_id'] ? 'selected' : '' ?>>
                                <?= e($yr['year_name'] ?? $yr['label'] ?? '') ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <!-- Status -->
                <div class="col-md-6">
                    <label class="form-label font-medium">Account Status</label>
                    <select name="status" class="form-select">
                        <option value="active"   <?= $student['status']==='active'?'selected':'' ?>>Active</option>
                        <option value="inactive" <?= $student['status']==='inactive'?'selected':'' ?>>Inactive</option>
                        <option value="left"     <?= $student['status']==='left'?'selected':'' ?>>Left / Discontinued</option>
                    </select>
                </div>

                <!-- Full Name -->
                <div class="col-md-6">
                    <label class="form-label font-medium">Full Name <span class="text-error">*</span></label>
                    <input type="text" name="full_name" class="form-control" value="<?= e($student['full_name']) ?>" required>
                </div>

                <!-- Phone -->
                <div class="col-md-6">
                    <label class="form-label font-medium">Phone Number <span class="text-error">*</span></label>
                    <input type="tel" name="phone" class="form-control" value="<?= e($student['phone']) ?>" required maxlength="15">
                </div>

                <!-- Email -->
                <div class="col-md-6">
                    <label class="form-label font-medium">Email</label>
                    <input type="email" name="email" class="form-control" value="<?= e($student['email']) ?>">
                </div>

                <!-- Registration No -->
                <div class="col-md-6">
                    <label class="form-label font-medium">Registration No</label>
                    <input type="text" name="reg_number" class="form-control font-medium" value="<?= e($student['reg_number']) ?>" readonly>
                    <div class="form-text">Registration number cannot be changed.</div>
                </div>

            </div>
        </div>
    </div>

    <!-- ② Room & Stay -->
    <div class="card mb-4">
        <div class="card-header">
            <h6 class="card-title mb-0"><i class="bi bi-door-closed text-primary me-2"></i>Room & Stay Details</h6>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-4">
                    <label class="form-label font-medium">Room Number</label>
                    <input type="text" name="room_number" class="form-control" value="<?= e($student['room_number']) ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label font-medium">Joining Date</label>
                    <input type="date" name="joined_at" class="form-control" value="<?= e($student['joined_at'] ?? '') ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label font-medium">Gender</label>
                    <select name="gender" class="form-select">
                        <option value="male"   <?= $student['gender']==='male'?'selected':'' ?>>Male</option>
                        <option value="female" <?= $student['gender']==='female'?'selected':'' ?>>Female</option>
                        <option value="other"  <?= $student['gender']==='other'?'selected':'' ?>>Other</option>
                    </select>
                </div>
                <div class="col-12">
                    <label class="form-label font-medium">Address</label>
                    <textarea name="address" class="form-control" rows="2"><?= e($student['address']) ?></textarea>
                </div>
            </div>
        </div>
    </div>

    <!-- ③ Guardian & Emergency -->
    <div class="card mb-4">
        <div class="card-header">
            <h6 class="card-title mb-0"><i class="bi bi-people text-primary me-2"></i>Guardian & Emergency Contact</h6>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-md-6">
                    <label class="form-label font-medium">Guardian Name</label>
                    <input type="text" name="guardian_name" class="form-control" value="<?= e($student['guardian_name'] ?? '') ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label font-medium">Guardian Phone</label>
                    <input type="tel" name="guardian_phone" class="form-control" value="<?= e($student['guardian_phone'] ?? '') ?>" maxlength="15">
                </div>
                <div class="col-md-6">
                    <label class="form-label font-medium">Date of Birth</label>
                    <input type="date" name="dob" class="form-control" value="<?= e($student['dob'] ?? '') ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label font-medium">Blood Group</label>
                    <select name="blood_group" class="form-select">
                        <option value="">-- Unknown --</option>
                        <?php foreach(['A+','A-','B+','B-','AB+','AB-','O+','O-'] as $bg): ?>
                        <option value="<?= $bg ?>" <?= ($student['blood_group'] ?? '')===$bg?'selected':'' ?>><?= $bg ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label font-medium">Emergency Contact</label>
                    <input type="tel" name="emergency_contact" class="form-control" value="<?= e($student['emergency_contact'] ?? '') ?>" maxlength="15">
                </div>
            </div>
        </div>
    </div>

    <!-- ④ Photo -->
    <div class="card mb-4">
        <div class="card-header">
            <h6 class="card-title mb-0"><i class="bi bi-camera text-primary me-2"></i>Update Photo</h6>
        </div>
        <div class="card-body">
            <div class="d-flex align-items-center gap-4">
                <?php if($student['photo_path']): ?>
                    <img src="<?= url($student['photo_path']) ?>" class="rounded-circle border" style="width:80px; height:80px; object-fit:cover;">
                <?php else: ?>
                    <div class="bg-light rounded-circle d-flex align-items-center justify-content-center text-tertiary" style="width:80px; height:80px;">
                        <i class="bi bi-person fs-2"></i>
                    </div>
                <?php endif; ?>
                
                <div class="flex-grow-1">
                    <input type="file" name="photo" class="form-control" accept="image/jpeg,image/png,image/webp" style="max-width:320px">
                    <div class="form-text">Leave blank to keep current photo. JPG/PNG/WEBP. Max 2 MB.</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Actions -->
    <div class="d-flex gap-3 mb-5">
        <button type="submit" class="btn btn-primary btn-lg px-5">
            <i class="bi bi-check-circle me-2"></i>Save Changes
        </button>
        <a href="<?= url('admin/students/'.$student['student_id']) ?>" class="btn btn-outline-secondary btn-lg">Cancel</a>
    </div>

</form>
</div>
</div>
