<?php $pageTitle = 'Complaints'; ?>

<div class="container-fluid px-0">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3 mb-4 animate-fadeIn">
        <div>
            <h4 class="fw-700 mb-1 text-dark">Complaints Management 🚨</h4>
            <p class="text-muted small mb-0">Track and resolve student grievances and feedback.</p>
        </div>
        <div class="d-flex align-items-center gap-2">
            <span class="badge rounded-pill bg-primary-subtle text-primary px-3 py-2 fw-600">
                <i class="bi bi-info-circle me-1"></i> <?= count($complaints) ?> Total Cases
            </span>
        </div>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden animate-fadeInUp">
        <div class="card-header bg-white border-0 py-3 px-4">
            <h6 class="fw-700 mb-0">Student Complaints</h6>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-surface-variant">
                        <tr>
                            <th class="ps-4 py-3 border-0 x-small">Date</th>
                            <th class="py-3 border-0 x-small">Student</th>
                            <th class="py-3 border-0 x-small">Issue</th>
                            <th class="py-3 border-0 x-small text-center">Priority</th>
                            <th class="py-3 border-0 x-small text-center">Status</th>
                            <th class="pe-4 py-3 border-0 text-end x-small">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach($complaints as $c): ?>
                        <tr>
                            <td class="ps-4">
                                <div class="small fw-600 text-dark"><?= date('d M Y', strtotime($c['created_at'])) ?></div>
                                <div class="x-small text-muted"><?= date('H:i A', strtotime($c['created_at'])) ?></div>
                            </td>
                            <td>
                                <a href="<?= url('admin/students/'.$c['student_id']) ?>" class="text-decoration-none d-flex align-items-center gap-2">
                                    <div class="avatar-sm-rounded bg-primary-subtle text-primary fw-700 d-flex align-items-center justify-content-center" style="width:32px;height:32px;border-radius:8px">
                                        <?= strtoupper(substr($c['student_name'],0,1)) ?>
                                    </div>
                                    <div class="fw-600 text-dark small"><?= e($c['student_name']) ?></div>
                                </a>
                            </td>
                            <td style="max-width: 300px;">
                                <div class="fw-700 text-dark small mb-1"><?= e($c['subject']) ?></div>
                                <div class="text-muted small text-truncate-2"><?= e($c['description']) ?></div>
                            </td>
                            <td class="text-center">
                                <?php if($c['priority']==='high'): ?>
                                    <span class="badge rounded-pill bg-danger-subtle text-danger px-3">High</span>
                                <?php elseif($c['priority']==='low'): ?>
                                    <span class="badge rounded-pill bg-secondary-subtle text-secondary px-3">Low</span>
                                <?php else: ?>
                                    <span class="badge rounded-pill bg-warning-subtle text-warning px-3">Medium</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <?php if($c['status']==='open'): ?>
                                    <span class="badge rounded-pill bg-danger-subtle text-danger px-3">Open</span>
                                <?php else: ?>
                                    <span class="badge rounded-pill bg-success-subtle text-success px-3">Resolved</span>
                                <?php endif; ?>
                            </td>
                            <td class="pe-4 text-end">
                                <?php if($c['status']==='open'): ?>
                                <form method="POST" action="<?= url('admin/complaints/'.$c['complaint_id'].'/status') ?>" class="d-inline">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="status" value="resolved">
                                    <button type="submit" class="btn btn-sm btn-success rounded-pill px-3 fw-700 shadow-sm">
                                        <i class="bi bi-check2-circle me-1"></i> Resolve
                                    </button>
                                </form>
                                <?php else: ?>
                                    <button class="btn btn-sm btn-light rounded-pill px-3 fw-700 text-muted" disabled>
                                        <i class="bi bi-check-all me-1"></i> Done
                                    </button>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if(empty($complaints)): ?>
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted opacity-50">
                                <i class="bi bi-clipboard-check fs-1 mb-2"></i>
                                <div class="small fw-600">No complaints reported yet.</div>
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<style>
.x-small {
    font-size: 0.65rem;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}

.text-truncate-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.bg-primary-subtle { background: #e3f2fd !important; }
.bg-success-subtle { background: #e8f5e9 !important; }
.bg-danger-subtle { background: #ffebee !important; }
.bg-warning-subtle { background: #fff8e1 !important; }
.bg-secondary-subtle { background: #f5f5f5 !important; }

.table th {
    font-weight: 700;
    color: var(--text-muted);
}
</style>
