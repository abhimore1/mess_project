<?php $pageTitle = 'Meal Slots'; ?>

<div class="page-header d-flex align-items-center justify-content-between mb-4 animate-fadeInUp">
    <div>
        <h4 class="fw-700 mb-1">Meal Slots</h4>
        <p class="text-muted small mb-0">Configure your daily mess timings for breakfast, lunch, and dinner.</p>
    </div>
    <button class="btn btn-primary-g shadow-sm" onclick="showAddSlot()">
        <i class="bi bi-plus-lg me-2"></i>Add New Slot
    </button>
</div>

<div class="row g-4 animate-fadeInUp stagger-1">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm overflow-hidden">
            <div class="card-header bg-white border-bottom py-3 d-flex align-items-center justify-content-between">
                <h6 class="fw-700 mb-0"><i class="bi bi-clock-history me-2 text-primary"></i>Configured Slots</h6>
                <span class="badge bg-primary-container text-primary px-3"><?= count($slots) ?> Active Slots</span>
            </div>
            
            <div class="card-body p-0">
                <div class="p-3 bg-surface-variant text-muted small border-bottom d-flex align-items-start gap-2">
                    <i class="bi bi-info-circle mt-1"></i>
                    <span>Defined timings will automatically appear in your Food Menu and Attendance tracker. Use the toggle to temporarily disable a slot.</span>
                </div>

                <div class="list-group list-group-flush" id="slotList">
                    <?php if(empty($slots)): ?>
                        <div class="p-5 text-center text-muted">
                            <div class="mb-3 opacity-25">
                                <i class="bi bi-clock-history" style="font-size: 4rem;"></i>
                            </div>
                            <h6 class="fw-600">No meal slots defined</h6>
                            <p class="small mb-0">Click the "Add New Slot" button to set your first mess timing.</p>
                        </div>
                    <?php endif; ?>

                    <?php foreach ($slots as $slot): ?>
                    <div class="list-group-item p-3 d-flex align-items-center gap-3 border-0 border-bottom slot-card" 
                         id="slot-<?= $slot['slot_id'] ?>" style="transition: all 0.2s ease;">
                        
                        <div class="slot-icon-wrapper">
                            <i class="bi bi-clock"></i>
                        </div>

                        <div class="flex-grow-1">
                            <div class="d-flex align-items-center gap-2 mb-1">
                                <h6 class="fw-700 mb-0 text-dark"><?= e($slot['name']) ?></h6>
                                <span class="badge bg-surface-variant text-muted small" style="font-size: 0.65rem; text-transform: uppercase; letter-spacing: 0.02em;">
                                    <?= ucfirst($slot['meal_type']) ?>
                                </span>
                            </div>
                            <div class="text-tertiary font-medium d-flex align-items-center gap-2" style="font-size: 0.85rem;">
                                <i class="bi bi-hourglass-split"></i>
                                <?= e($slot['slot_time'] ?? '—') ?>
                            </div>
                        </div>

                        <div class="d-flex align-items-center gap-2">
                            <div class="form-check form-switch mb-0">
                                <input class="form-check-input custom-switch" type="checkbox" <?= $slot['is_active'] ? 'checked' : '' ?>
                                       onchange="toggleSlot(<?= $slot['slot_id'] ?>, this.checked)">
                            </div>
                            <div class="vr mx-2" style="height: 20px; opacity: 0.1;"></div>
                            <button class="btn btn-icon btn-outline-primary border-0 me-1" 
                                    onclick='editSlot(<?= json_encode($slot) ?>)' title="Edit Slot">
                                <i class="bi bi-pencil-square"></i>
                            </button>
                            <button class="btn btn-icon btn-outline-danger border-0" 
                                    onclick="deleteSlot(<?= $slot['slot_id'] ?>)" title="Delete Slot">
                                <i class="bi bi-trash3"></i>
                            </button>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card border-0 shadow-sm bg-primary text-white position-relative overflow-hidden h-100 min-vh-25">
            <div class="card-body p-4 position-relative z-1">
                <h5 class="fw-700 mb-3">Attendance Tip</h5>
                <p class="opacity-75 small">Meal slots help you track attendance more accurately. You can mark student presence specifically for Breakfast, Lunch, or Dinner.</p>
                <div class="mt-4">
                    <a href="<?= url('admin/attendance') ?>" class="btn btn-light btn-sm px-4 fw-600 shadow-sm">View Attendance</a>
                </div>
            </div>
            <i class="bi bi-journal-check position-absolute bottom-0 end-0 opacity-10 m-n3" style="font-size: 8rem; transform: rotate(-15deg);"></i>
        </div>
    </div>
