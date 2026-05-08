<?php
/**
 * Application route definitions.
 * $router is injected by App::run() via require.
 *
 * @var Router $router
 */

// ── Public ──────────────────────────────────────────────────
$router->get('/',       'Auth\AuthController@showLogin');
$router->get('/login',  'Auth\AuthController@showLogin');
$router->post('/login', 'Auth\AuthController@login');
$router->get('/logout', 'Auth\AuthController@logout');

// ── Super Admin ──────────────────────────────────────────────
$router->group(['prefix' => '/super', 'middleware' => ['SuperAdmin']], function($r) {
    $r->get('/dashboard',              'SuperAdmin\DashboardController@index');
    $r->get('/tenants',                'SuperAdmin\TenantController@index');
    $r->get('/tenants/create',         'SuperAdmin\TenantController@create');
    $r->post('/tenants/store',         'SuperAdmin\TenantController@store');
    $r->get('/tenants/{id}/edit',      'SuperAdmin\TenantController@edit');
    $r->post('/tenants/{id}/update',   'SuperAdmin\TenantController@update');
    $r->post('/tenants/{id}/toggle',   'SuperAdmin\TenantController@toggleStatus');
    $r->get('/tenants/{id}/modules',   'SuperAdmin\TenantController@modules');
    $r->post('/tenants/{id}/modules',  'SuperAdmin\TenantController@saveModules');
    $r->get('/plans',                  'SuperAdmin\PlanController@index');
    $r->post('/plans/store',           'SuperAdmin\PlanController@store');
    $r->get('/plans/{id}/edit',        'SuperAdmin\PlanController@edit');
    $r->post('/plans/{id}/update',     'SuperAdmin\PlanController@update');
    $r->post('/plans/{id}/delete',     'SuperAdmin\PlanController@delete');
    $r->get('/audit-logs',             'SuperAdmin\AuditController@index');
    $r->get('/analytics',              'SuperAdmin\AnalyticsController@index');
    $r->get('/coordinators',           'SuperAdmin\CoordinatorController@index');
    $r->post('/coordinators/store',    'SuperAdmin\CoordinatorController@store');
    $r->get('/coordinators/{id}/edit', 'SuperAdmin\CoordinatorController@edit');
    $r->post('/coordinators/{id}/update', 'SuperAdmin\CoordinatorController@update');
    $r->post('/coordinators/{id}/delete', 'SuperAdmin\CoordinatorController@delete');
});

