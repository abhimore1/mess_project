<?php $pageTitle = 'Notifications'; ?>

<div class="container-fluid px-0">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4 animate-fadeIn">
                <div>
                    <h4 class="fw-700 mb-1">My Notifications 🔔</h4>
                    <p class="text-muted small mb-0">Stay updated with the latest alerts and announcements.</p>
                </div>
                <?php if (!empty($notifs)): ?>
                <div class="badge rounded-pill bg-primary-subtle text-primary px-3 py-2 fw-600 x-small">
                    <?= count($notifs) ?> Total Alerts
                </div>
                <?php endif; ?>
            </div>

            <?php if (empty($notifs)): ?>
                <div class="card border-0 shadow-sm rounded-4 animate-fadeInUp" style="border-radius: 24px;">
                    <div class="card-body p-5 text-center">
                        <div class="mb-4 d-inline-flex align-items-center justify-content-center bg-surface-variant rounded-circle" style="width:100px;height:100px;">
                            <i class="bi bi-bell-slash text-muted fs-1 opacity-50"></i>
                        </div>
                        <h5 class="fw-700 text-dark">All Caught Up!</h5>
                        <p class="text-muted mb-0 mx-auto" style="max-width: 300px;">
                            You have no new notifications at the moment. We'll alert you when something important happens.
                        </p>
                    </div>
                </div>
            <?php else: ?>
                <div class="notification-container animate-fadeInUp">
                    <?php foreach ($notifs as $index => $n): 
                        $icon = 'bi-info-circle';
                        $colorClass = 'text-primary';
                        $bgClass = 'bg-primary-subtle';
                        
                        if ($n['type'] === 'success') {
                            $icon = 'bi-check-circle';
                            $colorClass = 'text-success';
                            $bgClass = 'bg-success-subtle';
                        } elseif ($n['type'] === 'warning') {
                            $icon = 'bi-exclamation-triangle';
                            $colorClass = 'text-warning';
                            $bgClass = 'bg-warning-subtle';
                        } elseif ($n['type'] === 'danger') {
                            $icon = 'bi-x-circle';
                            $colorClass = 'text-danger';
                            $bgClass = 'bg-danger-subtle';
                        }
                    ?>
                    <div class="card border-0 shadow-sm mb-3 notification-card stagger-<?= $index % 5 ?> <?= !$n['is_read'] ? 'unread' : '' ?>" 
                         style="border-radius: 16px; overflow: hidden; transition: transform 0.2s;">
                        <div class="card-body p-3">
                            <div class="d-flex gap-3 align-items-start">
                                <div class="notif-icon <?= $bgClass ?> <?= $colorClass ?> rounded-circle d-flex align-items-center justify-content-center shadow-sm" 
                                     style="width: 48px; height: 48px; flex-shrink: 0;">
                                    <i class="bi <?= $icon ?> fs-5"></i>
                                </div>
                                <div class="flex-grow-1">
                                    <div class="d-flex justify-content-between align-items-start mb-1">
                                        <h6 class="mb-0 fw-700 <?= !$n['is_read'] ? 'text-dark' : 'text-secondary' ?> small">
                                            <?= e($n['title']) ?>
                                            <?php if (!$n['is_read']): ?>
                                                <span class="badge rounded-circle bg-primary p-1 ms-1" style="width: 6px; height: 6px; display: inline-block; vertical-align: middle;"></span>
                                            <?php endif; ?>
                                        </h6>
                                        <div class="x-small text-muted fw-600 opacity-75">
                                            <?= date('d M, H:i', strtotime($n['created_at'])) ?>
                                        </div>
                                    </div>
                                    <p class="mb-0 small text-muted line-height-1-5">
                                        <?= e($n['message']) ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
.bg-primary-subtle { background: #e3f2fd !important; }
.bg-success-subtle { background: #e8f5e9 !important; }
.bg-warning-subtle { background: #fff8e1 !important; }
.bg-danger-subtle { background: #ffebee !important; }

.notification-card:hover {
    transform: scale(1.01);
}

.notification-card.unread {
    background: #f8fbff;
    border-left: 4px solid var(--primary) !important;
}

.x-small {
    font-size: 0.65rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.line-height-1-5 {
    line-height: 1.5;
}

.notif-icon {
    transition: transform 0.3s;
}

.notification-card:hover .notif-icon {
    transform: rotate(15deg);
}
</style>
