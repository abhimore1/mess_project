<?php $pageTitle = 'Attendance Report'; ?>

<div class="page-header d-flex align-items-center justify-content-between mb-4 animate-fadeInUp">
    <div>
        <h4 class="fw-700 mb-1">Attendance Report</h4>
        <p class="text-muted small mb-0">Monthly breakdown of student meal attendance.</p>
    </div>
    <div class="d-flex gap-2">
        <button class="btn btn-outline-primary btn-sm shadow-sm" onclick="window.print()">
            <i class="bi bi-printer me-2"></i>Print Report
        </button>
        <a href="<?= url('admin/attendance') ?>" class="btn btn-primary-g btn-sm shadow-sm">
            <i class="bi bi-arrow-left me-2"></i>Back to Marking
        </a>
    </div>
</div>

<div class="card border-0 shadow-sm mb-4 animate-fadeInUp stagger-1">
    <div class="card-body p-3">
        <form class="row g-2 align-items-end" method="GET">
            <div class="col-md-4">
                <label class="form-label small fw-700 text-muted text-uppercase tracking-wider">Select Month</label>
                <div class="input-group shadow-xs">
                    <span class="input-group-text bg-white border-0"><i class="bi bi-calendar3"></i></span>
                    <input type="month" name="month" class="form-control border-0 bg-surface-variant" 
                           value="<?= e($month) ?>" onchange="this.form.submit()">
                </div>
            </div>
            <div class="col-md-5">
                <label class="form-label small fw-700 text-muted text-uppercase tracking-wider">Specific Student (Optional)</label>
                <div class="input-group shadow-xs">
                    <span class="input-group-text bg-white border-0"><i class="bi bi-person"></i></span>
                    <select name="student_id" class="form-select border-0 bg-surface-variant" onchange="this.form.submit()">
                        <option value="">All Students</option>
                        <?php foreach($students as $s): ?>
                            <option value="<?= $s['student_id'] ?>" <?= $studentId == $s['student_id'] ? 'selected' : '' ?>>
                                <?= e($s['full_name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-dark w-100 py-2 shadow-sm fw-600">
                    <i class="bi bi-filter me-2"></i>Apply Filters
                </button>
            </div>
        </form>
    </div>
</div>

<div class="row g-4 animate-fadeInUp stagger-2">
    <!-- Summary Stats -->
    <div class="col-lg-3">
        <div class="d-flex flex-column gap-3 sticky-top" style="top: 1rem;">
            <div class="card border-0 shadow-sm bg-success text-white">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="small opacity-75 fw-600">TOTAL PRESENT</div>
                            <h3 class="fw-800 mb-0"><?= count(array_filter($records, fn($r) => $r['status'] === 'present')) ?></h3>
                        </div>
                        <i class="bi bi-check-circle fs-1 opacity-25"></i>
                    </div>
                </div>
            </div>
            <div class="card border-0 shadow-sm bg-danger text-white">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="small opacity-75 fw-600">TOTAL ABSENT</div>
                            <h3 class="fw-800 mb-0"><?= count(array_filter($records, fn($r) => $r['status'] === 'absent')) ?></h3>
                        </div>
                        <i class="bi bi-x-circle fs-1 opacity-25"></i>
                    </div>
                </div>
            </div>
            <div class="card border-0 shadow-sm bg-info text-white">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="small opacity-75 fw-600">TOTAL LEAVE</div>
                            <h3 class="fw-800 mb-0"><?= count(array_filter($records, fn($r) => $r['status'] === 'leave')) ?></h3>
                        </div>
                        <i class="bi bi-calendar-event fs-1 opacity-25"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Data Table -->
    <div class="col-lg-9">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white border-bottom py-3">
                <h6 class="fw-700 mb-0"><i class="bi bi-list-check me-2 text-primary"></i>Daily Records</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-surface-variant">
                            <tr>
                                <th class="px-4 py-3 small fw-700 text-muted">DATE</th>
                                <th class="py-3 small fw-700 text-muted">MEAL SLOT</th>
                                <th class="py-3 small fw-700 text-muted">STUDENT</th>
                                <th class="py-3 small fw-700 text-muted text-center">STATUS</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(empty($records)): ?>
                                <tr>
                                    <td colspan="4" class="text-center py-5">
                                        <i class="bi bi-file-earmark-x fs-1 opacity-25 d-block mb-3"></i>
                                        <p class="text-muted mb-0">No attendance records found for this period.</p>
                                    </td>
                                </tr>
                            <?php endif; ?>
                            <?php foreach($records as $r): ?>
                                <tr>
                                    <td class="px-4">
                                        <div class="fw-600 text-dark"><?= date('d M Y', strtotime($r['date'])) ?></div>
                                        <div class="small text-muted"><?= date('l', strtotime($r['date'])) ?></div>
                                    </td>
                                    <td>
                                        <span class="badge bg-surface-variant text-primary fw-600"><?= e($r['slot_name']) ?></span>
                                    </td>
                                    <td>
                                        <div class="fw-700 text-dark"><?= e($r['full_name']) ?></div>
                                    </td>
                                    <td class="text-center">
                                        <?php 
                                            $badgeClass = 'bg-secondary-subtle text-secondary';
                                            if($r['status'] === 'present') $badgeClass = 'bg-success text-white shadow-sm';
                                            elseif($r['status'] === 'absent') $badgeClass = 'bg-danger text-white shadow-sm';
                                            elseif($r['status'] === 'leave') $badgeClass = 'bg-info text-white shadow-sm';
                                        ?>
                                        <span class="badge rounded-pill px-3 py-2 <?= $badgeClass ?>" style="font-size: 0.7rem; text-transform: uppercase;">
                                            <?= e($r['status']) ?>
                                        </span>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    .page-header, form, .sticky-top { display: none !important; }
    .col-lg-9 { width: 100% !important; }
    .card { border: 1px solid #eee !important; box-shadow: none !important; }
    .table-responsive { overflow: visible !important; }
    body { background: white !important; }
}
.tracking-wider { letter-spacing: 0.05em; }
</style>
