<form method="post" action="<?= url('parking-checkin-store') ?>">
    <div class="card bg-white rounded-10 border border-white mb-4">
        <div class="p-20 border-bottom"><h4 class="erp-detail-section-title mb-0">Check-in Kendaraan</h4></div>
        <div class="p-20 row g-4">
            <div class="col-md-4"><label class="erp-detail-label">Plat Nomor / LPR</label><input class="form-control text-uppercase" name="plate_number" required placeholder="B 1234 XYZ"></div>
            <div class="col-md-4"><label class="erp-detail-label">Jenis Kendaraan</label><select class="form-control" name="vehicle_type_id" required><?php foreach ($vehicleTypes as $type): ?><option value="<?= $type['id'] ?>"><?= htmlspecialchars($type['name']) ?></option><?php endforeach; ?></select></div>
            <div class="col-md-4"><label class="erp-detail-label">Area Parkir</label><select class="form-control" name="area_id"><?php foreach ($areas as $area): ?><option value="<?= $area['id'] ?>"><?= htmlspecialchars($area['name']) ?></option><?php endforeach; ?></select></div>
            <div class="col-md-4"><label class="erp-detail-label">Gate Masuk</label><select class="form-control" name="entry_gate_id"><?php foreach ($entryGates as $gate): ?><option value="<?= $gate['id'] ?>"><?= htmlspecialchars($gate['name']) ?></option><?php endforeach; ?></select></div>
            <div class="col-md-4"><label class="erp-detail-label">Waktu Masuk</label><input type="datetime-local" class="form-control" name="entry_time" value="<?= date('Y-m-d\TH:i') ?>"></div>
            <div class="col-md-4"><label class="erp-detail-label">Sumber</label><select class="form-control" name="source"><option value="manual">Manual</option><option value="lpr">LPR Camera</option><option value="qr">QR</option><option value="member">Member</option></select></div>
            <div class="col-md-6"><label class="erp-detail-label">Hubungkan Kunjungan Pasien</label><select class="form-control" name="patient_visit_id"><option value="">Tidak ada</option><?php foreach ($visits as $visit): ?><option value="<?= $visit['id'] ?>"><?= htmlspecialchars($visit['visit_no'] . ' - ' . $visit['patient_name']) ?></option><?php endforeach; ?></select></div>
            <div class="col-md-6"><label class="erp-detail-label">Catatan</label><input class="form-control" name="notes"></div>
        </div>
    </div>
    <div class="card bg-white rounded-10 border border-white p-20"><div class="d-flex justify-content-end flex-wrap gap-3"><a href="<?= url('parking-tickets') ?>" class="btn btn-light erp-btn"><i class="ri-close-line me-1"></i>Batal</a><button class="btn btn-primary text-white erp-btn"><i class="ri-save-line me-1"></i>Buat Tiket & Open Gate</button></div></div>
</form>
