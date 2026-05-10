<?php $pageTitle = 'Food Menu'; ?>

<div class="page-header d-flex align-items-center justify-content-between mb-4 animate-fadeInUp">
    <div>
        <h4 class="fw-700 mb-1">Weekly Food Menu</h4>
        <p class="text-muted small mb-0">Plan daily dishes for each meal slot.</p>
    </div>
    <div class="d-flex gap-2">
        <button class="btn btn-outline-primary btn-sm px-3 shadow-sm" onclick="window.print()">
            <i class="bi bi-printer me-2"></i>Print Menu
        </button>
    </div>
</div>

<?php if(empty($slots)): ?>
<div class="card border-0 shadow-sm rounded-4 p-5 text-center text-muted animate-fadeInUp">
    <i class="bi bi-clock-history fs-1 opacity-25 d-block mb-3"></i>
    <h6 class="fw-600">No Meal Slots Configured</h6>
    <p class="small mb-3">Create meal slots first to set up the food menu.</p>
    <a href="<?= url('admin/meal-slots') ?>" class="btn btn-primary-g btn-sm px-4 rounded-pill shadow-sm">
        <i class="bi bi-plus-lg me-1"></i> Add Meal Slots
    </a>
</div>
<?php else: ?>

<?php
$days = [1=>'Monday', 2=>'Tuesday', 3=>'Wednesday', 4=>'Thursday', 5=>'Friday', 6=>'Saturday', 7=>'Sunday'];
$dayAbbr = [1=>'Mon', 2=>'Tue', 3=>'Wed', 4=>'Thu', 5=>'Fri', 6=>'Sat', 7=>'Sun'];
$dayIcons = [1=>'bi-1-circle', 2=>'bi-2-circle', 3=>'bi-3-circle', 4=>'bi-4-circle', 5=>'bi-5-circle', 6=>'bi-6-circle', 7=>'bi-7-circle'];
$mealEmoji = ['breakfast'=>'🍳', 'lunch'=>'🍱', 'dinner'=>'🍛', 'snacks'=>'☕', 'other'=>'🍽️'];

// Build menu lookup: day => slot_id => items
$menuLookup = [];
foreach($menus as $m) {
    $menuLookup[$m['day_of_week']][$m['slot_id']] = $m['items'];
}

$todayDow = (int)date('N'); // 1=Mon...7=Sun
$activeSlot = $slots[0]['slot_id'] ?? null;
?>

<!-- Slot Tabs -->
<ul class="nav nav-pills gap-2 mb-4 flex-nowrap overflow-auto pb-2 animate-fadeInUp" id="slotTabs">
    <?php foreach($slots as $i => $slot):
        $emoji = $mealEmoji[$slot['meal_type']] ?? '🍽️';
    ?>
    <li class="nav-item flex-shrink-0">
        <button class="nav-link fw-600 px-4 py-2 rounded-pill d-flex align-items-center gap-2 <?= $i === 0 ? 'active' : '' ?>"
                onclick="switchSlot(<?= $slot['slot_id'] ?>, this)"
                id="tab-<?= $slot['slot_id'] ?>">
            <span style="font-size: 1.1em;"><?= $emoji ?></span>
            <?= e($slot['name']) ?>
            <span class="badge rounded-pill bg-white text-muted" style="font-size: 0.65rem;"><?= e($slot['slot_time']) ?></span>
        </button>
    </li>
    <?php endforeach; ?>
</ul>

