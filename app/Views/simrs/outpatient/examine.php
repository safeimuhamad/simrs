<?php if (!empty($_SESSION['success'])): ?><div class="alert alert-success"><?= htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?></div><?php endif; ?>
<div class="card bg-white rounded-10 border border-white mb-4"><div class="p-20">
    <h3 class="mb-1"><?= htmlspecialchars($visit['patient_name']) ?> <small class="text-body">(<?= htmlspecialchars($visit['medical_record_no']) ?>)</small></h3>
    <p class="mb-0"><?= htmlspecialchars($visit['visit_no']) ?> | <?= htmlspecialchars($visit['polyclinic_name']) ?> | <?= htmlspecialchars($visit['doctor_name'] ?: '-') ?> | Status: <?= htmlspecialchars($visit['status']) ?></p>
</div></div>

<form method="post" action="<?= url('simrs-medical-records-store') ?>">
    <input type="hidden" name="visit_id" value="<?= htmlspecialchars($visit['id']) ?>">
    <div class="card bg-white rounded-10 border border-white mb-4">
        <div class="p-20 border-bottom"><h4 class="mb-0">SOAP dan Vital Sign</h4></div>
        <div class="p-20 row g-3">
            <div class="col-md-2"><label>TD</label><input class="form-control" name="vital_bp" value="<?= htmlspecialchars($record['vital_bp'] ?? '') ?>"></div>
            <div class="col-md-2"><label>Nadi</label><input class="form-control" name="vital_pulse" value="<?= htmlspecialchars($record['vital_pulse'] ?? '') ?>"></div>
            <div class="col-md-2"><label>Suhu</label><input class="form-control" name="vital_temperature" value="<?= htmlspecialchars($record['vital_temperature'] ?? '') ?>"></div>
            <div class="col-md-2"><label>RR</label><input class="form-control" name="vital_respiration" value="<?= htmlspecialchars($record['vital_respiration'] ?? '') ?>"></div>
            <div class="col-md-2"><label>BB</label><input class="form-control" name="vital_weight" value="<?= htmlspecialchars($record['vital_weight'] ?? '') ?>"></div>
            <div class="col-md-6"><label>Subjective</label><textarea class="form-control" name="subjective"><?= htmlspecialchars($record['subjective'] ?? $visit['chief_complaint'] ?? '') ?></textarea></div>
            <div class="col-md-6"><label>Objective</label><textarea class="form-control" name="objective"><?= htmlspecialchars($record['objective'] ?? '') ?></textarea></div>
            <div class="col-md-6"><label>Assessment</label><textarea class="form-control" name="assessment"><?= htmlspecialchars($record['assessment'] ?? '') ?></textarea></div>
            <div class="col-md-6"><label>Plan</label><textarea class="form-control" name="plan"><?= htmlspecialchars($record['plan'] ?? '') ?></textarea></div>
            <div class="col-md-12"><label>Catatan</label><textarea class="form-control" name="notes"><?= htmlspecialchars($record['notes'] ?? '') ?></textarea></div>
        </div>
        <div class="p-20 border-top"><button class="btn btn-primary text-white">Simpan EMR</button></div>
    </div>
</form>

