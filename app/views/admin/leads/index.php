<?php ob_start(); ?>
<div class="d-flex justify-content-between align-items-center mb-3">
    <h3>Lidlar</h3>
    <?php $exportLink = '/admin/leads/export' . (!empty($exportQuery) ? '?' . $exportQuery : ''); ?>
    <a class="btn btn-outline-success" href="<?= $exportLink ?>">CSV eksport</a>
</div>
<form class="row g-3 mb-4" method="get">
    <div class="col-md-3">
        <label class="form-label">Mahsulot</label>
        <select class="form-select" name="product_id">
            <option value="">Hammasi</option>
            <?php foreach ($products as $product): ?>
                <option value="<?= (int)$product['id'] ?>" <?= ($filters['product_id'] ?? '') == $product['id'] ? 'selected' : '' ?>><?= htmlspecialchars($product['name'], ENT_QUOTES, 'UTF-8') ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-3">
        <label class="form-label">Boshlanish sanasi</label>
        <input type="date" class="form-control" name="date_from" value="<?= htmlspecialchars($filters['date_from'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
    </div>
    <div class="col-md-3">
        <label class="form-label">Tugash sanasi</label>
        <input type="date" class="form-control" name="date_to" value="<?= htmlspecialchars($filters['date_to'] ?? '', ENT_QUOTES, 'UTF-8') ?>">
    </div>
    <div class="col-md-3 align-self-end">
        <button class="btn btn-success w-100">Qidirish</button>
    </div>
</form>
<div class="table-responsive">
    <table class="table table-hover">
        <thead>
            <tr>
                <th>ID</th>
                <th>Ism</th>
                <th>Telefon</th>
                <th>Mahsulot</th>
                <th>Sana</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($leads as $lead): ?>
            <tr>
                <td><?= (int)$lead['id'] ?></td>
                <td><?= htmlspecialchars($lead['full_name'], ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars($lead['phone'], ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= htmlspecialchars($lead['product_name'] ?? '-', ENT_QUOTES, 'UTF-8') ?></td>
                <td><?= date('d.m.Y H:i', strtotime($lead['created_at'])) ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php $slot = ob_get_clean(); $title = 'Lidlar'; include __DIR__ . '/../../layouts/admin.php'; ?>
