<?php if (!empty($_SESSION['success'])): ?><div class="alert alert-success"><?= htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?></div><?php endif; ?>
<?php if (!empty($_SESSION['error'])): ?><div class="alert alert-danger"><?= htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></div><?php endif; ?>

<div class="card bg-white rounded-10 border border-white mb-4">
    <div class="p-20 border-bottom">
        <h3 class="mb-0">Kasir Pasien</h3>
        <p class="text-body fs-14 mb-0">Terima pembayaran pasien umum/asuransi tahap awal.</p>
    </div>
    <div class="p-20 border-bottom"><form class="row g-2"><input type="hidden" name="page" value="simrs-cashier"><div class="col-md-10"><input class="form-control" name="search" value="<?= htmlspecialchars($search ?? '') ?>" placeholder="Cari billing/pasien"></div><div class="col-md-2"><button class="btn btn-outline-primary w-100">Cari</button></div></form></div>
    <div class="p-20">
        <?php foreach ($billings as $billing): ?>
            <?php $remaining = max(0, (float)$billing['grand_total'] - (float)$billing['paid_amount']); ?>
            <form method="post" action="<?= url('simrs-cashier-pay') ?>" class="border rounded p-3 mb-3">
                <input type="hidden" name="billing_id" value="<?= htmlspecialchars($billing['id']) ?>">
                <div class="row g-3 align-items-end">
                    <div class="col-lg-3">
                        <strong><?= htmlspecialchars($billing['billing_no']) ?></strong><br>
                        <span><?= htmlspecialchars($billing['patient_name']) ?></span><br>
                        <small class="text-body"><?= htmlspecialchars($billing['visit_no']) ?></small>
                    </div>
                    <div class="col-lg-2"><label>Sisa</label><input class="form-control" readonly value="Rp <?= number_format($remaining, 0, ',', '.') ?>"></div>
                    <div class="col-lg-2"><label>Tanggal</label><input type="date" class="form-control" name="payment_date" value="<?= date('Y-m-d') ?>"></div>
                    <div class="col-lg-2"><label>Metode</label><select class="form-control" name="payment_method"><option value="cash">Cash</option><option value="transfer">Transfer</option><option value="debit">Debit</option><option value="qris">QRIS</option></select></div>
                    <div class="col-lg-2"><label>Jumlah</label><input class="form-control" name="amount" value="<?= number_format($remaining, 0, ',', '.') ?>"></div>
                    <div class="col-lg-1"><button class="btn btn-primary text-white w-100">Bayar</button></div>
                    <div class="col-lg-12"><input class="form-control" name="reference_no" placeholder="No referensi / catatan"></div>
                </div>
            </form>
        <?php endforeach; ?>
        <?php if (empty($billings)): ?><p class="text-center text-body py-4 mb-0">Tidak ada tagihan untuk dibayar.</p><?php endif; ?>
    </div>
    <?= adminListFooter($baseRoute ?? 'simrs-cashier', $search ?? '', $currentPage ?? 1, $totalPages ?? 1, $totalData ?? count($billings ?? []), $limit ?? 10) ?>
</div>
