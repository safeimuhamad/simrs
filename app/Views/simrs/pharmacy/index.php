<?php if (!empty($_SESSION['success'])): ?><div class="alert alert-success"><?= htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?></div><?php endif; ?>
<?php if (!empty($_SESSION['error'])): ?><div class="alert alert-danger"><?= htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></div><?php endif; ?>

<div class="row g-4 mb-4">
    <div class="col-lg-8">
        <div class="card bg-white rounded-10 border border-white h-100">
            <div class="p-20 border-bottom">
                <h3 class="mb-0">Farmasi</h3>
                <p class="text-body fs-14 mb-0">Validasi resep, siapkan obat, dan serah obat.</p>
            </div>
            <div class="p-20 border-bottom"><form class="row g-2"><input type="hidden" name="page" value="simrs-pharmacy"><div class="col-md-10"><input class="form-control" name="search" value="<?= htmlspecialchars($search ?? '') ?>" placeholder="Cari resep/pasien"></div><div class="col-md-2"><button class="btn btn-outline-primary w-100">Cari</button></div></form></div>
            <div class="default-table-area mx-minus-1">
                <div class="table-responsive">
                    <table class="table align-middle">
                        <thead><tr><th>No. Resep</th><th>Pasien</th><th>Kunjungan</th><th>Dokter</th><th>Status</th><th></th></tr></thead>
                        <tbody>
                        <?php foreach ($prescriptions as $rx): ?>
                            <tr>
                                <td><a class="fw-semibold text-primary" href="<?= url('simrs-pharmacy-show', ['id' => $rx['id']]) ?>"><?= htmlspecialchars($rx['prescription_no']) ?></a></td>
                                <td><?= htmlspecialchars($rx['patient_name'] ?? '-') ?><br><small class="text-body"><?= htmlspecialchars($rx['medical_record_no'] ?? '-') ?></small></td>
                                <td><?= htmlspecialchars($rx['visit_no'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($rx['doctor_name'] ?? '-') ?></td>
                                <td><?= simrsStatusBadge($rx['status'] ?? '', 'prescription') ?></td>
                                <td class="text-end"><a class="btn btn-sm btn-outline-primary" href="<?= url('simrs-pharmacy-show', ['id' => $rx['id']]) ?>">Proses</a></td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (empty($prescriptions)): ?><tr><td colspan="6" class="text-center py-4">Belum ada resep pending.</td></tr><?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <?= adminListFooter($baseRoute ?? 'simrs-pharmacy', $search ?? '', $currentPage ?? 1, $totalPages ?? 1, $totalData ?? count($prescriptions ?? []), $limit ?? 10) ?>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card bg-white rounded-10 border border-white h-100">
            <div class="p-20 border-bottom"><h4 class="mb-0">Stok Obat Kritis</h4></div>
            <div class="p-20">
                <?php foreach ($lowStock as $stock): ?>
                    <?php $gap = max(0, (float) $stock['minimum_stock'] - (float) $stock['current_stock']); ?>
                    <div class="d-flex justify-content-between align-items-start gap-3 border-bottom py-2">
                        <div>
                            <span class="fw-semibold"><?= htmlspecialchars($stock['name']) ?></span><br>
                            <small class="text-body"><?= htmlspecialchars($stock['sku'] ?? '-') ?> | Min: <?= number_format((float)$stock['minimum_stock'], 0, ',', '.') ?> <?= htmlspecialchars($stock['unit_name'] ?? '') ?></small>
                        </div>
                        <div class="text-end">
                            <span class="default-badge bg-danger bg-opacity-10 text-danger"><?= number_format((float)$stock['current_stock'], 0, ',', '.') ?></span><br>
                            <small class="text-danger">Kurang <?= number_format($gap, 0, ',', '.') ?></small>
                        </div>
                    </div>
                <?php endforeach; ?>
                <?php if (empty($lowStock)): ?><p class="text-body mb-0">Tidak ada stok kritis.</p><?php endif; ?>
            </div>
        </div>
    </div>
</div>
