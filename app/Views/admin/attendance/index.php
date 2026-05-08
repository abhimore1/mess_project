<?php
/**
 * Attendance marking view — optimized for mobile + premium desktop UI
 */
?>
<style>
    .slot-nav .nav-link {
        border: 1px solid var(--border);
        background: var(--card);
        color: var(--muted);
        border-radius: 12px;
        padding: 0.6rem 1.25rem;
        font-weight: 500;
        transition: all 0.2s;
    }
    .slot-nav .nav-link.active {
        background: var(--primary);
        color: #fff;
        border-color: var(--primary);
        box-shadow: 0 4px 12px rgba(26, 115, 232, 0.2);
    }
    .metric-card {
        background: var(--card);
        border: 1px solid var(--border);
        border-radius: 16px;
        padding: 1rem;
        display: flex;
        align-items: center;
        gap: 1rem;
        transition: all 0.2s;
    }
    .metric-card:hover { border-color: var(--primary); transform: translateY(-2px); }
    .metric-icon {
        width: 42px; height: 42px; border-radius: 10px;
        display: flex; align-items: center; justify-content: center; font-size: 1.2rem;
    }
    .att-card {
        background: var(--card);
        border: 1px solid var(--border);
        border-radius: 18px;
        padding: 1.25rem;
        text-align: center;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
    }
    .att-card:hover { box-shadow: 0 8px 24px rgba(0,0,0,0.06); }
    .att-card.present { border-color: #10b981; background: #f0fdf4; }
    .att-card.absent  { border-color: #ef4444; background: #fef2f2; }
    .att-card.leave   { border-color: #06b6d4; background: #ecfeff; }
    
    .att-btn {
        width: 36px; height: 36px; border-radius: 10px;
        border: 1px solid var(--border);
        background: var(--card);
        color: var(--muted);
        display: flex; align-items: center; justify-content: center;
        transition: all 0.2s; cursor: pointer; font-size: 1.1rem;
    }
    .btn-p.active { background: #10b981; color: #fff; border-color: #10b981; }
    .btn-a.active { background: #ef4444; color: #fff; border-color: #ef4444; }
    .btn-l.active { background: #06b6d4; color: #fff; border-color: #06b6d4; }
    
    .avatar-wrapper { position: relative; margin: 0 auto 0.75rem; width: 64px; height: 64px; }
    .avatar-img { width: 100%; height: 100%; border-radius: 20px; object-fit: cover; }
    .avatar-placeholder { width: 100%; height: 100%; border-radius: 20px; background: linear-gradient(135deg, var(--primary), var(--accent)); color: #fff; display: flex; align-items: center; justify-content: center; font-size: 1.5rem; font-weight: 700; }
    
    @media (max-width: 576px) {
        .att-card { padding: 1rem 0.5rem; }
        .att-btn { width: 32px; height: 32px; font-size: 1rem; }
    }
</style>

<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
    <div>
        <h4 class="mb-1 fw-800">Attendance</h4>
        <p class="text-muted small mb-0">Mark daily meal attendance for students</p>
    </div>
    <div class="d-flex gap-2 align-items-center">
        <div class="input-group input-group-sm" style="width: 160px;">
            <span class="input-group-text bg-white border-end-0"><i class="bi bi-calendar3"></i></span>
            <input type="date" id="dateInput" class="form-control border-start-0" value="<?= e($date) ?>" onchange="loadAttendance(this.value, <?= $slotId ?>)">
        </div>
        <a href="<?= url('admin/attendance/report') ?>" class="btn btn-outline-secondary btn-sm px-3" style="border-radius:10px">
            <i class="bi bi-file-earmark-bar-graph me-1"></i>Report
        </a>
    </div>
</div>

<!-- Slot Tabs -->
<div class="slot-nav mb-4">
    <div class="d-flex gap-2 overflow-x-auto pb-2" style="scrollbar-width: none;">
        <?php foreach ($slots as $slot): ?>
        <button class="nav-link text-nowrap <?= $slot['slot_id'] == $slotId ? 'active' : '' ?>"
                onclick="loadAttendance('<?= e($date) ?>', <?= $slot['slot_id'] ?>)">
            <i class="bi bi-clock me-2"></i><?= e($slot['name']) ?>
            <span class="ms-2 opacity-75" style="font-size: .75rem;"><?= e($slot['slot_time']??'') ?></span>
        </button>
        <?php endforeach; ?>
    </div>
</div>

<!-- Summary Metrics -->
<div class="row g-3 mb-4">
    <div class="col-6 col-lg-3">
        <div class="metric-card">
            <div class="metric-icon bg-success-subtle text-success"><i class="bi bi-check-lg"></i></div>
            <div>
                <div class="fw-700 fs-5 lh-1" id="presentCount"><?= $summary['present'] ?></div>
                <div class="text-muted x-small fw-600 mt-1">PRESENT</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="metric-card">
            <div class="metric-icon bg-danger-subtle text-danger"><i class="bi bi-x-lg"></i></div>
            <div>
                <div class="fw-700 fs-5 lh-1" id="absentCount"><?= $summary['absent'] ?></div>
                <div class="text-muted x-small fw-600 mt-1">ABSENT</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="metric-card">
            <div class="metric-icon bg-info-subtle text-info"><i class="bi bi-calendar-event"></i></div>
            <div>
                <div class="fw-700 fs-5 lh-1" id="leaveCount"><?= $summary['leave'] ?></div>
                <div class="text-muted x-small fw-600 mt-1">ON LEAVE</div>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="d-flex flex-column gap-2">
            <button class="btn btn-primary-g btn-sm w-100 py-2" onclick="markAllPresent()" style="border-radius:12px">
                <i class="bi bi-check-all me-1"></i>Mark All Present
            </button>
            <button class="btn btn-outline-secondary btn-sm w-100 py-2" onclick="saveBulk()" style="border-radius:12px">
                <i class="bi bi-cloud-arrow-up me-1"></i>Sync All
            </button>
        </div>
    </div>
</div>

<!-- Attendance Grid -->
<div class="row g-3" id="attendanceGrid">
<?php foreach ($students as $s): ?>
<div class="col-6 col-md-4 col-xl-3" data-student-id="<?= $s['student_id'] ?>">
    <div class="att-card <?= $s['att_status'] ?>" data-status="<?= $s['att_status'] ?>">
        <div class="avatar-wrapper">
            <?php if($s['photo_path']): ?>
            <img src="<?= url($s['photo_path']) ?>" class="avatar-img">
            <?php else: ?>
            <div class="avatar-placeholder"><?= strtoupper(substr($s['full_name'],0,1)) ?></div>
            <?php endif; ?>
        </div>
        
        <div class="fw-700 small text-truncate mb-0"><?= e($s['full_name']) ?></div>
        <div class="text-muted x-small fw-500"><?= e($s['room_number'] ? 'Room '.$s['room_number'] : 'No Room') ?></div>
        
        <div class="mt-3 d-flex justify-content-center gap-2">
            <button class="att-btn btn-p <?= $s['att_status']==='present'?'active':'' ?>"
                    onclick="setStatus(this,'present')" title="Present">
                <i class="bi bi-check-lg"></i>
            </button>
            <button class="att-btn btn-a <?= $s['att_status']==='absent'?'active':'' ?>"
                    onclick="setStatus(this,'absent')" title="Absent">
                <i class="bi bi-x-lg"></i>
            </button>
            <button class="att-btn btn-l <?= $s['att_status']==='leave'?'active':'' ?>"
                    onclick="setStatus(this,'leave')" title="Leave">
                <i class="bi bi-calendar-event"></i>
            </button>
        </div>
    </div>
</div>
<?php endforeach; ?>
</div>

<style>
.x-small { font-size: 0.68rem; text-transform: uppercase; letter-spacing: 0.05em; }
</style>

<script>
let currentSlot = <?= $slotId ?>;
let currentDate = '<?= e($date) ?>';

function setStatus(btn, status) {
    const card = btn.closest('.att-card');
    const studentId = btn.closest('[data-student-id]').dataset.studentId;
    
    // UI Update
    card.className = 'att-card ' + status;
    card.dataset.status = status;
    
    card.querySelectorAll('.att-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');

    // AJAX Save
    fetch('<?= url('admin/attendance/mark') ?>', {
        method: 'POST',
        headers: {'Content-Type':'application/x-www-form-urlencoded','X-CSRF-TOKEN': CSRF_TOKEN},
        body: new URLSearchParams({_token:CSRF_TOKEN,student_id:studentId,slot_id:currentSlot,date:currentDate,status})
    }).then(r=>r.json()).then(d=>{ if(d.success) updateCounts(); });
}

function updateCounts() {
    const cards = document.querySelectorAll('.att-card');
    let p=0,a=0,l=0;
    cards.forEach(c=>{ 
        if(c.dataset.status==='present') p++; 
        else if(c.dataset.status==='leave') l++; 
        else a++; 
    });
    document.getElementById('presentCount').textContent = p;
    document.getElementById('absentCount').textContent  = a;
    document.getElementById('leaveCount').textContent   = l;
}

function markAllPresent() {
    document.querySelectorAll('.att-card').forEach(card => {
        if(card.dataset.status !== 'present') {
            card.className = 'att-card present';
            card.dataset.status = 'present';
            card.querySelectorAll('.att-btn').forEach(b => {
                b.classList.remove('active');
                if(b.classList.contains('btn-p')) b.classList.add('active');
            });
        }
    });
    updateCounts();
    saveBulk(); // Auto-sync after bulk action
}

function saveBulk() {
    const records = [];
    document.querySelectorAll('[data-student-id]').forEach(el => {
        records.push({student_id: el.dataset.studentId, status: el.querySelector('.att-card').dataset.status});
    });
    fetch('<?= url('api/admin/attendance/bulk-mark') ?>', {
        method:'POST',
        headers:{'Content-Type':'application/x-www-form-urlencoded','X-CSRF-TOKEN':CSRF_TOKEN},
        body: new URLSearchParams({_token:CSRF_TOKEN,slot_id:currentSlot,date:currentDate,records:JSON.stringify(records)})
    }).then(r=>r.json()).then(d=>{
        if(d.success) showToast(`Synced ${d.count} attendance records`,'success');
    });
}

function loadAttendance(date, slotId) {
    window.location.href = '<?= url('admin/attendance') ?>?date='+date+'&slot_id='+slotId;
}
</script>
