<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
<meta name="theme-color" content="#6750A4">
<title><?= $pageTitle ?? 'Dashboard' ?> — <?= e(mess_name()) ?></title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Google+Sans:wght@300;400;500;600;700&family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.7/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
<link rel="stylesheet" href="<?= url('assets/css/theme.css') ?>">
<style>
/* Additional app-specific overrides */
.select2-container--default .select2-selection--single,
.select2-container--default .select2-selection--multiple {
    background: var(--surface-container-highest);
    border: 1px solid transparent;
    border-radius: var(--radius-sm);
    height: auto;
    padding: 8px 12px;
}
.select2-container--default .select2-selection--single:focus,
.select2-container--default.select2-container--focus .select2-selection--multiple {
    border-color: var(--primary);
    box-shadow: 0 0 0 3px var(--primary-container);
}
.select2-dropdown {
    background: var(--surface-container-lowest);
    border: 1px solid var(--outline-variant);
    border-radius: var(--radius-md);
    box-shadow: var(--shadow-4);
}
.select2-container--default .select2-results__option--highlighted[aria-selected] {
    background: var(--primary-container);
    color: var(--on-primary-container);
}
.dataTables_wrapper .dataTables_length,
.dataTables_wrapper .dataTables_filter,
.dataTables_wrapper .dataTables_info,
.dataTables_wrapper .dataTables_paginate {
    font-size: var(--font-size-xs);
    color: var(--text-secondary);
}
.dataTables_wrapper .dataTables_paginate .paginate_button {
    border-radius: var(--radius-full);
    padding: 6px 12px;
    margin: 0 2px;
    border: none !important;
    background: transparent !important;
    color: var(--text-secondary) !important;
}
.dataTables_wrapper .dataTables_paginate .paginate_button:hover {
    background: var(--surface-container) !important;
    color: var(--text-primary) !important;
}
.dataTables_wrapper .dataTables_paginate .paginate_button.current {
    background: var(--primary-container) !important;
    color: var(--on-primary-container) !important;
    font-weight: 600;
}
.dataTables_wrapper .dataTables_paginate .paginate_button.disabled {
    opacity: 0.5;
}
table.dataTable thead th {
    font-size: var(--font-size-xs);
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: var(--text-tertiary);
    background: var(--surface-container);
    border-bottom: 1px solid var(--outline-variant) !important;
}
table.dataTable tbody td {
    font-size: var(--font-size-sm);
    color: var(--text-primary);
    border-bottom: 1px solid var(--outline-variant);
}
table.dataTable tbody tr:hover {
    background: var(--surface-container-low);
}
/* Fix dropdown bullets */
.dropdown-menu, .dropdown-menu li { list-style: none !important; list-style-type: none !important; padding-left: 0 !important; margin-left: 0 !important; }
</style>
<?php if(isset($extraCss)) echo $extraCss; ?>
</head>
<body>

<!-- Sidebar -->
<aside class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <div class="brand-icon"><i class="bi bi-grid-3x3-gap-fill"></i></div>
        <div>
            <div class="brand-name"><?= e(mess_name()) ?></div>
            <div class="brand-sub"><?= e(ucfirst(str_replace('_',' ',auth_user()['role']))) ?></div>
        </div>
    </div>

    <nav class="sidebar-nav" id="sidebarNav">
        <?php include APP_PATH . '/Views/partials/sidebar.php'; ?>
    </nav>

    <div class="sidebar-footer">
        <div class="user-card">
            <div class="user-avatar"><?= strtoupper(substr(auth_user()['full_name'],0,1)) ?></div>
            <div class="user-info">
                <div class="user-name"><?= e(auth_user()['full_name']) ?></div>
                <div class="user-email"><?= e(auth_user()['email']) ?></div>
            </div>
            <a href="<?= url('logout') ?>" class="logout-btn" title="Logout">
                <i class="bi bi-box-arrow-right"></i>
            </a>
        </div>
    </div>
