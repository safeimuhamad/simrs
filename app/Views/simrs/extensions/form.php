<?php $isEdit = !empty($item); ?>
<div class="card bg-white rounded-10 border border-white p-20 mb-4">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div>
            <h3 class="mb-1"><?= htmlspecialchars($title) ?></h3>
            <p class="mb-0 text-body"><?= htmlspecialchars($module['subtitle']) ?></p>
        </div>
        <a href="<?= url($module['route']) ?>" class="btn btn-light erp-btn">
            <i class="ri-arrow-left-line me-1"></i>
            Kembali
        </a>
    </div>
</div>

<form method="post" action="<?= url($module['route'] . ($isEdit ? '-update' : '-store')) ?>">
    <?php if ($isEdit): ?><input type="hidden" name="id" value="<?= htmlspecialchars($item['id']) ?>"><?php endif; ?>

    <div class="card bg-white rounded-10 border border-white p-20 mb-4">
        <h4 class="erp-detail-section-title mb-3">Data Utama</h4>
        <div class="row g-4">
            <div class="col-md-3">
                <label class="erp-detail-label">No. Dokumen</label>
                <input class="form-control" name="record_no" value="<?= htmlspecialchars($item['record_no'] ?? '') ?>" placeholder="Otomatis jika kosong">
            </div>
            <div class="col-md-3">
                <label class="erp-detail-label">Tanggal</label>
                <input type="datetime-local" class="form-control" name="record_date" value="<?= htmlspecialchars(!empty($item['record_date']) ? date('Y-m-d\TH:i', strtotime($item['record_date'])) : date('Y-m-d\TH:i')) ?>">
            </div>
            <div class="col-md-3">
                <label class="erp-detail-label">Tipe</label>
                <select class="form-control" name="module_type">
                    <option value="">Pilih tipe</option>
                    <?php foreach ($module['types'] as $type): ?>
                        <option value="<?= htmlspecialchars($type) ?>" <?= ($item['module_type'] ?? '') === $type ? 'selected' : '' ?>><?= htmlspecialchars($type) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="erp-detail-label">Status</label>
                <select class="form-control" name="status">
                    <?php foreach ($module['statuses'] as $status): ?>
                        <option value="<?= htmlspecialchars($status) ?>" <?= ($item['status'] ?? 'draft') === $status ? 'selected' : '' ?>><?= htmlspecialchars(ucwords(str_replace('_', ' ', $status))) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-8">
                <label class="erp-detail-label">Subjek / Ringkasan</label>
                <input class="form-control" name="subject" value="<?= htmlspecialchars($item['subject'] ?? '') ?>" required placeholder="Contoh: Triase pasien, jadwal OK, klaim asuransi, hasil lab">
            </div>
            <div class="col-md-4">
                <label class="erp-detail-label">Prioritas</label>
                <select class="form-control" name="priority">
                    <?php foreach (['low','normal','high','urgent','critical'] as $priority): ?>
                        <option value="<?= $priority ?>" <?= ($item['priority'] ?? 'normal') === $priority ? 'selected' : '' ?>><?= ucfirst($priority) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
    </div>

    <div class="card bg-white rounded-10 border border-white p-20 mb-4">
        <h4 class="erp-detail-section-title mb-3">Relasi SIMRS</h4>
        <div class="row g-4">
            <div class="col-md-4">
                <label class="erp-detail-label">Pasien</label>
                <select class="form-control" name="patient_id">
                    <option value="">Tidak terkait pasien</option>
                    <?php foreach ($patients as $patient): ?>
                        <option value="<?= (int) $patient['id'] ?>" <?= (int)($item['patient_id'] ?? 0) === (int)$patient['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($patient['medical_record_no'] . ' - ' . $patient['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label class="erp-detail-label">Kunjungan</label>
                <select class="form-control" name="visit_id">
                    <option value="">Tidak terkait kunjungan</option>
                    <?php foreach ($visits as $visit): ?>
                        <option value="<?= (int) $visit['id'] ?>" <?= (int)($item['visit_id'] ?? 0) === (int)$visit['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($visit['visit_no'] . ' - ' . ($visit['patient_name'] ?? '-') . ' - ' . ($visit['polyclinic_name'] ?? '-')) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label class="erp-detail-label">Billing Pasien</label>
                <select class="form-control" name="billing_id">
                    <option value="">Tidak terkait billing</option>
                    <?php foreach ($billings as $billing): ?>
                        <option value="<?= (int) $billing['id'] ?>" <?= (int)($item['billing_id'] ?? 0) === (int)$billing['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($billing['billing_no'] . ' - ' . strtoupper($billing['status'])) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="erp-detail-label">No. Referensi</label>
                <input class="form-control" name="reference_no" value="<?= htmlspecialchars($item['reference_no'] ?? '') ?>" placeholder="SEP/FHIR/Invoice/Asset tag">
            </div>
            <div class="col-md-3">
                <label class="erp-detail-label">Nilai / Estimasi Biaya</label>
                <input class="form-control" name="amount" value="<?= htmlspecialchars((string)($item['amount'] ?? '')) ?>" placeholder="0">
            </div>
            <div class="col-md-3">
                <label class="erp-detail-label">Lokasi</label>
                <input class="form-control" name="location" value="<?= htmlspecialchars($item['location'] ?? '') ?>" placeholder="IGD / OK 1 / ICU / Gudang Farmasi">
            </div>
            <div class="col-md-3">
                <label class="erp-detail-label">Penanggung Jawab</label>
                <input class="form-control" name="assigned_to" value="<?= htmlspecialchars($item['assigned_to'] ?? '') ?>" placeholder="Dokter/perawat/petugas/vendor">
            </div>
        </div>
    </div>

    <div class="card bg-white rounded-10 border border-white p-20 mb-4">
        <h4 class="erp-detail-section-title mb-3">Catatan dan Integrasi</h4>
        <div class="row g-4">
            <div class="col-md-4">
                <label class="erp-detail-label">Readiness Integrasi</label>
                <select class="form-control" name="interoperability_status">
                    <?php foreach (['not_ready','mapped','queued','sent','accepted','failed'] as $status): ?>
                        <option value="<?= $status ?>" <?= ($item['interoperability_status'] ?? 'not_ready') === $status ? 'selected' : '' ?>><?= htmlspecialchars(ucwords(str_replace('_', ' ', $status))) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-8">
                <label class="erp-detail-label">Payload JSON / Metadata</label>
                <input class="form-control" name="payload_json" value="<?= htmlspecialchars($item['payload_json'] ?? '') ?>" placeholder='{"resourceType":"Encounter"}'>
            </div>
            <div class="col-md-12">
                <label class="erp-detail-label">Catatan</label>
                <textarea class="form-control" name="notes" rows="5" placeholder="Catatan klinis/operasional, hasil, instruksi, atau alasan status"><?= htmlspecialchars($item['notes'] ?? '') ?></textarea>
            </div>
        </div>
    </div>

    <div class="card bg-white rounded-10 border border-white p-20">
        <div class="d-flex justify-content-end flex-wrap gap-3">
            <a href="<?= url($module['route']) ?>" class="btn btn-light erp-btn"><i class="ri-close-line me-1"></i>Batal</a>
            <button class="btn btn-primary text-white erp-btn"><i class="ri-save-line me-1"></i><?= $isEdit ? 'Update' : 'Simpan' ?></button>
        </div>
    </div>
</form>
