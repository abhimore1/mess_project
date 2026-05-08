<!DOCTYPE html>
<html lang="en" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — MessSaaS</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css">
    <style>
        :root {
            --primary: #1a73e8;
            --primary-hover: #1558d6;
            --bg: #f0f4f9;
            --card: #ffffff;
            --border: #dadce0;
            --text-main: #202124;
            --text-muted: #5f6368;
            --focus-ring: rgba(26,115,232,0.2);
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Inter', 'Google Sans', sans-serif;
            background: var(--bg);
            color: var(--text-main);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
        }
        .login-card {
            background: var(--card);
            border-radius: 12px;
            padding: 40px 36px 36px;
            width: 100%;
            max-width: 450px;
            box-shadow: 0 1px 3px rgba(60,64,67,0.1), 0 4px 12px rgba(60,64,67,0.05);
            border: 1px solid var(--border);
            text-align: center;
        }
        .brand-logo {
            width: 48px; height: 48px;
            background: var(--card);
            color: var(--primary);
            border: 1px solid var(--border);
            border-radius: 12px;
            display: flex; align-items: center; justify-content: center;
            font-size: 1.5rem;
            margin: 0 auto 16px;
        }
        .brand-title {
            font-size: 1.5rem;
            font-weight: 500;
            color: var(--text-main);
            margin-bottom: 4px;
        }
        .brand-subtitle {
            font-size: 0.95rem;
            color: var(--text-main);
            margin-bottom: 32px;
            font-weight: 400;
        }
        .form-label {
            font-size: 0.85rem;
            font-weight: 500;
            color: var(--text-main);
            display: block;
            text-align: left;
            margin-bottom: 6px;
        }
        .form-control, .select2-container--default .select2-selection--single {
            background: var(--card) !important;
            border: 1px solid var(--border) !important;
            color: var(--text-main) !important;
            border-radius: 6px !important;
            padding: 10px 14px !important;
            height: auto !important;
            font-size: 0.95rem;
            transition: all 0.2s ease;
        }
        .form-control:focus {
            border-color: var(--primary) !important;
            box-shadow: inset 0 0 0 1px var(--primary) !important;
            outline: none;
        }
        .select2-container--default .select2-selection__rendered { color: var(--text-main) !important; line-height: 24px !important; padding-left: 0 !important; text-align: left;}
        .select2-container--default .select2-selection__arrow { top: 10px !important; right: 10px !important; }
        .select2-dropdown {
            background: var(--card) !important;
            border: 1px solid var(--border) !important;
            border-radius: 6px !important;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .select2-results__option {
            color: var(--text-main);
            font-size: 0.95rem;
            text-align: left;
        }
        .select2-container--default .select2-results__option--highlighted {
            background: rgba(26,115,232,0.1) !important;
            color: var(--primary) !important;
        }
        .btn-primary-custom {
            background: var(--primary);
            border: none; 
            border-radius: 6px;
            padding: 10px 24px;
            font-weight: 500; 
            font-size: 0.95rem;
            color: #fff; 
            width: 100%;
            cursor: pointer;
            transition: background 0.2s, box-shadow 0.2s;
            margin-top: 16px;
        }
        .btn-primary-custom:hover {
            background: var(--primary-hover);
            box-shadow: 0 1px 3px rgba(60,64,67,0.3), 0 2px 6px rgba(60,64,67,0.15);
        }
        .btn-primary-custom:focus {
            outline: 2px solid var(--focus-ring);
            outline-offset: 2px;
        }
        .btn-primary-custom .spinner {
            display: none; width: 18px; height: 18px;
            border: 2px solid rgba(255,255,255,.3);
            border-top-color: #fff;
            border-radius: 50%;
            animation: spin .7s linear infinite;
        }
        @keyframes spin { to { transform: rotate(360deg); } }
        
        .role-tabs { 
            display: flex; 
            gap: 8px; 
            margin-bottom: 24px; 
            background: #f8f9fa;
            padding: 4px;
            border-radius: 8px;
            border: 1px solid var(--border);
        }
        .role-tab {
            flex: 1; padding: 6px;
            border-radius: 6px; border: none;
            background: transparent; color: var(--text-muted);
            font-size: 0.8rem; font-weight: 500;
            cursor: pointer; transition: all .2s;
            text-align: center;
        }
        .role-tab.active {
            background: var(--card);
            color: var(--primary);
            box-shadow: 0 1px 2px rgba(0,0,0,0.1);
        }
        .role-tab:hover:not(.active) {
            color: var(--text-main);
        }
        .role-tab i { display: block; font-size: 1.1rem; margin-bottom: 2px; }
        
        .alert-flash {
            background: #fce8e6;
            border: 1px solid #c5221f;
            border-radius: 6px;
            color: #c5221f;
            padding: 10px 16px;
            font-size: 0.9rem;
            text-align: left;
        }
        .pass-toggle { position: relative; }
        .pass-toggle .toggle-eye {
            position: absolute; right: 14px; top: 34px;
            color: var(--text-muted); cursor: pointer; z-index: 5;
        }
        .divider { border-color: var(--border); margin: 32px 0 24px; }
        .tenant-panel { display: none; text-align: left; }
        .tenant-panel.show { display: block; }
        #tenantBranding {
            background: #f8f9fa;
            border: 1px solid var(--border);
            border-radius: 6px;
            padding: 8px 12px;
            display: none;
        }
        .text-left { text-align: left; }
    </style>
