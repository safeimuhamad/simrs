<div class="card bg-white rounded-10 border border-white mb-4">
    <div class="p-20 border-bottom">
        <h3 class="mb-0">Billing Pasien</h3>
        <p class="text-body fs-14 mb-0">Tagihan pasien yang belum lunas.</p>
    </div>
    <div class="p-20 border-bottom"><form class="row g-2"><input type="hidden" name="page" value="simrs-billing"><div class="col-md-10"><input class="form-control" name="search" value="<?= htmlspecialchars($search ?? '') ?>" placeholder="Cari billing/pasien"></div><div class="col-md-2"><button class="btn btn-outline-primary w-100">Cari</button></div></form></div>
    <div class="default-table-area mx-minus-1">
        <div class="table-responsive">
            <table class="table align-middle">
                <thead><tr><th>No. Billing</th><th>Pasien</th><th>Kunjungan</th><th>Status</th><th class="text-end">Total</th><th class="text-end">Dibayar</th></tr></thead>
                <tbody>
                <?php foreach ($billings as $billing): ?>
                    <tr>
                        <td><a class="fw-semibold text-primary" href="<?= url('simrs-billing-show', ['id' => $billing['id']]) ?>"><?= htmlspecialchars($billing['billing_no']) ?></a></td>
                        <td><?= htmlspecialchars($billing['patient_name'] ?? '-') ?><br><small class="text-body"><?= htmlspecialchars($billing['medical_record_no'] ?? '-') ?></small></td>
                        <td><?= htmlspecialchars($billing['visit_no'] ?? '-') ?></td>
                        <td><?= simrsStatusBadge($billing['status'] ?? '', 'billing') ?></td>
                        <td class="text-end">Rp <?= number_format((float)$billing['grand_total'], 0, ',', '.') ?></td>
                        <td class="text-end">Rp <?= number_format((float)$billing['paid_amount'], 0, ',', '.') ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($billings)): ?><tr><td colspan="6" class="text-center py-4">Tidak ada tagihan terbuka.</td></tr><?php endif; ?>
                </tbody>
            </table>
        </div>
        <?= adminListFooter($baseRoute ?? 'simrs-billing', $search ?? '', $currentPage ?? 1, $totalPages ?? 1, $totalData ?? count($billings ?? []), $limit ?? 10) ?>
    </div>
</div>
