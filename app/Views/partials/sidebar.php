<?php
/**
 * Dynamic sidebar — renders menu items based on active role + enabled modules + permissions.
 */
$role = auth_user()['role'];
?>

<?php if ($role === 'super_admin'): ?>
    <div class="nav-section">Platform</div>
    <a href="<?= url('super/dashboard') ?>" class="nav-link <?= str_contains($_SERVER['REQUEST_URI'],'super/dashboard')?'active':'' ?>">
        <i class="bi bi-speedometer2"></i> Dashboard
    </a>
    <a href="<?= url('super/tenants') ?>" class="nav-link <?= str_contains($_SERVER['REQUEST_URI'],'super/tenant')?'active':'' ?>">
        <i class="bi bi-building"></i> Mess Management
    </a>
    <a href="<?= url('super/plans') ?>" class="nav-link <?= str_contains($_SERVER['REQUEST_URI'],'super/plan')?'active':'' ?>">
        <i class="bi bi-layers"></i> Subscription Plans
    </a>
    <a href="<?= url('super/coordinators') ?>" class="nav-link <?= str_contains($_SERVER['REQUEST_URI'],'super/coord')?'active':'' ?>">
        <i class="bi bi-diagram-3"></i> Coordinators
    </a>
    <div class="nav-section">Analytics</div>
    <a href="<?= url('super/analytics') ?>" class="nav-link <?= str_contains($_SERVER['REQUEST_URI'],'analytics')?'active':'' ?>">
        <i class="bi bi-bar-chart-line"></i> Analytics
    </a>
    <a href="<?= url('super/audit-logs') ?>" class="nav-link <?= str_contains($_SERVER['REQUEST_URI'],'audit')?'active':'' ?>">
        <i class="bi bi-journal-text"></i> Audit Logs
    </a>

<?php elseif ($role === 'mess_admin'): ?>
    <div class="nav-section">Main</div>
    <a href="<?= url('admin/dashboard') ?>" class="nav-link <?= str_contains($_SERVER['REQUEST_URI'],'admin/dashboard')?'active':'' ?>">
        <i class="bi bi-speedometer2"></i> Dashboard
    </a>
    <a href="<?= url('admin/profile') ?>" class="nav-link <?= str_contains($_SERVER['REQUEST_URI'],'admin/profile')?'active':'' ?>">
        <i class="bi bi-person-badge"></i> Mess Profile
    </a>
    <a href="<?= url('admin/students') ?>" class="nav-link <?= str_contains($_SERVER['REQUEST_URI'],'admin/student')?'active':'' ?>">
        <i class="bi bi-people"></i> Students
    </a>

    <?php if (module_enabled('membership')): ?>
    <a href="<?= url('admin/memberships') ?>" class="nav-link <?= str_contains($_SERVER['REQUEST_URI'],'membership')?'active':'' ?>">
        <i class="bi bi-card-checklist"></i> Memberships
    </a>
    <?php endif; ?>

    <a href="<?= url('admin/payments') ?>" class="nav-link <?= str_contains($_SERVER['REQUEST_URI'],'admin/payment')?'active':'' ?>">
        <i class="bi bi-credit-card"></i> Payments
    </a>

    <div class="nav-section">Operations</div>
    <a href="<?= url('admin/attendance') ?>" class="nav-link <?= str_contains($_SERVER['REQUEST_URI'],'attendance')?'active':'' ?>">
        <i class="bi bi-calendar-check"></i> Attendance
    </a>
    <a href="<?= url('admin/meal-slots') ?>" class="nav-link <?= str_contains($_SERVER['REQUEST_URI'],'meal-slot')?'active':'' ?>">
        <i class="bi bi-clock"></i> Meal Slots
    </a>

    <?php if (module_enabled('food_menu')): ?>
    <a href="<?= url('admin/food-menu') ?>" class="nav-link <?= str_contains($_SERVER['REQUEST_URI'],'food-menu')?'active':'' ?>">
        <i class="bi bi-journal-text"></i> Food Menu
    </a>
    <?php endif; ?>

    <?php if (module_enabled('complaints')): ?>
    <a href="<?= url('admin/complaints') ?>" class="nav-link <?= str_contains($_SERVER['REQUEST_URI'],'complaint')?'active':'' ?>">
        <i class="bi bi-chat-square-text"></i> Complaints
        <?php
        $openComplaints = DB::queryOne("SELECT COUNT(*) AS c FROM complaints WHERE tenant_id=? AND status='open'",[auth_user()['tenant_id']]);
        if(($openComplaints['c']??0)>0): ?>
        <span class="nav-badge"><?= $openComplaints['c'] ?></span>
        <?php endif; ?>
    </a>
    <?php endif; ?>

    <div class="nav-section">Reports</div>
    <a href="<?= url('admin/reports') ?>" class="nav-link <?= str_contains($_SERVER['REQUEST_URI'],'report')?'active':'' ?>">
        <i class="bi bi-file-earmark-bar-graph"></i> Reports
    </a>
    <?php if (module_enabled('notifications')): ?>
    <a href="<?= url('admin/notifications') ?>" class="nav-link <?= str_contains($_SERVER['REQUEST_URI'],'notification')?'active':'' ?>">
        <i class="bi bi-bell"></i> Notifications
    </a>
    <?php endif; ?>

    <div class="nav-section">System</div>
    <a href="<?= url('admin/years') ?>" class="nav-link <?= str_contains($_SERVER['REQUEST_URI'],'admin/year')?'active':'' ?>">
        <i class="bi bi-calendar3"></i> Academic Years
    </a>
    <a href="<?= url('admin/settings') ?>" class="nav-link <?= str_contains($_SERVER['REQUEST_URI'],'settings')?'active':'' ?>">
        <i class="bi bi-gear"></i> Settings
    </a>

