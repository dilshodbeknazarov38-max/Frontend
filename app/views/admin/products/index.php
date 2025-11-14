<?php ob_start(); ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h3>Mahsulotlar</h3>
    <a class="btn btn-success" href="/admin/products/create"><i class="bi bi-plus-lg"></i> Yangi mahsulot</a>
</div>
<div class="table-responsive">
    <table class="table table-striped align-middle">
        <thead>
            <tr>
                <th>ID</th>
                <th>Mahsulot</th>
                <th>Kategoriya</th>
                <th>Narx</th>
                <th>Holat</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($products as $product): ?>
            <tr>
                <td><?= (int)$product['id'] ?></td>
                <td>
                    <div class="d-flex align-items-center gap-2">
                        <?php if (!empty($product['image_path'])): ?>
                            <img src="<?= htmlspecialchars($product['image_path'], ENT_QUOTES, 'UTF-8') ?>" alt="<?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8') ?>" width="60">
                        <?php endif; ?>
                        <div>
                            <strong><?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8') ?></strong>
                            <div class="small text-muted">/product/<?= htmlspecialchars($product['slug'], ENT_QUOTES, 'UTF-8') ?></div>
                        </div>
                    </div>
                </td>
                <td><?= htmlspecialchars($product['category_name'] ?? '—', ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= number_format($product['price'], 0, '.', ' ') ?> so'm</td>
                <td>
                    <span class="badge bg-<?= $product['status'] === 'active' ? 'success' : 'secondary' ?> badge-status">
                        <?= $product['status'] === 'active' ? 'faol' : 'noaktiv' ?>
                    </span>
                </td>
                <td class="text-end">
                    <div class="btn-group btn-group-sm">
                        <a class="btn btn-outline-primary" href="/admin/products/<?= $product['id'] ?>/edit">Tahrirlash</a>
                        <form action="/admin/products/<?= $product['id'] ?>/duplicate" method="post">
                            <input type="hidden" name="_token" value="<?= csrf_token() ?>">
                            <button class="btn btn-outline-secondary" type="submit">Nusxa</button>
                        </form>
                        <form action="/admin/products/<?= $product['id'] ?>/delete" method="post">
                            <input type="hidden" name="_token" value="<?= csrf_token() ?>">
                            <button class="btn btn-outline-danger" data-confirm="O'chirishni tasdiqlang" type="submit">O'chirish</button>
                        </form>
                    </div>
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php $slot = ob_get_clean(); $title = 'Mahsulotlar'; include __DIR__ . '/../../layouts/admin.php'; ?>
