<?php

class RoleDashboardController extends Controller
{
    public function registration(): void
    {
        $this->render('simrs_registration.manage', 'Dashboard Pendaftaran', 'Pantau pendaftaran, pasien baru, dan antrean poli hari ini.', 'registration');
    }

    public function doctor(): void
    {
        $this->render('simrs_outpatient.manage', 'Dashboard Dokter', 'Daftar pasien dokter, pemeriksaan aktif, dan resep hari ini.', 'doctor');
    }

    public function nurse(): void
    {
        $this->render('simrs_queue.manage', 'Dashboard Perawat', 'Pantau antrean poli, rawat jalan aktif, dan order penunjang.', 'nurse');
    }

    public function pharmacy(): void
    {
        $this->render('simrs_pharmacy.manage', 'Dashboard Farmasi', 'Validasi resep, proses serah obat, dan kontrol stok kritis.', 'pharmacy');
    }

    public function cashier(): void
    {
        $this->render('simrs_cashier.manage', 'Dashboard Kasir', 'Pantau tagihan pasien, transaksi kasir, dan pembayaran hari ini.', 'cashier');
    }

    public function finance(): void
    {
        $this->render('simrs_report.view', 'Dashboard Finance', 'Ringkasan pendapatan pasien, parkir, dan piutang operasional.', 'finance');
    }

    public function management(): void
    {
        $this->render('simrs_report.view', 'Dashboard Manajemen', 'Ringkasan performa layanan, pendapatan, dan utilisasi rumah sakit.', 'management');
    }

    private function render(string $permission, string $title, string $subtitle, string $type): void
    {
        if (empty($_SESSION['user_id'])) {
            $this->redirect('login');
        }

        requirePermission($permission);

        $model = new RoleDashboard();
        $data = $model->{$type}();

        $this->view('simrs/dashboard/role', [
            'title' => $title,
            'subtitle' => $subtitle,
            'dashboard' => $data,
            'type' => $type,
        ]);
    }
}
