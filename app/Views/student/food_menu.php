<?php $pageTitle = 'Food Menu'; ?>

<div class="container-fluid px-0">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4 animate-fadeIn">
        <div>
            <h4 class="fw-700 mb-1">Weekly Food Menu 🍽️</h4>
            <p class="text-muted small mb-0">Check what's being served throughout the week.</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <span class="badge rounded-pill bg-primary px-3 py-2 fw-600">
                <i class="bi bi-clock-history me-1"></i> Current Week
            </span>
        </div>
    </div>

    <div class="row g-4">
        <?php
        $days = [1 => 'Monday', 2 => 'Tuesday', 3 => 'Wednesday', 4 => 'Thursday', 5 => 'Friday', 6 => 'Saturday', 7 => 'Sunday'];
        $todayNum = (int)date('N');
        
        foreach ($days as $dNum => $dName): 
            $isToday = ($todayNum === $dNum);
        ?>
        <div class="col-xl-4 col-lg-6">
            <div class="card h-100 border-0 shadow-sm overflow-hidden animate-fadeInUp <?= $isToday ? 'border-start border-4 border-primary' : '' ?>" 
                 style="border-radius: 20px; transition: transform 0.2s;">
                
                <div class="card-header border-0 py-3 px-4 <?= $isToday ? 'bg-primary-subtle' : 'bg-white' ?>">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 fw-700 <?= $isToday ? 'text-primary' : '' ?>">
                            <?= $dName ?>
                        </h6>
                        <?php if ($isToday): ?>
                            <span class="badge rounded-pill bg-primary px-2 py-1 x-small shadow-sm">TODAY</span>
                        <?php endif; ?>
                    </div>
                </div>

                <div class="card-body p-0">
                    <div class="menu-items-list">
                        <?php 
                        $hasItems = false;
                        foreach ($menus as $m) {
                            if ((int)$m['day_of_week'] === $dNum) {
                                $hasItems = true;
                                
                                // Determine icon based on meal type or name
                                $icon = 'bi-egg-fried';
                                $type = strtolower($m['meal_type'] ?? $m['slot_name']);
                                if (strpos($type, 'lunch') !== false) $icon = 'bi-sun';
                                elseif (strpos($type, 'dinner') !== false) $icon = 'bi-moon-stars';
                                elseif (strpos($type, 'snack') !== false) $icon = 'bi-cup-hot';
                        ?>
                        <div class="menu-slot p-4 border-bottom last-child-no-border">
                            <div class="d-flex gap-3">
                                <div class="slot-icon-box rounded-circle bg-surface-variant d-flex align-items-center justify-content-center text-primary shadow-sm">
                                    <i class="bi <?= $icon ?> fs-5"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-start mb-1">
                                        <div class="fw-700 text-dark small text-uppercase letter-spacing-1"><?= e($m['slot_name']) ?></div>
                                        <div class="x-small text-muted fw-600 bg-light px-2 py-1 rounded"><?= e($m['slot_time']) ?></div>
                                    </div>
                                    <div class="menu-content fw-500 text-muted small mt-2">
                                        <?= nl2br(e($m['items'] ?: 'Menu not updated')) ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <?php 
                            }
                        } 
                        if (!$hasItems): ?>
                        <div class="text-center py-5 opacity-50">
                            <i class="bi bi-calendar-x fs-1 mb-2"></i>
                            <div class="small fw-600">No menu assigned for this day</div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<style>
.bg-primary-subtle { background: #e3f2fd !important; }

.x-small {
    font-size: 0.65rem;
    font-weight: 800;
    letter-spacing: 0.05em;
}

.slot-icon-box {
    width: 44px;
    height: 44px;
    flex-shrink: 0;
}

.menu-slot:hover {
    background: #fcfcfc;
}

.last-child-no-border:last-child {
    border-bottom: 0 !important;
}

.letter-spacing-1 {
    letter-spacing: 0.05em;
}

.menu-content {
    line-height: 1.6;
}

/* Day specific accent colors (optional but nice) */
.col-xl-4:nth-child(1) .card-header { border-top: 3px solid #6366f1; } /* Mon */
.col-xl-4:nth-child(2) .card-header { border-top: 3px solid #8b5cf6; } /* Tue */
.col-xl-4:nth-child(3) .card-header { border-top: 3px solid #ec4899; } /* Wed */
.col-xl-4:nth-child(4) .card-header { border-top: 3px solid #f43f5e; } /* Thu */
.col-xl-4:nth-child(5) .card-header { border-top: 3px solid #f59e0b; } /* Fri */
.col-xl-4:nth-child(6) .card-header { border-top: 3px solid #10b981; } /* Sat */
.col-xl-4:nth-child(7) .card-header { border-top: 3px solid #06b6d4; } /* Sun */
</style>
