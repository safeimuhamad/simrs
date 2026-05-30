<div class="card bg-white rounded-10 border border-white mb-4">
    <div class="p-20 border-bottom"><h3 class="mb-0">Laporan Farmasi</h3></div>
    <div class="p-20 border-bottom">
        <form class="row g-2">
            <input type="hidden" name="page" value="simrs-reports-pharmacy">
            <div class="col-md-3"><input type="date" class="form-control" name="from" value="<?= htmlspecialchars($from) ?>"></div>
            <div class="col-md-3"><input type="date" class="form-control" name="to" value="<?= htmlspecialchars($to) ?>"></div>
            <div class="col-md-2"><button class="btn btn-outline-primary w-100">Filter</button></div>
        </form>
    </div>
    <div class="default-table-area mx-minus-1"><div class="table-responsive"><table class="table align-middle"><thead><tr><th>Waktu</th><th>No. Resep</th><th>Pasien</th><th>Status</th><th class="text-end">Total</th></tr></thead><tbody>
        <?php foreach ($rows as $row): ?><tr><td><?= htmlspecialchars($row['created_at']) ?></td><td><?= htmlspecialchars($row['prescription_no']) ?></td><td><?= htmlspecialchars($row['patient_name']) ?></td><td><?= simrsStatusBadge($row['status'] ?? '', 'prescription') ?></td><td class="text-end">Rp <?= number_format((float)$row['total_amount'], 0, ',', '.') ?></td></tr><?php endforeach; ?>
        <?php if (empty($rows)): ?><tr><td colspan="5" class="text-center py-4">Tidak ada data.</td></tr><?php endif; ?>
    </tbody></table></div><?= adminListFooter($baseRoute ?? 'simrs-reports-pharmacy', $search ?? '', $currentPage ?? 1, $totalPages ?? 1, $totalData ?? count($rows ?? []), $limit ?? 10) ?></div>
</div>
