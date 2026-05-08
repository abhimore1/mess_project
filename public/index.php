<?php
/**
 * Front Controller — All requests enter here.
 * Location: /mess/public/index.php
 */

define('ROOT_PATH', dirname(__DIR__));
define('APP_PATH',  ROOT_PATH . '/app');
define('PUBLIC_PATH', __DIR__);

// Load environment
require_once ROOT_PATH . '/app/Core/Env.php';
Env::load(ROOT_PATH . '/.env');

// Autoloader
require_once ROOT_PATH . '/app/Core/Autoloader.php';
Autoloader::register();

// Bootstrap application
require_once ROOT_PATH . '/app/Core/App.php';
App::run();
