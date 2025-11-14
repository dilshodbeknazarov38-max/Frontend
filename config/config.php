<?php
$envPath = __DIR__ . '/env.php';
if (!file_exists($envPath)) {
    throw new RuntimeException('Env file missing. Copy config/env.example.php to config/env.php');
}
return require $envPath;
