<?php $pageTitle='Notifications'; ?>
<div class="row justify-content-center">
<div class="col-lg-8">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="mb-0 fw-700">Notifications</h5>
    </div>
    
    <?php if(empty($notifs)): ?>
        <div class="panel p-5 text-center text-muted">
            <i class="bi bi-bell-slash fs-1 d-block mb-3"></i>
            <p>You have no new notifications.</p>
        </div>
    <?php else: ?>
        <div class="list-group shadow-sm" style="border-radius:12px;overflow:hidden">
            <?php foreach($notifs as $n): 
                $icon = 'bi-info-circle text-info';
                if($n['type']==='success') $icon='bi-check-circle text-success';
                elseif($n['type']==='warning') $icon='bi-exclamation-triangle text-warning';
                elseif($n['type']==='danger') $icon='bi-x-circle text-danger';
            ?>
            <div class="list-group-item list-group-item-action p-3 <?= !$n['is_read'] ? 'bg-light' : '' ?>">
                <div class="d-flex gap-3">
                    <div class="fs-4"><i class="bi <?= $icon ?>"></i></div>
                    <div>
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <h6 class="mb-0 fw-600 <?= !$n['is_read'] ? 'text-dark' : 'text-secondary' ?>"><?= e($n['title']) ?></h6>
                            <small class="text-muted" style="font-size:.7rem"><?= format_date($n['created_at'],'d M H:i') ?></small>
                        </div>
                        <p class="mb-0 small text-muted"><?= e($n['message']) ?></p>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
</div>
