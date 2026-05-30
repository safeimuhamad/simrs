<div class="card bg-white rounded-10 border border-white mb-4">
    <div class="p-20 border-bottom"><h3 class="mb-0">Laporan Pendapatan</h3></div>
    <div class="p-20 border-bottom">
        <form class="row g-2">
            <input type="hidden" name="page" value="simrs-reports-income">
            <div class="col-md-3"><input type="date" class="form-control" name="from" value="<?= htmlspecialchars($from) ?>"></div>
            <div class="col-md-3"><input type="date" class="form-control" name="to" value="<?= htmlspecialchars($to) ?>"></div>
            <div class="col-md-2"><button class="btn btn-outline-primary w-100">Filter</button></div>
        </form>
    </div>
    <div class="default-table-area mx-minus-1"><div class="table-responsive"><table class="table align-middle"><thead><tr><th>Tanggal</th><th>No. Billing</th><th>Pasien</th><th>Metode</th><th class="text-end">Jumlah</th></tr></thead><tbody>
        <?php $total = 0; foreach ($rows as $row): $total += (float)$row['amount']; ?><tr><td><?= htmlspecialchars($row['payment_date']) ?></td><td><?= htmlspecialchars($row['billing_no']) ?></td><td><?= htmlspecialchars($row['patient_name']) ?></td><td><?= htmlspecialchars($row['payment_method']) ?></td><td class="text-end">Rp <?= number_format((float)$row['amount'], 0, ',', '.') ?></td></tr><?php endforeach; ?>
        <tr><th colspan="4" class="text-end">Total</th><th class="text-end">Rp <?= number_format($total, 0, ',', '.') ?></th></tr>
    </tbody></table></div><?= adminListFooter($baseRoute ?? 'simrs-reports-income', $search ?? '', $currentPage ?? 1, $totalPages ?? 1, $totalData ?? count($rows ?? []), $limit ?? 10) ?></div>
</div>
