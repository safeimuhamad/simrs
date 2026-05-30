<?php if (!empty($_SESSION['success'])): ?>
    <div class="alert alert-success">
        <?= $_SESSION['success'];
        unset($_SESSION['success']); ?>
    </div>
<?php endif; ?>

<div class="card bg-white rounded-10 border border-white mb-4">

    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3 p-20">

        <div>

            <h3 class="mb-0">
                Master User
            </h3>

            <p class="text-body fs-14 mb-0">
                Data pengguna, role, dan hak akses sistem
            </p>

        </div>

        <a
            href="<?= url('users-create') ?>"
            class="btn btn-primary text-white erp-btn"
        >
            + Tambah User
        </a>

    </div>

    <?php if (!empty($_SESSION['error'])): ?>

        <div class="alert alert-danger m-20">
            <?= $_SESSION['error'];
            unset($_SESSION['error']); ?>
        </div>

    <?php endif; ?>

    <div class="p-20 border-top">
        <form class="row g-2" method="GET">
            <input type="hidden" name="page" value="users">
            <div class="col-md-6">
                <input class="form-control" name="search" value="<?= htmlspecialchars($search ?? '') ?>" placeholder="Cari nama/email/role">
            </div>
            <div class="col-md-2">
                <select class="form-select" name="status">
                    <option value="">Semua Status</option>
                    <option value="active" <?= (($status ?? '') === 'active') ? 'selected' : '' ?>>Aktif</option>
                    <option value="pending" <?= (($status ?? '') === 'pending') ? 'selected' : '' ?>>Pending</option>
                    <option value="inactive" <?= (($status ?? '') === 'inactive') ? 'selected' : '' ?>>Nonaktif</option>
                </select>
            </div>
            <div class="col-md-4">
                <button class="btn btn-primary text-white erp-btn" type="submit">Cari</button>
                <a class="btn btn-light erp-btn simrs-filter-reset" href="<?= url('users') ?>">Reset</a>
            </div>
        </form>
    </div>

    <div class="default-table-area mx-minus-1">

        <div class="table-responsive">

            <table class="table align-middle">

                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Data Akses</th>
                        <th>Status</th>
                        <th>Last Login</th>
                    </tr>
                </thead>

                <tbody>

                    <?php if (!empty($users)): ?>

                        <?php foreach ($users as $user): ?>

                            <?php
                            $status = strtolower($user['status'] ?? 'active');

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
                                        href="<?= url('users-edit') ?>?id=<?= $user['id'] ?>"
                                        class="fw-semibold text-primary text-decoration-none"
                                    >
                                        <?= htmlspecialchars($user['name'] ?? '-') ?>
                                    </a>

                                </td>

                                <td>
                                    <?= htmlspecialchars($user['username'] ?? '-') ?>
                                </td>

                                <td>
                                    <?= htmlspecialchars($user['email'] ?? '-') ?>
                                </td>

                                <td>

                                    <span class="default-badge bg-primary bg-opacity-10 text-primary">

                                        <?= htmlspecialchars($user['role_name'] ?? '-') ?>

                                    </span>

                                </td>

                                <td>
                                    <?= htmlspecialchars($user['data_scope'] ?? '-') ?>
                                </td>

                                <td>

                                    <span class="default-badge <?= $statusClass ?>">

                                        <?= $statusLabel ?>

                                    </span>

                                </td>

                                <td>

                                    <?= !empty($user['last_login'])
                                        ? date('d M Y H:i', strtotime($user['last_login']))
                                        : '-' ?>

                                </td>

                            </tr>

                        <?php endforeach; ?>

                    <?php else: ?>

                        <tr>
                            <td colspan="7" class="text-center text-body py-4">
                                Belum ada data user.
                            </td>
                        </tr>

                    <?php endif; ?>

                </tbody>

            </table>

        </div>

        <?= adminListFooter('users', $search ?? '', $currentPage ?? 1, $totalPages ?? 1, $totalData ?? 0, $limit ?? 10) ?>

    </div>

</div>
