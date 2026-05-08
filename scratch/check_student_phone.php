<?php
define('ROOT_PATH', dirname(__DIR__));
define('APP_PATH',  ROOT_PATH . '/app');
require_once ROOT_PATH . '/app/Core/Env.php';
Env::load(ROOT_PATH . '/.env');
require_once ROOT_PATH . '/app/Core/Autoloader.php';
Autoloader::register();
$u = DB::queryOne("SELECT * FROM users WHERE email='more@gmail.com'");
print_r($u);
