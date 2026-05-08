<?php
namespace App\Controllers\Auth;

use DB;

use App\Core\Controller;
use App\Services\AuthService;

class AuthController extends Controller
{
    public function showLogin(): void
    {
        if (AuthService::check()) {
            $this->redirectByRole(AuthService::role());
        }
        // Load tenants for the mess-selector dropdown
        $tenants = \DB::query("SELECT slug, name FROM tenants WHERE status='active' ORDER BY name ASC");
        $this->view('auth/login', ['tenants' => $tenants, 'csrf' => $this->csrfToken()], 'auth');
    }

    public function login(): void
    {
        $this->verifyCsrf();

        $email    = trim($this->input('email', ''));
        $password = $this->input('password', '');
        $slug     = trim($this->input('mess_slug', '')) ?: null;

        $errors = $this->validate([
            'email'    => 'required|email',
            'password' => 'required|min:6',
        ]);

        if ($errors) {
            $_SESSION['_old']   = ['email' => $email, 'mess_slug' => $slug];
            $_SESSION['_flash']['error'] = array_values($errors)[0];
            $this->back();
        }

        $result = AuthService::attempt($email, $password, $slug);

        if (!$result['success']) {
            $_SESSION['_old']   = ['email' => $email, 'mess_slug' => $slug];
            $_SESSION['_flash']['error'] = $result['message'];
            $this->back();
        }

        $this->redirectByRole($result['role']);
    }

    public function logout(): void
    {
        AuthService::logout();
        $this->redirect('login');
    }

    public function tenantInfo(string $slug): void
    {
        $tenant = \DB::queryOne(
            "SELECT name, logo, primary_color, secondary_color FROM tenants WHERE slug=? AND status='active' LIMIT 1",
            [$slug]
        );
        $this->json($tenant ? ['success' => true, 'tenant' => $tenant] : ['success' => false]);
    }

    private function redirectByRole(string $role): never
    {
        match ($role) {
            'super_admin'  => $this->redirect('super/dashboard'),
            'mess_admin'   => $this->redirect('admin/dashboard'),
            'student'      => $this->redirect('student/dashboard'),
            'coordinator'  => $this->redirect('coordinator/dashboard'),
            default        => $this->redirect('login'),
        };
    }
}