<div class="row">
    <div class="col-lg-6">
        <form method="post" action="<?= url('simrs-treatments-store') ?>">
            <input type="hidden" name="visit_id" value="<?= htmlspecialchars($visit['id']) ?>">
            <div class="card bg-white rounded-10 border border-white mb-4">
                <div class="p-20 border-bottom"><h4 class="mb-0">Input Tindakan</h4></div>
                <div class="p-20">
                    <div class="row g-2">
                        <div class="col-md-6"><input class="form-control" name="item_name" required placeholder="Nama tindakan"></div>
                        <div class="col-md-2"><input class="form-control" name="quantity" value="1" placeholder="Qty"></div>
                        <div class="col-md-4"><input class="form-control" name="unit_price" value="0" placeholder="Tarif"></div>
                    </div>
                    <button class="btn btn-outline-primary mt-3">Tambah ke Billing</button>
                </div>
            </div>
        </form>

        <form method="post" action="<?= url('simrs-prescriptions-store') ?>">
            <input type="hidden" name="visit_id" value="<?= htmlspecialchars($visit['id']) ?>">
            <div class="card bg-white rounded-10 border border-white mb-4">
                <div class="p-20 border-bottom"><h4 class="mb-0">Input Resep</h4></div>
                <div class="p-20">
                    <?php for ($i = 0; $i < 3; $i++): ?>
                        <div class="border rounded p-2 mb-2">
                            <select class="form-control mb-2" name="items[<?= $i ?>][product_id]"><option value="">Obat manual / pilih stok</option><?php foreach ($medicines as $med): ?><option value="<?= $med['id'] ?>"><?= htmlspecialchars($med['name']) ?> (stok <?= (float)$med['current_stock'] ?>)</option><?php endforeach; ?></select>
                            <input class="form-control mb-2" name="items[<?= $i ?>][item_name]" placeholder="Nama obat">
                            <div class="row g-2"><div class="col"><input class="form-control" name="items[<?= $i ?>][dosage]" placeholder="Dosis"></div><div class="col"><input class="form-control" name="items[<?= $i ?>][frequency]" placeholder="Aturan pakai"></div></div>
                            <div class="row g-2 mt-1"><div class="col"><input class="form-control" name="items[<?= $i ?>][quantity]" value="1" placeholder="Qty"></div><div class="col"><input class="form-control" name="items[<?= $i ?>][unit_name]" value="unit"></div><div class="col"><input class="form-control" name="items[<?= $i ?>][price]" value="0" placeholder="Harga"></div></div>
                        </div>
                    <?php endfor; ?>
                    <button class="btn btn-primary text-white">Kirim Resep ke Farmasi</button>
                </div>
            </div>
        </form>
    </div>
    <div class="col-lg-6">
        <form method="post" action="<?= url('simrs-diagnoses-store') ?>">
            <input type="hidden" name="visit_id" value="<?= htmlspecialchars($visit['id']) ?>">
            <div class="card bg-white rounded-10 border border-white mb-4">
                <div class="p-20 border-bottom"><h4 class="mb-0">Diagnosa ICD Manual</h4></div>
                <div class="p-20">
                    <div class="row g-2 mb-3">
                        <div class="col-md-4">
                            <label>Kode ICD</label>
                            <input class="form-control" name="diagnosis_code" placeholder="J00">
                        </div>
                        <div class="col-md-8">
                            <label>Nama Diagnosa</label>
                            <input class="form-control" name="diagnosis_name" required placeholder="Nama diagnosa">
                        </div>
                        <div class="col-md-4">
                            <label>Tipe</label>
                            <select class="form-control" name="diagnosis_type">
                                <option value="primary">Primer</option>
                                <option value="secondary">Sekunder</option>
                            </select>
                        </div>
                        <div class="col-md-8">
                            <label>Catatan</label>
                            <input class="form-control" name="notes" placeholder="Catatan klinis">
                        </div>
                    </div>
                    <button class="btn btn-outline-primary mb-3">Simpan Diagnosa</button>
                    <p class="text-body">Diagnosa tersimpan: <?= count($diagnoses) ?></p>
                    <?php foreach ($diagnoses as $diag): ?>
                        <div class="border-bottom py-2">
                            <span class="fw-semibold"><?= htmlspecialchars($diag['diagnosis_code'] ?: '-') ?></span>
                            <?= htmlspecialchars(' - ' . $diag['diagnosis_name']) ?>
                            <span class="default-badge bg-info bg-opacity-10 text-info ms-2"><?= htmlspecialchars($diag['diagnosis_type'] ?? '-') ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </form>
    </div>
</div>
