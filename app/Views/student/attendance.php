<?php $pageTitle = 'My Attendance'; ?>

<div class="container-fluid px-0">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4 animate-fadeIn">
        <div>
            <h4 class="fw-700 mb-1">My Attendance Log 📊</h4>
            <p class="text-muted small mb-0">Track your daily meal attendance and self-mark status.</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <form method="GET" class="d-flex align-items-center gap-2 bg-white p-2 rounded-pill shadow-sm px-3">
                <i class="bi bi-calendar-event text-primary"></i>
                <input type="month" name="month" class="form-control border-0 shadow-none p-0 bg-transparent fw-600 text-dark" 
                       value="<?= e($month) ?>" onchange="this.form.submit()" style="width: auto;">
            </form>
        </div>
    </div>

    <!-- Attendance Summary Cards -->
    <div class="row g-4 mb-4 animate-fadeInUp">
        <?php 
            $presents = 0; $absents = 0; $leaves = 0;
            foreach($records as $r) {
                if($r['status'] === 'present') $presents++;
                elseif($r['status'] === 'absent') $absents++;
                else $leaves++;
            }
        ?>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 bg-success-subtle overflow-hidden">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="x-small text-success mb-1">Total Presents</div>
                            <h2 class="fw-800 text-success mb-0"><?= $presents ?></h2>
                        </div>
                        <div class="bg-white rounded-circle shadow-sm d-flex align-items-center justify-content-center" style="width:50px;height:50px;">
                            <i class="bi bi-check-circle-fill text-success fs-3"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 bg-danger-subtle overflow-hidden">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="x-small text-danger mb-1">Total Absents</div>
                            <h2 class="fw-800 text-danger mb-0"><?= $absents ?></h2>
                        </div>
                        <div class="bg-white rounded-circle shadow-sm d-flex align-items-center justify-content-center" style="width:50px;height:50px;">
                            <i class="bi bi-x-circle-fill text-danger fs-3"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 bg-info-subtle overflow-hidden">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="x-small text-info mb-1">Leaves / Others</div>
                            <h2 class="fw-800 text-info mb-0"><?= $leaves ?></h2>
                        </div>
                        <div class="bg-white rounded-circle shadow-sm d-flex align-items-center justify-content-center" style="width:50px;height:50px;">
                            <i class="bi bi-info-circle-fill text-info fs-3"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Self Mark Section -->
    <div class="card border-0 shadow-sm mb-4 overflow-hidden animate-fadeInUp stagger-1" style="border-radius: 20px;">
        <div class="card-header bg-primary py-3 px-4 d-flex justify-content-between align-items-center">
            <h6 class="fw-700 text-white mb-0 d-flex align-items-center">
                <i class="bi bi-hand-index-thumb me-2"></i>Self Mark Attendance (Today)
            </h6>
            <span class="badge rounded-pill bg-white text-primary x-small"><?= date('d M Y') ?></span>
        </div>
        <div class="card-body p-4">
            <div class="row g-4">
                <?php foreach($slots as $s): ?>
                <div class="col-md-4 col-lg-3">
                    <div class="slot-mark-card p-3 rounded-4 border-2 border-surface-variant d-flex flex-column text-center h-100">
                        <div class="fw-700 text-dark mb-1"><?= e($s['name']) ?></div>
                        <div class="x-small text-muted mb-3"><?= e($s['slot_time']) ?></div>
                        
                        <div class="d-grid gap-2 mt-auto">
                            <button class="btn btn-primary-g rounded-pill btn-sm fw-700 py-2" onclick="markSelf(<?= $s['slot_id'] ?>,'present')">
                                <i class="bi bi-check2 me-1"></i> Present
                            </button>
                            <button class="btn btn-outline-danger rounded-pill btn-sm fw-700 py-2" onclick="markSelf(<?= $s['slot_id'] ?>,'absent')">
                                <i class="bi bi-x-lg me-1"></i> Absent
                            </button>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
                <?php if(empty($slots)): ?>
                <div class="col-12 text-center py-4 text-muted">
                    <i class="bi bi-moon fs-1 opacity-25 mb-2"></i>
                    <div>No active slots found for today.</div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Attendance Records Table -->
    <div class="card border-0 shadow-sm animate-fadeInUp stagger-2" style="border-radius: 20px;">
        <div class="card-header bg-white border-0 py-3 px-4 d-flex justify-content-between align-items-center">
            <h6 class="fw-700 mb-0">Records for <?= date('F Y', strtotime($month.'-01')) ?></h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-surface-variant">
                        <tr>
                            <th class="ps-4 py-3 border-0 x-small">Date</th>
                            <th class="py-3 border-0 x-small">Meal Slot</th>
                            <th class="py-3 border-0 x-small">Status</th>
                            <th class="pe-4 py-3 border-0 x-small">Source</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($records as $r): ?>
                        <tr>
                            <td class="ps-4 small fw-600"><?= date('d M Y, D', strtotime($r['date'])) ?></td>
                            <td>
                                <div class="fw-700 small text-dark"><?= e($r['slot_name']) ?></div>
                                <div class="x-small text-muted"><?= e($r['slot_time']) ?></div>
                            </td>
                            <td>
                                <?php if($r['status']==='present'): ?>
                                    <span class="badge rounded-pill bg-success-subtle text-success px-3">Present</span>
                                <?php elseif($r['status']==='absent'): ?>
                                    <span class="badge rounded-pill bg-danger-subtle text-danger px-3">Absent</span>
                                <?php else: ?>
                                    <span class="badge rounded-pill bg-info-subtle text-info px-3"><?= ucfirst($r['status']) ?></span>
                                <?php endif; ?>
                            </td>
                            <td class="pe-4">
                                <span class="badge bg-light text-muted fw-700 x-small px-2 py-1">
                                    <?= $r['self_marked'] ? '<i class="bi bi-person me-1"></i>Self' : '<i class="bi bi-shield-check me-1"></i>Admin' ?>
                                </span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if(empty($records)): ?>
                        <tr>
                            <td colspan="4" class="text-center py-5 text-muted opacity-50">
                                <i class="bi bi-folder2-open fs-1 mb-2"></i>
                                <div class="small fw-600">No attendance records found for this month.</div>
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
.bg-success-subtle { background: #e8f5e9 !important; }
.bg-danger-subtle { background: #ffebee !important; }
.bg-info-subtle { background: #e0f7fa !important; }

.x-small {
    font-size: 0.65rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.slot-mark-card {
    background: #ffffff;
    border: 2px solid #f0f0f0;
    transition: all 0.2s;
}

.slot-mark-card:hover {
    border-color: var(--primary);
    transform: translateY(-2px);
}

.table th {
    font-weight: 700;
    color: var(--text-muted);
}
</style>

<script>
function markSelf(slotId, status) {
    Swal.fire({
        title: 'Confirm Attendance?',
        text: `Are you sure you want to mark yourself as ${status}?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Yes, Mark it!',
        confirmButtonColor: status === 'present' ? '#2e7d32' : '#d32f2f'
    }).then((result) => {
        if (result.isConfirmed) {
            fetch('<?= url("student/attendance/mark") ?>', {
                method:'POST',
                headers:{'X-CSRF-TOKEN':'<?= csrf() ?>','Content-Type':'application/x-www-form-urlencoded'},
                body: 'slot_id='+slotId+'&status='+status+'&_token=<?= csrf() ?>'
            }).then(r=>r.json()).then(d=>{
                if(d.success) { 
                    Swal.fire('Marked!', `Your attendance is now ${status}.`, 'success');
                    setTimeout(()=>location.reload(),1000); 
                }
                else { 
                    Swal.fire('Error', d.error||'Failed to mark attendance', 'error');
                }
            });
        }
    });
}
</script>
