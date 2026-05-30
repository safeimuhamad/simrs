<div class="card bg-white rounded-10 border border-white mb-4">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 p-20">
        <div>
            <h3 class="mb-1">Inventori Farmasi</h3>
            <p class="text-body fs-14 mb-0">Data obat dan alkes yang dipakai resep serta stok farmasi.</p>
        </div>
        <a href="<?= url('simrs-medical-items-create') ?>" class="btn btn-primary text-white erp-btn">
            <i class="ri-add-line me-1"></i> Tambah Item
        </a>
    </div>

    <form method="GET" class="px-20 pb-20">
        <input type="hidden" name="page" value="simrs-medical-items">
        <div class="row g-3 align-items-end">
            <div class="col-md-5">
                <label class="erp-detail-label">Cari item</label>
                <input type="text" name="search" class="form-control" value="<?= htmlspecialchars($search ?? '') ?>" placeholder="Nama, SKU, kategori">
            </div>
            <div class="col-md-auto">
                <button type="submit" class="btn btn-primary text-white erp-btn">Cari</button>
            </div>
        </div>
    </form>

    <div class="default-table-area mx-minus-1">
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>Item</th>
                        <th>Kategori</th>
                        <th>Satuan</th>
                        <th class="text-end">Stok</th>
                        <th class="text-end">Minimum</th>
                        <th class="text-end">Harga</th>
                        <th>Status</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($items)): ?>
                        <?php foreach ($items as $item): ?>
                            <?php $isCritical = (float) ($item['current_stock'] ?? 0) <= (float) ($item['minimum_stock'] ?? 0); ?>
                            <tr>
                                <td>
                                    <a class="fw-semibold text-primary text-decoration-none" href="<?= url('simrs-medical-items-edit', ['id' => $item['id']]) ?>">
                                        <?= htmlspecialchars($item['name'] ?? '-') ?>
                                    </a>
                                    <div class="fs-13 text-body"><?= htmlspecialchars($item['sku'] ?? '-') ?></div>
                                </td>
                                <td><?= htmlspecialchars(ucfirst($item['category'] ?? '-')) ?></td>
                                <td><?= htmlspecialchars($item['unit_name'] ?? '-') ?></td>
                                <td class="text-end <?= $isCritical ? 'text-danger fw-semibold' : '' ?>">
                                    <?= number_format((float) ($item['current_stock'] ?? 0), 2, ',', '.') ?>
                                </td>
                                <td class="text-end"><?= number_format((float) ($item['minimum_stock'] ?? 0), 2, ',', '.') ?></td>
                                <td class="text-end">Rp <?= number_format((float) ($item['unit_price'] ?? 0), 0, ',', '.') ?></td>
                                <td><?= simrsStatusBadge($item['status'] ?? '') ?></td>
                                <td class="text-end">
                                    <a href="<?= url('simrs-medical-items-edit', ['id' => $item['id']]) ?>" class="btn btn-sm btn-light">Edit</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr><td colspan="8" class="text-center text-body py-4">Belum ada item farmasi.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?= adminListFooter('simrs-medical-items', $search ?? '', $currentPage ?? 1, $totalPages ?? 1, $totalData ?? 0, $limit ?? 10) ?>
    </div>
</div>
