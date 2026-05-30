<?php if (!empty($_SESSION['error'])): ?>
    <div class="alert alert-danger mb-4">
        <?= $_SESSION['error']; unset($_SESSION['error']); ?>
    </div>
<?php endif; ?>
<div class="card bg-white rounded-10 border border-white p-20 mb-4">

    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">

        <div>
            <h3 class="mb-1">
                Edit Role
            </h3>

            <p class="mb-0 text-body">
                Perbarui role untuk mengatur hak akses, otorisasi menu, dan cakupan penggunaan sistem ERP.
            </p>
        </div>

        <a
            href="<?= url('roles') ?>"
            class="btn btn-light erp-btn"
        >
            <i class="ri-arrow-left-line me-1"></i>
            Kembali
        </a>

    </div>

</div>

<form method="POST" action="<?= url('roles-update') ?>">

    <input
        type="hidden"
        name="id"
        value="<?= $role['id'] ?>"
    >

    <div class="card bg-white rounded-10 border border-white mb-4">

        <div class="p-20 border-bottom">
            <h4 class="erp-detail-section-title mb-0">
                Informasi Role
            </h4>
        </div>

        <div class="p-20">

            <div class="row g-4">

                <div class="col-md-6">

                    <label class="erp-detail-label">
                        Nama Role <span class="text-danger">*</span>
                    </label>

                    <input
                        type="text"
                        name="name"
                        class="form-control"
                        value="<?= htmlspecialchars($role['name']) ?>"
                        required
                    >

                </div>

                <div class="col-md-6">

                    <label class="erp-detail-label">
                        Status
                    </label>

                    <select name="status" class="form-select">

                        <option
                            value="active"
                            <?= ($role['status'] ?? '') === 'active' ? 'selected' : '' ?>
                        >
                            Active
                        </option>

                        <option
                            value="inactive"
                            <?= ($role['status'] ?? '') === 'inactive' ? 'selected' : '' ?>
                        >
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
                        Deskripsi
                    </h4>
                </div>

                <div class="p-20">

                    <textarea
                        name="description"
                        rows="8"
                        class="form-control"
                        placeholder="Deskripsi dan fungsi role dalam sistem"
                    ><?= htmlspecialchars($role['description'] ?? '') ?></textarea>

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
                            <?= htmlspecialchars($role['status'] ?? '-') ?>
                        </strong>
                    </div>

                    <div class="d-flex justify-content-between mb-2">
                        <span>Tipe</span>
                        <strong>User Role</strong>
                    </div>

                    <hr>

                    <div class="text-body">
                        Role menentukan hak akses menu, fitur, approval workflow, dan data yang dapat diakses user.
                    </div>

                </div>

            </div>

        </div>

    </div>

    <div class="card bg-white rounded-10 border border-white p-20">

        <div class="d-flex justify-content-end flex-wrap gap-3">

            <a
                href="<?= url('roles') ?>"
                class="btn btn-light erp-btn"
            >
                <i class="ri-close-line me-1"></i>
                Batal
            </a>

            <?php if (can('role.permission')): ?>
                <a
                    href="<?= url('roles-permissions') ?>?id=<?= $role['id'] ?>"
                    class="btn btn-info text-white erp-btn"
                >
                    <i class="ri-shield-keyhole-line me-1"></i>
                    Hak Akses
                </a>
            <?php endif; ?>

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
