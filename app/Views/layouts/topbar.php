<div class="container-fluid">
    <div class="main-content d-flex flex-column">
        <header class="header-area bg-white border border-white simrs-topbar" id="header-area">
            <div class="d-flex align-items-center justify-content-between gap-3">
                <div class="d-flex align-items-center gap-3 flex-grow-1">
                    <button class="header-burger-menu bg-transparent p-0 border-0 position-relative d-xl-none" id="header-burger-menu">
                        <span class="material-symbols-outlined">menu</span>
                    </button>
                    <div class="position-relative simrs-top-search d-none d-md-block">
                        <span class="material-symbols-outlined position-absolute top-50 translate-middle-y text-body" style="left: 12px;">search</span>
                        <input class="form-control" placeholder="Cari menu, pasien, transaksi...">
                        <span class="position-absolute top-50 translate-middle-y badge bg-light text-body border" style="right: 10px;">Ctrl + K</span>
                    </div>
                </div>

                <div class="d-flex align-items-center gap-3">
                    <span class="simrs-icon-btn d-none d-sm-inline-grid">
                        <span class="material-symbols-outlined">notifications</span>
                        <span class="badge bg-danger rounded-pill">8</span>
                    </span>
                    <span class="simrs-icon-btn d-none d-sm-inline-grid">
                        <span class="material-symbols-outlined">mail</span>
                        <span class="badge bg-primary rounded-pill">3</span>
                    </span>
                    <span class="simrs-icon-btn d-none d-sm-inline-grid">
                        <span class="material-symbols-outlined">help</span>
                    </span>

                    <div class="dropdown admin-profile">
                        <div class="d-flex align-items-center bg-transparent border-0 text-start p-0 cursor dropdown-toggle" data-bs-toggle="dropdown">
                            <img class="rounded-circle" style="width: 44px; height: 44px; object-fit: cover;" src="<?= asset('images/doctor1.jpg') ?>" alt="doctor">
                            <div class="ms-2 d-none d-lg-block">
                                <div class="fw-bold text-dark"><?= htmlspecialchars($_SESSION['name'] ?? 'dr. Budi Santoso') ?></div>
                                <small class="text-body"><?= htmlspecialchars($_SESSION['role_name'] ?? 'Dokter Umum') ?></small>
                            </div>
                        </div>

                        <div class="dropdown-menu border-0 bg-white dropdown-menu-end">
                            <div class="d-flex align-items-center info">
                                <img class="rounded-circle" style="width: 40px; height: 40px; object-fit: cover;" src="<?= asset('images/doctor1.jpg') ?>" alt="doctor">
                                <div class="flex-grow-1 ms-10">
                                    <h3 class="fw-medium fs-17 mb-0"><?= htmlspecialchars($_SESSION['name'] ?? 'User') ?></h3>
                                    <span class="fs-15 fw-medium"><?= htmlspecialchars($_SESSION['role_name'] ?? $_SESSION['user_role'] ?? 'User') ?></span>
                                </div>
                            </div>
                            <ul class="admin-link mb-0 list-unstyled">
                                <li><a class="dropdown-item admin-item-link d-flex align-items-center text-body" href="<?= url('logout') ?>"><i class="material-symbols-outlined">logout</i><span class="ms-2">Logout</span></a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <div class="main-content-container overflow-hidden">