// ── Mess Admin ───────────────────────────────────────────────
$router->group(['prefix' => '/admin', 'middleware' => ['Auth', 'Tenant']], function($r) {
    $r->get('/dashboard',                      'Admin\DashboardController@index');
    $r->get('/profile',                        'Admin\ProfileController@index');

    // Students
    $r->get('/students',                       'Admin\StudentController@index');
    $r->get('/students/create',                'Admin\StudentController@create');
    $r->post('/students/store',                'Admin\StudentController@store');
    $r->get('/students/import/template',       'Admin\StudentController@downloadTemplate');
    $r->post('/students/import',               'Admin\StudentController@importExcel');
    $r->get('/students/export/pdf',            'Admin\StudentController@exportPdf');
    $r->get('/students/export/excel',          'Admin\StudentController@exportExcel');
    $r->get('/students/suggestions',           'Admin\StudentController@searchSuggestions');
    
    // Dynamic student routes (must be below static routes)
    $r->get('/students/{id}',                  'Admin\StudentController@show');
    $r->get('/students/{id}/edit',             'Admin\StudentController@edit');
    $r->post('/students/{id}/update',          'Admin\StudentController@update');
    $r->post('/students/{id}/delete',          'Admin\StudentController@delete');

    // Memberships
    $r->get('/memberships',                    'Admin\MembershipController@index');
    $r->get('/memberships/plans',              'Admin\MembershipController@plans');
    $r->post('/memberships/plans/store',       'Admin\MembershipController@storePlan');
    $r->post('/memberships/assign',            'Admin\MembershipController@assign');
    $r->post('/memberships/{id}/renew',        'Admin\MembershipController@renew');

    // Payments
    $r->get('/payments',                       'Admin\PaymentController@index');
    $r->get('/payments/create',                'Admin\PaymentController@create');
    $r->post('/payments/store',                'Admin\PaymentController@store');
    $r->get('/payments/{id}/receipt',          'Admin\PaymentController@receipt');
    $r->get('/payments/{id}/pdf',              'Admin\PaymentController@downloadPdf');

    // Attendance
    $r->get('/attendance',                     'Admin\AttendanceController@index');
    $r->post('/attendance/mark',               'Admin\AttendanceController@mark');
    $r->get('/attendance/report',              'Admin\AttendanceController@report');

    // Meal Slots
    $r->get('/meal-slots',                     'Admin\MealSlotController@index');
    $r->post('/meal-slots/store',              'Admin\MealSlotController@store');
    $r->post('/meal-slots/{id}/update',        'Admin\MealSlotController@update');
    $r->post('/meal-slots/{id}/delete',        'Admin\MealSlotController@delete');

    // Food Menu
    $r->get('/food-menu',                      'Admin\FoodMenuController@index');
    $r->post('/food-menu/store',               'Admin\FoodMenuController@store');

    // Complaints
    $r->get('/complaints',                     'Admin\ComplaintController@index');
    $r->post('/complaints/{id}/update-status', 'Admin\ComplaintController@updateStatus');

    // Notifications
    $r->get('/notifications',                  'Admin\NotificationController@index');
    $r->post('/notifications/send',            'Admin\NotificationController@send');

    // Reports
    $r->get('/reports',                        'Admin\ReportController@index');
    $r->get('/reports/export',                 'Admin\ReportController@export');

    // Settings
    $r->get('/settings',                       'Admin\SettingsController@index');
    $r->post('/settings/save',                 'Admin\SettingsController@save');

    // Academic Years
    $r->get('/years',                          'Admin\YearController@index');
    $r->post('/years/store',                   'Admin\YearController@store');
    $r->post('/years/{id}/toggle',             'Admin\YearController@toggleStatus');
    $r->post('/years/{id}/delete',             'Admin\YearController@delete');
});

// ── Student Portal ───────────────────────────────────────────
$router->group(['prefix' => '/student', 'middleware' => ['Auth', 'Tenant']], function($r) {
    $r->get('/dashboard',                'Student\PortalController@dashboard');
    $r->get('/profile',                  'Student\PortalController@profile');
    $r->get('/payments',                 'Student\PortalController@payments');
    $r->get('/payments/{id}/receipt',    'Student\PortalController@receipt');
    $r->get('/attendance',               'Student\PortalController@attendance');
    $r->post('/attendance/self-mark',    'Student\PortalController@selfMark');
    $r->get('/food-menu',                'Student\PortalController@foodMenu');
    $r->get('/complaints',               'Student\PortalController@complaints');
    $r->post('/complaints/submit',       'Student\PortalController@submitComplaint');
    $r->get('/notifications',            'Student\PortalController@notifications');
    $r->get('/membership',               'Student\PortalController@membership');
});

// ── Coordinator ──────────────────────────────────────────────
$router->group(['prefix' => '/coordinator', 'middleware' => ['Auth']], function($r) {
    $r->get('/dashboard',   'Coordinator\DashboardController@index');
    $r->get('/students',    'Coordinator\DashboardController@students');
    $r->get('/reports',     'Coordinator\DashboardController@reports');
    $r->get('/complaints',  'Coordinator\DashboardController@complaints');
});

// ── AJAX API endpoints (JSON) ────────────────────────────────
$router->group(['prefix' => '/api'], function($r) {
    $r->get('/tenant/{slug}/info',          'Auth\AuthController@tenantInfo');
    $r->post('/admin/students/ajax-list',   'Admin\StudentController@ajaxList');
    $r->post('/admin/attendance/bulk-mark', 'Admin\AttendanceController@bulkMark');
    $r->get('/notifications/unread-count',  'Admin\NotificationController@unreadCount');
});
