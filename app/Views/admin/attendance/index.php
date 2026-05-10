<?php
/**
 * ERP-Style Attendance Marking — Optimized for 250+ students
 */
?>
<style>
    .slot-nav .nav-link {
        border: 1px solid var(--border);
        background: var(--card);
        color: var(--muted);
        border-radius: 12px;
        padding: 0.5rem 1rem;
        font-weight: 500;
        transition: all 0.2s;
        font-size: 0.85rem;
    }
    .slot-nav .nav-link.active {
        background: var(--primary);
        color: #fff;
        border-color: var(--primary);
        box-shadow: 0 4px 12px rgba(26, 115, 232, 0.15);
    }
    
    /* Stats Bar */
    .stats-bar {
        background: var(--card);
        border: 1px solid var(--border);
        border-radius: 14px;
        padding: 0.75rem 1.25rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 1.5rem;
        box-shadow: 0 2px 4px rgba(0,0,0,0.02);
    }
    .stat-item { display: flex; align-items: center; gap: 0.75rem; }
    .stat-val { font-weight: 800; font-size: 1.25rem; line-height: 1; }
    .stat-label { font-size: 0.65rem; font-weight: 700; color: var(--text-tertiary); text-transform: uppercase; letter-spacing: 0.05em; }

    /* Compact Table */
    .attendance-table {
        background: var(--card);
        border-radius: 14px;
        overflow: hidden;
        border: 1px solid var(--border);
    }
    .attendance-table th {
        background: var(--surface-container-low);
        font-weight: 700;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: var(--text-tertiary);
        padding: 12px 16px;
        border-bottom: 2px solid var(--border);
    }
    .attendance-table td {
        padding: 8px 16px;
        vertical-align: middle;
        border-bottom: 1px solid var(--surface-container-highest);
    }
    .attendance-table tr:last-child td { border-bottom: none; }
    .attendance-table tr:hover td { background: var(--surface-container-lowest); }

    /* Compact Buttons */
    .mark-btn {
        width: 32px; height: 32px; border-radius: 8px;
        border: 1.5px solid var(--outline-variant);
        background: transparent;
        color: var(--text-tertiary);
        display: inline-flex; align-items: center; justify-content: center;
        transition: all 0.2s; cursor: pointer; font-size: 0.9rem;
    }
    .mark-btn:hover { border-color: var(--primary); color: var(--primary); }
    
    .mark-btn.btn-p.active { background: #10b981; color: #fff; border-color: #10b981; box-shadow: 0 2px 6px rgba(16, 185, 129, 0.3); }
    .mark-btn.btn-a.active { background: #ef4444; color: #fff; border-color: #ef4444; box-shadow: 0 2px 6px rgba(239, 68, 68, 0.3); }
    .mark-btn.btn-l.active { background: #06b6d4; color: #fff; border-color: #06b6d4; box-shadow: 0 2px 6px rgba(6, 182, 212, 0.3); }

    .student-avatar-mini {
        width: 32px; height: 32px; border-radius: 8px;
        background: var(--primary-container);
        color: var(--primary);
        display: flex; align-items: center; justify-content: center;
        font-weight: 700; font-size: 0.8rem; flex-shrink: 0;
    }
    
    /* Search Bar Sticky */
    .search-sticky {
        position: sticky;
        top: 0;
        z-index: 100;
        background: var(--surface);
        padding: 0.5rem 0;
        margin-bottom: 0.5rem;
    }
    
    @media (max-width: 768px) {
        .stats-bar { flex-direction: column; align-items: flex-start; gap: 1rem; }
        .attendance-table td { padding: 10px 8px; }
        .room-col { display: none; }
    }
</style>

<div class="animate-fadeInUp">
    <div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3">
        <div>
            <h4 class="mb-1 fw-800 text-dark">Mess Attendance</h4>
            <p class="text-muted small mb-0">Compact view for managing 250+ students efficiently.</p>
        </div>
        <div class="d-flex gap-2 align-items-center">
            <button class="btn btn-primary btn-sm px-3 shadow-xs fw-600" style="border-radius:8px" onclick="showQrModal()">
                <i class="bi bi-qr-code-scan me-1"></i>QR Code
            </button>
            <input type="date" id="dateInput" class="form-control form-control-sm shadow-xs border-0 bg-white" 
                   value="<?= e($date) ?>" onchange="loadAttendance(this.value, <?= $slotId ?>)" style="width: 150px; border-radius: 8px;">
            <a href="<?= url('admin/attendance/report') ?>" class="btn btn-outline-secondary btn-sm px-3 shadow-xs" style="border-radius:8px">
                <i class="bi bi-file-earmark-bar-graph me-1"></i>Report
            </a>
        </div>
    </div>


    <!-- Slot Tabs -->
    <div class="slot-nav mb-4">
        <div class="d-flex gap-2 overflow-x-auto pb-2 no-scrollbar">
            <?php foreach ($slots as $slot): ?>
            <button class="nav-link text-nowrap <?= $slot['slot_id'] == $slotId ? 'active' : '' ?>"
                    onclick="loadAttendance('<?= e($date) ?>', <?= $slot['slot_id'] ?>)">
                <i class="bi bi-clock me-1"></i><?= e($slot['name']) ?>
                <span class="ms-1 opacity-75" style="font-size: .7rem;"><?= e($slot['slot_time']??'') ?></span>
            </button>
            <?php endforeach; ?>
        </div>
    </div>

    <!-- Stats Bar -->
    <div class="stats-bar shadow-xs">
        <div class="d-flex gap-4">
            <div class="stat-item">
                <div class="stat-val text-success" id="presentCount"><?= $summary['present'] ?></div>
                <div class="stat-label">Present</div>
            </div>
            <div class="vr opacity-10"></div>
            <div class="stat-item">
                <div class="stat-val text-danger" id="absentCount"><?= $summary['absent'] ?></div>
                <div class="stat-label">Absent</div>
            </div>
            <div class="vr opacity-10"></div>
            <div class="stat-item">
                <div class="stat-val text-info" id="leaveCount"><?= $summary['leave'] ?></div>
                <div class="stat-label">Leave</div>
            </div>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-primary-g btn-sm px-3 fw-600 shadow-sm" onclick="markAllPresent()" style="border-radius:8px">
                <i class="bi bi-check-all me-1"></i>Mark All Present
            </button>
            <button class="btn btn-dark btn-sm px-3 fw-600 shadow-sm" onclick="saveBulk()" style="border-radius:8px">
                <i class="bi bi-cloud-arrow-up me-1"></i>Sync Changes
            </button>
        </div>
    </div>

    <!-- Search & Filter Bar -->
    <div class="search-sticky">
        <div class="input-group shadow-xs rounded-3 overflow-hidden">
            <span class="input-group-text bg-white border-0"><i class="bi bi-search text-muted"></i></span>
            <input type="text" id="attendanceSearch" class="form-control border-0 bg-white" 
                   placeholder="Search student by name or room number (e.g. 101)..." onkeyup="filterStudents()">
            <button class="btn btn-white border-0 text-muted" onclick="clearSearch()" id="clearBtn" style="display:none;">
                <i class="bi bi-x-circle-fill"></i>
            </button>
        </div>
    </div>

    <!-- Attendance Table -->
    <div class="attendance-table shadow-sm">
        <table class="table table-borderless mb-0">
            <thead>
                <tr>
                    <th style="width: 50px;">#</th>
                    <th>STUDENT NAME</th>
                    <th class="room-col text-center">ROOM</th>
                    <th class="text-center" style="width: 180px;">ATTENDANCE STATUS</th>
                </tr>
            </thead>
            <tbody id="attendanceList">
                <?php $i=1; foreach ($students as $s): ?>
                <tr class="student-row" data-student-id="<?= $s['student_id'] ?>" data-search="<?= strtolower($s['full_name'] . ' ' . ($s['room_number'] ?? '')) ?>">
                    <td class="text-muted small fw-600"><?= $i++ ?></td>
                    <td>
                        <div class="d-flex align-items-center gap-3">
                            <div class="student-avatar-mini">
                                <?= strtoupper(mb_substr($s['full_name'], 0, 1)) ?>
                            </div>
                            <div class="fw-700 text-dark small"><?= e($s['full_name']) ?></div>
                        </div>
                    </td>
                    <td class="room-col text-center">
                        <span class="badge bg-surface-variant text-muted fw-600 px-2" style="font-size: 0.75rem;">
                            <?= e($s['room_number'] ? 'R-'.$s['room_number'] : '—') ?>
                        </span>
                    </td>
                    <td>
                        <div class="d-flex justify-content-center gap-2" data-status="<?= $s['att_status'] ?>">
                            <button class="mark-btn btn-p <?= $s['att_status']==='present'?'active':'' ?>"
                                    onclick="setStatus(this,'present')" title="Mark Present">
                                <i class="bi bi-check-lg"></i>
                            </button>
                            <button class="mark-btn btn-a <?= $s['att_status']==='absent'?'active':'' ?>"
                                    onclick="setStatus(this,'absent')" title="Mark Absent">
                                <i class="bi bi-x-lg"></i>
                            </button>
                            <button class="mark-btn btn-l <?= $s['att_status']==='leave'?'active':'' ?>"
                                    onclick="setStatus(this,'leave')" title="Mark Leave">
                                <i class="bi bi-calendar-event"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
                <tr id="noResults" style="display:none;">
                    <td colspan="4" class="text-center py-5 text-muted">
                        <i class="bi bi-search fs-1 opacity-25 d-block mb-3"></i>
                        No students match your search.
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>

<!-- QR Code Modal -->
<div class="modal fade" id="qrModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow" style="border-radius: 16px;">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-700">Scan to Mark Attendance</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center pb-5 pt-4">
                <div id="qrcode" class="d-inline-block bg-white p-3 rounded-4 shadow-sm border mb-3"></div>
                <p class="text-muted small mb-0">Students can scan this QR code to self-mark their attendance for the current slot.</p>
                <div class="mt-3 fw-bold text-dark" id="qrSlotName"></div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
<script>
let currentSlot = <?= $slotId ?>;
let currentDate = '<?= e($date) ?>';
const qrToken = '<?= $qrToken ?>';

document.addEventListener('DOMContentLoaded', function() {
    // Move modal to body to prevent layout confinement issues
    document.body.appendChild(document.getElementById('qrModal'));
});

function showQrModal() {
    const qrContainer = document.getElementById('qrcode');
    qrContainer.innerHTML = ''; // Clear existing QR code
    
    const qrUrl = `<?= url('student/attendance/scan') ?>?slot_id=${currentSlot}&date=${currentDate}&token=${qrToken}`;
    
    new QRCode(qrContainer, {
        text: qrUrl,
        width: 240,
        height: 240,
        colorDark : "#1f2937",
        colorLight : "#ffffff",
        correctLevel : QRCode.CorrectLevel.H
    });
    
    const activeSlot = document.querySelector('.slot-nav .nav-link.active');
    const slotText = activeSlot ? activeSlot.textContent.trim() : 'Unknown Slot';
    document.getElementById('qrSlotName').textContent = `${slotText} (${currentDate})`;
    
    const qrModal = new bootstrap.Modal(document.getElementById('qrModal'));
    qrModal.show();
}

function setStatus(btn, status) {
    const parent = btn.parentElement;
    const row = btn.closest('.student-row');
    const studentId = row.dataset.studentId;
    
    // UI Update
    parent.dataset.status = status;
    parent.querySelectorAll('.mark-btn').forEach(b => b.classList.remove('active'));
    btn.classList.add('active');

    // AJAX Save
    fetch('<?= url('admin/attendance/mark') ?>', {
        method: 'POST',
        headers: {'Content-Type':'application/x-www-form-urlencoded','X-CSRF-TOKEN': CSRF_TOKEN},
        body: new URLSearchParams({_token:CSRF_TOKEN,student_id:studentId,slot_id:currentSlot,date:currentDate,status})
    }).then(r=>r.json()).then(d=>{ if(d.success) updateCounts(); });
}

function updateCounts() {
    let p=0, a=0, l=0;
    document.querySelectorAll('[data-status]').forEach(el => {
        const s = el.dataset.status;
        if(s==='present') p++; else if(s==='leave') l++; else a++;
    });
    document.getElementById('presentCount').textContent = p;
    document.getElementById('absentCount').textContent  = a;
    document.getElementById('leaveCount').textContent   = l;
}

function filterStudents() {
    const q = document.getElementById('attendanceSearch').value.toLowerCase();
    const rows = document.querySelectorAll('.student-row');
    const clearBtn = document.getElementById('clearBtn');
    let found = 0;
    
    clearBtn.style.display = q ? 'block' : 'none';
    
    rows.forEach(row => {
        if(row.dataset.search.includes(q)) {
            row.style.display = '';
            found++;
        } else {
            row.style.display = 'none';
        }
    });
    
    document.getElementById('noResults').style.display = found === 0 ? '' : 'none';
}

function clearSearch() {
    document.getElementById('attendanceSearch').value = '';
    filterStudents();
}

function markAllPresent() {
    Swal.fire({
        title: 'Mark All Present?',
        text: 'This will mark all currently visible students as Present.',
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Yes, All Present',
        confirmButtonColor: '#10b981',
        background: 'var(--card)',
        color: 'var(--text)'
    }).then(r => {
        if(r.isConfirmed) {
            document.querySelectorAll('.student-row:not([style*="display: none"])').forEach(row => {
                const btn = row.querySelector('.btn-p');
                if(!btn.classList.contains('active')) setStatus(btn, 'present');
            });
            showToast('Marked all as present','success');
        }
    });
}

function saveBulk() {
    const btn = event.target;
    const originalHtml = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Syncing...';

    const records = [];
    document.querySelectorAll('.student-row').forEach(row => {
        records.push({
            student_id: row.dataset.studentId, 
            status: row.querySelector('[data-status]').dataset.status
        });
    });

    fetch('<?= url('api/admin/attendance/bulk-mark') ?>', {
        method:'POST',
        headers:{'Content-Type':'application/x-www-form-urlencoded','X-CSRF-TOKEN':CSRF_TOKEN},
        body: new URLSearchParams({_token:CSRF_TOKEN,slot_id:currentSlot,date:currentDate,records:JSON.stringify(records)})
    }).then(r=>r.json()).then(d=>{
        btn.disabled = false;
        btn.innerHTML = originalHtml;
        if(d.success) showToast(`Successfully synced ${d.count} records`,'success');
    }).catch(() => {
        btn.disabled = false;
        btn.innerHTML = originalHtml;
    });
}

function loadAttendance(date, slotId) {
    window.location.href = '<?= url('admin/attendance') ?>?date='+date+'&slot_id='+slotId;
}
</script>