</div>

<!-- Add Slot Modal -->
<div class="modal fade" id="addSlotModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header border-bottom px-4 pt-4">
                <h5 class="modal-title fw-700">Create New Meal Slot</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <form id="addSlotForm">
                    <?= csrf_field() ?>
                    <div class="mb-3">
                        <label class="form-label font-medium small text-muted text-uppercase tracking-wider">Slot Name</label>
                        <input type="text" name="name" class="form-control form-control-lg border-0 bg-surface-variant" 
                               placeholder="e.g. Morning Breakfast" required style="font-size: 0.95rem;">
                    </div>
                    <div class="mb-3">
                        <label class="form-label font-medium small text-muted text-uppercase tracking-wider">Timing / Range</label>
                        <input type="text" name="slot_time" class="form-control form-control-lg border-0 bg-surface-variant" 
                               placeholder="e.g. 07:30 AM - 09:30 AM" required style="font-size: 0.95rem;">
                    </div>
                    <div class="mb-4">
                        <label class="form-label font-medium small text-muted text-uppercase tracking-wider">Meal Category</label>
                        <select name="meal_type" class="form-select form-select-lg border-0 bg-surface-variant" style="font-size: 0.95rem;">
                            <option value="breakfast">🍳 Breakfast</option>
                            <option value="lunch">🍱 Lunch</option>
                            <option value="snacks">☕ Snacks</option>
                            <option value="dinner">🍛 Dinner</option>
                            <option value="other">🍽️ Other</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary-g w-100 py-3 fw-700 shadow-sm">
                        Confirm & Create Slot
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Edit Slot Modal -->
<div class="modal fade" id="editSlotModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px;">
            <div class="modal-header border-bottom px-4 pt-4">
                <h5 class="modal-title fw-700">Edit Meal Slot</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <form id="editSlotForm">
                    <?= csrf_field() ?>
                    <input type="hidden" name="slot_id" id="edit_slot_id">
                    <div class="mb-3">
                        <label class="form-label font-medium small text-muted text-uppercase tracking-wider">Slot Name</label>
                        <input type="text" name="name" id="edit_name" class="form-control form-control-lg border-0 bg-surface-variant" required style="font-size: 0.95rem;">
                    </div>
                    <div class="mb-3">
                        <label class="form-label font-medium small text-muted text-uppercase tracking-wider">Timing / Range</label>
                        <input type="text" name="slot_time" id="edit_slot_time" class="form-control form-control-lg border-0 bg-surface-variant" required style="font-size: 0.95rem;">
                    </div>
                    <div class="mb-4">
                        <label class="form-label font-medium small text-muted text-uppercase tracking-wider">Meal Category</label>
                        <select name="meal_type" id="edit_meal_type" class="form-select form-select-lg border-0 bg-surface-variant" style="font-size: 0.95rem;">
                            <option value="breakfast">🍳 Breakfast</option>
                            <option value="lunch">🍱 Lunch</option>
                            <option value="snacks">☕ Snacks</option>
                            <option value="dinner">🍛 Dinner</option>
                            <option value="other">🍽️ Other</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary-g w-100 py-3 fw-700 shadow-sm">
                        Save Changes
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<style>
.slot-icon-wrapper {
    width: 48px;
    height: 48px;
    border-radius: 14px;
    background: var(--primary-container);
    color: var(--primary);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.3rem;
    flex-shrink: 0;
}

.slot-card:hover {
    background-color: var(--surface-container-low) !important;
}

