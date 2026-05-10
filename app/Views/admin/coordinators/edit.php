<div class="container-fluid p-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1 text-gradient-primary">Edit Coordinator</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0" style="background:transparent; padding:0;">
                    <li class="breadcrumb-item"><a href="<?= url('admin/dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= url('admin/coordinators') ?>">Coordinators</a></li>
                    <li class="breadcrumb-item active">Edit</li>
                </ol>
            </nav>
        </div>
        <a href="<?= url('admin/coordinators') ?>" class="btn btn-outline-secondary btn-sm rounded-pill">
            <i class="bi bi-arrow-left me-1"></i> Back to List
        </a>
    </div>

    <form action="<?= url('admin/coordinators/' . $coordinator['user_id'] . '/update') ?>" method="POST" class="row g-4">
        <?= csrf_field() ?>
        
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white py-3 border-bottom border-light">
                    <h6 class="mb-0 text-dark font-weight-bold">Basic Information</h6>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label text-xs text-uppercase text-secondary font-weight-bolder opacity-7">Full Name</label>
                            <input type="text" name="full_name" class="form-control rounded-3" value="<?= e($coordinator['full_name']) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-xs text-uppercase text-secondary font-weight-bolder opacity-7">Email Address</label>
                            <input type="email" name="email" class="form-control rounded-3" value="<?= e($coordinator['email']) ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-xs text-uppercase text-secondary font-weight-bolder opacity-7">Phone Number</label>
                            <input type="text" name="phone" class="form-control rounded-3" value="<?= e($coordinator['phone']) ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-xs text-uppercase text-secondary font-weight-bolder opacity-7">Status</label>
                            <select name="status" class="form-select rounded-3">
                                <option value="active" <?= $coordinator['user_status'] === 'active' ? 'selected' : '' ?>>Active</option>
                                <option value="inactive" <?= $coordinator['user_status'] === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-xs text-uppercase text-secondary font-weight-bolder opacity-7">New Password</label>
                            <input type="password" name="password" class="form-control rounded-3" placeholder="Leave blank to keep current">
                            <small class="text-muted text-xs">At least 8 characters if changing.</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card border-0 shadow-sm rounded-4">
                <div class="card-header bg-white py-3 border-bottom border-light">
                    <h6 class="mb-0 text-dark font-weight-bold">Access Controls</h6>
                </div>
                <div class="card-body p-4">
                    <p class="text-xs text-muted mb-3">Select the modules this coordinator is allowed to access.</p>
                    
                    <div class="permissions-list">
                        <?php 
                        $permMap = [
                            'students'      => 'students.view',
                            'payments'      => 'payments.view',
                            'attendance'    => 'attendance.view',
                            'food_menu'     => 'food_menu.view',
                            'complaints'    => 'complaints.view',
                            'reports'       => 'reports.view',
                            'membership'    => 'membership.view',
                            'settings'      => 'settings.manage',
                        ];

                        foreach ($allEnabled as $module): 
                            if ($module['slug'] === 'dashboard') continue;
                            $permKey = $permMap[$module['slug']] ?? null;
                            if (!$permKey) continue;
                            $isChecked = in_array($permKey, $coordinator['custom_permissions']);
                        ?>
                            <div class="form-check form-switch mb-3">
                                <input class="form-check-input" type="checkbox" name="permissions[]" value="<?= $permKey ?>" id="perm_<?= $module['slug'] ?>" <?= $isChecked ? 'checked' : '' ?>>
                                <label class="form-check-label ps-2" for="perm_<?= $module['slug'] ?>">
                                    <span class="d-block text-sm font-weight-bold"><?= e($module['name']) ?></span>
                                    <span class="text-xs text-secondary">Allows managing <?= strtolower($module['name']) ?>.</span>
                                </label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <hr class="my-4 light">
                    
                    <button type="submit" class="btn btn-info w-100 rounded-pill shadow-sm text-white font-weight-bold">
                        Update Coordinator
                    </button>
                </div>
            </div>
        </div>
    </form>
</div>
