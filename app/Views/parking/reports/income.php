<div class="card bg-white rounded-10 border border-white mb-4">
    <div class="p-20 border-bottom">
        <h3 class="mb-0">Laporan Pendapatan Parkir</h3>
        <p class="text-body fs-14 mb-0">Rekap pendapatan parkir berdasarkan tanggal pembayaran.</p>
    </div>
    <div class="p-20 border-bottom">
        <form class="row g-2">
            <input type="hidden" name="page" value="parking-reports-income">
            <div class="col-md-3"><input type="date" class="form-control" name="from" value="<?= htmlspecialchars($from) ?>"></div>
            <div class="col-md-3"><input type="date" class="form-control" name="to" value="<?= htmlspecialchars($to) ?>"></div>
            <div class="col-md-2"><button class="btn btn-outline-primary w-100">Filter</button></div>
        </form>
    </div>
    <div class="default-table-area mx-minus-1">
        <div class="table-responsive">
            <table class="table align-middle">
                <thead><tr><th>Tanggal</th><th class="text-end">Transaksi</th><th class="text-end">Cash</th><th class="text-end">Non-Cash</th><th class="text-end">Total Pendapatan</th></tr></thead>
                <tbody>
                    <?php $grandTotal = 0; foreach ($rows as $row): $grandTotal += (float) $row['total_income']; ?>
                        <tr>
                            <td><?= htmlspecialchars($row['payment_date']) ?></td>
                            <td class="text-end"><?= number_format((int) $row['total_transactions'], 0, ',', '.') ?></td>
                            <td class="text-end">Rp <?= number_format((float) $row['cash_income'], 0, ',', '.') ?></td>
                            <td class="text-end">Rp <?= number_format((float) $row['non_cash_income'], 0, ',', '.') ?></td>
                            <td class="text-end fw-semibold">Rp <?= number_format((float) $row['total_income'], 0, ',', '.') ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($rows)): ?><tr><td colspan="5" class="text-center py-4">Belum ada pendapatan pada periode ini.</td></tr><?php endif; ?>
                    <tr><th colspan="4" class="text-end">Total</th><th class="text-end">Rp <?= number_format($grandTotal, 0, ',', '.') ?></th></tr>
                </tbody>
            </table>
        </div>
        <?= adminListFooter($baseRoute ?? 'parking-reports-income', $search ?? '', $currentPage ?? 1, $totalPages ?? 1, $totalData ?? count($rows ?? []), $limit ?? 10) ?>
    </div>
</div>
