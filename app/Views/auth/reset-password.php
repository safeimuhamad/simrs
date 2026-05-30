<div class="container py-5">

    <div class="row justify-content-center">

        <div class="col-md-5">

            <div class="card border-0 rounded-10 shadow-sm">

                <div class="card-body p-4">

                    <div class="text-center mb-4">

                        <h3 class="mb-2">
                            Reset Password
                        </h3>

                        <p class="text-body mb-0">
                            Buat password baru untuk akun Anda.
                        </p>

                    </div>

                    <form method="POST" action="<?= url('reset-password-save') ?>">

                        <input
                            type="hidden"
                            name="token"
                            value="<?= htmlspecialchars($token ?? '') ?>"
                        >

                        <div class="mb-3">

                            <label class="form-label">
                                Password Baru
                            </label>

                            <input
                                type="password"
                                name="password"
                                minlength="8"
                                class="form-control"
                                required
                            >

                        </div>

                        <div class="mb-4">

                            <label class="form-label">
                                Konfirmasi Password
                            </label>

                            <input
                                type="password"
                                name="password_confirm"
                                minlength="8"
                                class="form-control"
                                required
                            >

                        </div>

                        <button class="btn btn-primary text-white w-100">

                            Simpan Password Baru

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
