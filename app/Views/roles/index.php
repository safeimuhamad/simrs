<div class="card bg-white rounded-10 border border-white mb-4">

    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 p-20">

        <div>
            <h3 class="mb-0">
                Role Management
            </h3>

            <p class="text-body fs-14 mb-0">
                Data role dan pengaturan akses pengguna sistem
            </p>
        </div>

        <?php if (can('role.create')): ?>
            <a
                href="<?= url('roles-create') ?>"
                class="btn btn-primary text-white erp-btn"
            >
                + Tambah Role
            </a>
        <?php endif; ?>

    </div>

    <div class="p-20 border-top">
        <form class="row g-2" method="GET">
            <input type="hidden" name="page" value="roles">
            <div class="col-md-6">
                <input class="form-control" name="search" value="<?= htmlspecialchars($search ?? '') ?>" placeholder="Cari role/deskripsi">
            </div>
            <div class="col-md-2">
                <select class="form-select" name="status">
                    <option value="">Semua Status</option>
                    <option value="active" <?= (($status ?? '') === 'active') ? 'selected' : '' ?>>Aktif</option>
                    <option value="inactive" <?= (($status ?? '') === 'inactive') ? 'selected' : '' ?>>Nonaktif</option>
                </select>
            </div>
            <div class="col-md-4">
                <button class="btn btn-primary text-white erp-btn" type="submit">Cari</button>
                <a class="btn btn-light erp-btn simrs-filter-reset" href="<?= url('roles') ?>">Reset</a>
            </div>
        </form>
    </div>

    <div class="default-table-area mx-minus-1">

        <div class="table-responsive">

            <table class="table align-middle">

                <thead>
                    <tr>
                        <th>Role</th>
                        <th style="min-width:220px;">Deskripsi</th>
                        <th>Status</th>
                        <th class="text-end">Aksi</th>
                    </tr>
                </thead>

                <tbody>

                    <?php if (!empty($roles)): ?>

                        <?php foreach ($roles as $role): ?>

                            <?php
                            $status = strtolower($role['status'] ?? 'active');

                            $statusClass = $status === 'active'
                                ? 'bg-success bg-opacity-10 text-success'
                                : 'bg-secondary bg-opacity-10 text-secondary';

                            $statusLabel = $status === 'active'
                                ? 'Aktif'
                                : 'Nonaktif';
                            ?>

                            <tr>

                                <td>
                                    <a
                                        href="<?= url('roles-edit') ?>?id=<?= $role['id'] ?>"
                                        class="fw-semibold text-primary text-decoration-none"
                                    >
                                        <?= htmlspecialchars($role['name'] ?? '-') ?>
                                    </a>
                                </td>

                                <td class="text-wrap" style="min-width:220px; max-width:420px;">
                                    <?= htmlspecialchars($role['description'] ?? '-') ?>
                                </td>

                                <td>
                                    <span class="default-badge <?= $statusClass ?>">
                                        <?= $statusLabel ?>
                                    </span>
                                </td>

                                <td class="text-end">
                                    <?php if (can('role.permission')): ?>
                                        <a
                                            href="<?= url('roles-permissions', ['id' => $role['id']]) ?>"
                                            class="btn btn-light btn-sm"
                                        >
                                            Hak Akses
                                        </a>
                                    <?php endif; ?>
                                </td>

                            </tr>

                        <?php endforeach; ?>

                    <?php else: ?>

                            <tr>
                                <td colspan="4" class="text-center text-body py-4">
                                Belum ada data role.
                            </td>
                        </tr>

                    <?php endif; ?>

                </tbody>

            </table>

        </div>

        <?= adminListFooter('roles', $search ?? '', $currentPage ?? 1, $totalPages ?? 1, $totalData ?? 0, $limit ?? 10) ?>

    </div>

</div>
