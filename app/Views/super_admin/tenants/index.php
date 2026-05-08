<?php $pageTitle='Manage Messes'; ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h5 class="fw-700 mb-0">All Messes</h5>
    <a href="<?= url('super/tenants/create') ?>" class="btn btn-primary-g btn-sm">
        <i class="bi bi-plus me-1"></i>Add Mess
    </a>
</div>
<div class="panel">
    <div class="panel-header gap-2 flex-wrap">
        <form class="d-flex gap-2" method="GET">
            <input type="search" name="q" class="form-control form-control-sm" placeholder="Search..." value="<?= e($search) ?>" style="width:200px">
            <select name="status" class="form-select form-select-sm" style="width:120px" onchange="this.form.submit()">
                <option value="active" <?= $status==='active'?'selected':'' ?>>Active</option>
                <option value="inactive" <?= $status==='inactive'?'selected':'' ?>>Inactive</option>
                <option value="">All</option>
            </select>
            <button class="btn btn-outline-secondary btn-sm" style="border-radius:8px">Search</button>
        </form>
    </div>
    <div class="table-responsive">
        <table class="table" id="tenantsTable">
            <thead><tr><th>Mess Name</th><th>Owner</th><th>Plan</th><th>Students</th><th>Status</th><th>Actions</th></tr></thead>
            <tbody>
            <?php foreach ($tenants as $t): ?>
            <tr>
                <td><div class="fw-600 small"><?= e($t['name']) ?></div><div class="text-muted" style="font-size:.73rem"><?= e($t['slug']) ?></div></td>
                <td class="small"><?= e($t['owner_name']??'—') ?></td>
                <td class="small"><?= e($t['plan_name']??'—') ?></td>
                <td><?= number_format($t['student_count']) ?></td>
                <td><?= badge($t['status']) ?></td>
                <td>
                    <a href="<?= url('super/tenants/'.$t['tenant_id'].'/edit') ?>" class="btn btn-sm btn-outline-secondary me-1" style="border-radius:8px"><i class="bi bi-pencil"></i></a>
                    <a href="<?= url('super/tenants/'.$t['tenant_id'].'/modules') ?>" class="btn btn-sm btn-outline-primary me-1" style="border-radius:8px"><i class="bi bi-puzzle"></i></a>
                    <button onclick="toggleTenant(<?= $t['tenant_id'] ?>,'<?= csrf() ?>')" class="btn btn-sm btn-outline-warning" style="border-radius:8px"><i class="bi bi-toggle-on"></i></button>
                </td>
            </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?= paginate_links($pagination, url('super/tenants')) ?>
<script>
new DataTable('#tenantsTable',{pageLength:15,searching:false,ordering:true});
function toggleTenant(id,token) {
    if(!confirm('Toggle mess status?')) return;
    fetch(`<?= url('super/tenants/') ?>${id}/toggle`,{method:'POST',headers:{'X-CSRF-TOKEN':token,'Content-Type':'application/x-www-form-urlencoded'},body:'_token='+token})
    .then(r=>r.json()).then(d=>{ if(d.success){showToast('Status changed to '+d.status,'success');setTimeout(()=>location.reload(),800);} });
}
</script>
