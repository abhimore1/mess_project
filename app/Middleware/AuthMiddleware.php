<?php
namespace App\Middleware;

class AuthMiddleware
{
    public function handle(): void
    {
        if (empty($_SESSION['user_id'])) {
            // Save intended URL so we can redirect back after login
            if ($_SERVER['REQUEST_METHOD'] === 'GET') {
                $_SESSION['intended_url'] = $_SERVER['REQUEST_URI'];
            }
            $base = rtrim(env('APP_URL', ''), '/');
            header("Location: $base/login");
            exit;
        }

        // Fingerprint check removed to prevent logouts when User-Agent or IP changes (e.g., DevTools mobile view or cellular switching)
    }
}
