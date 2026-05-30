<?php if (!empty($_SESSION['error'])): ?>
    <div class="alert alert-danger mb-4">
        <?= $_SESSION['error']; unset($_SESSION['error']); ?>
    </div>
<?php endif; ?>

<div class="card bg-white rounded-10 border border-white p-20 mb-4">

    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">

        <div>
            <h3 class="mb-1">
                Tambah Role
            </h3>

            <p class="mb-0 text-body">
                Tambahkan role baru untuk mengatur hak akses dan otorisasi pengguna pada sistem ERP.
            </p>
        </div>

        <a href="<?= url('roles') ?>" class="btn btn-light erp-btn">
            <i class="ri-arrow-left-line me-1"></i>
            Kembali
        </a>

    </div>

</div>

<form method="POST" action="<?= url('roles-store') ?>">

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
                        placeholder="Contoh: Sales Manager"
                        required
                    >

                </div>

                <div class="col-md-6">

                    <label class="erp-detail-label">
                        Status
                    </label>

                    <select name="status" class="form-select">
                        <option value="active">
                            Active
                        </option>

                        <option value="inactive">
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
                    ></textarea>

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
                        <span>Status Awal</span>
                        <strong>Active</strong>
                    </div>

                    <div class="d-flex justify-content-between mb-2">
                        <span>Tipe</span>
                        <strong>User Role</strong>
                    </div>

                    <hr>

                    <div class="text-body">
                        Role digunakan untuk menentukan hak akses menu, fitur, approval, dan data yang dapat diakses oleh user.
                    </div>

                </div>

            </div>

        </div>

    </div>

    <div class="card bg-white rounded-10 border border-white p-20">

        <div class="d-flex justify-content-end flex-wrap gap-3">

            <a href="<?= url('roles') ?>" class="btn btn-light erp-btn">
                <i class="ri-close-line me-1"></i>
                Batal
            </a>

            <button type="submit" class="btn btn-primary text-white erp-btn">
                <i class="ri-save-line me-1"></i>
                Simpan
            </button>

        </div>

    </div>

</form>