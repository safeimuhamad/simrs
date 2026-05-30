<div class="card bg-white rounded-10 border border-white mb-4">
    <div class="p-20 border-bottom"><h3 class="mb-0">Laporan Kunjungan</h3></div>
    <div class="p-20 border-bottom">
        <form class="row g-2">
            <input type="hidden" name="page" value="simrs-reports-visits">
            <div class="col-md-3"><input type="date" class="form-control" name="from" value="<?= htmlspecialchars($from) ?>"></div>
            <div class="col-md-3"><input type="date" class="form-control" name="to" value="<?= htmlspecialchars($to) ?>"></div>
            <div class="col-md-2"><button class="btn btn-outline-primary w-100">Filter</button></div>
        </form>
    </div>
    <div class="default-table-area mx-minus-1"><div class="table-responsive"><table class="table align-middle"><thead><tr><th>Tanggal</th><th>Pasien</th><th>Poli</th><th>Dokter</th><th>Status</th></tr></thead><tbody>
        <?php foreach ($rows as $row): ?><tr><td><?= htmlspecialchars($row['visit_date']) ?></td><td><?= htmlspecialchars($row['patient_name']) ?><br><small><?= htmlspecialchars($row['medical_record_no']) ?></small></td><td><?= htmlspecialchars($row['polyclinic_name']) ?></td><td><?= htmlspecialchars($row['doctor_name'] ?: '-') ?></td><td><?= simrsStatusBadge($row['status'] ?? '') ?></td></tr><?php endforeach; ?>
        <?php if (empty($rows)): ?><tr><td colspan="5" class="text-center py-4">Tidak ada data.</td></tr><?php endif; ?>
    </tbody></table></div><?= adminListFooter($baseRoute ?? 'simrs-reports-visits', $search ?? '', $currentPage ?? 1, $totalPages ?? 1, $totalData ?? count($rows ?? []), $limit ?? 10) ?></div>
</div>
