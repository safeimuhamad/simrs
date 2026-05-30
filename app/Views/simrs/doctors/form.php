<?php $item = $item ?? []; ?>
<div class="card bg-white rounded-10 border border-white mb-4">
    <div class="p-20 border-bottom"><h4 class="erp-detail-section-title mb-0">Informasi Dokter</h4></div>
    <div class="p-20 row g-4">
        <div class="col-md-3"><label class="erp-detail-label">Kode dokter</label><input class="form-control" name="doctor_code" value="<?= htmlspecialchars($item['doctor_code'] ?? '') ?>" placeholder="Otomatis jika kosong"></div>
        <div class="col-md-5"><label class="erp-detail-label">Nama dokter *</label><input class="form-control" name="name" required value="<?= htmlspecialchars($item['name'] ?? '') ?>"></div>
        <div class="col-md-4"><label class="erp-detail-label">Spesialis</label><input class="form-control" name="specialist" value="<?= htmlspecialchars($item['specialist'] ?? '') ?>"></div>
        <div class="col-md-4"><label class="erp-detail-label">No. SIP</label><input class="form-control" name="sip_no" value="<?= htmlspecialchars($item['sip_no'] ?? '') ?>"></div>
        <div class="col-md-4"><label class="erp-detail-label">Telepon</label><input class="form-control" name="phone" value="<?= htmlspecialchars($item['phone'] ?? '') ?>"></div>
        <div class="col-md-4"><label class="erp-detail-label">Email</label><input type="email" class="form-control" name="email" value="<?= htmlspecialchars($item['email'] ?? '') ?>"></div>
        <div class="col-md-6"><label class="erp-detail-label">User ID login dokter</label><input class="form-control" name="user_id" value="<?= htmlspecialchars($item['user_id'] ?? '') ?>"></div>
        <div class="col-md-6"><label class="erp-detail-label">Status</label><select class="form-control" name="status"><option value="active" <?= ($item['status'] ?? 'active') === 'active' ? 'selected' : '' ?>>Active</option><option value="inactive" <?= ($item['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>Inactive</option></select></div>
    </div>
</div>
