<?php
use App\Core\Database;

$rootPath = dirname(__DIR__);

$config = require $rootPath . '/config/config.php';

if (!defined('BASE_PATH')) {
    define('BASE_PATH', $rootPath);
}
if (!defined('PUBLIC_PATH')) {
    define('PUBLIC_PATH', BASE_PATH . '/public');
}
if (!defined('STORAGE_PATH')) {
    define('STORAGE_PATH', BASE_PATH . '/storage');
}

$sessionName = $config['security']['session_name'] ?? 'cpa_session';
if (session_status() === PHP_SESSION_NONE) {
    session_name($sessionName);
    session_start([
        'cookie_httponly' => true,
        'cookie_secure' => isset($_SERVER['HTTPS']),
        'cookie_samesite' => 'Lax',
    ]);
}

require_once BASE_PATH . '/app/helpers/helpers.php';
require_once BASE_PATH . '/app/helpers/security.php';

spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $baseDir = BASE_PATH . '/app/';
    if (strncmp($prefix, $class, strlen($prefix)) !== 0) {
        return;
    }
    $relativeClass = substr($class, strlen($prefix));
    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';
    if (file_exists($file)) {
        require_once $file;
    }
});

Database::init($config['db']);

setlocale(LC_ALL, 'uz_UZ.UTF-8');
mb_internal_encoding('UTF-8');
date_default_timezone_set('Asia/Tashkent');
