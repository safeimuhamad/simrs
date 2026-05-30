<?php $item = $item ?? []; ?>

<div class="card bg-white rounded-10 border border-white mb-4">
    <div class="p-20 border-bottom">
        <h4 class="erp-detail-section-title mb-0">Informasi Item Farmasi</h4>
    </div>
    <div class="p-20">
        <div class="row g-4">
            <div class="col-md-3">
                <label class="erp-detail-label">SKU / Kode</label>
                <input type="text" name="sku" class="form-control" value="<?= htmlspecialchars($item['sku'] ?? '') ?>">
            </div>
            <div class="col-md-5">
                <label class="erp-detail-label">Nama Item <span class="text-danger">*</span></label>
                <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($item['name'] ?? '') ?>" required>
            </div>
            <div class="col-md-2">
                <label class="erp-detail-label">Kategori</label>
                <select name="category" class="form-control">
                    <?php foreach (['obat' => 'Obat', 'alkes' => 'Alkes', 'bmhp' => 'BMHP', 'lainnya' => 'Lainnya'] as $value => $label): ?>
                        <option value="<?= $value ?>" <?= ($item['category'] ?? 'obat') === $value ? 'selected' : '' ?>><?= $label ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <label class="erp-detail-label">Status</label>
                <select name="status" class="form-control">
                    <option value="active" <?= ($item['status'] ?? 'active') === 'active' ? 'selected' : '' ?>>Active</option>
                    <option value="inactive" <?= ($item['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>Inactive</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="erp-detail-label">Tipe</label>
                <select name="item_type" class="form-control">
                    <option value="medicine" <?= ($item['item_type'] ?? 'medicine') === 'medicine' ? 'selected' : '' ?>>Obat</option>
                    <option value="supply" <?= ($item['item_type'] ?? '') === 'supply' ? 'selected' : '' ?>>Alkes/BMHP</option>
                    <option value="service" <?= ($item['item_type'] ?? '') === 'service' ? 'selected' : '' ?>>Jasa Medis</option>
                </select>
            </div>
            <div class="col-md-3">
                <label class="erp-detail-label">Satuan</label>
                <input type="text" name="unit_name" class="form-control" value="<?= htmlspecialchars($item['unit_name'] ?? 'pcs') ?>">
            </div>
            <div class="col-md-3">
                <label class="erp-detail-label">Stok Saat Ini</label>
                <input type="number" step="0.01" name="current_stock" class="form-control" value="<?= htmlspecialchars($item['current_stock'] ?? 0) ?>">
            </div>
            <div class="col-md-3">
                <label class="erp-detail-label">Stok Minimum</label>
                <input type="number" step="0.01" name="minimum_stock" class="form-control" value="<?= htmlspecialchars($item['minimum_stock'] ?? 0) ?>">
            </div>
            <div class="col-md-4">
                <label class="erp-detail-label">Harga Satuan</label>
                <input type="text" name="unit_price" class="form-control rupiah-input" value="<?= number_format((float) ($item['unit_price'] ?? 0), 0, ',', '.') ?>">
            </div>
            <div class="col-md-8">
                <label class="erp-detail-label">Keterangan</label>
                <textarea name="description" class="form-control" rows="3"><?= htmlspecialchars($item['description'] ?? '') ?></textarea>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.rupiah-input').forEach(function (input) {
        input.addEventListener('input', function () {
            const value = input.value.replace(/\D/g, '');
            input.value = value ? new Intl.NumberFormat('id-ID').format(value) : '';
        });
    });
});
</script>
