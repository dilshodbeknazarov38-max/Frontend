<?php ob_start(); ?>
<div class="row g-4">
    <div class="col-md-6">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <h5 class="text-muted">Jami mahsulotlar</h5>
                <h2><?= $productCount ?></h2>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <h5 class="text-muted">Jami lidlar</h5>
                <h2><?= $leadCount ?></h2>
            </div>
        </div>
    </div>
</div>
<?php $slot = ob_get_clean(); $title = 'Boshqaruv paneli'; include __DIR__ . '/../layouts/admin.php'; ?>
