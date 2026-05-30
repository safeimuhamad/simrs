<?php if (!empty($_SESSION['error'])): ?>
    <div class="alert alert-danger">
        <?= $_SESSION['error']; unset($_SESSION['error']); ?>
    </div>
<?php endif; ?>

<div class="card bg-white rounded-10 border border-white p-20 mb-4">

    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">

        <div>
            <h3 class="mb-1">
                Tambah User
            </h3>

            <p class="mb-0 text-body">
                Tambahkan user baru dan kirim email aktivasi agar user dapat membuat password sendiri.
            </p>
        </div>

        <a href="<?= url('users') ?>" class="btn btn-light erp-btn">
            <i class="ri-arrow-left-line me-1"></i>
            Kembali
        </a>

    </div>

</div>

<form method="POST" action="<?= url('users-store') ?>">

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
                        required
                    >
                </div>

                <div class="col-md-6">
                    <label class="erp-detail-label">
                        Email <span class="text-danger">*</span>
                    </label>

                    <input
                        type="email"
                        name="email"
                        class="form-control"
                        required
                    >

                    <small class="text-body">
                        Link aktivasi akan dikirim ke email ini.
                    </small>
                </div>

                <div class="col-md-6">
                    <label class="erp-detail-label">
                        Role <span class="text-danger">*</span>
                    </label>

                    <select name="role_id" class="form-select" required>
                        <option value="">
                            -- Pilih Role --
                        </option>

                        <?php foreach ($roles as $role): ?>
                            <option value="<?= $role['id'] ?>">
                                <?= htmlspecialchars($role['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-6">
                    <label class="erp-detail-label">
                        Data Scope <span class="text-danger">*</span>
                    </label>

                    <select name="data_scope" class="form-select" required>
                        <option value="all">
                            All Data
                        </option>

                        <option value="own" selected>
                            Own Data
                        </option>

                        <option value="department">
                            Department Data
                        </option>

                        <option value="branch">
                            Branch Data
                        </option>

                        <option value="assigned">
                            Assigned Data
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
                        User dibuat untuk role operasional SIMRS. Setelah disimpan, sistem mengirim email aktivasi agar user dapat membuat password sendiri.
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
                        <span>Status Awal</span>
                        <strong>Pending</strong>
                    </div>

                    <div class="d-flex justify-content-between mb-2">
                        <span>Aktivasi</span>
                        <strong>Email</strong>
                    </div>

                    <hr>

                    <div class="text-body">
                        Hak akses user ditentukan berdasarkan Role dan Data Scope yang dipilih.
                    </div>
                </div>

            </div>

        </div>

    </div>

    <input type="hidden" name="status" value="pending">

    <div class="card bg-white rounded-10 border border-white p-20">

        <div class="d-flex justify-content-end flex-wrap gap-3">

            <a href="<?= url('users') ?>" class="btn btn-light erp-btn">
                <i class="ri-close-line me-1"></i>
                Batal
            </a>

            <button type="submit" class="btn btn-primary text-white erp-btn">
                <i class="ri-save-line me-1"></i>
                Simpan & Kirim Aktivasi
            </button>

        </div>

    </div>

</form>
