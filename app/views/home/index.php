<!doctype html>
<html lang="uz">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($meta['title'] ?? 'Mahsulotlar', ENT_QUOTES, 'UTF-8') ?></title>
    <meta name="description" content="<?= htmlspecialchars($meta['description'] ?? 'Eng ko\'p sotilgan mahsulotlar', ENT_QUOTES, 'UTF-8') ?>">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css">
    <link rel="stylesheet" href="/assets/css/main.css">
</head>
<body>
<header class="navbar">
    <button id="menuToggle"><i class="bi bi-list"></i></button>
    <div class="brand"><?= htmlspecialchars(config('app_name', 'CPA Market'), ENT_QUOTES, 'UTF-8') ?></div>
    <div class="category-menu">
        <strong>Toifalar</strong>
        <?php foreach ($categories as $category): ?>
            <a href="?category=<?= (int)$category['id'] ?>"><?= htmlspecialchars($category['name'], ENT_QUOTES, 'UTF-8') ?></a>
        <?php endforeach; ?>
    </div>
</header>
<section class="hero">
    <h1>Eng ko'p sotilgan mahsulotlar bir joyda</h1>
    <p>Bir domain orqali cheksiz landing sahifalarini boshqaring. Oson, tez va xavfsiz marketing tizimi.</p>
    <form class="search-bar" method="get" action="/">
        <input type="text" name="q" value="<?= htmlspecialchars($searchTerm, ENT_QUOTES, 'UTF-8') ?>" placeholder="Mahsulot qidirish...">
        <button type="submit">Qidirish</button>
    </form>
</section>
<section style="padding: 20px;">
    <h2>Eng ko'p sotilganlar</h2>
    <div class="products-grid">
        <?php foreach ($topProducts as $product): ?>
            <article class="product-card">
                <img src="<?= htmlspecialchars($product['image_path'], ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8') ?>">
                <h3><?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8') ?></h3>
                <p><?= htmlspecialchars(mb_substr(strip_tags($product['description']), 0, 90), ENT_QUOTES, 'UTF-8') ?>...</p>
                <div class="price"><?= number_format($product['price'], 0, '.', ' ') ?> so'm</div>
                <a href="/product/<?= htmlspecialchars($product['slug'], ENT_QUOTES, 'UTF-8') ?>">Ko'rish</a>
            </article>
        <?php endforeach; ?>
    </div>
</section>
<section style="padding: 20px;">
    <h2>Yangi mahsulotlar</h2>
    <?php if (empty($latestProducts)): ?>
        <p>Bu toifa yoki qidiruv bo'yicha mahsulot topilmadi.</p>
    <?php endif; ?>
    <div class="products-grid">
        <?php foreach ($latestProducts as $product): ?>
            <article class="product-card">
                <img src="<?= htmlspecialchars($product['image_path'], ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8') ?>">
                <h3><?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8') ?></h3>
                <p><?= htmlspecialchars(mb_substr(strip_tags($product['description']), 0, 90), ENT_QUOTES, 'UTF-8') ?>...</p>
                <div class="price"><?= number_format($product['price'], 0, '.', ' ') ?> so'm</div>
                <a href="/product/<?= htmlspecialchars($product['slug'], ENT_QUOTES, 'UTF-8') ?>">Landingga o'tish</a>
            </article>
        <?php endforeach; ?>
    </div>
</section>
<script src="/assets/js/main.js"></script>
</body>
</html>
