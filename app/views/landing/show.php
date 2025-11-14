<!doctype html>
<html lang="uz">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8') ?> | <?= htmlspecialchars(config('app_name', 'CPA Market'), ENT_QUOTES, 'UTF-8') ?></title>
    <meta name="description" content="<?= htmlspecialchars(substr(strip_tags($product['description']), 0, 160), ENT_QUOTES, 'UTF-8') ?>">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap">
    <link rel="stylesheet" href="/assets/css/main.css">
</head>
<body>
<?php if (!empty($pixelId)): ?>
<script>
!function(f,b,e,v,n,t,s)
{if(f.fbq)return;n=f.fbq=function(){n.callMethod?
 n.callMethod.apply(n,arguments):n.queue.push(arguments)};
 if(!f._fbq)f._fbq=n;n.push=n;n.loaded=!0;n.version='2.0';
 n.queue=[];t=b.createElement(e);t.async=!0;
 t.src=v;s=b.getElementsByTagName(e)[0];
 s.parentNode.insertBefore(t,s)}(window, document,'script',
 'https://connect.facebook.net/en_US/fbevents.js');
fbq('init', '<?= htmlspecialchars($pixelId, ENT_QUOTES, 'UTF-8') ?>');
fbq('track', 'PageView');
fbq('track', 'ViewContent', {content_name: <?= json_encode($product['name']) ?>, content_ids: [<?= json_encode($product['slug']) ?>], value: <?= (float)$product['price'] ?>, currency: 'UZS'});
</script>
<noscript><img height="1" width="1" style="display:none" src="https://www.facebook.com/tr?id=<?= urlencode($pixelId) ?>&ev=PageView&noscript=1"/></noscript>
<?php endif; ?>
<header class="navbar">
    <div class="brand"><a href="/" style="text-decoration:none; color:inherit;">← Bosh sahifa</a></div>
</header>
<section class="products-grid" style="grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); align-items:flex-start;">
    <div>
        <img src="<?= htmlspecialchars($product['image_path'], ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8') ?>" style="width:100%; border-radius:20px;">
        <?php if (!empty($videoUrl)): ?>
            <div style="margin-top:20px;">
                <?php if (str_contains(strtolower($videoUrl), 'youtube')): ?>
                    <iframe width="100%" height="315" src="<?= htmlspecialchars($videoUrl, ENT_QUOTES, 'UTF-8') ?>" title="Video" frameborder="0" allowfullscreen></iframe>
                <?php else: ?>
                    <video controls style="width:100%; border-radius:12px;">
                        <source src="<?= htmlspecialchars($videoUrl, ENT_QUOTES, 'UTF-8') ?>" type="video/mp4">
                    </video>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
    <div>
        <h1><?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8') ?></h1>
        <p><?= nl2br(htmlspecialchars($product['description'], ENT_QUOTES, 'UTF-8')) ?></p>
        <div class="price" style="font-size:2rem;"><?= number_format($product['price'], 0, '.', ' ') ?> so'm</div>
        <div class="lead-form mt-4">
            <h3>Buyurtma berish</h3>
            <form id="leadForm" method="post" action="/product/<?= htmlspecialchars($product['slug'], ENT_QUOTES, 'UTF-8') ?>/lead">
                <input type="hidden" name="_token" value="<?= csrf_token() ?>">
                <input type="hidden" name="product_id" value="<?= (int)$product['id'] ?>">
                <div class="mb-3">
                    <label class="form-label">To'liq ism</label>
                    <input type="text" name="full_name" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Telefon raqam</label>
                    <input type="text" name="phone" class="form-control" data-mask="phone" placeholder="+998" required>
                </div>
                <button type="submit">Buyurtma berish</button>
            </form>
            <div id="message" class="mt-3" style="display:none;"></div>
        </div>
    </div>
</section>
<script src="/assets/js/main.js"></script>
<script>
const form = document.getElementById('leadForm');
const msg = document.getElementById('message');
form.addEventListener('submit', async (e) => {
    e.preventDefault();
    msg.style.display = 'none';
    const data = new FormData(form);
    try {
        const res = await fetch(form.action, { method: 'POST', body: data });
        const json = await res.json();
        msg.style.display = 'block';
        msg.className = json.success ? 'alert-success' : 'alert';
        msg.textContent = json.message;
        if (json.success) {
            form.reset();
            <?php if (!empty($pixelId)): ?>
            if (typeof fbq !== 'undefined') {
                fbq('track', 'Lead', {content_name: <?= json_encode($product['name']) ?>});
            }
            <?php endif; ?>
        }
        if (json.token) {
            const tokenField = form.querySelector('input[name=\"_token\"]');
            if (tokenField) {
                tokenField.value = json.token;
            }
        }
    } catch (error) {
        msg.style.display = 'block';
        msg.className = 'alert';
        msg.textContent = 'Kutilmagan xatolik yuz berdi. Keyinroq urinib ko\\'ring.';
    }
});
</script>
</body>
</html>
