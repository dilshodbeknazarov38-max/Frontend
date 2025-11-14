<?php
require __DIR__ . '/../app/bootstrap.php';

use App\Core\Database;
use App\Models\Admin;

$message = '';
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verify_csrf($_POST['_token'] ?? '')) {
        $message = 'CSRF token noto\'g\'ri.';
    } else {
        $schema = file_get_contents(BASE_PATH . '/sql/schema.sql');
        try {
            Database::connection()->exec($schema);
            $name = sanitize($_POST['name'] ?? 'Admin');
            $emailInput = $_POST['email'] ?? 'admin@example.com';
            $email = filter_var($emailInput, FILTER_VALIDATE_EMAIL) ?: 'admin@example.com';
            $password = $_POST['password'] ?? 'admin123';
            $existing = Admin::findByEmail($email);
            if (!$existing) {
                Admin::create($name, $email, $password);
            }
            $message = 'Jadvallar yaratildi va admin tayyor.';
            $success = true;
        } catch (Exception $e) {
            $message = 'O\'rnatishda xatolik: ' . $e->getMessage();
        }
    }
}
?>
<!doctype html>
<html lang="uz">
<head>
    <meta charset="utf-8">
    <title>O'rnatish</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card shadow-sm">
                <div class="card-body">
                    <h3 class="mb-3">CPA tizimini o'rnatish</h3>
                    <?php if ($message): ?>
                        <div class="alert <?= $success ? 'alert-success' : 'alert-warning' ?>"><?= $message ?></div>
                    <?php endif; ?>
                    <?php if ($success): ?>
                        <p class="text-success small mb-3">Iltimos, xavfsizlik uchun <code>public/install.php</code> faylini o'chirib tashlang.</p>
                    <?php endif; ?>
                    <form method="post">
                        <input type="hidden" name="_token" value="<?= csrf_token() ?>">
                        <div class="mb-3">
                            <label class="form-label">Admin ism</label>
                            <input type="text" name="name" class="form-control" value="Admin" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Admin email</label>
                            <input type="email" name="email" class="form-control" value="admin@example.com" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Parol</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <button class="btn btn-success w-100">Jadvallarni yaratish</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>
