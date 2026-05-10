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
            --primary: #E65100; /* Rich Indian Orange */
            --primary-hover: #BF360C;
            --text-main: #202124;
            --text-muted: #5f6368;
            --border: #dadce0;
        }
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Inter', sans-serif;
            min-height: 100vh;
            display: grid;
            place-items: center;
            background-image: url('<?= url('assets/images/mess_india_bg.png') ?>');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            position: relative;
            padding: 20px;
        }
        body::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(0, 0, 0, 0.45);
            backdrop-filter: blur(8px);
            z-index: 1;
        }
        .login-card {
            background: #ffffff;
            width: 100%;
            max-width: 400px;
            padding: 48px 40px;
            border-radius: 28px;
            box-shadow: 0 24px 64px rgba(0, 0, 0, 0.5);
            position: relative;
            z-index: 2;
            text-align: center;
        }
        .brand-logo {
            font-family: 'Google Sans', sans-serif;
            font-weight: 700;
            font-size: 1.25rem;
            color: var(--primary);
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        .brand-logo i {
            font-size: 1.75rem;
        }
        .login-card h1 {
            font-family: 'Google Sans', sans-serif;
            font-size: 2rem;
            font-weight: 500;
            color: var(--text-main);
            margin-bottom: 8px;
        }
        .login-card p.subtitle {
            font-size: 0.95rem;
            color: var(--text-muted);
            margin-bottom: 32px;
        }
        
        .form-floating {
            position: relative;
            margin-bottom: 16px;
            text-align: left;
        }
        .form-floating .form-control {
            width: 100%;
            padding: 20px 16px 6px;
            height: 56px;
            font-size: 1rem;
            border: 1px solid var(--border);
            border-radius: 8px;
            background: #fff;
            color: var(--text-main);
            transition: all 0.2s ease;
        }
        .form-floating .form-control:focus {
            outline: none;
            border-color: var(--primary);
            border-width: 2px;
            padding-left: 15px;
            padding-right: 15px;
        }
        .form-floating label {
            position: absolute;
            top: 0; left: 0;
            padding: 16px;
            color: var(--text-muted);
            transition: all 0.2s ease;
            pointer-events: none;
        }
        .form-floating .form-control:focus ~ label,
        .form-floating .form-control:not(:placeholder-shown) ~ label {
            transform: scale(0.85) translateY(-0.8rem) translateX(0.1rem);
            color: var(--primary);
        }
        
        .pass-toggle {
            position: absolute;
            right: 14px;
            top: 14px;
            color: var(--text-muted);
            cursor: pointer;
            padding: 4px;
        }
        
        .btn-submit {
            width: 100%;
            padding: 14px;
            background: var(--primary);
            color: #fff;
            border: none;
            border-radius: 100px;
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
            margin-top: 24px;
        }
        .btn-submit:hover {
            background: var(--primary-hover);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2);
        }
        
        .alert-flash {
            background: #fce8e6;
            border: 1px solid #c5221f;
            color: #c5221f;
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 0.9rem;
            text-align: left;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .footer-links {
            margin-top: 32px;
            font-size: 0.8rem;
            color: var(--text-muted);
            display: flex;
            justify-content: space-between;
        }
    </style>
</head>
<body>

<div class="login-card">
    <div class="brand-logo">
        <i class="bi bi-restaurant"></i>
        <span>Mess India</span>
    </div>
    
    <h1>Sign in</h1>
    <p class="subtitle">Welcome back! Please enter your details.</p>

    <?php if ($error = flash('error')): ?>
    <div class="alert-flash">
        <i class="bi bi-exclamation-triangle-fill"></i>
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
            <label for="emailInput">Email Address</label>
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

        <button type="submit" class="btn-submit" id="loginBtn">
            <span id="btnText">Sign In</span>
            <div class="spinner-border spinner-border-sm ms-2" id="spinner" style="display:none"></div>
        </button>
    </form>
    
    <div class="footer-links">
        <span>Mess India Dashboard</span>
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
