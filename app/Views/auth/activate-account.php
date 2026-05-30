<div class="container d-flex align-items-center justify-content-center min-vh-100">
    <div class="card bg-white rounded-10 border border-white p-20" style="max-width: 460px; width: 100%;">

        <h3 class="mb-2 text-center">Aktivasi Akun</h3>

        <p class="text-center text-muted mb-4">
            Halo, <strong><?= htmlspecialchars($user['name'] ?? '-') ?></strong><br>
            Silakan buat password untuk mengaktifkan akun Anda.
        </p>

        <?php if (!empty($_SESSION['error'])): ?>
            <div class="alert alert-danger">
                <?= $_SESSION['error']; unset($_SESSION['error']); ?>
            </div>
        <?php endif; ?>

        <form method="POST" action="<?= url('activate-account-save') ?>">
            <input type="hidden" name="token" value="<?= htmlspecialchars($token) ?>">

            <div class="mb-3">
                <label class="form-label">Password Baru</label>
                <input type="password" name="password" class="form-control" minlength="8" required>
            </div>

            <div class="mb-4">
                <label class="form-label">Konfirmasi Password</label>
                <input type="password" name="password_confirm" class="form-control" minlength="8" required>
            </div>

            <button type="submit" class="btn btn-primary text-white w-100">
                Aktifkan Akun
            </button>
        </form>

        <div class="text-center mt-3">
            <a href="<?= url('login') ?>">Kembali ke Login</a>
        </div>

    </div>
</div>
