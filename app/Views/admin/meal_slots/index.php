<?php $pageTitle = 'Meal Slots'; ?>
<div class="row g-4">
    <div class="col-lg-8 mx-auto">
        <div class="panel">
            <div class="panel-header">
                <h6><i class="bi bi-clock me-2"></i>Meal Slots Management</h6>
                <button class="btn btn-primary-g btn-sm" onclick="showAddSlot()"><i class="bi bi-plus me-1"></i>Add New Slot</button>
            </div>
            <div class="panel-body p-0">
                <div class="p-3 bg-light border-bottom small text-muted">
                    Define the timings for Breakfast, Lunch, Dinner, etc. These will appear in your Food Menu and Attendance tracker.
                </div>
                <ul class="list-unstyled mb-0" id="slotList">
                <?php if(empty($slots)): ?>
                    <li class="p-5 text-center text-muted">
                        <i class="bi bi-clock-history fs-1 d-block mb-3 opacity-25"></i>
                        No meal slots defined yet. Click "Add New Slot" to get started.
                    </li>
                <?php endif; ?>
                <?php foreach ($slots as $slot): ?>
                <li class="d-flex align-items-center gap-3 p-3 border-bottom" style="border-color:var(--border)!important" id="slot-<?= $slot['slot_id'] ?>">
                    <div class="text-center" style="width:40px;height:40px;border-radius:12px;background:rgba(26,115,232,.1);color:var(--primary);display:flex;align-items:center;justify-content:center;font-size:1.1rem">
                        <i class="bi bi-clock"></i>
                    </div>
                    <div style="flex:1">
                        <div class="fw-700 text-dark"><?= e($slot['name']) ?></div>
                        <div class="text-muted" style="font-size:.78rem"><?= e($slot['slot_time']??'') ?> &bull; <span class="badge bg-light text-muted border"><?= ucfirst($slot['meal_type']) ?></span></div>
                    </div>
                    <div class="form-check form-switch mb-0 me-2">
                        <input class="form-check-input" type="checkbox" <?= $slot['is_active']?'checked':'' ?>
                               onchange="toggleSlot(<?= $slot['slot_id'] ?>, this.checked)" style="width:42px;height:22px;cursor:pointer">
                    </div>
                    <button class="btn btn-sm btn-outline-danger border-0" style="padding:.4rem"
                            onclick="deleteSlot(<?= $slot['slot_id'] ?>)"><i class="bi bi-trash fs-6"></i></button>
                </li>
                <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Add Slot Modal -->
<div class="modal fade" id="addSlotModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="background:var(--card);border-color:var(--border);border-radius:16px">
            <div class="modal-header border-bottom px-4">
                <h6 class="modal-title fw-700">Add Meal Slot</h6>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <form id="addSlotForm">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label class="form-label">SLOT NAME</label>
                        <input type="text" name="name" class="form-control" placeholder="e.g. Breakfast" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">TIME RANGE</label>
                        <input type="text" name="slot_time" class="form-control" placeholder="e.g. 07:30 - 09:00" required>
                    </div>
                    <div class="mb-4">
                        <label class="form-label">MEAL TYPE</label>
                        <select name="meal_type" class="form-select">
                            <option value="breakfast">Breakfast</option>
                            <option value="lunch">Lunch</option>
                            <option value="snacks">Snacks</option>
                            <option value="dinner">Dinner</option>
                            <option value="other">Other</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary-g w-100 py-2">Create Slot</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function showAddSlot() { new bootstrap.Modal(document.getElementById('addSlotModal')).show(); }

document.getElementById('addSlotForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const data = new FormData(this);
    fetch('<?= url('admin/meal-slots/store') ?>', {
        method:'POST',
        headers:{'X-CSRF-TOKEN':CSRF_TOKEN},
        body: new URLSearchParams(data)
    }).then(r=>r.json()).then(d=>{
        if(d.success){ showToast('Meal slot added!','success'); setTimeout(()=>location.reload(),800); }
        else showToast('Error adding slot','danger');
    });
});

function toggleSlot(id, enabled) {
    fetch(`<?= url('admin/meal-slots/') ?>${id}/update`, {
        method:'POST',
        headers:{'X-CSRF-TOKEN':CSRF_TOKEN},
        body: new URLSearchParams({_token:CSRF_TOKEN, is_active: enabled?1:0, _method:'POST'})
    }).then(r=>r.json()).then(d=>{ if(d.success) showToast(enabled?'Slot enabled':'Slot disabled'); });
}

function deleteSlot(id) {
    Swal.fire({
        title:'Delete slot?',
        text:'This may affect existing attendance data.',
        icon:'warning',
        showCancelButton:true,
        confirmButtonColor:'#d33',
        background:'var(--card)',
        color:'var(--text)'
    }).then(r=>{ if(r.isConfirmed) {
        fetch(`<?= url('admin/meal-slots/') ?>${id}/delete`,{
            method:'POST',
            headers:{'X-CSRF-TOKEN':CSRF_TOKEN},
            body:'_token='+CSRF_TOKEN
        }).then(r=>r.json()).then(()=>{ 
            document.getElementById('slot-'+id).style.opacity='0';
            setTimeout(()=>document.getElementById('slot-'+id).remove(),300);
            showToast('Deleted','success'); 
        });
    }});
}
</script>