</aside>
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<!-- Topbar -->
<header class="topbar">
    <button class="menu-toggle" id="sidebarToggle"><i class="bi bi-list"></i></button>
    <div class="page-title"><?= $pageTitle ?? 'Dashboard' ?></div>
    <div class="topbar-actions">
        <button class="action-btn position-relative" id="notifBtn" onclick="loadNotifications()">
            <i class="bi bi-bell"></i>
            <span class="badge-dot" id="notifDot" style="display:none"></span>
        </button>
        <div class="dropdown">
            <button class="user-avatar dropdown-toggle border-0" data-bs-toggle="dropdown" style="width:40px;height:40px">
                <?= strtoupper(substr(auth_user()['full_name'],0,1)) ?>
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow-4">
                <li><a class="dropdown-item text-secondary text-label" href="#"><?= e(auth_user()['full_name']) ?></a></li>
                <li><div class="dropdown-divider"></div></li>
                <li><a class="dropdown-item" href="<?= url(auth_user()['role'] === 'super_admin' ? 'super/dashboard' : 'admin/settings') ?>"><i class="bi bi-gear"></i>Settings</a></li>
                <li><a class="dropdown-item" href="<?= url('logout') ?>" style="color:var(--error)"><i class="bi bi-box-arrow-right"></i>Logout</a></li>
            </ul>
        </div>
    </div>
</header>

<!-- Main content -->
<main class="main-content">
    <?php if ($flash = flash('success')): ?>
    <div class="alert alert-success">
        <i class="bi bi-check-circle-fill"></i>
        <span><?= e($flash) ?></span>
        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" style="width:24px;height:24px;padding:0;background:transparent;border:none;color:var(--success);cursor:pointer"><i class="bi bi-x-lg"></i></button>
    </div>
    <?php endif; ?>
    <?php if ($flash = flash('error')): ?>
    <div class="alert alert-error">
        <i class="bi bi-exclamation-circle-fill"></i>
        <span><?= e($flash) ?></span>
        <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" style="width:24px;height:24px;padding:0;background:transparent;border:none;color:var(--error);cursor:pointer"><i class="bi bi-x-lg"></i></button>
    </div>
    <?php endif; ?>

    <?= $content ?>
</main>

<!-- Toast container -->
<div class="toast-container" id="toastContainer"></div>

<!-- Mobile Bottom Navigation -->
<nav class="bottom-nav d-lg-none">
    <div class="bottom-nav-items">
        <a href="<?= url('admin/dashboard') ?>" class="bottom-nav-item <?= str_contains($_SERVER['REQUEST_URI'],'admin/dashboard')?'active':'' ?>">
            <i class="bi bi-speedometer2"></i>
            <span>Home</span>
        </a>
        <a href="<?= url('admin/students') ?>" class="bottom-nav-item <?= str_contains($_SERVER['REQUEST_URI'],'admin/student')?'active':'' ?>">
            <i class="bi bi-people"></i>
            <span>Students</span>
        </a>
        <a href="<?= url('admin/payments/create') ?>" class="bottom-nav-item" style="color:var(--primary)">
            <i class="bi bi-plus-circle-fill" style="font-size:1.75rem"></i>
            <span>Collect</span>
        </a>
        <a href="<?= url('admin/attendance') ?>" class="bottom-nav-item <?= str_contains($_SERVER['REQUEST_URI'],'attendance')?'active':'' ?>">
            <i class="bi bi-calendar-check"></i>
            <span>Attendance</span>
        </a>
        <a href="<?= url('admin/settings') ?>" class="bottom-nav-item <?= str_contains($_SERVER['REQUEST_URI'],'settings')?'active':'' ?>">
            <i class="bi bi-gear"></i>
            <span>Settings</span>
        </a>
    </div>
