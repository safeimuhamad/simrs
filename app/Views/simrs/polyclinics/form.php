<?php $item = $item ?? []; ?>
<div class="card bg-white rounded-10 border border-white mb-4">
    <div class="p-20 border-bottom"><h4 class="erp-detail-section-title mb-0">Informasi Poli</h4></div>
    <div class="p-20 row g-4">
        <div class="col-md-3"><label class="erp-detail-label">Kode poli *</label><input class="form-control" name="clinic_code" required value="<?= htmlspecialchars($item['clinic_code'] ?? '') ?>"></div>
        <div class="col-md-4"><label class="erp-detail-label">Nama poli *</label><input class="form-control" name="name" required value="<?= htmlspecialchars($item['name'] ?? '') ?>"></div>
        <div class="col-md-2"><label class="erp-detail-label">Prefix antrean</label><input class="form-control" name="queue_prefix" maxlength="5" value="<?= htmlspecialchars($item['queue_prefix'] ?? 'A') ?>"></div>
        <div class="col-md-3"><label class="erp-detail-label">Lokasi</label><input class="form-control" name="location" value="<?= htmlspecialchars($item['location'] ?? '') ?>"></div>
        <div class="col-md-4"><label class="erp-detail-label">Status</label><select class="form-control" name="status"><option value="active" <?= ($item['status'] ?? 'active') === 'active' ? 'selected' : '' ?>>Active</option><option value="inactive" <?= ($item['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>Inactive</option></select></div>
    </div>
</div>
