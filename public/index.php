<?php
require __DIR__ . '/../app/bootstrap.php';

$router = require BASE_PATH . '/routes/web.php';

$router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
