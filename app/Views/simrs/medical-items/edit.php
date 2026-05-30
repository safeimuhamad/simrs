<?php if (!empty($_SESSION['error'])): ?>
    <div class="alert alert-danger mb-4"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
<?php endif; ?>

<div class="card bg-white rounded-10 border border-white p-20 mb-4">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div>
            <h3 class="mb-1">Edit Item Farmasi</h3>
            <p class="mb-0 text-body">Perbarui data stok, harga, dan status item farmasi.</p>
        </div>
        <a href="<?= url('simrs-medical-items') ?>" class="btn btn-light erp-btn"><i class="ri-arrow-left-line me-1"></i> Kembali</a>
    </div>
</div>

<form method="POST" action="<?= url('simrs-medical-items-update') ?>">
    <input type="hidden" name="id" value="<?= htmlspecialchars($item['id'] ?? '') ?>">
    <?php include __DIR__ . '/form.php'; ?>
    <div class="card bg-white rounded-10 border border-white p-20">
        <div class="d-flex justify-content-end flex-wrap gap-3">
            <a href="<?= url('simrs-medical-items') ?>" class="btn btn-light erp-btn">Batal</a>
            <button type="submit" class="btn btn-primary text-white erp-btn">Update</button>
        </div>
    </div>
</form>
