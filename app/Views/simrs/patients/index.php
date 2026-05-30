<div class="card bg-white rounded-10 border border-white mb-4">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 p-20">
        <div>
            <h3 class="mb-0">Master Pasien</h3>
            <p class="text-body fs-14 mb-0">Data pasien dan nomor rekam medis.</p>
        </div>
        <a href="<?= url('simrs-patients-create') ?>" class="btn btn-primary text-white erp-btn">+ Tambah Pasien</a>
    </div>
    <div class="p-20 border-top">
        <form class="row g-2">
            <input type="hidden" name="page" value="simrs-patients">
            <div class="col-md-10"><input class="form-control" name="search" value="<?= htmlspecialchars($search ?? '') ?>" placeholder="Cari nama, RM, NIK, telepon"></div>
            <div class="col-md-2"><button class="btn btn-outline-primary w-100">Cari</button></div>
        </form>
    </div>
    <div class="default-table-area mx-minus-1">
        <div class="table-responsive">
            <table class="table align-middle">
                <thead><tr><th>No. RM</th><th>Nama</th><th>NIK</th><th>Kontak</th><th>Pembayaran</th><th>Status</th></tr></thead>
                <tbody>
                <?php foreach ($items as $item): ?>
                    <tr>
                        <td><a class="fw-semibold text-primary" href="<?= url('simrs-patients-show', ['id' => $item['id']]) ?>"><?= htmlspecialchars($item['medical_record_no']) ?></a></td>
                        <td><?= htmlspecialchars($item['name']) ?></td>
                        <td><?= htmlspecialchars($item['nik'] ?: '-') ?></td>
                        <td><?= htmlspecialchars($item['phone'] ?: '-') ?></td>
                        <td><?= htmlspecialchars(strtoupper($item['insurance_type'] ?? 'umum')) ?></td>
                        <td><?= simrsStatusBadge($item['status'] ?? '') ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($items)): ?><tr><td colspan="6" class="text-center py-4">Belum ada pasien.</td></tr><?php endif; ?>
                </tbody>
            </table>
        </div>
        <?= adminListFooter($baseRoute ?? 'simrs-patients', $search ?? '', $currentPage ?? 1, $totalPages ?? 1, $totalData ?? count($items ?? []), $limit ?? 15) ?>
    </div>
</div>
