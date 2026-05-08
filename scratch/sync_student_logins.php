<?php
define('ROOT_PATH', dirname(__DIR__));
define('APP_PATH',  ROOT_PATH . '/app');
require_once ROOT_PATH . '/app/Core/Env.php';
Env::load(ROOT_PATH . '/.env');
require_once ROOT_PATH . '/app/Core/Autoloader.php';
Autoloader::register();

$students = DB::query("SELECT s.* FROM students s LEFT JOIN users u ON u.email = s.email AND u.tenant_id = s.tenant_id WHERE u.user_id IS NULL AND s.email IS NOT NULL AND s.email != ''");

$roleId = DB::queryOne("SELECT role_id FROM roles WHERE slug='student' LIMIT 1")['role_id'];

echo "Found " . count($students) . " students without logins.\n";

foreach ($students as $s) {
    // Double check existence to avoid race conditions or DB state issues
    $exists = DB::queryOne("SELECT user_id FROM users WHERE email = ? AND tenant_id = ?", [$s['email'], $s['tenant_id']]);
    if ($exists) continue;

    echo "Creating login for {$s['full_name']} ({$s['email']})...\n";
    DB::insert('users', [
        'tenant_id'     => $s['tenant_id'],
        'role_id'       => $roleId,
        'email'         => $s['email'],
        'password_hash' => password_hash($s['phone'], PASSWORD_BCRYPT, ['cost' => 12]),
        'full_name'     => $s['full_name'],
        'status'        => 'active',
        'created_at'    => date('Y-m-d H:i:s'),
        'updated_at'    => date('Y-m-d H:i:s'),
    ]);
}
echo "Done!\n";
