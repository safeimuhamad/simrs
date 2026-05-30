<div class="card bg-white rounded-10 border border-white mb-4">
    <div class="p-20 border-bottom">
        <h3 class="mb-0">Laporan Ticket Lost</h3>
        <p class="text-body fs-14 mb-0">Daftar kendaraan dengan status tiket hilang.</p>
    </div>
    <div class="p-20 border-bottom">
        <form class="row g-2">
            <input type="hidden" name="page" value="parking-reports-lost-ticket">
            <div class="col-md-10">
                <input class="form-control" name="search" value="<?= htmlspecialchars($search ?? '') ?>" placeholder="Cari tiket, plat nomor, area, atau jenis kendaraan">
            </div>
            <div class="col-md-2"><button class="btn btn-outline-primary w-100">Cari</button></div>
        </form>
    </div>
    <div class="default-table-area mx-minus-1">
        <div class="table-responsive">
            <table class="table align-middle">
                <thead><tr><th>No Tiket</th><th>Plat Nomor</th><th>Jenis</th><th>Area</th><th>Masuk</th><th>Keluar</th><th class="text-end">Denda/Tagihan</th><th>Status Bayar</th></tr></thead>
                <tbody>
                    <?php foreach ($rows as $row): ?>
                        <tr>
                            <td><a class="fw-semibold text-primary" href="<?= url('parking-tickets-show', ['id' => $row['id']]) ?>"><?= htmlspecialchars($row['ticket_no']) ?></a></td>
                            <td class="fw-semibold"><?= htmlspecialchars($row['plate_number']) ?></td>
                            <td><?= htmlspecialchars($row['vehicle_type_name'] ?: '-') ?></td>
                            <td><?= htmlspecialchars($row['area_name'] ?: '-') ?></td>
                            <td><?= htmlspecialchars($row['entry_time']) ?></td>
                            <td><?= htmlspecialchars($row['exit_time'] ?: '-') ?></td>
                            <td class="text-end">Rp <?= number_format((float) $row['payable_amount'], 0, ',', '.') ?></td>
                            <td><?= simrsStatusBadge($row['payment_status'] ?? '', 'payment') ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($rows)): ?><tr><td colspan="8" class="text-center py-4">Belum ada ticket lost.</td></tr><?php endif; ?>
                </tbody>
            </table>
        </div>
        <?= adminListFooter($baseRoute ?? 'parking-reports-lost-ticket', $search ?? '', $currentPage ?? 1, $totalPages ?? 1, $totalData ?? count($rows ?? []), $limit ?? 10) ?>
    </div>
</div>
