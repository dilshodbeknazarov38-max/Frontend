<?php ob_start(); ?>
<h3>Umumiy sozlamalar</h3>
<form method="post" class="mt-4">
    <input type="hidden" name="_token" value="<?= csrf_token() ?>">
    <div class="mb-3">
        <label class="form-label">Facebook Pixel ID</label>
        <input type="text" class="form-control" name="facebook_pixel" value="<?= htmlspecialchars($pixel ?? '', ENT_QUOTES, 'UTF-8') ?>" placeholder="1234567890">
        <div class="form-text">Bu ID barcha landing sahifalarga avtomatik qo'shiladi.</div>
    </div>
    <button class="btn btn-success">Saqlash</button>
</form>
<?php $slot = ob_get_clean(); $title = 'Sozlamalar'; include __DIR__ . '/../../layouts/admin.php'; ?>
