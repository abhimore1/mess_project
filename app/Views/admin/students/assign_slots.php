<?php
/**
 * Assign Meal Slots to Students
 */
?>
<div class="d-flex align-items-center justify-content-between mb-4 flex-wrap gap-3 animate-fadeInUp">
    <div>
        <h4 class="mb-1 fw-800 text-dark">Assign Meal Slots</h4>
        <p class="text-muted small mb-0">Control which meal slots each student has access to.</p>
    </div>
    <div class="d-flex gap-2">
        <button class="btn btn-primary fw-600 shadow-sm px-4 rounded-pill" id="saveBulkBtn" onclick="saveBulkAssignments()">
            <i class="bi bi-check-circle me-1"></i> Save Assignments
        </button>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-4 animate-fadeInUp stagger-1">
    <div class="card-body p-0">
        <!-- Search Bar -->
        <div class="p-3 border-bottom bg-surface-lowest">
            <div class="input-group bg-white rounded-pill shadow-xs overflow-hidden" style="max-width: 400px;">
                <span class="input-group-text bg-transparent border-0 text-muted ps-4"><i class="bi bi-search"></i></span>
                <input type="text" id="studentSearch" class="form-control border-0 shadow-none" placeholder="Search student by name or room..." onkeyup="filterStudents()">
            </div>
        </div>

        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0" id="assignmentsTable">
                <thead class="bg-surface-variant text-muted x-small">
                    <tr>
                        <th class="ps-4 py-3 border-0">Student</th>
                        <?php foreach($slots as $slot): ?>
                        <th class="text-center py-3 border-0">
                            <div class="fw-700"><?= e($slot['name']) ?></div>
                            <div style="font-size: 0.65rem;" class="opacity-75"><?= e($slot['slot_time']) ?></div>
                        </th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach($students as $s): 
                        $mySlots = $assignedSlots[$s['student_id']] ?? [];
                    ?>
                    <tr class="student-row" data-student-id="<?= $s['student_id'] ?>" data-search="<?= strtolower($s['full_name'] . ' ' . ($s['room_number'] ?? '')) ?>">
                        <td class="ps-4">
                            <div class="fw-600 text-dark small"><?= e($s['full_name']) ?></div>
                            <div class="text-muted x-small">Room: <?= e($s['room_number'] ? 'R-'.$s['room_number'] : '—') ?></div>
                        </td>
                        <?php foreach($slots as $slot): 
                            $isChecked = in_array($slot['slot_id'], $mySlots);
                        ?>
                        <td class="text-center">
                            <div class="form-check form-switch d-inline-block m-0 p-0">
                                <input class="form-check-input slot-toggle ms-0" type="checkbox" role="switch" 
                                       value="<?= $slot['slot_id'] ?>" <?= $isChecked ? 'checked' : '' ?>
                                       style="width: 2.5em; height: 1.25em; cursor: pointer;">
                            </div>
                        </td>
                        <?php endforeach; ?>
                    </tr>
                    <?php endforeach; ?>
                    
                    <?php if(empty($students)): ?>
                    <tr>
                        <td colspan="<?= count($slots) + 1 ?>" class="text-center py-5 text-muted">
                            <i class="bi bi-people fs-1 opacity-25 d-block mb-3"></i>
                            No active students found.
                        </td>
                    </tr>
                    <?php endif; ?>
                    
                    <tr id="noResults" style="display:none;">
                        <td colspan="<?= count($slots) + 1 ?>" class="text-center py-5 text-muted">
                            <i class="bi bi-search fs-1 opacity-25 d-block mb-3"></i>
                            No students match your search.
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
.x-small {
    font-size: 0.75rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}
</style>

<script>
function filterStudents() {
    const q = document.getElementById('studentSearch').value.toLowerCase();
    const rows = document.querySelectorAll('.student-row');
    let found = 0;
    
    rows.forEach(row => {
        if(row.dataset.search.includes(q)) {
            row.style.display = '';
            found++;
        } else {
            row.style.display = 'none';
        }
    });
    
    document.getElementById('noResults').style.display = found === 0 && q !== '' ? '' : 'none';
}

function saveBulkAssignments() {
    const btn = document.getElementById('saveBulkBtn');
    const originalHtml = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Saving...';

    const records = [];
    document.querySelectorAll('.student-row').forEach(row => {
        const studentId = row.dataset.studentId;
        const slotIds = [];
        
        row.querySelectorAll('.slot-toggle:checked').forEach(checkbox => {
            slotIds.push(checkbox.value);
        });
        
        records.push({ student_id: studentId, slot_ids: slotIds });
    });

    fetch('<?= url('api/admin/students/slots/bulk-assign') ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-CSRF-TOKEN': CSRF_TOKEN
        },
        body: new URLSearchParams({
            _token: CSRF_TOKEN,
            records: JSON.stringify(records)
        })
    })
    .then(r => r.json())
    .then(d => {
        btn.disabled = false;
        btn.innerHTML = originalHtml;
        if(d.success) {
            Swal.fire('Saved!', d.message, 'success');
        } else {
            Swal.fire('Error', d.error || 'Failed to save assignments', 'error');
        }
    })
    .catch(() => {
        btn.disabled = false;
        btn.innerHTML = originalHtml;
        Swal.fire('Error', 'Network error. Try again.', 'error');
    });
}
</script>
