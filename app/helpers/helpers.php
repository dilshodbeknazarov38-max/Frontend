<?php
use App\Core\Database;

if (!function_exists('config')) {
    function config(string $key = null, $default = null)
    {
        static $config;
        if (!$config) {
            $config = require BASE_PATH . '/config/config.php';
        }
        if ($key === null) {
            return $config;
        }
        $segments = explode('.', $key);
        $value = $config;
        foreach ($segments as $segment) {
            if (!isset($value[$segment])) {
                return $default;
            }
            $value = $value[$segment];
        }
        return $value;
    }
}

if (!function_exists('url')) {
    function url(string $path = ''): string
    {
        $base = rtrim(config('app_url') ?? '/', '/');
        return $base . '/' . ltrim($path, '/');
    }
}

if (!function_exists('asset')) {
    function asset(string $path): string
    {
        return url('assets/' . ltrim($path, '/'));
    }
}

if (!function_exists('view')) {
    function view(string $template, array $data = []): string
    {
        $viewPath = BASE_PATH . '/app/views/' . $template . '.php';
        if (!file_exists($viewPath)) {
            throw new RuntimeException("View {$template} not found");
        }
        extract($data);
        ob_start();
        include $viewPath;
        return ob_get_clean();
    }
}

if (!function_exists('csrf_token')) {
    function csrf_token(): string
    {
        if (empty($_SESSION['_token'])) {
            $_SESSION['_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['_token'];
    }
}

if (!function_exists('verify_csrf')) {
    function verify_csrf(?string $token): bool
    {
        return isset($_SESSION['_token']) && hash_equals($_SESSION['_token'], (string)$token);
    }
}

if (!function_exists('sanitize')) {
    function sanitize(string $value): string
    {
        return trim(strip_tags($value));
    }
}

if (!function_exists('slugify')) {
    function slugify(string $text): string
    {
        $text = preg_replace('~[^\pL\d]+~u', '-', $text);
        $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
        $text = preg_replace('~[^-\w]+~', '', $text);
        $text = trim($text, '-');
        $text = preg_replace('~-+~', '-', $text);
        return strtolower($text ?: 'product');
    }
}

if (!function_exists('flash')) {
    function flash(string $key, string $message = null)
    {
        if ($message === null) {
            $msg = $_SESSION['flash'][$key] ?? null;
            unset($_SESSION['flash'][$key]);
            return $msg;
        }
        $_SESSION['flash'][$key] = $message;
    }
}

if (!function_exists('old')) {
    function old(string $key, string $default = ''): string
    {
        return $_SESSION['old'][$key] ?? $default;
    }
}

if (!function_exists('set_old')) {
    function set_old(array $inputs): void
    {
        $_SESSION['old'] = $inputs;
    }
}

if (!function_exists('clear_old')) {
    function clear_old(): void
    {
        unset($_SESSION['old']);
    }
}

if (!function_exists('auth_admin')) {
    function auth_admin(): ?array
    {
        if (!isset($_SESSION['admin_id'])) {
            return null;
        }
        static $admin;
        if (!$admin) {
            $stmt = Database::query('SELECT id, name, email FROM admins WHERE id = :id LIMIT 1', [
                'id' => $_SESSION['admin_id'],
            ]);
            $admin = $stmt->fetch() ?: null;
        }
        return $admin;
    }
}