<?php elseif ($role === 'student'): ?>
    <div class="nav-section">My Account</div>
    <a href="<?= url('student/dashboard') ?>" class="nav-link"><i class="bi bi-house"></i> Dashboard</a>
    <a href="<?= url('student/profile') ?>"   class="nav-link"><i class="bi bi-person"></i> My Profile</a>
    <a href="<?= url('student/membership') ?>" class="nav-link"><i class="bi bi-card-checklist"></i> Membership</a>
    <a href="<?= url('student/payments') ?>"  class="nav-link"><i class="bi bi-receipt"></i> Payments</a>
    <?php if(module_enabled('attendance')): ?>
    <a href="<?= url('student/attendance') ?>" class="nav-link"><i class="bi bi-calendar-check"></i> Attendance</a>
    <?php endif; ?>
    <?php if(module_enabled('food_menu')): ?>
    <a href="<?= url('student/food-menu') ?>" class="nav-link"><i class="bi bi-journal-text"></i> Food Menu</a>
    <?php endif; ?>
    <?php if(module_enabled('complaints')): ?>
    <a href="<?= url('student/complaints') ?>" class="nav-link"><i class="bi bi-chat-square-text"></i> Complaints</a>
    <?php endif; ?>
    <a href="<?= url('student/notifications') ?>" class="nav-link"><i class="bi bi-bell"></i> Notifications</a>

<?php elseif ($role === 'coordinator'): ?>
    <div class="nav-section">Overview</div>
    <a href="<?= url('coordinator/dashboard') ?>" class="nav-link"><i class="bi bi-speedometer2"></i> Dashboard</a>
    <a href="<?= url('coordinator/students') ?>"  class="nav-link"><i class="bi bi-people"></i> Students</a>
    <a href="<?= url('coordinator/reports') ?>"   class="nav-link"><i class="bi bi-bar-chart-line"></i> Reports</a>
    <a href="<?= url('coordinator/complaints') ?>" class="nav-link"><i class="bi bi-chat-square-text"></i> Complaints</a>
<?php endif; ?>
