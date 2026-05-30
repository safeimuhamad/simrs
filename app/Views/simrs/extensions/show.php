<div class="card bg-white rounded-10 border border-white p-20 mb-4">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div>
            <h3 class="mb-1"><?= htmlspecialchars($module['title']) ?></h3>
            <p class="mb-0 text-body"><?= htmlspecialchars($item['record_no']) ?> | <?= simrsStatusBadge($item['status'] ?? '') ?></p>
        </div>
        <div class="d-flex gap-2">
            <a href="<?= url($module['route']) ?>" class="btn btn-light erp-btn"><i class="ri-arrow-left-line me-1"></i>Kembali</a>
            <a href="<?= url($module['route'] . '-edit', ['id' => $item['id']]) ?>" class="btn btn-primary text-white erp-btn"><i class="ri-edit-line me-1"></i>Update</a>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="card bg-white rounded-10 border border-white p-20 h-100">
            <h4 class="erp-detail-section-title mb-3">Detail Data</h4>
            <div class="row g-3">
                <?php
                $details = [
                    'No. Dokumen' => $item['record_no'] ?? '-',
                    'No. Referensi' => $item['reference_no'] ?? '-',
                    'Tipe' => $item['module_type'] ?? '-',
                    'Subjek' => $item['subject'] ?? '-',
                    'Tanggal' => !empty($item['record_date']) ? date('d/m/Y H:i', strtotime($item['record_date'])) : '-',
                    'Prioritas' => $item['priority'] ?? '-',
                    'Lokasi' => $item['location'] ?? '-',
                    'Penanggung Jawab' => $item['assigned_to'] ?? '-',
                    'Nilai' => isset($item['amount']) ? 'Rp ' . number_format((float) $item['amount'], 0, ',', '.') : '-',
                    'Readiness Integrasi' => $item['interoperability_status'] ?? '-',
                ];
                ?>
                <?php foreach ($details as $label => $value): ?>
                    <div class="col-md-6">
                        <div class="erp-detail-label"><?= htmlspecialchars($label) ?></div>
                        <div class="fw-semibold"><?= htmlspecialchars((string) ($value ?: '-')) ?></div>
                    </div>
                <?php endforeach; ?>
            </div>

            <hr class="my-4">
            <div class="erp-detail-label">Catatan</div>
            <div class="text-body" style="white-space: pre-line;"><?= htmlspecialchars($item['notes'] ?: '-') ?></div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card bg-white rounded-10 border border-white p-20 mb-4">
            <h4 class="erp-detail-section-title mb-3">Relasi SIMRS</h4>
            <div class="mb-3">
                <div class="erp-detail-label">Pasien</div>
                <div class="fw-semibold"><?= htmlspecialchars($item['patient_name'] ?: '-') ?></div>
                <small><?= htmlspecialchars($item['medical_record_no'] ?: '-') ?></small>
            </div>
            <div class="mb-3">
                <div class="erp-detail-label">Kunjungan</div>
                <div class="fw-semibold"><?= htmlspecialchars($item['visit_no'] ?: '-') ?></div>
            </div>
            <div>
                <div class="erp-detail-label">Billing</div>
                <div class="fw-semibold"><?= htmlspecialchars($item['billing_no'] ?: '-') ?></div>
            </div>
        </div>

        <div class="card bg-white rounded-10 border border-white p-20">
            <h4 class="erp-detail-section-title mb-3">Payload Integrasi</h4>
            <pre class="mb-0 p-3 rounded bg-light text-wrap" style="white-space: pre-wrap;"><?= htmlspecialchars($item['payload_json'] ?: '-') ?></pre>
        </div>
    </div>
</div>
