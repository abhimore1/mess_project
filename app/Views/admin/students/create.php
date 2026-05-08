<?php $pageTitle='Add Student'; ?>

<div class="d-flex align-items-center gap-3 mb-4">
    <a href="<?= url('admin/students') ?>" class="btn btn-icon btn-outline-secondary" title="Back to Students">
        <i class="bi bi-arrow-left"></i>
    </a>
    <div>
        <h4 class="mb-0 fw-700 text-primary">Add New Student</h4>
        <div class="text-tertiary small">Fill in the details below to register a student</div>
    </div>
</div>

<div class="row justify-content-center">
<div class="col-lg-9">

<form method="POST" action="<?= url('admin/students/store') ?>" enctype="multipart/form-data">
    <?= csrf_field() ?>

    <!-- ① Required Info -->
    <div class="card mb-4">
        <div class="card-header">
            <h6 class="card-title mb-0">
                <i class="bi bi-person-badge text-primary me-2"></i>Basic Information
                <span class="badge badge-error ms-2" style="font-size:0.65rem">* Required</span>
            </h6>
        </div>
        <div class="card-body">
            <div class="row g-3">

                <!-- Academic Year (prominent, pre-selected) -->
                <div class="col-12">
                    <label class="form-label font-medium">Academic Year</label>
                    <select name="year_id" class="form-select form-select-lg" style="max-width:280px">
                        <option value="">-- No Year --</option>
                        <?php foreach($years as $yr): ?>
                            <option value="<?= $yr['year_id'] ?>"
                                <?= (old('year_id', $defaultYearId) == $yr['year_id']) ? 'selected' : '' ?>>
                                <?= e($yr['year_name'] ?? $yr['label'] ?? '') ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <div class="form-text">Select the academic session for this student.</div>
                </div>

                <!-- Full Name -->
                <div class="col-md-6">
                    <label class="form-label font-medium">Full Name <span class="text-error">*</span></label>
                    <input type="text" name="full_name" class="form-control" placeholder="e.g. Rahul Sharma"
                           value="<?= e(old('full_name')) ?>" required autofocus>
                </div>

                <!-- Phone -->
                <div class="col-md-6">
                    <label class="form-label font-medium">Phone Number <span class="text-error">*</span></label>
                    <input type="tel" name="phone" class="form-control" placeholder="10-digit mobile number"
                           value="<?= e(old('phone')) ?>" required maxlength="15">
                </div>

                <!-- Email -->
                <div class="col-md-6">
                    <label class="form-label font-medium">Email <span class="text-tertiary font-normal">(optional)</span></label>
                    <input type="email" name="email" class="form-control" placeholder="student@email.com"
                           value="<?= e(old('email')) ?>">
                </div>

                <!-- Registration No (auto-suggested, editable) -->
                <div class="col-md-6">
                    <label class="form-label font-medium">Registration No
                        <span class="badge badge-secondary ms-1" style="font-size:0.6rem">Auto-generated</span>
                    </label>
                    <input type="text" name="reg_number" class="form-control font-medium"
                           value="<?= e(old('reg_number', $suggestReg)) ?>"
                           placeholder="STU-00001">
                    <div class="form-text">Auto-filled. Edit if needed.</div>
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
                    <input type="text" name="room_number" class="form-control" placeholder="e.g. A-101"
                           value="<?= e(old('room_number')) ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label font-medium">Joining Date</label>
                    <input type="date" name="joined_at" class="form-control"
                           value="<?= e(old('joined_at', date('Y-m-d'))) ?>">
                </div>
                <div class="col-md-4">
                    <label class="form-label font-medium">Gender</label>
                    <select name="gender" class="form-select">
                        <option value="male"   <?= old('gender','male')==='male'   ? 'selected' : '' ?>>Male</option>
                        <option value="female" <?= old('gender')==='female' ? 'selected' : '' ?>>Female</option>
                        <option value="other"  <?= old('gender')==='other'  ? 'selected' : '' ?>>Other</option>
                    </select>
                </div>
                <div class="col-12">
                    <label class="form-label font-medium">Address</label>
                    <textarea name="address" class="form-control" rows="2"
                              placeholder="Home address of student"><?= e(old('address')) ?></textarea>
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
                    <input type="text" name="guardian_name" class="form-control" placeholder="Parent / Guardian name"
                           value="<?= e(old('guardian_name')) ?>">
                </div>
                <div class="col-md-6">
                    <label class="form-label font-medium">Guardian Phone</label>
                    <input type="tel" name="guardian_phone" class="form-control" placeholder="Guardian contact"
                           value="<?= e(old('guardian_phone')) ?>" maxlength="15">
                </div>
                <div class="col-md-6">
                    <label class="form-label font-medium">Date of Birth</label>
                    <input type="date" name="dob" class="form-control" value="<?= e(old('dob')) ?>">
                </div>
                <div class="col-md-3">
                    <label class="form-label font-medium">Blood Group</label>
                    <select name="blood_group" class="form-select">
                        <option value="">-- Unknown --</option>
                        <?php foreach(['A+','A-','B+','B-','AB+','AB-','O+','O-'] as $bg): ?>
                        <option value="<?= $bg ?>" <?= old('blood_group')===$bg?'selected':'' ?>><?= $bg ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label font-medium">Emergency Contact</label>
                    <input type="tel" name="emergency_contact" class="form-control" placeholder="Alt. contact"
                           value="<?= e(old('emergency_contact')) ?>" maxlength="15">
                </div>
            </div>
        </div>
    </div>

    <!-- ④ Photo -->
    <div class="card mb-4">
        <div class="card-header">
            <h6 class="card-title mb-0"><i class="bi bi-camera text-primary me-2"></i>Photo <span class="text-tertiary font-normal">(optional)</span></h6>
        </div>
        <div class="card-body">
            <input type="file" name="photo" class="form-control" accept="image/jpeg,image/png,image/webp" style="max-width:320px">
            <div class="form-text">JPG/PNG/WEBP. Max 2 MB.</div>
        </div>
    </div>

    <!-- Actions -->
    <div class="d-flex gap-3 mb-5">
        <button type="submit" class="btn btn-primary btn-lg px-5">
            <i class="bi bi-person-check me-2"></i>Register Student
        </button>
        <a href="<?= url('admin/students') ?>" class="btn btn-outline-secondary btn-lg">Cancel</a>
    </div>

</form>
</div>
</div>
