<!doctype html>
<html lang="uz">
<head>
    <meta charset="utf-8">
    <title>Admin kirish</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #0f9f5f; display:flex; align-items:center; justify-content:center; min-height:100vh; }
        .card { border-radius: 16px; box-shadow: 0 30px 80px rgba(0,0,0,0.25); }
    </style>
</head>
<body>
<div class="card p-4" style="max-width:420px; width:100%;">
    <div class="text-center mb-4">
        <h3 class="fw-bold">CPA Panel</h3>
        <p class="text-muted">Login qilish uchun ma'lumotlarni kiriting</p>
    </div>
    <?php if ($error = flash('error')): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>
    <form method="post" action="/admin/login">
        <input type="hidden" name="_token" value="<?= csrf_token() ?>">
        <div class="mb-3">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" required>
        </div>
        <div class="mb-3">
            <label class="form-label">Parol</label>
            <input type="password" name="password" class="form-control" required>
        </div>
        <button class="btn btn-success w-100">Kirish</button>
    </form>
</div>
</body>
</html>