<!-- Slot Content Panels -->
<?php foreach($slots as $i => $slot): ?>
<div class="slot-panel animate-fadeInUp" id="panel-<?= $slot['slot_id'] ?>" style="<?= $i !== 0 ? 'display:none;' : '' ?>">
    <div class="row g-3">
        <?php foreach($days as $dNum => $dName):
            $foodItems = $menuLookup[$dNum][$slot['slot_id']] ?? '';
            $isToday = ($dNum == $todayDow);
        ?>
        <div class="col-6 col-md-4 col-lg-3">
            <div class="card border-0 shadow-sm h-100 day-food-card <?= $isToday ? 'today-card' : '' ?>" style="border-radius: 16px; overflow: hidden;">
                <!-- Day Header -->
                <div class="card-header border-0 py-2 px-3 d-flex align-items-center justify-content-between"
                     style="background: <?= $isToday ? 'var(--primary)' : 'var(--surface-container)' ?>;">
                    <span class="fw-800 small text-uppercase" style="color: <?= $isToday ? 'white' : 'var(--text-primary)' ?>; letter-spacing: 0.05em;">
                        <?= $dayAbbr[$dNum] ?>
                    </span>
                    <?php if($isToday): ?>
                    <span class="badge bg-white text-primary" style="font-size: 0.6rem; font-weight: 700;">TODAY</span>
                    <?php endif; ?>
                </div>
                <!-- Food Input -->
                <div class="card-body p-2">
                    <form class="menu-form h-100" data-slot="<?= $slot['slot_id'] ?>" data-day="<?= $dNum ?>">
                        <input type="hidden" name="_token" value="<?= csrf() ?>">
                        <input type="hidden" name="slot_id" value="<?= $slot['slot_id'] ?>">
                        <input type="hidden" name="day_of_week" value="<?= $dNum ?>">
                        <textarea name="items" class="form-control menu-textarea border-0 w-100"
                                  rows="4"
                                  placeholder="e.g. Dal, Rice, Roti..."
                                  style="font-size: 0.82rem; border-radius: 10px; resize: none; background: var(--surface-container-low);"><?= e($foodItems) ?></textarea>
                        <button type="submit" class="btn btn-sm w-100 mt-2 save-btn fw-600" style="border-radius: 8px; font-size: 0.8rem;">
                            <i class="bi bi-check-lg me-1"></i>Save
                        </button>
                    </form>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>
<?php endforeach; ?>

<?php endif; ?>

<style>
/* Slot Tabs */
#slotTabs .nav-link {
    background: var(--surface-container);
    color: var(--text-secondary);
    border: none;
    transition: all 0.2s;
}
#slotTabs .nav-link.active {
    background: var(--primary) !important;
    color: white !important;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}
#slotTabs .nav-link:hover:not(.active) {
    background: var(--surface-container-high);
}
#slotTabs .nav-link .badge {
    background: rgba(255,255,255,0.3) !important;
    color: rgba(255,255,255,0.9) !important;
}
#slotTabs .nav-link:not(.active) .badge {
    background: var(--surface-container-highest) !important;
    color: var(--text-tertiary) !important;
}

/* Day cards */
.day-food-card {
    transition: transform 0.2s, box-shadow 0.2s;
}
.day-food-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.1) !important;
}
.today-card {
    box-shadow: 0 0 0 2px var(--primary), 0 4px 16px rgba(0,0,0,0.1) !important;
}

/* Textarea */
.menu-textarea:focus {
    background: white !important;
    box-shadow: 0 0 0 2px var(--primary-container) !important;
    outline: none;
}

/* Save button */
.save-btn {
    background: var(--primary-container);
    color: var(--primary);
    border: none;
}
.save-btn:hover {
    background: var(--primary);
    color: white;
}
.save-btn.saving {
    background: var(--surface-container);
    color: var(--text-tertiary);
    pointer-events: none;
}
.save-btn.saved {
    background: #e8f5e9;
    color: #2e7d32;
}

@media (max-width: 576px) {
    .col-6 { width: 50%; }
}

@media print {
    .page-header, .save-btn, #slotTabs { display: none !important; }
    .menu-textarea { background: white !important; border: 1px solid #ddd !important; }
}
</style>

<script>
let activeSlotId = <?= $slots[0]['slot_id'] ?? 'null' ?>;

function switchSlot(slotId, btn) {
    // Hide all panels
    document.querySelectorAll('.slot-panel').forEach(p => p.style.display = 'none');
    // Deactivate all tabs
    document.querySelectorAll('#slotTabs .nav-link').forEach(b => b.classList.remove('active'));
    // Show selected panel
    document.getElementById('panel-' + slotId).style.display = '';
    btn.classList.add('active');
    activeSlotId = slotId;
}

// AJAX form submit for each menu form
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.menu-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const btn = this.querySelector('.save-btn');
            const originalHtml = btn.innerHTML;
            btn.classList.add('saving');
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>Saving...';

            fetch('<?= url('admin/food-menu/store') ?>', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': CSRF_TOKEN,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: new URLSearchParams(new FormData(this))
            })
            .then(r => r.json())
            .then(d => {
                btn.classList.remove('saving');
                if(d.success) {
                    btn.classList.add('saved');
                    btn.innerHTML = '<i class="bi bi-check-circle-fill me-1"></i>Saved!';
                    setTimeout(() => {
                        btn.classList.remove('saved');
                        btn.innerHTML = originalHtml;
                    }, 2000);
                } else {
                    btn.innerHTML = originalHtml;
                    showToast('Error saving menu', 'error');
                }
            })
            .catch(() => {
                btn.classList.remove('saving');
                btn.innerHTML = originalHtml;
                showToast('Network error', 'error');
            });
        });
    });
});
</script>
