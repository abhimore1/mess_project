<?php
namespace App\Middleware;

class AdminAccessMiddleware
{
    public function handle(): void
    {
        $role = $_SESSION['role_slug'] ?? '';

        // Students have no access to /admin
        if ($role === 'student') {
            header("Location: " . env('APP_URL') . "/student/dashboard");
            exit;
        }

        // Super Admin and Mess Admin have full access to /admin
        if (in_array($role, ['super_admin', 'mess_admin'])) {
            return;
        }

        // Coordinator has dynamic access based on custom_permissions
        if ($role === 'coordinator') {
            $basePath = parse_url(env('APP_URL'), PHP_URL_PATH) ?: '';
            $uri = str_replace($basePath, '', $_SERVER['REQUEST_URI']);
            $uri = trim(parse_url($uri, PHP_URL_PATH), '/');
            
            // Allow basic dashboard access for coordinator
            if ($uri === 'admin/dashboard' || $uri === 'admin/profile') {
                return;
            }

            // Map URI segments to permissions
            $segmentMap = [
                'students'      => 'students.view',
                'payments'      => 'payments.view',
                'attendance'    => 'attendance.view',
                'meal-slots'    => 'settings.manage', // usually tied to settings or food menu
                'food-menu'     => 'food_menu.view',
                'complaints'    => 'complaints.view',
                'reports'       => 'reports.view',
                'settings'      => 'settings.manage',
                'years'         => 'settings.manage',
                'notifications' => 'reports.view', // tied or open
                'memberships'   => 'membership.view',
                'coordinators'  => 'superadmin.tenants', // Only mess admin should manage coordinators
            ];

            $segments = explode('/', $uri);
            $baseSegment = '';
            if (isset($segments[0]) && $segments[0] === 'admin' && isset($segments[1])) {
                $baseSegment = $segments[1];
            }

            if ($baseSegment === 'coordinators') {
                // Coordinators cannot manage other coordinators
                header("Location: " . env('APP_URL') . "/admin/dashboard?error=unauthorized");
                exit;
            }

            if (isset($segmentMap[$baseSegment])) {
                $requiredPerm = $segmentMap[$baseSegment];
                if (!can($requiredPerm)) {
                    header("Location: " . env('APP_URL') . "/admin/dashboard?error=unauthorized");
                    exit;
                }
            }

            return;
        }

        // Default deny
        header("Location: " . env('APP_URL') . "/login");
        exit;
    }
}
