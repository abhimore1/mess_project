<?php $pageTitle='Food Menu'; ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-0 fw-700">Weekly Menu</h5>
</div>
<div class="row g-4">
<?php
$days = [1=>'Monday',2=>'Tuesday',3=>'Wednesday',4=>'Thursday',5=>'Friday',6=>'Saturday',7=>'Sunday'];
foreach($days as $dNum=>$dName): 
?>
<div class="col-md-6 col-lg-4">
    <div class="panel h-100 <?= date('N')==$dNum ? 'border-primary shadow' : '' ?>">
        <div class="panel-header bg-light d-flex justify-content-between align-items-center">
            <h6 class="mb-0 <?= date('N')==$dNum ? 'text-primary fw-800' : '' ?>"><?= $dName ?></h6>
            <?php if(date('N')==$dNum): ?><span class="badge bg-primary">Today</span><?php endif; ?>
        </div>
        <div class="panel-body p-0">
            <ul class="list-unstyled mb-0">
            <?php 
            $hasItems = false;
            foreach($menus as $m) {
                if($m['day_of_week'] == $dNum) {
                    $hasItems = true;
            ?>
            <li class="p-3 border-bottom" style="border-color:var(--border)!important">
                <div class="fw-600 small mb-1 text-primary"><?= e($m['slot_name']) ?> <span class="text-muted ms-1 fw-normal" style="font-size:.7rem"><?= e($m['slot_time']) ?></span></div>
                <div class="small"><?= nl2br(e($m['items']?:'—')) ?></div>
            </li>
            <?php 
                }
            } 
            if(!$hasItems): ?>
            <li class="p-4 text-center text-muted small">No menu assigned.</li>
            <?php endif; ?>
            </ul>
        </div>
    </div>
</div>
<?php endforeach; ?>
</div>
