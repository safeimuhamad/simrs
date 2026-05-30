<div class="container py-5">

    <div class="row justify-content-center">

        <div class="col-md-5">

            <div class="card border-0 rounded-10 shadow-sm">

                <div class="card-body p-4">

                    <div class="text-center mb-4">

                        <h3 class="mb-2">
                            Lupa Password
                        </h3>

                        <p class="text-body mb-0">
                            Masukkan email akun Anda untuk menerima link reset password.
                        </p>

                    </div>
                    <?php if (!empty($_SESSION['success'])): ?>
                        <div class="alert alert-success mb-4">
                            <?= $_SESSION['success']; unset($_SESSION['success']); ?>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($_SESSION['error'])): ?>
                        <div class="alert alert-danger mb-4">
                            <?= $_SESSION['error']; unset($_SESSION['error']); ?>
                        </div>
                    <?php endif; ?>
                    <form method="POST" action="<?= url('forgot-password-send') ?>">

                        <div class="mb-4">

                            <label class="form-label">
                                Email
                            </label>

                            <input
                                type="email"
                                name="email"
                                class="form-control"
                                required
                                autofocus
                            >

                        </div>

                        <button class="btn btn-primary text-white w-100">

                            Kirim Link Reset Password

                        </button>

                    </form>

                    <div class="text-center mt-4">

                        <a href="<?= url('login') ?>">
                            Kembali ke Login
                        </a>

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>