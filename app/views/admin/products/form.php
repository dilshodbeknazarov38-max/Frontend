<?php
    $currentStatus = $product['status'] ?? 'active';
    ob_start();
?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h3><?= isset($product) ? 'Mahsulotni tahrirlash' : 'Yangi mahsulot' ?></h3>
    <a class="btn btn-outline-secondary" href="/admin/products">Ortga</a>
</div>
<form method="post" enctype="multipart/form-data">
    <input type="hidden" name="_token" value="<?= csrf_token() ?>">
    <div class="row g-4">
        <div class="col-md-8">
            <div class="card p-3 mb-3">
                <div class="mb-3">
                    <label class="form-label">Mahsulot nomi</label>
                    <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($product['name'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Slug (URL)</label>
                    <input type="text" name="slug" class="form-control" value="<?= htmlspecialchars($product['slug'] ?? '', ENT_QUOTES, 'UTF-8') ?>" placeholder="auto">
                    <div class="form-text">Agar bo'sh qoldirilsa, avtomatik yaratiladi.</div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Mahsulot tavsifi</label>
                    <textarea name="description" class="form-control" rows="6" required><?= htmlspecialchars($product['description'] ?? '', ENT_QUOTES, 'UTF-8') ?></textarea>
                </div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label">Narx (so'm)</label>
                        <input type="number" name="price" class="form-control" step="0.01" value="<?= htmlspecialchars((string)($product['price'] ?? ''), ENT_QUOTES, 'UTF-8') ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Flow/redirect link</label>
                        <input type="url" name="flow_url" class="form-control" value="<?= htmlspecialchars($product['flow_url'] ?? '', ENT_QUOTES, 'UTF-8') ?>" required>
                    </div>
                </div>
                <div class="row g-3 mt-1">
                    <div class="col-md-6">
                        <label class="form-label">Video URL (YouTube yoki MP4)</label>
                        <input type="url" name="video_url" class="form-control" value="<?= htmlspecialchars($product['video_url'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Kategoriya</label>
                        <select name="category_id" class="form-select" required>
                            <option value="">Tanlang</option>
                            <?php foreach ($categories as $category): ?>
                                <option value="<?= (int)$category['id'] ?>" <?= (isset($product['category_id']) && $product['category_id'] == $category['id']) ? 'selected' : '' ?>><?= htmlspecialchars($category['name'], ENT_QUOTES, 'UTF-8') ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card p-3 mb-3">
                <label class="form-label">Mahsulot rasmi</label>
                <?php if (!empty($product['image_path'])): ?>
                    <div class="mb-2"><img src="<?= htmlspecialchars($product['image_path'], ENT_QUOTES, 'UTF-8') ?>" alt="" class="img-fluid rounded"></div>
                <?php endif; ?>
                <input type="file" name="image" accept="image/*" class="form-control" <?= isset($product) ? '' : 'required' ?>>
            </div>
            <div class="card p-3">
                <label class="form-label">Holat</label>
                <select name="status" class="form-select">
                    <option value="active" <?= $currentStatus === 'active' ? 'selected' : '' ?>>Faol</option>
                    <option value="inactive" <?= $currentStatus === 'inactive' ? 'selected' : '' ?>>Noaktiv</option>
                </select>
                <button class="btn btn-success w-100 mt-3">Saqlash</button>
            </div>
        </div>
    </div>
</form>
<?php $slot = ob_get_clean(); $title = 'Mahsulot formasi'; include __DIR__ . '/../../layouts/admin.php'; ?>
