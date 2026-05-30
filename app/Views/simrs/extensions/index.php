<div class="card bg-white rounded-10 border border-white mb-4">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 p-20">
        <div>
            <h3 class="mb-0"><?= htmlspecialchars($module['title']) ?></h3>
            <p class="text-body fs-14 mb-0"><?= htmlspecialchars($module['subtitle']) ?></p>
        </div>
        <a href="<?= url($module['route'] . '-create') ?>" class="btn btn-primary text-white erp-btn">
            <span class="material-symbols-outlined align-middle me-1">add</span>
            Tambah Data
        </a>
    </div>

    <div class="p-20 border-top">
        <form class="row g-2 align-items-center">
            <input type="hidden" name="page" value="<?= htmlspecialchars($module['route']) ?>">
            <div class="col-md-5">
                <input class="form-control" name="search" value="<?= htmlspecialchars($search ?? '') ?>" placeholder="Cari nomor, pasien, subjek, lokasi, catatan">
            </div>
            <div class="col-md-3">
                <select class="form-control" name="module_type">
                    <option value="">Semua Tipe</option>
                    <?php foreach ($module['types'] as $type): ?>
                        <option value="<?= htmlspecialchars($type) ?>" <?= ($_GET['module_type'] ?? '') === $type ? 'selected' : '' ?>><?= htmlspecialchars($type) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2">
                <select class="form-control" name="status">
                    <option value="">Semua Status</option>
                    <?php foreach ($module['statuses'] as $status): ?>
                        <option value="<?= htmlspecialchars($status) ?>" <?= ($_GET['status'] ?? '') === $status ? 'selected' : '' ?>><?= htmlspecialchars(ucwords(str_replace('_', ' ', $status))) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-1"><button class="btn btn-primary text-white w-100 erp-btn">Cari</button></div>
            <div class="col-md-1"><a class="btn btn-light w-100 erp-btn" href="<?= url($module['route']) ?>">Reset</a></div>
        </form>
    </div>

    <div class="default-table-area mx-minus-1">
        <div class="table-responsive">
            <table class="table align-middle">
                <thead>
                    <tr>
                        <th>No. Dokumen</th>
                        <th>Pasien / Kunjungan</th>
                        <th>Tipe</th>
                        <th>Subjek</th>
                        <th>Tanggal</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                <?php foreach ($rows as $row): ?>
                    <tr>
                        <td>
                            <a class="fw-semibold text-primary" href="<?= url($module['route'] . '-show', ['id' => $row['id']]) ?>">
                                <?= htmlspecialchars($row['record_no']) ?>
                            </a>
                            <?php if (!empty($row['reference_no'])): ?><br><small><?= htmlspecialchars($row['reference_no']) ?></small><?php endif; ?>
                        </td>
                        <td>
                            <?= htmlspecialchars($row['patient_name'] ?: '-') ?>
                            <br><small><?= htmlspecialchars(trim(($row['medical_record_no'] ?? '') . ' ' . ($row['visit_no'] ?? '')) ?: '-') ?></small>
                        </td>
                        <td><?= htmlspecialchars($row['module_type'] ?: '-') ?></td>
                        <td>
                            <?= htmlspecialchars($row['subject'] ?: '-') ?>
                            <?php if (!empty($row['location'])): ?><br><small><?= htmlspecialchars($row['location']) ?></small><?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars(date('d/m/Y H:i', strtotime($row['record_date'] ?? $row['created_at']))) ?></td>
                        <td><?= simrsStatusBadge($row['status'] ?? '') ?></td>
                        <td><a class="btn btn-sm btn-outline-primary" href="<?= url($module['route'] . '-edit', ['id' => $row['id']]) ?>">Update</a></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($rows)): ?>
                    <tr><td colspan="7" class="text-center py-4">Belum ada data.</td></tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
        <?= adminListFooter($baseRoute ?? $module['route'], $search ?? '', $currentPage ?? 1, $totalPages ?? 1, $totalData ?? count($rows ?? []), $limit ?? 10) ?>
    </div>
</div>
