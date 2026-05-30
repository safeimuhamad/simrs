<div class="card bg-white rounded-10 border border-white p-20 mb-4"><div class="d-flex justify-content-between align-items-center flex-wrap gap-3"><div><h3 class="mb-1">Daftar Kunjungan</h3><p class="mb-0 text-body">Input pendaftaran pasien rawat jalan dan antrean poli.</p></div><a href="<?= url('simrs-registration') ?>" class="btn btn-light erp-btn"><i class="ri-arrow-left-line me-1"></i>Kembali</a></div></div>
<form method="post" action="<?= url('simrs-registration-store') ?>">
<div class="card bg-white rounded-10 border border-white mb-4">
    <div class="p-20 border-bottom"><h4 class="erp-detail-section-title mb-0">Daftar Kunjungan Rawat Jalan</h4></div>
    <div class="p-20 row g-4">
        <div class="col-md-6"><label class="erp-detail-label">Pasien</label><select class="form-control" name="patient_id" required><?php foreach ($patients as $patient): ?><option value="<?= $patient['id'] ?>"><?= htmlspecialchars($patient['medical_record_no'] . ' - ' . $patient['name']) ?></option><?php endforeach; ?></select><small><a href="<?= url('simrs-patients-create') ?>">Pasien baru? Tambah master pasien</a></small></div>
        <div class="col-md-3"><label class="erp-detail-label">Tanggal</label><input type="date" class="form-control" name="visit_date" value="<?= date('Y-m-d') ?>"></div>
        <div class="col-md-3"><label class="erp-detail-label">Pembayaran</label><select class="form-control" name="payment_type"><option value="umum">Umum</option><option value="asuransi">Asuransi</option><option value="bpjs">BPJS</option></select></div>
        <div class="col-md-4"><label class="erp-detail-label">Poli</label><select class="form-control" name="polyclinic_id" required><?php foreach ($polyclinics as $poly): ?><option value="<?= $poly['id'] ?>"><?= htmlspecialchars($poly['name']) ?></option><?php endforeach; ?></select></div>
        <div class="col-md-4"><label class="erp-detail-label">Dokter</label><select class="form-control" name="doctor_id"><option value="">-</option><?php foreach ($doctors as $doctor): ?><option value="<?= $doctor['id'] ?>"><?= htmlspecialchars($doctor['name']) ?></option><?php endforeach; ?></select></div>
        <div class="col-md-4"><label class="erp-detail-label">Jadwal</label><select class="form-control" name="schedule_id"><option value="">-</option><?php foreach ($schedules as $schedule): ?><option value="<?= $schedule['id'] ?>"><?= htmlspecialchars($schedule['polyclinic_name'] . ' - ' . $schedule['doctor_name'] . ' ' . substr($schedule['start_time'],0,5)) ?></option><?php endforeach; ?></select></div>
        <div class="col-md-4"><label class="erp-detail-label">Biaya administrasi</label><input type="number" class="form-control" name="registration_fee" value="0"></div>
        <div class="col-md-8"><label class="erp-detail-label">Keluhan awal</label><input class="form-control" name="chief_complaint"></div>
    </div>
</div>
<div class="card bg-white rounded-10 border border-white p-20"><div class="d-flex justify-content-end flex-wrap gap-3"><a href="<?= url('simrs-registration') ?>" class="btn btn-light erp-btn"><i class="ri-close-line me-1"></i>Batal</a><button class="btn btn-primary text-white erp-btn"><i class="ri-save-line me-1"></i>Daftarkan</button></div></div>
</form>
