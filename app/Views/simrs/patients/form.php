<?php $item = $item ?? []; ?>
<div class="card bg-white rounded-10 border border-white mb-4">
    <div class="p-20 border-bottom"><h4 class="mb-0">Data Pasien</h4></div>
    <div class="p-20">
        <div class="row g-3">
            <div class="col-md-4"><label class="erp-detail-label">No. RM</label><input class="form-control" name="medical_record_no" value="<?= htmlspecialchars($item['medical_record_no'] ?? '') ?>" placeholder="Otomatis jika kosong"></div>
            <div class="col-md-4"><label class="erp-detail-label">NIK</label><input class="form-control" name="nik" value="<?= htmlspecialchars($item['nik'] ?? '') ?>"></div>
            <div class="col-md-4"><label class="erp-detail-label">Nama pasien *</label><input class="form-control" name="name" required value="<?= htmlspecialchars($item['name'] ?? '') ?>"></div>
            <div class="col-md-3"><label class="erp-detail-label">Gender</label><select class="form-control" name="gender"><option value="">-</option><option value="male" <?= ($item['gender'] ?? '') === 'male' ? 'selected' : '' ?>>Laki-laki</option><option value="female" <?= ($item['gender'] ?? '') === 'female' ? 'selected' : '' ?>>Perempuan</option></select></div>
            <div class="col-md-3"><label class="erp-detail-label">Tanggal lahir</label><input type="date" class="form-control" name="birth_date" value="<?= htmlspecialchars($item['birth_date'] ?? '') ?>"></div>
            <div class="col-md-3"><label class="erp-detail-label">Tempat lahir</label><input class="form-control" name="birth_place" value="<?= htmlspecialchars($item['birth_place'] ?? '') ?>"></div>
            <div class="col-md-3"><label class="erp-detail-label">Gol. darah</label><input class="form-control" name="blood_type" value="<?= htmlspecialchars($item['blood_type'] ?? '') ?>"></div>
            <div class="col-md-4"><label class="erp-detail-label">Telepon</label><input class="form-control" name="phone" value="<?= htmlspecialchars($item['phone'] ?? '') ?>"></div>
            <div class="col-md-4"><label class="erp-detail-label">Email</label><input type="email" class="form-control" name="email" value="<?= htmlspecialchars($item['email'] ?? '') ?>"></div>
            <div class="col-md-4"><label class="erp-detail-label">Jenis pembayaran</label><select class="form-control" name="insurance_type"><?php foreach (['umum','asuransi','bpjs'] as $type): ?><option value="<?= $type ?>" <?= ($item['insurance_type'] ?? 'umum') === $type ? 'selected' : '' ?>><?= strtoupper($type) ?></option><?php endforeach; ?></select></div>
            <div class="col-md-6"><label class="erp-detail-label">No. asuransi/BPJS</label><input class="form-control" name="insurance_no" value="<?= htmlspecialchars($item['insurance_no'] ?? '') ?>"></div>
            <div class="col-md-6"><label class="erp-detail-label">Status</label><select class="form-control" name="status"><option value="active" <?= ($item['status'] ?? 'active') === 'active' ? 'selected' : '' ?>>Active</option><option value="inactive" <?= ($item['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>Inactive</option></select></div>
            <div class="col-md-12"><label class="erp-detail-label">Alamat</label><textarea class="form-control" name="address"><?= htmlspecialchars($item['address'] ?? '') ?></textarea></div>
            <div class="col-md-12"><label class="erp-detail-label">Alergi / catatan penting</label><textarea class="form-control" name="allergy_notes"><?= htmlspecialchars($item['allergy_notes'] ?? '') ?></textarea></div>
            <div class="col-md-6"><label class="erp-detail-label">Kontak darurat</label><input class="form-control" name="emergency_contact_name" value="<?= htmlspecialchars($item['emergency_contact_name'] ?? '') ?>"></div>
            <div class="col-md-6"><label class="erp-detail-label">No. kontak darurat</label><input class="form-control" name="emergency_contact_phone" value="<?= htmlspecialchars($item['emergency_contact_phone'] ?? '') ?>"></div>
        </div>
    </div>
</div>
