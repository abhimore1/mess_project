<?php $pageTitle='Food Menu'; ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="mb-0 fw-700">Food Menu</h5>
</div>
<div class="row g-4">
<?php
$days = [1=>'Monday',2=>'Tuesday',3=>'Wednesday',4=>'Thursday',5=>'Friday',6=>'Saturday',7=>'Sunday'];
foreach($days as $dNum=>$dName): 
?>
<div class="col-md-6 col-lg-4">
    <div class="panel h-100">
        <div class="panel-header bg-light"><h6><?= $dName ?></h6></div>
        <div class="panel-body p-0">
            <ul class="list-unstyled mb-0">
            <?php foreach($slots as $slot): 
                $menuItem = '';
                foreach($menus as $m) {
                    if($m['day_of_week'] == $dNum && $m['slot_id'] == $slot['slot_id']) {
                        $menuItem = $m['items']; break;
                    }
                }
            ?>
            <li class="p-3 border-bottom" style="border-color:var(--border)!important">
                <form method="POST" action="<?= url('admin/food-menu/store') ?>" class="menu-form">
                    <?= csrf_field() ?>
                    <input type="hidden" name="day_of_week" value="<?= $dNum ?>">
                    <input type="hidden" name="slot_id" value="<?= $slot['slot_id'] ?>">
                    <div class="fw-600 small mb-2 text-primary"><?= e($slot['name']) ?> <span class="text-muted ms-1" style="font-size:.7rem"><?= e($slot['slot_time']) ?></span></div>
                    <textarea name="items" class="form-control form-control-sm" rows="2" placeholder="Enter menu items..."><?= e($menuItem) ?></textarea>
                    <div class="text-end mt-2"><button type="submit" class="btn btn-sm btn-outline-success" style="font-size:.7rem;padding:.2rem .5rem">Save</button></div>
                </form>
            </li>
            <?php endforeach; ?>
            </ul>
        </div>
    </div>
</div>
<?php endforeach; ?>
</div>
<script>
document.querySelectorAll('.menu-form').forEach(f => {
    f.addEventListener('submit', e => {
        e.preventDefault();
        const btn = f.querySelector('button');
        btn.innerHTML = '<i class="bi bi-hourglass"></i>';
        fetch(f.action, {method:'POST', body:new FormData(f)})
        .then(r=>r.json()).then(d=>{
            if(d.success) { btn.innerHTML='<i class="bi bi-check"></i>'; btn.classList.replace('btn-outline-success','btn-success'); setTimeout(()=>{btn.innerHTML='Save';btn.classList.replace('btn-success','btn-outline-success');},2000); }
        });
    });
});
</script>
