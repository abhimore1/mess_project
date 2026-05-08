<?php
namespace App\Middleware;

class SuperAdminMiddleware
{
    public function handle(): void
    {
        (new AuthMiddleware())->handle();
        if (($_SESSION['role_slug'] ?? '') !== 'super_admin') {
            http_response_code(403);
            die('Access denied: Super Admin only.');
        }
    }
}
