<?php $pageTitle = 'Food Menu'; ?>

<?php
$days       = [1=>'Monday', 2=>'Tuesday', 3=>'Wednesday', 4=>'Thursday', 5=>'Friday', 6=>'Saturday', 7=>'Sunday'];
$dayShort   = [1=>'MON', 2=>'TUE', 3=>'WED', 4=>'THU', 5=>'FRI', 6=>'SAT', 7=>'SUN'];
$dayColors  = [1=>'#6366f1', 2=>'#8b5cf6', 3=>'#ec4899', 4=>'#f43f5e', 5=>'#f59e0b', 6=>'#10b981', 7=>'#06b6d4'];
$mealEmoji  = ['breakfast'=>'🍳', 'lunch'=>'🍱', 'dinner'=>'🍛', 'snacks'=>'☕', 'other'=>'🍽️'];
$mealIcons  = ['breakfast'=>'bi-egg-fried', 'lunch'=>'bi-sun', 'dinner'=>'bi-moon-stars', 'snacks'=>'bi-cup-hot', 'other'=>'bi-grid'];
$todayDow   = (int)date('N');

// Build lookup: day_of_week => slot_id => items
$menuLookup = [];
foreach ($menus as $m) {
    $menuLookup[(int)$m['day_of_week']][$m['slot_id']] = $m['items'];
}
?>

<!-- Header -->
<div class="d-flex align-items-center justify-content-between mb-4 animate-fadeInUp flex-wrap gap-2">
    <div>
        <h4 class="fw-800 mb-1">🍽️ Weekly Food Menu</h4>
        <p class="text-muted small mb-0">Day-wise meal plan — your assigned slots are highlighted.</p>
    </div>
    <div class="d-flex gap-2 align-items-center">
        <span class="badge rounded-pill px-3 py-2 fw-600" style="background: var(--primary-container); color: var(--primary);">
            <i class="bi bi-calendar-week me-1"></i> Current Week
        </span>
    </div>
</div>

<?php if(empty($allSlots)): ?>
<div class="card border-0 shadow-sm rounded-4 p-5 text-center text-muted">
    <i class="bi bi-clock-history fs-1 opacity-25 d-block mb-3"></i>
    <p class="small fw-600">No meal slots configured yet. Contact your mess admin.</p>
</div>
<?php else: ?>

<!-- Legend -->
<div class="d-flex gap-3 mb-4 flex-wrap animate-fadeInUp stagger-1">
    <div class="d-flex align-items-center gap-2 small">
        <span class="badge rounded-pill px-3" style="background:#e8f5e9; color:#2e7d32;">✓ Your Slot</span>
        <span class="text-muted">You are assigned to this meal</span>
    </div>
    <div class="d-flex align-items-center gap-2 small">
        <span class="badge rounded-pill px-3 bg-light text-secondary">Not Assigned</span>
        <span class="text-muted">You can view but not mark attendance</span>
    </div>
</div>

<!-- Day-wise tabs -->
<ul class="nav nav-pills gap-2 mb-4 flex-nowrap overflow-auto pb-2 animate-fadeInUp stagger-1" id="dayTabs">
    <?php foreach($days as $dNum => $dName): $isToday = ($dNum == $todayDow); ?>
    <li class="nav-item flex-shrink-0">
        <button class="nav-link fw-700 px-3 py-2 rounded-pill <?= $isToday ? 'active' : '' ?>"
                onclick="switchDay(<?= $dNum ?>, this)"
                style="min-width: 64px; text-align: center; <?= $isToday ? '' : '' ?>">
            <?= $dayShort[$dNum] ?>
            <?php if($isToday): ?><div style="font-size: 0.6rem; opacity: 0.8; letter-spacing: 0.05em;">TODAY</div><?php endif; ?>
        </button>
    </li>
    <?php endforeach; ?>
</ul>

