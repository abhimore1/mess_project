<?php $pageTitle = 'Weekly Food Menu'; ?>

<div class="page-header d-flex align-items-center justify-content-between mb-4 animate-fadeInUp">
    <div>
        <h4 class="fw-700 mb-1">Weekly Food Menu</h4>
        <p class="text-muted small mb-0">Plan and manage the dishes for each meal slot across the week.</p>
    </div>
    <div class="d-flex gap-2">
        <button class="btn btn-outline-primary btn-sm px-3 shadow-sm" onclick="window.print()">
            <i class="bi bi-printer me-2"></i>Print Menu
        </button>
        <button class="btn btn-primary-g btn-sm px-3 shadow-sm" onclick="location.reload()">
            <i class="bi bi-arrow-clockwise me-2"></i>Refresh
        </button>
    </div>
</div>

<div class="row g-4 animate-fadeInUp stagger-1">
<?php
$days = [1=>'Monday', 2=>'Tuesday', 3=>'Wednesday', 4=>'Thursday', 5=>'Friday', 6=>'Saturday', 7=>'Sunday'];
$dayIcons = [1=>'calendar-event', 2=>'calendar-date', 3=>'calendar2-week', 4=>'calendar3', 5=>'calendar-check', 6=>'calendar-heart', 7=>'calendar-sun'];

foreach($days as $dNum => $dName): 
?>
<div class="col-md-6 col-lg-4">
    <div class="card border-0 shadow-sm overflow-hidden h-100 day-card" style="border-radius: 16px;">
        <div class="card-header bg-white border-bottom py-3 d-flex align-items-center gap-2">
            <div class="day-indicator">
                <i class="bi bi-<?= $dayIcons[$dNum] ?>"></i>
            </div>
            <h6 class="fw-800 mb-0 text-uppercase tracking-wider" style="font-size: 0.9rem; color: var(--text-primary);">
                <?= $dName ?>
            </h6>
        </div>
        <div class="card-body p-0 bg-surface-variant">
            <div class="list-group list-group-flush">
            <?php foreach($slots as $slot): 
                $menuItem = '';
                foreach($menus as $m) {
                    if($m['day_of_week'] == $dNum && $m['slot_id'] == $slot['slot_id']) {
                        $menuItem = $m['items']; break;
                    }
                }
                
                // Determine icon based on meal name or type
                $mealIcon = 'plate-fill';
                $nameLower = strtolower($slot['name']);
                if(str_contains($nameLower, 'breakfast')) $mealIcon = 'egg-fried';
                elseif(str_contains($nameLower, 'lunch')) $mealIcon = 'box-seam';
                elseif(str_contains($nameLower, 'dinner')) $mealIcon = 'moon-stars';
                elseif(str_contains($nameLower, 'snack')) $mealIcon = 'cup-hot';
            ?>
            <div class="list-group-item p-3 border-0 border-bottom bg-white m-2 rounded-3 shadow-xs">
                <form method="POST" action="<?= url('admin/food-menu/store') ?>" class="menu-form">
                    <?= csrf_field() ?>
                    <input type="hidden" name="day_of_week" value="<?= $dNum ?>">
                    <input type="hidden" name="slot_id" value="<?= $slot['slot_id'] ?>">
                    
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <div class="d-flex align-items-center gap-2">
                            <span class="text-primary opacity-75"><i class="bi bi-<?= $mealIcon ?>"></i></span>
                            <span class="fw-700 small text-dark"><?= e($slot['name']) ?></span>
                        </div>
                        <span class="text-muted" style="font-size: 0.65rem; font-weight: 600;">
                            <i class="bi bi-clock me-1"></i><?= e($slot['slot_time']) ?>
                        </span>
                    </div>

                    <div class="position-relative">
                        <textarea name="items" class="form-control menu-textarea border-0 bg-surface-variant" 
                                  rows="3" placeholder="What's cooking?..." 
                                  style="font-size: 0.85rem; border-radius: 10px; resize: none;"><?= e($menuItem) ?></textarea>
                        <button type="submit" class="btn btn-save-mini shadow-sm" title="Save this slot">
                            <i class="bi bi-check-lg"></i>
                        </button>
                    </div>
                </form>
            </div>
            <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>
<?php endforeach; ?>
</div>

<style>
.day-indicator {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    background: var(--primary);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1rem;
}

.day-card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}
.day-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.08) !important;
}

.menu-textarea:focus {
    background-color: white !important;
    box-shadow: 0 0 0 2px var(--primary-container) !important;
}

.btn-save-mini {
    position: absolute;
    bottom: 8px;
    right: 8px;
    width: 28px;
    height: 28px;
    border-radius: 8px;
    background: white;
    border: none;
    color: var(--primary);
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s ease;
    z-index: 5;
    opacity: 0;
    transform: scale(0.8);
}

.menu-form:hover .btn-save-mini, 
.menu-textarea:focus + .btn-save-mini {
    opacity: 1;
    transform: scale(1);
}

.btn-save-mini:hover {
    background: var(--primary);
    color: white;
}

.btn-save-mini.saved {
    background: var(--success) !important;
    color: white !important;
    opacity: 1 !important;
    transform: scale(1) !important;
}

.tracking-wider {
    letter-spacing: 0.05em;
}

@media print {
    .page-header, .btn-save-mini, .menu-textarea::placeholder { display: none !important; }
    .col-md-6, .col-lg-4 { width: 50% !important; flex: 0 0 50% !important; max-width: 50% !important; }
    .menu-textarea { background: white !important; border: 1px solid #eee !important; }
    body { background: white !important; }
}
</style>

<script>
document.querySelectorAll('.menu-form').forEach(f => {
    f.addEventListener('submit', e => {
        e.preventDefault();
        const btn = f.querySelector('.btn-save-mini');
        const icon = btn.querySelector('i');
        const originalIcon = icon.className;
        
        btn.disabled = true;
        icon.className = 'spinner-border spinner-border-sm';
        
        fetch(f.action, {
            method: 'POST', 
            body: new FormData(f),
            headers: {'X-Requested-With': 'XMLHttpRequest'}
        })
        .then(r => r.json())
        .then(d => {
            if(d.success) { 
                btn.classList.add('saved');
                icon.className = 'bi bi-check-lg';
                showToast('Menu saved successfully', 'success');
                setTimeout(() => {
                    btn.classList.remove('saved');
                    icon.className = originalIcon;
                    btn.disabled = false;
                }, 2000); 
            } else {
                showToast('Error saving menu', 'danger');
                icon.className = originalIcon;
                btn.disabled = false;
            }
        })
        .catch(() => {
            showToast('Connection error', 'danger');
            icon.className = originalIcon;
            btn.disabled = false;
        });
    });
});
</script>
