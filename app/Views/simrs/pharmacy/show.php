<?php if (!empty($_SESSION['error'])): ?><div class="alert alert-danger"><?= htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></div><?php endif; ?>

<div class="card bg-white rounded-10 border border-white mb-4">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 p-20">
        <div>
            <h3 class="mb-1"><?= htmlspecialchars($rx['prescription_no']) ?></h3>
            <p class="mb-0 text-body"><?= htmlspecialchars($rx['patient_name']) ?> | <?= htmlspecialchars($rx['medical_record_no']) ?> | <?= htmlspecialchars($rx['visit_no']) ?></p>
        </div>
        <?= simrsStatusBadge($rx['status'] ?? '', 'prescription') ?>
    </div>
    <div class="default-table-area mx-minus-1 border-top">
        <div class="table-responsive">
            <table class="table align-middle">
                <thead><tr><th>Obat</th><th>Dosis</th><th>Aturan</th><th class="text-end">Qty</th><th class="text-end">Stok</th><th class="text-end">Harga</th></tr></thead>
                <tbody>
                <?php foreach ($rx['items'] as $item): ?>
                    <?php $stockLow = !empty($item['product_id']) && (float)$item['current_stock'] < (float)$item['quantity']; ?>
                    <tr>
                        <td><?= htmlspecialchars($item['item_name']) ?><?= $stockLow ? ' <span class="text-danger">(stok kurang)</span>' : '' ?></td>
                        <td><?= htmlspecialchars($item['dosage'] ?: '-') ?></td>
                        <td><?= htmlspecialchars($item['frequency'] ?: '-') ?></td>
                        <td class="text-end"><?= number_format((float)$item['quantity'], 2, ',', '.') ?> <?= htmlspecialchars($item['unit_name']) ?></td>
                        <td class="text-end"><?= $item['product_id'] ? number_format((float)$item['current_stock'], 2, ',', '.') : '-' ?></td>
                        <td class="text-end">Rp <?= number_format((float)$item['price'], 0, ',', '.') ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="p-20 border-top d-flex gap-2 flex-wrap">
        <?php if ($rx['status'] === 'pending'): ?><a class="btn btn-outline-primary" href="<?= url('simrs-pharmacy-status', ['id' => $rx['id'], 'status' => 'verified']) ?>">Validasi</a><?php endif; ?>
        <?php if (in_array($rx['status'], ['pending','verified'], true)): ?><a class="btn btn-outline-info" href="<?= url('simrs-pharmacy-status', ['id' => $rx['id'], 'status' => 'prepared']) ?>">Tandai Siap</a><?php endif; ?>
        <?php if (in_array($rx['status'], ['pending','verified','prepared'], true)): ?>
            <form method="post" action="<?= url('simrs-pharmacy-dispense') ?>">
                <input type="hidden" name="prescription_id" value="<?= htmlspecialchars($rx['id']) ?>">
                <button class="btn btn-primary text-white">Serahkan Obat</button>
            </form>
        <?php endif; ?>
        <a class="btn btn-light" href="<?= url('simrs-pharmacy') ?>">Kembali</a>
    </div>
</div>
