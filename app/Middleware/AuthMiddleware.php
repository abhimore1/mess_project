<?php
namespace App\Middleware;

class AuthMiddleware
{
    public function handle(): void
    {
        if (empty($_SESSION['user_id'])) {
            $base = rtrim(env('APP_URL', ''), '/');
            header("Location: $base/login");
            exit;
        }

        // Fingerprint check removed to prevent logouts when User-Agent or IP changes (e.g., DevTools mobile view or cellular switching)
    }
}
