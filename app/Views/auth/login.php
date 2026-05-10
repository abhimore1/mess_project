<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login — Mess India</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Google+Sans:wght@400;500;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" rel="stylesheet">
    <style>
        :root {
            --primary: #1a73e8; /* Google Blue */
            --primary-hover: #1558d6;
            --text-main: #202124;
            --text-muted: #5f6368;
            --border: #dadce0;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background-image: url('<?= url('assets/images/mess_india_bg.png') ?>');
            background-size: cover;
            background-position: center;
            position: relative;
        }
        body::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(8px); /* Premium blur effect */
            z-index: 1;
        }
        .login-card {
            background: #ffffff;
            width: 100%;
            max-width: 448px;
            padding: 48px 40px 36px;
            border-radius: 28px; /* Google's modern radius */
            box-shadow: 0 12px 32px rgba(0, 0, 0, 0.15), 0 2px 6px rgba(0, 0, 0, 0.08);
            position: relative;
            z-index: 2;
            text-align: center;
            margin: 20px;
        }
        .brand-icon {
            width: 64px;
            height: 64px;
            color: var(--primary);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
            margin: 0 auto 16px;
        }
        .login-card h1 {
            font-family: 'Google Sans', 'Inter', sans-serif;
            font-size: 2rem;
            font-weight: 500;
            color: var(--text-main);
            margin-bottom: 8px;
            letter-spacing: -0.5px;
        }
        .login-card p.subtitle {
            font-family: 'Google Sans', 'Inter', sans-serif;
            font-size: 1.1rem;
            color: var(--text-main);
            margin-bottom: 40px;
            font-weight: 400;
        }
        
        .form-floating {
            position: relative;
            margin-bottom: 24px;
            text-align: left;
        }
        .form-floating .form-control {
            width: 100%;
            padding: 24px 16px 8px;
            height: 56px;
            font-size: 1rem;
            border: 1px solid var(--border);
            border-radius: 8px; /* Google uses slightly rounded inputs */
            background: #fff;
            color: var(--text-main);
            transition: all 0.2s ease;
            font-family: inherit;
        }
        .form-floating .form-control:focus {
            outline: none;
            border-color: var(--primary);
            border-width: 2px;
            padding-left: 15px; /* Adjust for 2px border */
            padding-right: 15px;
        }
        .form-floating label {
            position: absolute;
            top: 0;
            left: 0;
            height: 100%;
            padding: 16px;
            pointer-events: none;
            border: 1px solid transparent;
            transform-origin: 0 0;
            transition: opacity .1s ease-in-out,transform .1s ease-in-out;
            color: var(--text-muted);
            font-size: 1rem;
        }
        .form-floating .form-control:focus ~ label,
        .form-floating .form-control:not(:placeholder-shown) ~ label {
            transform: scale(.85) translateY(-0.5rem) translateX(0.15rem);
            color: var(--primary);
        }
        .form-floating .form-control:not(:focus):not(:placeholder-shown) ~ label {
            color: var(--text-muted);
        }
        
        .pass-toggle {
            position: absolute;
            right: 14px;
            top: 14px;
            color: var(--text-muted);
            cursor: pointer;
            padding: 4px;
            background: transparent;
            font-size: 1.1rem;
        }
        .pass-toggle:hover {
            color: var(--text-main);
        }
        
        .form-actions {
            display: flex;
            justify-content: flex-end;
            margin-top: 40px;
            margin-bottom: 16px;
        }
        
        .btn-submit {
            padding: 12px 28px;
            background: var(--primary);
            color: #fff;
            border: none;
            border-radius: 100px; /* Pill shape like Google */
            font-size: 0.95rem;
            font-weight: 500;
            cursor: pointer;
            transition: background 0.2s, box-shadow 0.2s;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            letter-spacing: 0.25px;
            font-family: 'Google Sans', 'Inter', sans-serif;
        }
        .btn-submit:hover {
            background: var(--primary-hover);
            box-shadow: 0 1px 3px rgba(60,64,67,0.3), 0 4px 8px rgba(60,64,67,0.15);
        }
        .btn-submit:active {
            background: #174ea6;
        }
        
        .alert-flash {
            background: #fce8e6;
            border: 1px solid #c5221f;
            color: #c5221f;
            padding: 12px 16px;
            border-radius: 8px;
            font-size: 0.9rem;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 8px;
            text-align: left;
        }
        
        .spinner {
            display: none;
            width: 18px; height: 18px;
            border: 2px solid rgba(255,255,255,0.3);
            border-top-color: #fff;
            border-radius: 50%;
            animation: spin 0.8s linear infinite;
        }
        @keyframes spin { to { transform: rotate(360deg); } }
        
        .footer-text {
            margin-top: 24px;
            font-size: 0.85rem;
            color: var(--text-muted);
            text-align: left;
            display: flex;
            justify-content: space-between;
        }
        .footer-text span {
            color: var(--text-muted);
        }
    </style>
</head>
<body>

<div class="login-card">
    <div class="brand-icon">
        <svg xmlns="http://www.w3.org/2000/svg" width="48" height="48" fill="currentColor" class="bi bi-person-circle" viewBox="0 0 16 16">
            <path d="M11 6a3 3 0 1 1-6 0 3 3 0 0 1 6 0"/>
            <path fill-rule="evenodd" d="M0 8a8 8 0 1 1 16 0A8 8 0 0 1 0 8m8-7a7 7 0 0 0-5.468 11.37C3.242 11.226 4.805 10 8 10s4.757 1.225 5.468 2.37A7 7 0 0 0 8 1"/>
        </svg>
    </div>
    
    <h1>Sign in</h1>
    <p class="subtitle">to continue to Mess India</p>

    <?php if ($error = flash('error')): ?>
    <div class="alert-flash">
        <i class="bi bi-exclamation-circle-fill"></i>
        <span><?= e($error) ?></span>
    </div>
    <?php endif; ?>

    <form method="POST" action="<?= url('login') ?>" id="loginForm">
        <?= csrf_field() ?>

        <div class="form-floating">
            <input type="email" name="email" id="emailInput" class="form-control"
                   value="<?= e(old('email')) ?>"
                   placeholder=" "
                   autocomplete="email" required>
            <label for="emailInput">Email</label>
        </div>

        <div class="form-floating">
            <input type="password" name="password" id="passInput" class="form-control"
                   placeholder=" "
                   autocomplete="current-password" required>
            <label for="passInput">Password</label>
            <span class="pass-toggle" onclick="togglePass()">
                <i class="bi bi-eye" id="eyeIcon"></i>
            </span>
        </div>

        <div class="form-actions">
            <button type="submit" class="btn-submit" id="loginBtn">
                <span id="btnText">Next</span>
                <div class="spinner" id="spinner"></div>
            </button>
        </div>
    </form>
    
    <div class="footer-text">
        <span>Mess India</span>
        <span>&copy; <?= date('Y') ?></span>
    </div>
</div>

<script>
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

document.getElementById('loginForm').addEventListener('submit', function() {
    document.getElementById('btnText').textContent = 'Please wait...';
    document.getElementById('spinner').style.display = 'block';
    document.getElementById('loginBtn').disabled = true;
});
</script>
</body>
</html>