.custom-switch {
    width: 42px !important;
    height: 22px !important;
    cursor: pointer;
}

.tracking-wider {
    letter-spacing: 0.05em;
}

.min-vh-25 {
    min-height: 250px;
}
</style>

<script>
function showAddSlot() { new bootstrap.Modal(document.getElementById('addSlotModal')).show(); }

function editSlot(slot) {
    document.getElementById('edit_slot_id').value = slot.slot_id;
    document.getElementById('edit_name').value = slot.name;
    document.getElementById('edit_slot_time').value = slot.slot_time;
    document.getElementById('edit_meal_type').value = slot.meal_type;
    new bootstrap.Modal(document.getElementById('editSlotModal')).show();
}

document.getElementById('addSlotForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const btn = this.querySelector('button[type="submit"]');
    const originalText = btn.innerHTML;
    
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Creating...';
    
    const data = new FormData(this);
    fetch('<?= url('admin/meal-slots/store') ?>', {
        method:'POST',
        headers:{'X-CSRF-TOKEN':CSRF_TOKEN},
        body: new URLSearchParams(data)
    }).then(r=>r.json()).then(d=>{
        if(d.success){ 
            showToast('Meal slot added successfully!','success'); 
            setTimeout(()=>location.reload(),800); 
        } else {
            btn.disabled = false;
            btn.innerHTML = originalText;
            showToast('Error adding slot','danger');
        }
    }).catch(()=>{
        btn.disabled = false;
        btn.innerHTML = originalText;
        showToast('Connection error','danger');
    });
});

document.getElementById('editSlotForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const btn = this.querySelector('button[type="submit"]');
    const originalText = btn.innerHTML;
    
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Saving...';
    
    const data = new FormData(this);
    const slotId = document.getElementById('edit_slot_id').value;
    
    fetch(`<?= url('admin/meal-slots/') ?>${slotId}/update`, {
        method:'POST',
        headers:{'X-CSRF-TOKEN':CSRF_TOKEN},
        body: new URLSearchParams(data)
    }).then(r=>r.json()).then(d=>{
        if(d.success){ 
            showToast('Meal slot updated successfully!','success'); 
            setTimeout(()=>location.reload(),800); 
        } else {
            btn.disabled = false;
            btn.innerHTML = originalText;
            showToast('Error updating slot','danger');
        }
    }).catch(()=>{
        btn.disabled = false;
        btn.innerHTML = originalText;
        showToast('Connection error','danger');
    });
});

function toggleSlot(id, enabled) {
    fetch(`<?= url('admin/meal-slots/') ?>${id}/update`, {
        method:'POST',
        headers:{'X-CSRF-TOKEN':CSRF_TOKEN},
        body: new URLSearchParams({_token:CSRF_TOKEN, is_active: enabled?1:0, _method:'POST'})
    }).then(r=>r.json()).then(d=>{ 
        if(d.success) showToast(enabled?'Slot enabled':'Slot disabled','success'); 
    });
}

function deleteSlot(id) {
    Swal.fire({
        title: 'Delete this slot?',
        text: 'This will remove the timing from attendance and menu. This action cannot be undone.',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, delete it',
        cancelButtonText: 'Cancel',
        confirmButtonColor: '#d33',
        background: 'var(--card)',
        color: 'var(--text)',
        customClass: {
            confirmButton: 'btn btn-danger px-4',
            cancelButton: 'btn btn-outline-secondary px-4'
        },
        buttonsStyling: false
    }).then(r=>{ if(r.isConfirmed) {
        fetch(`<?= url('admin/meal-slots/') ?>${id}/delete`,{
            method:'POST',
            headers:{'X-CSRF-TOKEN':CSRF_TOKEN},
            body:'_token='+CSRF_TOKEN
        }).then(r=>r.json()).then(()=>{ 
            const el = document.getElementById('slot-'+id);
            el.style.transform = 'translateX(20px)';
            el.style.opacity = '0';
            setTimeout(()=>el.remove(), 300);
            showToast('Meal slot deleted','success'); 
        });
    }});
}
</script>
