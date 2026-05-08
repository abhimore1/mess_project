<?php $pageTitle='Coordinator Dashboard'; ?>
<div class="panel p-5 text-center">
    <h3 class="fw-700 text-primary">Welcome, <?= e(auth_user()['full_name']) ?></h3>
    <p class="text-muted">You are currently assigned to manage <?= count($tenants) ?> messes.</p>
</div>
