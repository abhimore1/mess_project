<?php
/**
 * Environment loader — parses .env file into $_ENV and getenv()
 */
class Env
{
    public static function load(string $path): void
    {
        if (!file_exists($path)) return;

        $lines = file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            if (str_starts_with(trim($line), '#')) continue;
            if (!str_contains($line, '=')) continue;

            [$key, $value] = explode('=', $line, 2);
            $key   = trim($key);
            $value = trim($value);

            // Strip surrounding quotes
            if (preg_match('/^"(.*)"$/', $value, $m)) $value = $m[1];
            if (preg_match("/^'(.*)'$/", $value, $m)) $value = $m[1];

            if (!array_key_exists($key, $_ENV)) {
                $_ENV[$key] = $value;
                putenv("$key=$value");
            }
        }
    }
}

function env(string $key, mixed $default = null): mixed
{
    return $_ENV[$key] ?? getenv($key) ?: $default;
}
