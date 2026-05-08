<?php $pageTitle = 'My Profile'; ?>

<div class="container-fluid px-0">
    <div class="row g-4">
        <!-- Profile Header/Summary Card -->
        <div class="col-12">
            <div class="card border-0 shadow-sm overflow-hidden animate-fadeIn" style="border-radius: 24px;">
                <div class="profile-cover"></div>
                <div class="card-body p-4 pt-0">
                    <div class="d-flex flex-column flex-md-row align-items-center gap-4 mt-n5 position-relative">
                        <div class="profile-avatar-wrapper shadow-lg position-relative">
                            <?php if ($student['photo_path']): ?>
                                <img src="<?= url($student['photo_path']) ?>" class="w-100 h-100 object-fit-cover rounded-circle" id="profileImgPreview">
                            <?php else: ?>
                                <div class="w-100 h-100 d-flex align-items-center justify-content-center bg-primary text-white fs-1 fw-bold rounded-circle" id="profileInitials">
                                    <?= strtoupper(substr($student['full_name'], 0, 1)) ?>
                                </div>
                            <?php endif; ?>
                            <div class="status-indicator <?= $student['status'] === 'active' ? 'bg-success' : 'bg-secondary' ?>"></div>
                            
                            <!-- Photo Upload Trigger -->
                            <button class="btn btn-sm btn-primary rounded-circle position-absolute bottom-0 end-0 shadow-sm border border-2 border-white d-flex align-items-center justify-content-center" 
                                    style="width:36px; height:36px; z-index: 15;" onclick="document.getElementById('photoInput').click()">
                                <i class="bi bi-camera-fill"></i>
                            </button>
                            <form action="<?= url('student/profile/photo') ?>" method="POST" enctype="multipart/form-data" id="photoForm" class="d-none">
                                <?= csrf_field() ?>
                                <input type="file" name="photo" id="photoInput" onchange="this.form.submit()">
                            </form>
                        </div>
                        <div class="text-center text-md-start pt-md-4 mt-2">
                            <h3 class="fw-700 mb-1"><?= e($student['full_name']) ?></h3>
                            <div class="d-flex flex-wrap align-items-center justify-content-center justify-content-md-start gap-2 mb-3">
                                <span class="badge rounded-pill bg-surface-variant text-dark px-3 py-2 fw-600 small">
                                    <i class="bi bi-person-badge me-1"></i> <?= e($student['reg_number']) ?>
                                </span>
                                <span class="badge rounded-pill <?= $student['status'] === 'active' ? 'bg-success-subtle text-success' : 'bg-secondary-subtle text-secondary' ?> px-3 py-2 fw-600 small">
                                    <?= ucfirst($student['status']) ?>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Left Column: Details -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-4 animate-fadeInUp" style="border-radius: 20px;">
                <div class="card-header bg-white border-0 py-3 px-4">
                    <h6 class="fw-700 mb-0 d-flex align-items-center">
                        <i class="bi bi-person me-2 text-primary"></i>Personal & Academic Information
                    </h6>
                </div>
                <div class="card-body p-4 pt-2">
                    <div class="row g-4">
                        <div class="col-sm-6">
                            <div class="info-group">
                                <label class="info-label">Gender</label>
                                <div class="info-value"><?= ucfirst(e($student['gender'] ?? 'Not specified')) ?></div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="info-group">
                                <label class="info-label">Date of Birth</label>
                                <div class="info-value"><?= $student['dob'] ? date('d M Y', strtotime($student['dob'])) : '—' ?></div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="info-group">
                                <label class="info-label">Blood Group</label>
                                <div class="info-value"><span class="badge bg-danger-subtle text-danger px-2"><?= e($student['blood_group'] ?? '—') ?></span></div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="info-group">
                                <label class="info-label">Date of Joining</label>
                                <div class="info-value"><?= date('d M Y', strtotime($student['joined_at'])) ?></div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="info-group">
                                <label class="info-label">Room Number</label>
                                <div class="info-value fw-700 text-primary"><?= e($student['room_number'] ?? 'Not Assigned') ?></div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="info-group">
                                <label class="info-label">Permanent Address</label>
                                <div class="info-value"><?= nl2br(e($student['address'] ?? 'No address provided.')) ?></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm animate-fadeInUp stagger-1" style="border-radius: 20px;">
                <div class="card-header bg-white border-0 py-3 px-4">
                    <h6 class="fw-700 mb-0 d-flex align-items-center">
                        <i class="bi bi-shield-check me-2 text-warning"></i>Guardian & Emergency Details
                    </h6>
                </div>
                <div class="card-body p-4 pt-2">
                    <div class="row g-4">
                        <div class="col-sm-6">
                            <div class="info-group">
                                <label class="info-label">Guardian Name</label>
                                <div class="info-value"><?= e($student['guardian_name'] ?? '—') ?></div>
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="info-group">
                                <label class="info-label">Guardian Phone</label>
                                <div class="info-value"><?= e($student['guardian_phone'] ?? '—') ?></div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="info-group p-3 rounded-4 bg-danger-subtle border-start border-4 border-danger">
                                <label class="info-label text-danger mb-1">Emergency Contact</label>
                                <div class="info-value fw-700 text-danger d-flex align-items-center">
                                    <i class="bi bi-telephone-outbound me-2"></i>
                                    <?= e($student['emergency_contact'] ?? 'Not provided') ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right Column: Contact & Security -->
        <div class="col-lg-4">
            <div class="card border-0 shadow-sm mb-4 animate-fadeInUp" style="border-radius: 20px;">
                <div class="card-header bg-white border-0 py-3 px-4">
                    <h6 class="fw-700 mb-0 d-flex align-items-center">
                        <i class="bi bi-envelope me-2 text-primary"></i>Contact Information
                    </h6>
                </div>
                <div class="card-body p-4 pt-2">
                    <div class="contact-item d-flex align-items-center gap-3 mb-4">
                        <div class="contact-icon bg-primary-subtle text-primary rounded-circle shadow-sm">
                            <i class="bi bi-telephone"></i>
                        </div>
                        <div>
                            <div class="x-small text-muted">Phone Number</div>
                            <div class="fw-600"><?= e($student['phone']) ?></div>
                        </div>
                    </div>
                    <div class="contact-item d-flex align-items-center gap-3">
                        <div class="contact-icon bg-success-subtle text-success rounded-circle shadow-sm">
                            <i class="bi bi-envelope-at"></i>
                        </div>
                        <div>
                            <div class="x-small text-muted">Email Address</div>
                            <div class="fw-600"><?= e($student['email'] ?? '—') ?></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Change Password Form -->
            <div class="card border-0 shadow-sm animate-fadeInUp stagger-1" style="border-radius: 20px;">
                <div class="card-header bg-white border-0 py-3 px-4">
                    <h6 class="fw-700 mb-0 d-flex align-items-center">
                        <i class="bi bi-shield-lock me-2 text-warning"></i>Change Password
                    </h6>
                </div>
                <div class="card-body p-4 pt-2">
                    <form action="<?= url('student/profile/password') ?>" method="POST">
                        <?= csrf_field() ?>
                        <div class="mb-3">
                            <label class="form-label x-small fw-700">Current Password</label>
                            <input type="password" name="current_password" class="form-control form-control-sm rounded-pill px-3" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label x-small fw-700">New Password</label>
                            <input type="password" name="new_password" class="form-control form-control-sm rounded-pill px-3" required>
                        </div>
                        <div class="mb-4">
                            <label class="form-label x-small fw-700">Confirm New Password</label>
                            <input type="password" name="confirm_password" class="form-control form-control-sm rounded-pill px-3" required>
                        </div>
                        <button type="submit" class="btn btn-primary-g w-100 rounded-pill py-2 fw-700">
                            Update Password
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.profile-cover {
    height: 120px;
    background: linear-gradient(45deg, var(--primary), var(--secondary));
    opacity: 0.8;
}

.mt-n5 {
    margin-top: -3rem !important;
}

.profile-avatar-wrapper {
    width: 130px;
    height: 130px;
    background: white;
    padding: 6px;
    border-radius: 50%;
    position: relative;
    z-index: 10;
}

.status-indicator {
    position: absolute;
    top: 10px;
    right: 10px;
    width: 24px;
    height: 24px;
    border: 4px solid white;
    border-radius: 50%;
    z-index: 11;
}

.info-group {
    margin-bottom: 0.5rem;
}

.info-label {
    font-size: 0.65rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: var(--text-tertiary);
    margin-bottom: 2px;
}

.info-value {
    font-size: 0.95rem;
    font-weight: 500;
    color: var(--text-primary);
}

.contact-icon {
    width: 44px;
    height: 44px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
}

.bg-primary-subtle { background: #e3f2fd; }
.bg-success-subtle { background: #e8f5e9; }
.bg-danger-subtle { background: #ffebee; }
.bg-secondary-subtle { background: #f5f5f5; }

.x-small {
    font-size: 0.7rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}
</style>