</head>
<body>

<div class="login-card">
    <!-- Brand -->
    <div class="brand-logo"><i class="bi bi-grid-3x3-gap-fill"></i></div>
    <h1 class="brand-title">MessSaaS</h1>
    <p class="brand-subtitle">Multi-Tenant Mess Management</p>

    <!-- Flash error -->
    <?php if ($error = flash('error')): ?>
    <div class="alert-flash mb-3 d-flex align-items-center gap-2">
        <i class="bi bi-exclamation-circle"></i> <?= e($error) ?>
    </div>
    <?php endif; ?>

    <!-- Role selector tabs -->
    <div class="role-tabs mb-3" id="roleTabs">
        <button class="role-tab active" data-role="super_admin" type="button">
            <i class="bi bi-shield-lock"></i>Super Admin
        </button>
        <button class="role-tab" data-role="mess_admin" type="button">
            <i class="bi bi-building"></i>Mess Admin
        </button>
        <button class="role-tab" data-role="student" type="button">
            <i class="bi bi-person-badge"></i>Student
        </button>
        <button class="role-tab" data-role="coordinator" type="button">
            <i class="bi bi-diagram-3"></i>Coordinator
        </button>
    </div>

    <!-- Mess selector (hidden for super admin) -->
    <div class="tenant-panel mb-3" id="tenantPanel">
        <label class="form-label">SELECT YOUR MESS</label>
        <select name="mess_slug" id="messSelect" class="form-control" style="width:100%">
            <option value="">— Choose your mess —</option>
            <?php foreach ($tenants as $t): ?>
            <option value="<?= e($t['slug']) ?>" <?= old('mess_slug') === $t['slug'] ? 'selected' : '' ?>>
                <?= e($t['name']) ?>
            </option>
            <?php endforeach; ?>
        </select>
        <!-- Dynamic tenant branding preview -->
        <div id="tenantBranding" class="mt-2">
            <div class="d-flex align-items-center gap-2">
                <img id="tenantLogo" src="" width="28" height="28" style="border-radius:6px;display:none">
                <span id="tenantName" class="fw-600 small"></span>
            </div>
        </div>
    </div>

    <!-- Login form -->
    <form method="POST" action="<?= url('login') ?>" id="loginForm" class="text-left">
        <?= csrf_field() ?>
        <input type="hidden" name="mess_slug" id="hiddenSlug" value="">

        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control"
                   value="<?= e(old('email')) ?>"
                   autocomplete="email" required>
        </div>

        <div class="mb-4 pass-toggle">
            <label class="form-label">Password</label>
            <input type="password" name="password" id="passInput" class="form-control"
                   autocomplete="current-password" required>
            <span class="toggle-eye" onclick="togglePass()">
                <i class="bi bi-eye" id="eyeIcon"></i>
            </span>
        </div>

        <button type="submit" class="btn-primary-custom d-flex align-items-center justify-content-center gap-2" id="loginBtn">
            <span id="btnText">Sign In</span>
            <div class="spinner" id="spinner"></div>
        </button>
    </form>

    <hr class="divider">
    <p class="text-center text-muted" style="font-size:.78rem">
        &copy; <?= date('Y') ?> MessSaaS &mdash; Secure Multi-Tenant Platform
    </p>
</div>

<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
const APP_URL = '<?= url() ?>';

// Role tab switching
document.querySelectorAll('.role-tab').forEach(tab => {
    tab.addEventListener('click', function() {
        document.querySelectorAll('.role-tab').forEach(t => t.classList.remove('active'));
        this.classList.add('active');
        const role = this.dataset.role;
        const panel = document.getElementById('tenantPanel');
        if (role === 'super_admin') {
            panel.classList.remove('show');
            document.getElementById('hiddenSlug').value = '';
        } else {
            panel.classList.add('show');
        }
    });
});

// Select2 for mess dropdown
$('#messSelect').select2({
    placeholder: '— Choose your mess —',
    allowClear: true,
    theme: 'default'
}).on('change', function() {
    const slug = $(this).val();
    document.getElementById('hiddenSlug').value = slug;
    if (slug) loadTenantBranding(slug);
    else document.getElementById('tenantBranding').style.display = 'none';
});

function loadTenantBranding(slug) {
    fetch(`${APP_URL}/api/tenant/${slug}/info`)
        .then(r => r.json())
        .then(data => {
            if (data.success) {
                document.getElementById('tenantName').textContent = data.tenant.name;
                document.getElementById('tenantBranding').style.display = 'block';
                // Update accent color dynamically
                document.documentElement.style.setProperty('--primary', data.tenant.primary_color || '#6366f1');
            }
        }).catch(() => {});
}

// Password toggle
function togglePass() {
    const input = document.getElementById('passInput');
    const icon  = document.getElementById('eyeIcon');
    if (input.type === 'password') {
        input.type = 'text';
        icon.className = 'bi bi-eye-slash';
    } else {
        input.type = 'password';
        icon.className = 'bi bi-eye';
    }
}

// Loading spinner on submit
document.getElementById('loginForm').addEventListener('submit', function() {
    document.getElementById('btnText').textContent = 'Signing in…';
    document.getElementById('spinner').style.display = 'block';
    document.getElementById('loginBtn').disabled = true;
});
</script>
</body>
</html>
