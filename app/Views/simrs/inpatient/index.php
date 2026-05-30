<div class="card bg-white rounded-10 border border-white mb-4">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 p-20">
        <div>
            <h3 class="mb-0">Rawat Inap</h3>
            <p class="text-body fs-14 mb-0">Pantau pasien rawat inap, kamar, kelas, dan status perawatan.</p>
        </div>
    </div>
    <div class="p-20 border-top">
        <form class="row g-2 align-items-center">
            <input type="hidden" name="page" value="simrs-inpatient">
            <div class="col-md-8"><input class="form-control" name="search" value="<?= htmlspecialchars($search ?? '') ?>" placeholder="Cari pasien, no. RM, admisi, kamar"></div>
            <div class="col-md-2"><button class="btn btn-primary text-white w-100 erp-btn">Cari</button></div>
            <div class="col-md-2"><a class="btn btn-light w-100 erp-btn" href="<?= url('simrs-inpatient') ?>">Reset</a></div>
        </form>
    </div>
    <div class="default-table-area mx-minus-1">
        <div class="table-responsive">
            <table class="table align-middle">
                <thead><tr><th>No. Admisi</th><th>Pasien</th><th>Kamar / Kelas</th><th>Dokter</th><th>Tanggal Masuk</th><th>Status</th></tr></thead>
                <tbody>
                <?php foreach ($rows as $row): ?>
                    <tr>
                        <td class="fw-semibold"><?= htmlspecialchars($row['admission_no']) ?></td>
                        <td><?= htmlspecialchars($row['patient_name']) ?><br><small><?= htmlspecialchars($row['medical_record_no']) ?></small></td>
                        <td><?= htmlspecialchars($row['room_name']) ?> - <?= htmlspecialchars($row['bed_no']) ?><br><small><?= htmlspecialchars($row['class_name']) ?></small></td>
                        <td><?= htmlspecialchars($row['doctor_name'] ?: '-') ?></td>
                        <td><?= htmlspecialchars($row['admission_date']) ?></td>
                        <td><?= simrsStatusBadge($row['status'] ?? '', 'inpatient') ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($rows)): ?><tr><td colspan="6" class="text-center py-4">Belum ada data rawat inap.</td></tr><?php endif; ?>
                </tbody>
            </table>
        </div>
        <?= adminListFooter($baseRoute ?? 'simrs-inpatient', $search ?? '', $currentPage ?? 1, $totalPages ?? 1, $totalData ?? count($rows ?? []), $limit ?? 10) ?>
    </div>
</div>
