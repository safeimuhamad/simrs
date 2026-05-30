<div class="card bg-white rounded-10 border border-white mb-4">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 p-20">
        <div><h3 class="mb-0"><?= htmlspecialchars($config['title']) ?></h3><p class="text-body fs-14 mb-0">Data master Parking Management.</p></div>
        <a href="<?= url($config['route'] . '-create') ?>" class="btn btn-primary text-white erp-btn"><span class="material-symbols-outlined">add</span> Tambah</a>
    </div>
    <div class="p-20 border-top">
        <form class="row g-2">
            <input type="hidden" name="page" value="<?= htmlspecialchars($config['route']) ?>">
            <div class="col-md-10"><input class="form-control" name="search" value="<?= htmlspecialchars($search ?? '') ?>" placeholder="Cari nama/kode"></div>
            <div class="col-md-2"><button class="btn btn-outline-primary w-100">Cari</button></div>
        </form>
    </div>
    <div class="default-table-area mx-minus-1"><div class="table-responsive">
        <table class="table align-middle">
            <thead><tr><?php foreach ($config['fields'] as $label): ?><th><?= htmlspecialchars($label) ?></th><?php endforeach; ?><th class="text-end">Aksi</th></tr></thead>
            <tbody>
            <?php foreach ($items as $item): ?>
                <tr>
                    <?php $first = true; foreach ($config['fields'] as $field => $label): ?>
                        <td>
                            <?php if ($first): $first = false; ?>
                                <a class="fw-semibold text-primary" href="<?= url($config['route'] . '-edit', ['id' => $item['id']]) ?>"><?= htmlspecialchars((string)($item[$field] ?? '-')) ?></a>
                            <?php elseif ($field === 'status'): ?>
                                <?= simrsStatusBadge($item[$field] ?? '') ?>
                            <?php else: ?>
                                <?= htmlspecialchars((string)($item[$field] ?? '-')) ?>
                            <?php endif; ?>
                        </td>
                    <?php endforeach; ?>
                    <td class="text-end">
                        <a class="btn btn-sm btn-light" href="<?= url($config['route'] . '-edit', ['id' => $item['id']]) ?>">Edit</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            <?php if (empty($items)): ?><tr><td colspan="<?= count($config['fields']) + 1 ?>" class="text-center py-4">Belum ada data.</td></tr><?php endif; ?>
            </tbody>
        </table>
    </div><?= adminListFooter($baseRoute ?? $config['route'], $search ?? '', $currentPage ?? 1, $totalPages ?? 1, $totalData ?? count($items ?? []), $limit ?? 10) ?></div>
</div>
