<?php $admin = auth_admin(); ?>
<!doctype html>
<html lang="uz">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= $title ?? 'Admin panel' ?> | CPA Landing</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="/admin/assets/css/admin.css">
</head>
<body>
<nav class="navbar navbar-expand-lg navbar-dark bg-success">
    <div class="container-fluid">
        <a class="navbar-brand" href="/admin">CPA Panel</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#adminNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="adminNav">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                <li class="nav-item"><a class="nav-link" href="/admin/products">Mahsulotlar</a></li>
                <li class="nav-item"><a class="nav-link" href="/admin/leads">Lidlar</a></li>
                <li class="nav-item"><a class="nav-link" href="/admin/settings">Sozlamalar</a></li>
            </ul>
            <div class="d-flex align-items-center text-white">
                <i class="bi bi-person-circle me-2"></i>
                <span class="me-3"><?= $admin['name'] ?? 'Administrator' ?></span>
                <a class="btn btn-outline-light btn-sm" href="/admin/logout">Chiqish</a>
            </div>
        </div>
    </div>
</nav>
<main class="container py-4">
    <?php if ($message = flash('success')): ?>
        <div class="alert alert-success"><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>
    <?php if ($error = flash('error')): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error, ENT_QUOTES, 'UTF-8') ?></div>
    <?php endif; ?>
    <?= $slot ?? '' ?>
</main>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="/admin/assets/js/admin.js"></script>
</body>
</html>
