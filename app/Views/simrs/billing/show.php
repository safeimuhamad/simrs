<div class="card bg-white rounded-10 border border-white mb-4">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 p-20">
        <div>
            <h3 class="mb-1"><?= htmlspecialchars($billing['billing_no']) ?></h3>
            <p class="mb-0 text-body"><?= htmlspecialchars($billing['patient_name']) ?> | <?= htmlspecialchars($billing['medical_record_no']) ?> | <?= htmlspecialchars($billing['visit_no']) ?></p>
        </div>
        <?= simrsStatusBadge($billing['status'] ?? '', 'billing') ?>
    </div>
    <div class="row g-4 p-20 border-top">
        <div class="col-lg-8">
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead><tr><th>Item</th><th class="text-end">Qty</th><th class="text-end">Harga</th><th class="text-end">Total</th></tr></thead>
                    <tbody>
                    <?php foreach ($billing['items'] as $item): ?>
                        <tr>
                            <td><?= htmlspecialchars($item['item_name']) ?></td>
                            <td class="text-end"><?= number_format((float)$item['quantity'], 2, ',', '.') ?></td>
                            <td class="text-end">Rp <?= number_format((float)$item['unit_price'], 0, ',', '.') ?></td>
                            <td class="text-end">Rp <?= number_format((float)$item['total_price'], 0, ',', '.') ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                    <tfoot><tr><th colspan="3" class="text-end">Grand Total</th><th class="text-end">Rp <?= number_format((float)$billing['grand_total'], 0, ',', '.') ?></th></tr></tfoot>
                </table>
            </div>
        </div>
        <div class="col-lg-4">
            <form method="post" action="<?= url('simrs-billing-add-item') ?>" class="border rounded p-3 mb-3">
                <input type="hidden" name="billing_id" value="<?= htmlspecialchars($billing['id']) ?>">
                <h5>Tambah Item Manual</h5>
                <input class="form-control mb-2" name="item_name" required placeholder="Nama item/tindakan">
                <input class="form-control mb-2" name="quantity" value="1" placeholder="Qty">
                <input class="form-control mb-2" name="unit_price" value="0" placeholder="Harga">
                <button class="btn btn-outline-primary w-100">Tambah</button>
            </form>
            <div class="border rounded p-3">
                <h5>Pembayaran</h5>
                <?php foreach ($billing['payments'] as $payment): ?>
                    <div class="d-flex justify-content-between border-bottom py-2">
                        <span><?= htmlspecialchars($payment['payment_no']) ?></span>
                        <strong>Rp <?= number_format((float)$payment['amount'], 0, ',', '.') ?></strong>
                    </div>
                <?php endforeach; ?>
                <?php if (empty($billing['payments'])): ?><p class="text-body mb-0">Belum ada pembayaran.</p><?php endif; ?>
            </div>
        </div>
    </div>
</div>