</nav>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script src="https://cdn.datatables.net/1.13.7/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.7/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
const APP_URL = '<?= url() ?>';
const CSRF_TOKEN = '<?= csrf() ?>';

// Mobile sidebar - ensure closed on page load
document.addEventListener('DOMContentLoaded', () => {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    if (sidebar) sidebar.classList.remove('open');
    if (overlay) overlay.classList.remove('show');
});

// Toggle sidebar on hamburger click
document.getElementById('sidebarToggle')?.addEventListener('click', (e) => {
    e.stopPropagation();
    document.getElementById('sidebar').classList.toggle('open');
    document.getElementById('sidebarOverlay').classList.toggle('show');
});

// Close sidebar when clicking overlay
document.getElementById('sidebarOverlay')?.addEventListener('click', () => {
    document.getElementById('sidebar').classList.remove('open');
    document.getElementById('sidebarOverlay').classList.remove('show');
});

// Close sidebar when clicking inside sidebar nav links (mobile)
document.querySelectorAll('.sidebar-nav .nav-link').forEach(link => {
    link.addEventListener('click', () => {
        if (window.innerWidth <= 992) {
            document.getElementById('sidebar').classList.remove('open');
            document.getElementById('sidebarOverlay').classList.remove('show');
        }
    });
});

// Prevent dropdown clicks from opening sidebar
document.querySelector('.dropdown')?.addEventListener('click', (e) => {
    e.stopPropagation();
});



// Toast helper
function showToast(message, type = 'success') {
    const icons = {success:'bi-check-circle-fill', error:'bi-x-circle-fill', warning:'bi-exclamation-triangle-fill', info:'bi-info-circle-fill'};
    const colors = {success:'var(--success)', error:'var(--error)', warning:'var(--warning)', info:'var(--info)'};
    const toast = document.createElement('div');
    toast.className = 'toast';
    toast.innerHTML = `
        <i class="bi ${icons[type]||icons.info}" style="color:${colors[type]};font-size:1.25rem;flex-shrink:0"></i>
        <span style="flex:1;font-size:var(--font-size-sm);color:var(--text-primary)">${message}</span>
        <button onclick="this.parentElement.remove()" class="btn-icon btn-icon-sm" style="flex-shrink:0;color:var(--text-tertiary)">
            <i class="bi bi-x-lg"></i>
        </button>`;
    document.getElementById('toastContainer').appendChild(toast);
    setTimeout(() => toast.style.opacity = '0', 3500);
    setTimeout(() => toast.remove(), 4000);
}

// AJAX CSRF helper
$.ajaxSetup({ headers: { 'X-CSRF-TOKEN': CSRF_TOKEN } });

// Notifications unread count
function loadNotifications() {
    fetch(`${APP_URL}/api/notifications/unread-count`)
        .then(r => r.json()).then(d => {
            if (d.count > 0) document.getElementById('notifDot').style.display = 'block';
        }).catch(()=>{});
}
loadNotifications();

// SweetAlert confirm helper with Material Design styling
function confirmDelete(btn) {
    Swal.fire({
        title: 'Are you sure?',
        text: 'This action cannot be undone.',
        icon: 'warning',
        iconColor: 'var(--warning)',
        showCancelButton: true,
        confirmButtonColor: '#B3261E',
        cancelButtonColor: '#625B71',
        confirmButtonText: 'Yes, delete it!',
        cancelButtonText: 'Cancel',
        background: '#FEF7FF',
        color: '#1D1B20',
        customClass: {
            popup: 'animate-scaleIn',
            confirmButton: 'btn',
            cancelButton: 'btn'
        },
        buttonsStyling: false
    }).then(r => { 
        if (r.isConfirmed) { 
            if (typeof btn === 'string') { window.location.href = btn; }
            else { btn.closest('form').submit(); }
        } 
    });
}
</script>
<?php if(isset($extraJs)) echo $extraJs; ?>
</body>
</html>
