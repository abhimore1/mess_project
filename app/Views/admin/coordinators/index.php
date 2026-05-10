<div class="container-fluid p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1 text-gradient-primary">Coordinators</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0" style="background:transparent; padding:0;">
                    <li class="breadcrumb-item"><a href="<?= url('admin/dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item active">Coordinators</li>
                </ol>
            </nav>
        </div>
        <a href="<?= url('admin/coordinators/create') ?>" class="btn btn-primary btn-sm rounded-pill shadow-sm">
            <i class="bi bi-plus-lg me-1"></i> Add Coordinator
        </a>
    </div>

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4 py-3 text-uppercase text-secondary font-xs font-weight-bolder opacity-7">Coordinator</th>
                        <th class="py-3 text-uppercase text-secondary font-xs font-weight-bolder opacity-7">Contact Info</th>
                        <th class="py-3 text-uppercase text-secondary font-xs font-weight-bolder opacity-7">Status</th>
                        <th class="py-3 text-uppercase text-secondary font-xs font-weight-bolder opacity-7 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($coordinators)): ?>
                        <tr>
                            <td colspan="4" class="text-center py-5">
                                <div class="text-muted">
                                    <i class="bi bi-people display-4 mb-3 d-block opacity-25"></i>
                                    <p class="mb-0">No coordinators found for your mess.</p>
                                    <small>Create your first coordinator to help manage operations.</small>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($coordinators as $coord): ?>
                            <tr>
                                <td class="ps-4">
                                    <div class="d-flex px-2 py-1">
                                        <div class="avatar avatar-sm bg-gradient-info rounded-circle me-3 d-flex align-items-center justify-content-center text-white font-weight-bold" style="width: 40px; height: 40px;">
                                            <?= strtoupper(substr($coord['full_name'], 0, 1)) ?>
                                        </div>
                                        <div class="d-flex flex-column justify-content-center">
                                            <h6 class="mb-0 text-sm font-weight-bold"><?= e($coord['full_name']) ?></h6>
                                            <p class="text-xs text-secondary mb-0">Joined <?= format_date($coord['created_at']) ?></p>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <div class="d-flex flex-column">
                                        <p class="text-sm font-weight-bold mb-0"><i class="bi bi-envelope-at me-1 text-muted"></i> <?= e($coord['email']) ?></p>
                                        <p class="text-xs text-secondary mb-0"><i class="bi bi-telephone me-1 text-muted"></i> <?= e($coord['phone'] ?: 'N/A') ?></p>
                                    </div>
                                </td>
                                <td>
                                    <?= badge($coord['user_status']) ?>
                                </td>
                                <td class="text-center pe-4">
                                    <a href="<?= url('admin/coordinators/' . $coord['user_id'] . '/edit') ?>" class="btn btn-icon btn-sm btn-outline-info rounded-circle border-0 me-1" title="Edit">
                                        <i class="bi bi-pencil-square"></i>
                                    </a>
                                    <button type="button" class="btn btn-icon btn-sm btn-outline-danger rounded-circle border-0" 
                                            onclick="confirmDelete('<?= url('admin/coordinators/' . $coord['user_id'] . '/delete') ?>', 'Are you sure you want to deactivate this coordinator?')" 
                                            title="Deactivate">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function confirmDelete(url, message) {
    if (confirm(message)) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = url;
        
        const token = document.createElement('input');
        token.type = 'hidden';
        token.name = '_token';
        token.value = '<?= csrf() ?>';
        
        form.appendChild(token);
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
