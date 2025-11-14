<?php

if (!function_exists('getallheaders')) {
    function getallheaders(): array
    {
        $headers = [];
        foreach ($_SERVER as $name => $value) {
            if (str_starts_with($name, 'HTTP_')) {
                $header = str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))));
                $headers[$header] = $value;
            }
        }
        return $headers;
    }
}

if (!function_exists('sanitize')) {
    function sanitize(string $value): string
    {
        return trim(strip_tags($value));
    }
}

if (!function_exists('sanitize_array')) {
    function sanitize_array(array $data): array
    {
        return array_map(fn($value) => is_string($value) ? sanitize($value) : $value, $data);
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

if (!function_exists('rotate_csrf_token')) {
    function rotate_csrf_token(): void
    {
        $_SESSION['_token'] = bin2hex(random_bytes(32));
    }
}

if (!function_exists('verify_csrf')) {
    function verify_csrf(?string $token): bool
    {
        if (empty($_SESSION['_token'])) {
            return false;
        }
        return hash_equals($_SESSION['_token'], (string)$token);
    }
}

if (!function_exists('get_client_ip')) {
    function get_client_ip(): string
    {
        $keys = ['HTTP_CF_CONNECTING_IP', 'HTTP_X_FORWARDED_FOR', 'REMOTE_ADDR'];
        foreach ($keys as $key) {
            if (!empty($_SERVER[$key])) {
                $value = $_SERVER[$key];
                if ($key === 'HTTP_X_FORWARDED_FOR') {
                    $parts = explode(',', $value);
                    $value = trim($parts[0]);
                }
                return $value;
            }
        }
        return '127.0.0.1';
    }
}

if (!function_exists('rate_limit_allow')) {
    function rate_limit_allow(string $key, int $maxAttempts, int $decaySeconds): bool
    {
        $path = STORAGE_PATH . '/cache/ratelimit.json';
        $now = time();
        $data = file_exists($path) ? json_decode((string)file_get_contents($path), true) : [];
        $bucket = $data[$key] ?? ['count' => 0, 'expires' => $now + $decaySeconds];
        if ($bucket['expires'] <= $now) {
            $bucket = ['count' => 0, 'expires' => $now + $decaySeconds];
        }
        $bucket['count']++;
        $data[$key] = $bucket;
        file_put_contents($path, json_encode($data), LOCK_EX);
        return $bucket['count'] <= $maxAttempts;
    }
}

if (!function_exists('get_api_key')) {
    function get_api_key(): string
    {
        return (string)(config('api.key') ?? '');
    }
}

if (!function_exists('extract_api_token')) {
    function extract_api_token(): ?string
    {
        $headers = array_change_key_case(getallheaders() ?: [], CASE_LOWER);
        if (!empty($headers['authorization']) && str_starts_with($headers['authorization'], 'Bearer ')) {
            return trim(substr($headers['authorization'], 7));
        }
        if (!empty($headers['x-api-key'])) {
            return trim($headers['x-api-key']);
        }
        if (!empty($_GET['api_key'])) {
            return sanitize((string)$_GET['api_key']);
        }
        if (!empty($_POST['api_key'])) {
            return sanitize((string)$_POST['api_key']);
        }
        return null;
    }
}

if (!function_exists('validate_api_token')) {
    function validate_api_token(?string $token): bool
    {
        $stored = get_api_key();
        return $stored !== '' && $token !== null && hash_equals($stored, $token);
    }
}

if (!function_exists('login_attempt_key')) {
    function login_attempt_key(string $identifier): string
    {
        return sha1(strtolower($identifier) . '|' . get_client_ip());
    }
}

if (!function_exists('login_attempt_store')) {
    function login_attempt_store(): string
    {
        return STORAGE_PATH . '/cache/login_attempts.json';
    }
}

if (!function_exists('login_is_locked')) {
    function login_is_locked(string $key): int
    {
        $path = login_attempt_store();
        if (!file_exists($path)) {
            return 0;
        }
        $data = json_decode((string)file_get_contents($path), true) ?: [];
        if (empty($data[$key]['locked_until'])) {
            return 0;
        }
        $remaining = $data[$key]['locked_until'] - time();
        return $remaining > 0 ? $remaining : 0;
    }
}

if (!function_exists('record_login_attempt')) {
    function record_login_attempt(string $key, bool $success, int $maxAttempts = 5, int $lockSeconds = 900): void
    {
        $path = login_attempt_store();
        $data = file_exists($path) ? json_decode((string)file_get_contents($path), true) : [];
        $entry = $data[$key] ?? ['attempts' => 0, 'locked_until' => 0];
        if ($success) {
            unset($data[$key]);
        } else {
            if ($entry['locked_until'] > time()) {
                // still locked, keep state
            } else {
                $entry['attempts']++;
                if ($entry['attempts'] >= $maxAttempts) {
                    $entry['locked_until'] = time() + $lockSeconds;
                    $entry['attempts'] = 0;
                }
                $data[$key] = $entry;
            }
            $data[$key] = $entry;
        }
        file_put_contents($path, json_encode($data), LOCK_EX);
    }
}
