<?php
/**
 * PSR-4-style manual autoloader.
 * Namespace root: App\ → /app/
 */
class Autoloader
{
    private static array $classMap = [];

    public static function register(): void
    {
        spl_autoload_register([self::class, 'load']);
        // Also load helpers
        self::loadHelpers();
    }

    public static function load(string $class): void
    {
        // Check class map cache first
        if (isset(self::$classMap[$class])) {
            require_once self::$classMap[$class];
            return;
        }

        // Convert namespace to file path
        // App\Controllers\Auth\AuthController → /app/Controllers/Auth/AuthController.php
        $file = APP_PATH . '/' . str_replace(['App\\', '\\'], ['', '/'], $class) . '.php';

        if (file_exists($file)) {
            self::$classMap[$class] = $file;
            require_once $file;
            return;
        }

        // Fallback for core classes without namespace (like Router, DB, App)
        $coreFile = APP_PATH . '/Core/' . $class . '.php';
        if (!str_contains($class, '\\') && file_exists($coreFile)) {
            self::$classMap[$class] = $coreFile;
            require_once $coreFile;
        }
    }

    private static function loadHelpers(): void
    {
        $helpers = glob(APP_PATH . '/Helpers/*.php');
        foreach ($helpers as $helper) {
            require_once $helper;
        }
    }
}
