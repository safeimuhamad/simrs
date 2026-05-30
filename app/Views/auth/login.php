<?php
$testUsers = [
    ['Super Admin', 'superadmin@simrs.test', 'simrs123'],
    ['Admin RS', 'admin.rs@simrs.test', 'simrs123'],
    ['Pendaftaran', 'pendaftaran@simrs.test', 'simrs123'],
    ['Dokter', 'dokter@simrs.test', 'simrs123'],
    ['Perawat', 'perawat@simrs.test', 'simrs123'],
    ['Farmasi', 'farmasi@simrs.test', 'simrs123'],
    ['Kasir', 'kasir@simrs.test', 'simrs123'],
    ['Finance', 'finance@simrs.test', 'simrs123'],
    ['Manajemen', 'manajemen@simrs.test', 'simrs123'],
    ['Parking Admin', 'parking.admin@simrs.test', 'simrs123'],
    ['Parking Operator', 'parking.operator@simrs.test', 'simrs123'],
];
?>

<div class="simrs-auth-page">
    <section class="simrs-auth-hero">
        <img src="<?= uploadAsset('website/content/hospital-hero.png') ?>" alt="Gedung rumah sakit">
        <div class="simrs-auth-wave">
            <div class="simrs-brand-mark">
                <span class="material-symbols-outlined">local_hospital</span>
            </div>
            <h1 class="simrs-auth-title">SIM Rumah Sakit</h1>
            <p class="simrs-auth-copy">
                Sistem Informasi Manajemen Rumah Sakit terintegrasi untuk pelayanan kesehatan yang lebih baik.
            </p>
            <div class="simrs-auth-benefits">
                <div class="simrs-auth-benefit"><span class="material-symbols-outlined">health_and_safety</span>Pelayanan<br>Terpadu</div>
                <div class="simrs-auth-benefit"><span class="material-symbols-outlined">verified_user</span>Data Aman<br>& Terpercaya</div>
                <div class="simrs-auth-benefit"><span class="material-symbols-outlined">monitoring</span>Monitoring<br>Real-time</div>
                <div class="simrs-auth-benefit"><span class="material-symbols-outlined">groups</span>Kolaborasi<br>Tim Medis</div>
            </div>
        </div>
    </section>

    <section class="simrs-auth-card">
        <div class="text-center mb-4">
            <div class="simrs-brand-mark mx-auto mb-4">
                <span class="material-symbols-outlined">local_hospital</span>
            </div>
            <h2 class="simrs-login-heading mb-2">Selamat Datang Kembali</h2>
            <p class="text-body mb-0">Silakan login untuk melanjutkan</p>
        </div>

        <?php if (!empty($_SESSION['error'])): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></div>
        <?php endif; ?>

        <?php if (!empty($_SESSION['success'])): ?>
            <div class="alert alert-success"><?= htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?></div>
        <?php endif; ?>

        <form method="POST" action="<?= url('process-login') ?>">
            <div class="mb-3">
                <label class="form-label">Email / Username</label>
                <div class="simrs-input-wrap">
                    <span class="material-symbols-outlined">person</span>
                    <input type="email" name="email" class="form-control" placeholder="admin.rs@simrs.test" required autofocus>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">Password</label>
                <div class="simrs-input-wrap">
                    <span class="material-symbols-outlined">lock</span>
                    <input type="password" name="password" class="form-control" placeholder="Masukkan password" required>
                    <span class="material-symbols-outlined">visibility_off</span>
                </div>
            </div>

            <div class="d-flex justify-content-between align-items-center mb-4">
                <label class="d-flex gap-2 align-items-center mb-0">
                    <input type="checkbox" checked>
                    <span>Ingat saya</span>
                </label>
                <a href="<?= url('forgot-password') ?>">Lupa Password?</a>
            </div>

            <button type="submit" class="btn simrs-login-btn text-white w-100">
                <span class="material-symbols-outlined align-middle me-2">lock</span>
                Login
            </button>
        </form>

        <div class="d-flex align-items-center gap-3 my-4">
            <hr class="flex-grow-1">
            <span class="text-body">atau</span>
            <hr class="flex-grow-1">
        </div>

        <button type="button" class="btn btn-outline-primary w-100 py-3 fw-semibold">
            <span class="material-symbols-outlined align-middle me-2">fingerprint</span>
            Login dengan SSO
        </button>

        <div class="mt-4">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <strong>Akun Testing SIMRS</strong>
                <span class="text-body fs-12">Password semua: <strong>simrs123</strong></span>
            </div>
            <div class="simrs-test-users">
                <table class="table table-sm">
                    <thead><tr><th>Role</th><th>Email</th><th>Password</th></tr></thead>
                    <tbody>
                        <?php foreach ($testUsers as $user): ?>
                            <tr>
                                <td><?= htmlspecialchars($user[0]) ?></td>
                                <td><?= htmlspecialchars($user[1]) ?></td>
                                <td><code><?= htmlspecialchars($user[2]) ?></code></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <p class="text-center text-body mt-4 mb-0 fs-14">
            <span class="material-symbols-outlined align-middle fs-18 me-1">shield</span>
            Sistem aman sesuai standar keamanan data rumah sakit
        </p>
    </section>
</div>
