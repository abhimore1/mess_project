<?php $pageTitle='My Attendance'; ?>
<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
    <h5 class="mb-0 fw-700">Attendance Log</h5>
    <form method="GET" class="d-flex gap-2">
        <input type="month" name="month" class="form-control form-control-sm" value="<?= e($month) ?>" onchange="this.form.submit()">
    </form>
</div>

<div class="row g-4 mb-4">
    <div class="col-12">
        <div class="panel bg-primary-subtle border-primary">
            <div class="panel-body">
                <h6 class="fw-700 text-primary mb-3">Self Mark Attendance (Today)</h6>
                <div class="d-flex flex-wrap gap-3">
                    <?php foreach($slots as $s): ?>
                    <div class="card shadow-sm border-0 flex-fill text-center p-3" style="min-width:150px;border-radius:12px">
                        <div class="fw-600 mb-1"><?= e($s['name']) ?></div>
                        <div class="text-muted small mb-2"><?= e($s['slot_time']) ?></div>
                        <button class="btn btn-sm btn-outline-success" onclick="markSelf(<?= $s['slot_id'] ?>,'present')">Present</button>
                        <button class="btn btn-sm btn-outline-danger mt-1" onclick="markSelf(<?= $s['slot_id'] ?>,'absent')">Absent</button>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="panel">
    <div class="panel-header"><h6>Records for <?= date('F Y', strtotime($month.'-01')) ?></h6></div>
    <div class="table-responsive">
        <table class="table">
            <thead><tr><th>Date</th><th>Meal Slot</th><th>Status</th><th>Marked By</th></tr></thead>
            <tbody>
            <?php foreach($records as $r): ?>
            <tr>
                <td><?= format_date($r['date']) ?></td>
                <td>
                    <div class="fw-600 small"><?= e($r['slot_name']) ?></div>
                    <div class="text-muted" style="font-size:.7rem"><?= e($r['slot_time']) ?></div>
                </td>
                <td>
                    <?php if($r['status']==='present'): ?><span class="badge bg-success">Present</span>
                    <?php elseif($r['status']==='absent'): ?><span class="badge bg-danger">Absent</span>
                    <?php else: ?><span class="badge bg-info">Leave</span><?php endif; ?>
                </td>
                <td class="small text-muted"><?= $r['self_marked'] ? 'Self' : 'Admin' ?></td>
            </tr>
            <?php endforeach; ?>
            <?php if(empty($records)): ?><tr><td colspan="4" class="text-center text-muted py-4">No records found for this month.</td></tr><?php endif; ?>
            </tbody>
        </table>
    </div>
</div>
<script>
function markSelf(slotId, status) {
    if(!confirm('Mark attendance as '+status+'?')) return;
    fetch('<?= url("student/attendance/mark") ?>', {
        method:'POST',
        headers:{'X-CSRF-TOKEN':'<?= csrf() ?>','Content-Type':'application/x-www-form-urlencoded'},
        body: 'slot_id='+slotId+'&status='+status+'&_token=<?= csrf() ?>'
    }).then(r=>r.json()).then(d=>{
        if(d.success) { showToast('Attendance marked!','success'); setTimeout(()=>location.reload(),800); }
        else { showToast(d.error||'Failed','error'); }
    });
}
</script>
