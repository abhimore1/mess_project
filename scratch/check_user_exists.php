<?php
define('ROOT_PATH', dirname(__DIR__));
define('APP_PATH',  ROOT_PATH . '/app');

require_once ROOT_PATH . '/app/Core/Env.php';
Env::load(ROOT_PATH . '/.env');

require_once ROOT_PATH . '/app/Core/Autoloader.php';
Autoloader::register();

// use App\Core\DB;

$rows = DB::query("SELECT u.email, u.tenant_id, r.slug as role FROM users u JOIN roles r ON r.role_id = u.role_id");
foreach ($rows as $r) {
    echo "Email: {$r['email']} | Tenant: " . ($r['tenant_id'] ?? 'NULL') . " | Role: {$r['role']}\n";
}
