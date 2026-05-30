<div class="card bg-white rounded-10 border border-white mb-4">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 p-20">
        <div>
            <h3 class="mb-0">Radiologi</h3>
            <p class="text-body fs-14 mb-0">Daftar permintaan pemeriksaan radiologi dan status hasil pasien.</p>
        </div>
    </div>
    <div class="p-20 border-top">
        <form class="row g-2 align-items-center">
            <input type="hidden" name="page" value="simrs-radiology">
            <div class="col-md-8"><input class="form-control" name="search" value="<?= htmlspecialchars($search ?? '') ?>" placeholder="Cari order, pasien, no. RM, pemeriksaan"></div>
            <div class="col-md-2"><button class="btn btn-primary text-white w-100 erp-btn">Cari</button></div>
            <div class="col-md-2"><a class="btn btn-light w-100 erp-btn" href="<?= url('simrs-radiology') ?>">Reset</a></div>
        </form>
    </div>
    <div class="default-table-area mx-minus-1">
        <div class="table-responsive">
            <table class="table align-middle">
                <thead><tr><th>No. Order</th><th>Pasien</th><th>Dokter</th><th>Pemeriksaan</th><th>Tanggal</th><th>Status</th></tr></thead>
                <tbody>
                <?php foreach ($rows as $row): ?>
                    <tr>
                        <td class="fw-semibold"><?= htmlspecialchars($row['order_no']) ?></td>
                        <td><?= htmlspecialchars($row['patient_name']) ?><br><small><?= htmlspecialchars($row['medical_record_no']) ?></small></td>
                        <td><?= htmlspecialchars($row['doctor_name'] ?: '-') ?></td>
                        <td><?= htmlspecialchars($row['examination']) ?></td>
                        <td><?= htmlspecialchars($row['order_date']) ?></td>
                        <td><?= simrsStatusBadge($row['status'] ?? '', 'order') ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($rows)): ?><tr><td colspan="6" class="text-center py-4">Belum ada order radiologi.</td></tr><?php endif; ?>
                </tbody>
            </table>
        </div>
        <?= adminListFooter($baseRoute ?? 'simrs-radiology', $search ?? '', $currentPage ?? 1, $totalPages ?? 1, $totalData ?? count($rows ?? []), $limit ?? 10) ?>
    </div>
</div>
