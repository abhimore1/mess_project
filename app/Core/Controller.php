<?php
namespace App\Core;

/**
 * Base Controller — shared helpers for all controllers.
 */
abstract class Controller
{
    protected function view(string $template, array $data = [], string $layout = 'app'): void
    {
        extract($data);
        $layoutFile = APP_PATH . "/Views/layouts/{$layout}.php";
        $viewFile   = APP_PATH . "/Views/{$template}.php";

        if (!file_exists($viewFile)) {
            throw new \RuntimeException("View not found: $viewFile");
        }

        // Capture view content
        ob_start();
        include $viewFile;
        $content = ob_get_clean();

        // Render inside layout
        if ($layout && file_exists($layoutFile)) {
            include $layoutFile;
        } else {
            echo $content;
        }
    }

    protected function json(mixed $data, int $code = 200): never
    {
        http_response_code($code);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
        exit;
    }

    protected function redirect(string $path): never
    {
        $base = rtrim(env('APP_URL', ''), '/');
        header("Location: $base/$path");
        exit;
    }

    protected function back(): never
    {
        $ref = $_SERVER['HTTP_REFERER'] ?? env('APP_URL', '/');
        header("Location: $ref");
        exit;
    }

    protected function input(string $key, mixed $default = null): mixed
    {
        return $_POST[$key] ?? $_GET[$key] ?? $default;
    }

    protected function validate(array $rules): array
    {
        $errors = [];
        foreach ($rules as $field => $rule) {
            $value = $this->input($field);
            foreach (explode('|', $rule) as $r) {
                if ($r === 'required' && empty($value)) {
                    $errors[$field] = ucfirst(str_replace('_', ' ', $field)) . ' is required.';
                } elseif (str_starts_with($r, 'min:')) {
                    $min = (int)substr($r, 4);
                    if (strlen((string)$value) < $min) {
                        $errors[$field] = ucfirst($field) . " must be at least $min characters.";
                    }
                } elseif ($r === 'email' && !filter_var($value, FILTER_VALIDATE_EMAIL)) {
                    $errors[$field] = 'Invalid email address.';
                } elseif ($r === 'numeric' && !is_numeric($value)) {
                    $errors[$field] = ucfirst($field) . ' must be numeric.';
                }
            }
        }
        return $errors;
    }

    protected function isAjax(): bool
    {
        return ($_SERVER['HTTP_X_REQUESTED_WITH'] ?? '') === 'XMLHttpRequest';
    }

    protected function csrfToken(): string
    {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    protected function verifyCsrf(): void
    {
        $token = $_POST['_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';
        if (!hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
            http_response_code(419);
            if ($this->isAjax()) {
                $this->json(['error' => 'CSRF token mismatch'], 419);
            }
            die('CSRF token mismatch. Please refresh and try again.');
        }
    }

    protected function abort(int $code, string $message = ''): never
    {
        http_response_code($code);
        echo $message ?: "Error $code";
        exit;
    }

    protected function can(string $permission): bool
    {
        $perms = $_SESSION['permissions'] ?? [];
        return in_array($permission, $perms) || in_array('*', $perms);
    }

    protected function requirePermission(string $permission): void
    {
        if (!$this->can($permission)) {
            $this->abort(403, 'Access denied.');
        }
    }

    protected function requireModule(string $slug): void
    {
        if (!module_enabled($slug)) {
            $this->abort(403, 'Module disabled or not subscribed.');
        }
    }
}
