<div class="card bg-white rounded-10 border border-white mb-4">
    <div class="p-20 border-bottom"><h4 class="erp-detail-section-title mb-0">Validasi Parkir Pasien</h4></div>
    <form method="post" action="<?= url('parking-validations-store') ?>" class="p-20 row g-4">
        <div class="col-md-4"><label class="erp-detail-label">Tiket Aktif</label><select class="form-control" name="ticket_id" required><?php foreach ($tickets as $ticket): ?><option value="<?= $ticket['id'] ?>"><?= htmlspecialchars($ticket['ticket_no'] . ' - ' . $ticket['plate_number']) ?></option><?php endforeach; ?></select></div>
        <div class="col-md-4"><label class="erp-detail-label">Kunjungan Pasien</label><select class="form-control" name="patient_visit_id"><option value="">Manual / rawat inap</option><?php foreach ($visits as $visit): ?><option value="<?= $visit['id'] ?>"><?= htmlspecialchars($visit['visit_no'] . ' - ' . $visit['patient_name']) ?></option><?php endforeach; ?></select></div>
        <div class="col-md-4"><label class="erp-detail-label">Tipe Validasi</label><select class="form-control" name="validation_type"><option value="outpatient">Rawat Jalan</option><option value="inpatient">Rawat Inap</option><option value="emergency">Emergency</option><option value="manual">Manual</option></select></div>
        <div class="col-md-4"><label class="erp-detail-label">Diskon</label><select class="form-control" name="discount_type"><option value="free">Gratis</option><option value="percent">Persen</option><option value="amount">Nominal</option><option value="special_rate">Tarif Khusus</option></select></div>
        <div class="col-md-4"><label class="erp-detail-label">Nilai</label><input class="form-control" name="discount_value" value="0"></div>
        <div class="col-md-4"><label class="erp-detail-label">Catatan</label><input class="form-control" name="notes"></div>
        <div class="col-md-12 d-flex justify-content-end"><button class="btn btn-primary text-white erp-btn"><i class="ri-save-line me-1"></i>Simpan Validasi</button></div>
    </form>
    <?= adminListFooter($baseRoute ?? 'parking-validations', $search ?? '', $currentPage ?? 1, $totalPages ?? 1, $totalData ?? count($tickets ?? []), $limit ?? 10) ?>
</div>
