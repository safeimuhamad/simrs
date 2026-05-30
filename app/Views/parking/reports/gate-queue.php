<div class="card bg-white rounded-10 border border-white mb-4">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 p-20 border-bottom">
        <div>
            <h3 class="mb-0">Antrean Gate</h3>
            <p class="text-body fs-14 mb-0">Pantau kendaraan aktif, belum bayar, dan ticket lost di gate.</p>
        </div>
        <a href="<?= url('parking-checkin') ?>" class="btn btn-primary text-white erp-btn">
            <span class="material-symbols-outlined">add</span> Check-In
        </a>
    </div>
    <div class="p-20 border-bottom">
        <form class="row g-2">
            <input type="hidden" name="page" value="parking-gate-queue">
            <div class="col-md-10">
                <input class="form-control" name="search" value="<?= htmlspecialchars($search ?? '') ?>" placeholder="Cari tiket, plat nomor, area, atau jenis kendaraan">
            </div>
            <div class="col-md-2">
                <button class="btn btn-outline-primary w-100">Cari</button>
            </div>
        </form>
    </div>
    <div class="default-table-area mx-minus-1">
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>No Tiket</th>
                        <th>Plat Nomor</th>
                        <th>Jenis</th>
                        <th>Area</th>
                        <th>Masuk</th>
                        <th class="text-end">Durasi</th>
                        <th>Status</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rows as $row): ?>
                        <tr>
                            <td><a class="fw-semibold text-primary" href="<?= url('parking-tickets-show', ['id' => $row['id']]) ?>"><?= htmlspecialchars($row['ticket_no']) ?></a></td>
                            <td class="fw-semibold"><?= htmlspecialchars($row['plate_number']) ?></td>
                            <td><?= htmlspecialchars($row['vehicle_type_name'] ?: '-') ?></td>
                            <td><?= htmlspecialchars($row['area_name'] ?: '-') ?></td>
                            <td><?= htmlspecialchars($row['entry_time']) ?></td>
                            <td class="text-end"><?= floor(((int) $row['duration_minutes']) / 60) ?>j <?= ((int) $row['duration_minutes']) % 60 ?>m</td>
                            <td><?= simrsStatusBadge($row['status'] ?? '', 'parking') ?></td>
                            <td class="text-end"><a class="btn btn-sm btn-outline-primary" href="<?= url('parking-tickets-show', ['id' => $row['id']]) ?>">Proses</a></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($rows)): ?><tr><td colspan="8" class="text-center py-4">Tidak ada antrean gate.</td></tr><?php endif; ?>
                </tbody>
            </table>
        </div>
        <?= adminListFooter($baseRoute ?? 'parking-gate-queue', $search ?? '', $currentPage ?? 1, $totalPages ?? 1, $totalData ?? count($rows ?? []), $limit ?? 10) ?>
    </div>
</div>
