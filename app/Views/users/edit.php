<?php if (!empty($_SESSION['error'])): ?>
    <div class="alert alert-danger">
        <?= $_SESSION['error']; unset($_SESSION['error']); ?>
    </div>
<?php endif; ?>
<div class="card bg-white rounded-10 border border-white p-20 mb-4">

    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">

        <div>
            <h3 class="mb-1">
                Edit User
            </h3>

            <p class="mb-0 text-body">
                Perbarui pengaturan akses, role, dan cakupan data user dalam SIMRS.
            </p>
        </div>

        <a
            href="<?= url('users') ?>"
            class="btn btn-light erp-btn"
        >
            <i class="ri-arrow-left-line me-1"></i>
            Kembali
        </a>

    </div>

</div>

<form method="POST" action="<?= url('users-update') ?>">

    <input
        type="hidden"
        name="id"
        value="<?= htmlspecialchars($user['id']) ?>"
    >

    <div class="card bg-white rounded-10 border border-white mb-4">

        <div class="p-20 border-bottom">
            <h4 class="erp-detail-section-title mb-0">
                Informasi User
            </h4>
        </div>

        <div class="p-20">

            <div class="row g-4">

                <div class="col-md-6">

                    <label class="erp-detail-label">
                        Nama <span class="text-danger">*</span>
                    </label>

                    <input
                        type="text"
                        name="name"
                        class="form-control"
                        value="<?= htmlspecialchars($user['name'] ?? '') ?>"
                        required
                    >

                </div>

                <div class="col-md-6">

                    <label class="erp-detail-label">
                        Email
                    </label>

                    <input
                        type="email"
                        name="email"
                        class="form-control"
                        value="<?= htmlspecialchars($user['email'] ?? '') ?>"
                        readonly
                    >

                    <small class="text-body">
                        Email login tidak dapat diubah.
                    </small>

                </div>

                <div class="col-md-6">

                    <label class="erp-detail-label">
                        Role <span class="text-danger">*</span>
                    </label>

                    <select
                        name="role_id"
                        class="form-select"
                        required
                    >

                        <option value="">
                            -- Pilih Role --
                        </option>

                        <?php foreach ($roles as $role): ?>

                            <option
                                value="<?= $role['id'] ?>"
                                <?= ($user['role_id'] ?? '') == $role['id'] ? 'selected' : '' ?>
                            >
                                <?= htmlspecialchars($role['name']) ?>
                            </option>

                        <?php endforeach; ?>

                    </select>

                </div>

            </div>

        </div>

    </div>

    <div class="card bg-white rounded-10 border border-white mb-4">

        <div class="p-20 border-bottom">
            <h4 class="erp-detail-section-title mb-0">
                Hak Akses & Status
            </h4>
        </div>

        <div class="p-20">

            <div class="row g-4">

                <div class="col-md-6">

                    <label class="erp-detail-label">
                        Data Scope
                    </label>

                    <select
                        name="data_scope"
                        class="form-select"
                        required
                    >

                        <option value="all" <?= ($user['data_scope'] ?? '') === 'all' ? 'selected' : '' ?>>
                            All Data
                        </option>

                        <option value="own" <?= ($user['data_scope'] ?? '') === 'own' ? 'selected' : '' ?>>
                            Own Data
                        </option>

                        <option value="department" <?= ($user['data_scope'] ?? '') === 'department' ? 'selected' : '' ?>>
                            Department Data
                        </option>

                        <option value="branch" <?= ($user['data_scope'] ?? '') === 'branch' ? 'selected' : '' ?>>
                            Branch Data
                        </option>

                        <option value="assigned" <?= ($user['data_scope'] ?? '') === 'assigned' ? 'selected' : '' ?>>
                            Assigned Data
                        </option>

                    </select>

                </div>

                <div class="col-md-6">

                    <label class="erp-detail-label">
                        Status
                    </label>

                    <select
                        name="status"
                        class="form-select"
                    >

                        <option value="pending" <?= ($user['status'] ?? '') === 'pending' ? 'selected' : '' ?>>
                            Pending Activation
                        </option>

                        <option value="active" <?= ($user['status'] ?? '') === 'active' ? 'selected' : '' ?>>
                            Active
                        </option>

                        <option value="inactive" <?= ($user['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>
                            Inactive
                        </option>

                    </select>

                </div>

            </div>

        </div>

    </div>

    <div class="row g-4 mb-4">

        <div class="col-lg-8">

            <div class="card bg-white rounded-10 border border-white h-100">

                <div class="p-20 border-bottom">
                    <h4 class="erp-detail-section-title mb-0">
                        Catatan Akses
                    </h4>
                </div>

                <div class="p-20">

                    <div class="text-body">

                        <p class="mb-3">
                            Role menentukan menu dan fitur yang dapat diakses user.
                        </p>

                        <p class="mb-0">
                            Data Scope menentukan batasan data yang dapat dilihat dan dikelola oleh user di dalam sistem.
                        </p>

                    </div>

                </div>

            </div>

        </div>

        <div class="col-lg-4">

            <div class="card bg-white rounded-10 border border-white h-100">

                <div class="p-20 border-bottom">
                    <h4 class="erp-detail-section-title mb-0">
                        Ringkasan
                    </h4>
                </div>

                <div class="p-20">

                    <div class="d-flex justify-content-between mb-2">
                        <span>Status</span>
                        <strong>
                            <?= htmlspecialchars($user['status'] ?? '-') ?>
                        </strong>
                    </div>

                    <div class="d-flex justify-content-between mb-2">
                        <span>Data Scope</span>
                        <strong>
                            <?= htmlspecialchars($user['data_scope'] ?? '-') ?>
                        </strong>
                    </div>

                    <hr>

                    <div class="text-body">
                        Pengaturan role dan data scope akan memengaruhi hak akses user di seluruh modul SIMRS.
                    </div>

                </div>

            </div>

        </div>

    </div>

    <div class="card bg-white rounded-10 border border-white p-20">

        <div class="d-flex justify-content-end flex-wrap gap-3">

            <a
                href="<?= url('users') ?>"
                class="btn btn-light erp-btn"
            >
                <i class="ri-close-line me-1"></i>
                Batal
            </a>

            <button
                type="submit"
                class="btn btn-primary text-white erp-btn"
            >
                <i class="ri-save-line me-1"></i>
                Update
            </button>

        </div>

    </div>

</form>