<!-- Day Panels -->
<?php foreach($days as $dNum => $dName): $isToday = ($dNum == $todayDow); ?>
<div class="day-panel animate-fadeInUp" id="day-panel-<?= $dNum ?>" style="<?= !$isToday ? 'display:none;' : '' ?>">
    <div class="row g-3">
        <?php foreach($allSlots as $slot):
            $slotId     = $slot['slot_id'];
            $isAssigned = in_array($slotId, $assignedSlotIds);
            $items      = $menuLookup[$dNum][$slotId] ?? null;
            $emoji      = $mealEmoji[$slot['meal_type']] ?? '🍽️';
            $icon       = $mealIcons[$slot['meal_type']] ?? 'bi-grid';
        ?>
        <div class="col-md-6 col-lg-4">
            <div class="card border-0 shadow-sm h-100 slot-food-card <?= !$isAssigned ? 'not-assigned' : 'assigned' ?>" 
                 style="border-radius: 18px; overflow: hidden; border-left: 4px solid <?= $isAssigned ? $dayColors[$dNum] : '#dee2e6' ?> !important;">
                <!-- Slot header -->
                <div class="card-header border-0 py-3 px-4 d-flex justify-content-between align-items-center"
                     style="background: <?= $isAssigned ? $dayColors[$dNum].'18' : '#f8f9fa' ?>;">
                    <div class="d-flex align-items-center gap-2">
                        <span style="font-size: 1.3em;"><?= $emoji ?></span>
                        <div>
                            <div class="fw-800 small" style="color: <?= $isAssigned ? $dayColors[$dNum] : '#6c757d' ?>; letter-spacing: 0.03em;">
                                <?= e($slot['name']) ?>
                            </div>
                            <div class="x-small text-muted">
                                <i class="bi bi-clock me-1"></i><?= e($slot['slot_time'] ?? '—') ?>
                            </div>
                        </div>
                    </div>
                    <?php if($isAssigned): ?>
                    <span class="badge rounded-pill fw-600" style="background:#e8f5e9; color:#2e7d32; font-size: 0.65rem;">
                        <i class="bi bi-check-circle-fill me-1"></i>Your Slot
                    </span>
                    <?php else: ?>
                    <span class="badge rounded-pill fw-500 bg-light text-muted" style="font-size: 0.65rem;">
                        <i class="bi bi-lock me-1"></i>Not Assigned
                    </span>
                    <?php endif; ?>
                </div>
                <!-- Food items -->
                <div class="card-body p-4" style="background: <?= $isAssigned ? 'white' : '#fafafa' ?>;">
                    <?php if($items): ?>
                        <div class="food-items-text fw-500" style="color: <?= $isAssigned ? '#1a1a2e' : '#adb5bd' ?>; line-height: 1.7; font-size: 0.9rem;">
                            <?php
                            $lines = explode("\n", trim($items));
                            foreach($lines as $line):
                                $line = trim($line);
                                if(!$line) continue;
                            ?>
                            <div class="d-flex align-items-start gap-2 mb-1">
                                <i class="bi bi-dot" style="font-size: 1.1rem; color: <?= $isAssigned ? $dayColors[$dNum] : '#dee2e6' ?>; flex-shrink:0; margin-top: -1px;"></i>
                                <span><?= e($line) ?></span>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="text-center py-2 opacity-40">
                            <i class="bi bi-journal-x mb-2 d-block" style="font-size: 1.5rem; color: #ccc;"></i>
                            <span class="small text-muted">Menu not updated</span>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>
<?php endforeach; ?>

<?php endif; ?>

<style>
#dayTabs .nav-link {
    background: var(--surface-container);
    color: var(--text-secondary);
    border: none;
    font-size: 0.78rem;
    transition: all 0.2s;
}
#dayTabs .nav-link.active {
    background: var(--primary) !important;
    color: white !important;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
}
#dayTabs .nav-link:hover:not(.active) {
    background: var(--surface-container-high);
}
.slot-food-card {
    transition: transform 0.2s, box-shadow 0.2s;
}
.slot-food-card.assigned:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 24px rgba(0,0,0,0.1) !important;
}
.not-assigned {
    opacity: 0.7;
}
.x-small {
    font-size: 0.7rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.04em;
}
</style>

<script>
function switchDay(dayNum, btn) {
    document.querySelectorAll('.day-panel').forEach(p => p.style.display = 'none');
    document.querySelectorAll('#dayTabs .nav-link').forEach(b => b.classList.remove('active'));
    document.getElementById('day-panel-' + dayNum).style.display = '';
    btn.classList.add('active');
}
</script>
