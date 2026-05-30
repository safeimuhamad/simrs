<div class="card bg-white rounded-10 border border-white mb-4">
    <div class="p-20 border-bottom d-flex justify-content-between">
        <h4 class="mb-0"><?= htmlspecialchars($patient['name']) ?></h4>
        <a href="<?= url('simrs-patients-edit', ['id' => $patient['id']]) ?>" class="btn btn-primary text-white">Edit</a>
    </div>
    <div class="p-20 row g-3">
        <?php foreach (['medical_record_no' => 'No. RM','nik' => 'NIK','gender' => 'Gender','birth_date' => 'Tanggal Lahir','phone' => 'Telepon','insurance_type' => 'Pembayaran','insurance_no' => 'No. Asuransi/BPJS','address' => 'Alamat','allergy_notes' => 'Alergi'] as $key => $label): ?>
            <div class="col-md-4"><small class="text-body"><?= $label ?></small><div class="fw-semibold"><?= nl2br(htmlspecialchars($patient[$key] ?? '-')) ?></div></div>
        <?php endforeach; ?>
    </div>
</div>
