<?php $pageTitle = e($student['full_name']) . ' — Profile'; ?>

<div class="d-flex align-items-center gap-3 mb-4">
    <a href="<?= url('admin/students') ?>" class="btn btn-icon btn-outline-secondary" title="Back to Students">
        <i class="bi bi-arrow-left"></i>
    </a>
    <div>
        <h4 class="mb-0 fw-700 text-primary">Student Profile</h4>
        <div class="text-tertiary small">Managing information for <strong><?= e($student['full_name']) ?></strong></div>
    </div>
    <div class="ms-auto d-flex gap-2">
        <a href="<?= url('admin/students/'.$student['student_id'].'/edit') ?>" class="btn btn-primary">
            <i class="bi bi-pencil me-2"></i>Edit Profile
        </a>
    </div>
</div>

<div class="row g-4">
    <!-- Left Column: Quick Info -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body text-center p-4">
                <div class="mb-3 position-relative d-inline-block">
                    <?php if($student['photo_path']): ?>
                        <img src="<?= url($student['photo_path']) ?>" class="rounded-circle border p-1" width="120" height="120" style="object-fit:cover">
                    <?php else: ?>
                        <div class="rounded-circle bg-primary bg-opacity-10 d-flex align-items-center justify-content-center text-primary fw-800 fs-1 border border-primary border-opacity-25" style="width:120px; height:120px;">
                            <?= strtoupper(substr($student['full_name'],0,1)) ?>
                        </div>
                    <?php endif; ?>
                    <div class="position-absolute bottom-0 end-0">
                        <span class="badge rounded-pill bg-success border border-white p-2" title="Active Account">
                            <span class="visually-hidden">Active</span>
                        </span>
                    </div>
                </div>
                
                <h5 class="fw-700 mb-1"><?= e($student['full_name']) ?></h5>
                <div class="badge bg-light text-primary border mb-3"><?= e($student['reg_number']) ?></div>
                
                <div class="d-flex justify-content-center gap-2 mb-4">
                    <?= badge($student['status']) ?>
                </div>

                <div class="text-start pt-3 border-top mt-2">
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="bg-light rounded p-2 text-primary">
                            <i class="bi bi-telephone fs-5"></i>
                        </div>
                        <div>
                            <div class="text-tertiary small">Phone</div>
                            <div class="fw-600"><?= e($student['phone']) ?></div>
                        </div>
                    </div>
                    
                    <div class="d-flex align-items-center gap-3 mb-3">
                        <div class="bg-light rounded p-2 text-primary">
                            <i class="bi bi-envelope fs-5"></i>
                        </div>
                        <div class="text-truncate">
                            <div class="text-tertiary small">Email</div>
                            <div class="fw-600"><?= e($student['email'] ?: '—') ?></div>
                        </div>
                    </div>

                    <div class="d-flex align-items-center gap-3">
                        <div class="bg-light rounded p-2 text-primary">
                            <i class="bi bi-door-open fs-5"></i>
                        </div>
                        <div>
                            <div class="text-tertiary small">Room</div>
                            <div class="fw-600"><?= e($student['room_number'] ?: '—') ?></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Academic Context -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent border-0 pt-4 px-4 pb-0">
                <h6 class="fw-700 mb-0">System Info</h6>
            </div>
            <div class="card-body p-4">
                <div class="mb-3">
                    <label class="text-tertiary small d-block mb-1">Joined Date</label>
                    <div class="fw-600"><i class="bi bi-calendar-check me-2 text-muted"></i><?= format_date($student['joined_at']) ?></div>
                </div>
                <div>
                    <label class="text-tertiary small d-block mb-1">Registration Date</label>
                    <div class="fw-600"><i class="bi bi-clock me-2 text-muted"></i><?= format_date($student['created_at']) ?></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Right Column: Detailed Info & Tabs -->
    <div class="col-lg-8">
        
        <!-- Detailed Information Card -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-transparent border-0 pt-4 px-4 pb-0 d-flex align-items-center justify-content-between">
                <h6 class="fw-700 mb-0"><i class="bi bi-info-circle text-primary me-2"></i>Personal & Guardian Details</h6>
                <span class="badge bg-primary bg-opacity-10 text-primary px-3 py-2 rounded-pill">Blood: <?= e($student['blood_group'] ?: 'N/A') ?></span>
            </div>
            <div class="card-body p-4">
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded-3">
                            <div class="text-tertiary small mb-1"><i class="bi bi-people me-2"></i>Guardian Name</div>
                            <div class="fw-700"><?= e($student['guardian_name'] ?: '—') ?></div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="p-3 bg-light rounded-3">
                            <div class="text-tertiary small mb-1"><i class="bi bi-telephone-outbound me-2"></i>Guardian Phone</div>
                            <div class="fw-700"><?= e($student['guardian_phone'] ?: '—') ?></div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <label class="text-tertiary small mb-1">Gender</label>
                        <div class="fw-600"><?= ucfirst(e($student['gender'] ?: '—')) ?></div>
                    </div>
                    <div class="col-md-4">
                        <label class="text-tertiary small mb-1">Date of Birth</label>
                        <div class="fw-600"><?= format_date($student['dob']) ?></div>
                    </div>
                    <div class="col-md-4">
                        <label class="text-tertiary small mb-1">Emergency Contact</label>
                        <div class="fw-600 text-error font-medium"><?= e($student['emergency_contact'] ?: '—') ?></div>
                    </div>
                    <div class="col-12">
                        <label class="text-tertiary small mb-1">Permanent Address</label>
                        <div class="fw-500 bg-light p-3 rounded border-start border-primary border-4">
                            <?= nl2br(e($student['address'] ?: 'No address provided.')) ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Dynamic Tabs -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-0 p-0">
                <ul class="nav nav-pills p-3 gap-2" id="studentTab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active rounded-pill px-4" data-bs-toggle="tab" data-bs-target="#memberships" type="button">Memberships</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link rounded-pill px-4" data-bs-toggle="tab" data-bs-target="#payments" type="button">Payments</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link rounded-pill px-4" data-bs-toggle="tab" data-bs-target="#attendance" type="button">Attendance</button>
                    </li>
                </ul>
            </div>
            <div class="card-body p-0">
                <div class="tab-content">
                    <!-- Memberships -->
                    <div class="tab-pane fade show active p-4" id="memberships">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Plan Name</th>
                                        <th>Period</th>
                                        <th class="text-center">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($memberships as $m): ?>
                                    <tr>
                                        <td class="fw-600 text-primary"><?= e($m['plan_name']) ?></td>
                                        <td>
                                            <div class="small text-muted">From: <?= format_date($m['start_date']) ?></div>
                                            <div class="small text-muted">To: <?= format_date($m['end_date']) ?></div>
                                        </td>
                                        <td class="text-center"><?= badge($m['status']) ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                    <?php if(empty($memberships)): ?>
                                    <tr>
                                        <td colspan="3" class="text-center py-5 text-tertiary">
                                            <i class="bi bi-folder-x fs-1 d-block mb-2"></i>
                                            No active or past memberships found.
                                        </td>
                                    </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Payments -->
                    <div class="tab-pane fade p-4" id="payments">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Receipt #</th>
                                        <th>Amount</th>
                                        <th>Date</th>
                                        <th class="text-center">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($payments as $p): ?>
                                    <tr>
                                        <td><a href="<?= url('admin/payments/'.$p['payment_id'].'/receipt') ?>" class="fw-600 text-decoration-none"><?= e($p['receipt_number']) ?></a></td>
                                        <td class="fw-700 text-dark"><?= format_currency($p['net_amount']) ?></td>
                                        <td class="text-muted"><?= format_date($p['payment_date']) ?></td>
                                        <td class="text-center"><?= badge($p['status']) ?></td>
                                    </tr>
                                    <?php endforeach; ?>
                                    <?php if(empty($payments)): ?>
                                    <tr>
                                        <td colspan="4" class="text-center py-5 text-tertiary">
                                            <i class="bi bi-cash-stack fs-1 d-block mb-2"></i>
                                            No payment records available.
                                        </td>
                                    </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <!-- Attendance -->
                    <div class="tab-pane fade p-4" id="attendance">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>Date</th>
                                        <th>Meal Slot</th>
                                        <th class="text-center">Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($attendance as $a): ?>
                                    <tr>
                                        <td class="fw-500"><?= format_date($a['date']) ?></td>
                                        <td class="text-muted"><?= e($a['slot_name']) ?></td>
                                        <td class="text-center">
                                            <?php if($a['status']==='present'): ?><span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3">Present</span>
                                            <?php elseif($a['status']==='absent'): ?><span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-3">Absent</span>
                                            <?php else: ?><span class="badge bg-info bg-opacity-10 text-info rounded-pill px-3">Leave</span><?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                    <?php if(empty($attendance)): ?>
                                    <tr>
                                        <td colspan="3" class="text-center py-5 text-tertiary">
                                            <i class="bi bi-calendar-x fs-1 d-block mb-2"></i>
                                            No attendance recorded yet.
                                        </td>
                                    </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
