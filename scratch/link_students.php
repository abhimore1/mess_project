<?php
define('ROOT_PATH', dirname(__DIR__));
define('APP_PATH',  ROOT_PATH . '/app');
require_once ROOT_PATH . '/app/Core/Env.php';
Env::load(ROOT_PATH . '/.env');
require_once ROOT_PATH . '/app/Core/Autoloader.php';
Autoloader::register();

// Link students to users based on email and tenant_id
$users = DB::query("SELECT user_id, email, tenant_id FROM users WHERE tenant_id IS NOT NULL");

echo "Checking " . count($users) . " users...\n";

foreach ($users as $u) {
    echo "Linking user {$u['email']} to student profile...\n";
    DB::execute("UPDATE students SET user_id = ? WHERE email = ? AND tenant_id = ? AND user_id IS NULL", [
        $u['user_id'],
        $u['email'],
        $u['tenant_id']
    ]);
}
echo "Done!\n";
