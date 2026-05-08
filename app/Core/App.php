<?php
/**
 * Application bootstrapper — wires everything together.
 */
class App
{
    public static function run(): void
    {
        // Start session
        if (session_status() === PHP_SESSION_NONE) {
            ini_set('session.use_strict_mode', 1);
            ini_set('session.cookie_httponly', 1);
            ini_set('session.cookie_samesite', 'Lax');
            session_name('MESS_SESSION');
            session_start();
        }

        // Set timezone
        date_default_timezone_set('Asia/Kolkata');

        // Security headers
        header('X-Frame-Options: SAMEORIGIN');
        header('X-Content-Type-Options: nosniff');
        header('X-XSS-Protection: 1; mode=block');

        // Handle method override (_method field for PUT/DELETE via forms)
        $method = $_SERVER['REQUEST_METHOD'];
        if ($method === 'POST' && isset($_POST['_method'])) {
            $method = strtoupper($_POST['_method']);
        }

        // Load router
        $router = new Router();
        require_once ROOT_PATH . '/routes/web.php';

        // Dispatch
        $router->dispatch($method, $_SERVER['REQUEST_URI']);
    }
}
