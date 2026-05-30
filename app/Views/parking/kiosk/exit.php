<?php
$nowTs = time();
$entryTs = !empty($ticket['entry_time']) ? strtotime($ticket['entry_time']) : $nowTs - 9000;
$exitTs = !empty($ticket['exit_time']) ? strtotime($ticket['exit_time']) : $nowTs;
$durationMinutes = (int) ($ticket['duration_minutes'] ?? ($calc['duration_minutes'] ?? max(0, ceil(($exitTs - $entryTs) / 60))));
$hours = intdiv($durationMinutes, 60);
$minutes = $durationMinutes % 60;
$durationText = ($hours > 0 ? $hours . ' Jam ' : '') . $minutes . ' Menit';
$plateText = strtoupper($ticket['plate_number'] ?? 'B 1234 ABC');
$vehicleName = strtoupper($ticket['vehicle_type_name'] ?? 'Mobil');
$amount = (float) ($ticket['payable_amount'] ?? ($calc['payable_amount'] ?? 0));
$isPaid = $paid || in_array($ticket['payment_status'] ?? '', ['paid', 'waived'], true);
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($title ?? 'Kiosk Parkir Keluar') ?></title>
    <link rel="stylesheet" href="<?= asset('css/remixicon.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/style.css') ?>?v=20260526-2">
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; min-height: 100vh; background: #eef8f1; color: #071d49; font-family: "Nunito", Arial, sans-serif; }
        .parking-kiosk { min-height: 100vh; display: flex; flex-direction: column; padding: 16px; gap: 14px; background: radial-gradient(circle at 10% 0%, rgba(16, 185, 129, .12), transparent 32%), #eef8f1; }
        .kiosk-frame { flex: 1; display: grid; grid-template-rows: auto auto 1fr auto; overflow: hidden; border: 1px solid #cae7d8; border-radius: 14px; background: rgba(255,255,255,.96); box-shadow: 0 22px 70px rgba(6, 95, 70, .14); }
        .kiosk-bar { min-height: 54px; display: grid; grid-template-columns: 1fr auto 1fr; align-items: center; padding: 0 24px; color: #fff; background: linear-gradient(90deg, #047335, #079447); font-weight: 950; letter-spacing: .2px; }
        .kiosk-bar h1 { margin: 0; font-size: clamp(22px, 2vw, 34px); text-align: center; color: #fff; }
        .kiosk-clock { justify-self: end; display: flex; gap: 18px; font-size: clamp(14px, 1.2vw, 18px); }
        .brand-row { display: flex; justify-content: space-between; align-items: center; gap: 24px; padding: 24px 38px; border-bottom: 1px solid #d7eadf; }
        .brand { display: flex; align-items: center; gap: 16px; }
        .brand-mark { width: 72px; height: 72px; border-radius: 18px; display: grid; place-items: center; color: #fff; background: linear-gradient(135deg, #13b8a6, #2563eb); box-shadow: 0 16px 34px rgba(20,184,166,.2); }
        .brand-mark i { font-size: 40px; }
        .brand h2, .hero-copy h2 { margin: 0; color: #06245a; font-weight: 950; letter-spacing: 0; }
        .brand p, .hero-copy p { margin: 4px 0 0; color: #065f46; font-weight: 700; }
        .hero-copy { text-align: right; }
        .content { padding: 26px 34px 0; display: grid; gap: 18px; align-content: start; }
        .welcome { display: flex; align-items: center; justify-content: center; gap: 30px; text-align: left; }
        .hero-icon { width: 108px; height: 108px; border-radius: 999px; display: grid; place-items: center; color: #fff; background: linear-gradient(135deg, #047335, #08a650); box-shadow: 0 20px 42px rgba(8,166,80,.22); }
        .hero-icon i { font-size: 62px; }
        .welcome h3 { margin: 0; color: #047335; font-size: clamp(34px, 3vw, 52px); font-weight: 950; }
        .welcome p { margin: 6px 0 0; color: #10295a; font-size: clamp(18px, 1.4vw, 24px); font-weight: 800; line-height: 1.35; }
        .vehicle-card { border: 1px solid #d1e9dd; border-radius: 16px; padding: 18px; background: #fff; }
        .section-title { margin: 0 0 12px; color: #047335; font-size: clamp(18px, 1.4vw, 25px); font-weight: 950; text-transform: uppercase; }
        .camera-preview { position: relative; height: clamp(210px, 25vh, 320px); overflow: hidden; border-radius: 10px; background: linear-gradient(180deg, #dcfce7, #f8fafc 48%, #738193 49%, #354154 100%); }
        .camera-preview::before { content: ""; position: absolute; inset: 0; background: linear-gradient(90deg, rgba(20,95,40,.34), transparent 18%, transparent 82%, rgba(20,95,40,.28)), repeating-linear-gradient(90deg, transparent 0 94px, rgba(255,255,255,.18) 94px 98px); opacity: .8; }
        .gate-arm { position: absolute; right: 18px; top: 34%; width: 260px; height: 14px; border-radius: 999px; background: repeating-linear-gradient(90deg, #ef4444 0 38px, #fff 38px 76px); transform: rotate(-8deg); transform-origin: right center; box-shadow: 0 5px 18px rgba(15,23,42,.18); }
        .gate-box { position: absolute; right: 38px; bottom: 18%; width: 54px; height: 118px; border-radius: 10px; background: #f59e0b; box-shadow: inset 0 -12px rgba(0,0,0,.08); }
        .camera-box { position: absolute; left: 34px; top: 28%; width: 74px; height: 78px; border-radius: 12px; background: #111827; border: 8px solid #e5e7eb; }
        .camera-box::after { content: ""; position: absolute; width: 28px; height: 28px; left: 15px; top: 17px; border-radius: 999px; background: radial-gradient(circle, #38bdf8 0 18%, #0f172a 20% 100%); border: 4px solid #64748b; }
        .car { position: absolute; left: 50%; bottom: 10%; width: min(480px, 58%); height: 168px; transform: translateX(-50%); border-radius: 48px 48px 26px 26px; background: linear-gradient(180deg, #ffffff, #dbe3ec); box-shadow: 0 28px 50px rgba(15,23,42,.32); }
        .car::before { content: ""; position: absolute; left: 18%; right: 18%; top: -54px; height: 82px; border-radius: 80px 80px 18px 18px; background: linear-gradient(180deg, #f8fafc, #9fc3df); border: 8px solid #e5eef8; }
        .car::after { content: ""; position: absolute; left: 50%; bottom: 16px; width: 134px; height: 38px; transform: translateX(-50%); border-radius: 8px; background: #0f172a; box-shadow: -178px 0 0 #111827, 178px 0 0 #111827; }
        .plate { position: absolute; left: 50%; bottom: 36px; transform: translateX(-50%); min-width: 128px; padding: 7px 14px; border-radius: 6px; color: #fff; background: #111827; font-weight: 900; text-align: center; letter-spacing: 2px; border: 2px solid #fff; }
        .plate-card { display: grid; grid-template-columns: auto 1fr; align-items: center; gap: 22px; margin-top: 14px; border: 1px solid #d1e9dd; border-radius: 14px; padding: 16px 20px; background: #fcfffd; }
        .plate-icon { width: 78px; height: 70px; border-radius: 14px; display: grid; place-items: center; border: 4px solid #047335; color: #047335; font-weight: 950; line-height: 1; box-shadow: 0 0 0 10px #ecfdf5; }
        .plate-label { color: #0e3f92; font-size: 15px; font-weight: 950; text-transform: uppercase; }
        .plate-number { color: #061f54; font-size: clamp(34px, 3.6vw, 58px); font-weight: 950; letter-spacing: 3px; line-height: 1; }
        .plate-sub { color: #10295a; font-size: clamp(17px, 1.3vw, 23px); font-weight: 800; margin-top: 5px; }
        .checkout-grid { display: grid; grid-template-columns: 1fr 1fr; border: 1px solid #d1e9dd; border-radius: 14px; overflow: hidden; background: #fff; }
        .metric { display: flex; align-items: center; gap: 15px; padding: 16px 22px; border-bottom: 1px solid #d1e9dd; }
        .metric:nth-child(odd) { border-right: 1px solid #d1e9dd; }
        .metric:nth-last-child(-n+2) { border-bottom: 0; }
        .metric i { width: 50px; height: 50px; border-radius: 999px; display: grid; place-items: center; color: #0d63d6; background: #eef6ff; font-size: 29px; }
        .metric strong { display: block; color: #0f47a8; font-size: 13px; text-transform: uppercase; }
        .metric span { color: #061f54; font-size: clamp(17px, 1.3vw, 23px); font-weight: 900; line-height: 1.2; }
        .metric.price i { color: #047335; background: #ecfdf5; }
        .metric.price span { color: #047335; font-size: clamp(25px, 2vw, 38px); }
        .payment-methods { display: grid; grid-template-columns: repeat(4, 1fr); gap: 10px; margin-top: 14px; padding: 15px; border-radius: 14px; background: linear-gradient(90deg, #edfdf4, #f7fffb); border: 1px solid #d1e9dd; text-align: center; }
        .pay-method { border: 0; border-radius: 12px; padding: 12px 6px; background: transparent; color: #064e3b; font-weight: 900; }
        .pay-method i { display: block; font-size: 34px; margin-bottom: 4px; }
        .search-line { display: flex; gap: 10px; margin-bottom: 14px; }
        .search-line input { flex: 1; border: 1px solid #d1e9dd; border-radius: 12px; padding: 14px 16px; color: #08255c; font-weight: 800; text-transform: uppercase; }
        .search-line button { border: 0; border-radius: 12px; padding: 0 22px; color: #fff; background: linear-gradient(90deg, #047335, #08a650); font-weight: 950; }
        .action-bar { display: grid; grid-template-columns: 1fr auto; align-items: center; gap: 20px; margin-top: auto; padding: 22px 34px; color: #fff; background: linear-gradient(90deg, #047335, #08a650); border-radius: 14px; box-shadow: 0 -10px 36px rgba(8,166,80,.24); }
        .action-bar button, .action-bar a { border: 0; background: transparent; color: #fff; text-decoration: none; }
        .action-main { display: flex; align-items: center; gap: 18px; text-align: left; }
        .action-main i { width: 72px; height: 72px; border-radius: 999px; display: grid; place-items: center; background: rgba(255,255,255,.18); font-size: 42px; }
        .action-main strong { display: block; font-size: clamp(24px, 2vw, 36px); font-weight: 950; text-transform: uppercase; }
        .action-main span { font-size: clamp(16px, 1.3vw, 22px); opacity: .95; }
        .action-gate { width: 120px; text-align: center; font-size: 64px; }
        .footer-info { display: grid; grid-template-columns: 1fr auto; gap: 24px; align-items: center; padding: 18px 32px; border: 1px solid #d6e6f6; border-radius: 16px; background: rgba(255,255,255,.9); color: #0b2b66; }
        .footer-info .help { display: flex; align-items: center; gap: 14px; padding-left: 34px; border-left: 1px solid #d6e6f6; }
        .footer-info i { font-size: 36px; color: #047335; }
        @media (max-width: 900px) {
            .kiosk-bar, .brand-row, .welcome, .footer-info { grid-template-columns: 1fr; flex-direction: column; text-align: center; }
            .hero-copy { text-align: center; }
            .plate-card, .checkout-grid { grid-template-columns: 1fr; text-align: center; }
            .plate-icon { margin: auto; }
            .metric, .metric:nth-child(odd) { border-right: 0; }
            .metric:nth-last-child(2) { border-bottom: 1px solid #d1e9dd; }
            .payment-methods { grid-template-columns: repeat(2, 1fr); }
        }
    </style>
</head>
<body>
<main class="parking-kiosk">
    <section class="kiosk-frame">
        <div class="kiosk-bar">
            <div></div>
            <h1>KIOSK PARKIR - KELUAR</h1>
            <div class="kiosk-clock"><span><?= strtoupper(date('d M Y', $exitTs)) ?></span><span><?= date('H:i', $exitTs) ?> WIB</span></div>
        </div>

        <header class="brand-row">
            <div class="brand">
                <div class="brand-mark"><i class="ri-hospital-line"></i></div>
                <div><h2>SIM Rumah Sakit</h2><p>Sistem Informasi Manajemen</p></div>
            </div>
            <div class="hero-copy"><h2>Terima Kasih</h2><p>Semoga lekas sembuh</p></div>
        </header>

        <div class="content">
            <section class="welcome">
                <div class="hero-icon"><i class="ri-car-fill"></i></div>
                <div><h3><?= $isPaid ? 'Pembayaran Berhasil!' : 'Terima Kasih!' ?></h3><p><?= $isPaid ? 'Palang akan terbuka otomatis' : 'Silakan lakukan pembayaran<br>Palang terbuka setelah pembayaran' ?></p></div>
            </section>

            <section class="vehicle-card">
                <form class="search-line" method="get">
                    <input type="hidden" name="page" value="parking-kiosk-exit">
                    <input name="ticket" value="<?= htmlspecialchars($keyword) ?>" placeholder="Scan QR / masukkan tiket / plat nomor">
                    <button>Cari</button>
                </form>

                <h3 class="section-title">Data Kendaraan</h3>
                <div class="camera-preview">
                    <div class="camera-box"></div><div class="gate-box"></div><div class="gate-arm"></div><div class="car"><div class="plate"><?= htmlspecialchars($plateText) ?></div></div>
                </div>

                <div class="plate-card">
                    <div class="plate-icon">1 2<br>ABCD</div>
                    <div>
                        <div class="plate-label">Nomor Polisi</div>
                        <div class="plate-number"><?= htmlspecialchars($plateText) ?></div>
                        <div class="plate-sub">Jenis Kendaraan: <?= htmlspecialchars($vehicleName) ?></div>
                    </div>
                </div>

                <div class="checkout-grid mt-3">
                    <div class="metric"><i class="ri-calendar-line"></i><div><strong>Waktu Masuk</strong><span><?= date('d M Y', $entryTs) ?><br><?= date('H:i:s', $entryTs) ?> WIB</span></div></div>
                    <div class="metric"><i class="ri-calendar-check-line"></i><div><strong>Waktu Keluar</strong><span><?= date('d M Y', $exitTs) ?><br><?= date('H:i:s', $exitTs) ?> WIB</span></div></div>
                    <div class="metric"><i class="ri-time-line"></i><div><strong>Durasi Parkir</strong><span><?= htmlspecialchars($durationText) ?></span></div></div>
                    <div class="metric price"><i class="ri-money-dollar-circle-line"></i><div><strong>Total Biaya</strong><span>Rp <?= number_format($amount, 0, ',', '.') ?></span><small>(Termasuk PPN)</small></div></div>
                </div>

                <div class="payment-methods">
                    <button form="payForm" name="payment_method" value="qris" class="pay-method"><i class="ri-qr-code-line"></i>QRIS</button>
                    <button form="payForm" name="payment_method" value="e_money" class="pay-method"><i class="ri-bank-card-line"></i>E-Money</button>
                    <button form="payForm" name="payment_method" value="debit" class="pay-method"><i class="ri-bank-card-2-line"></i>Kartu Debit</button>
                    <button form="payForm" name="payment_method" value="cash" class="pay-method"><i class="ri-cash-line"></i>Tunai</button>
                </div>
            </section>
        </div>

        <form id="payForm" method="post" action="<?= url('parking-kiosk-exit-pay') ?>" class="action-bar">
            <input type="hidden" name="ticket_id" value="<?= htmlspecialchars($ticket['id'] ?? '') ?>">
            <input type="hidden" name="exit_gate_id" value="<?= htmlspecialchars($gate['id'] ?? '') ?>">
            <button class="action-main" type="submit" <?= empty($ticket) ? 'disabled' : '' ?>>
                <i class="<?= $isPaid ? 'ri-check-line' : 'ri-wallet-3-line' ?>"></i>
                <span><strong><?= $isPaid ? 'Pembayaran Berhasil' : 'Bayar & Buka Palang' ?></strong><span><?= $isPaid ? 'Palang akan terbuka otomatis' : 'Pilih metode pembayaran di atas' ?></span></span>
            </button>
            <div class="action-gate"><i class="ri-roadster-line"></i></div>
        </form>
    </section>

    <footer class="footer-info">
        <div class="d-flex align-items-center gap-3"><i class="ri-information-line"></i><div><strong>Informasi</strong><br>Kehilangan struk akan dikenakan biaya tambahan sesuai ketentuan rumah sakit.</div></div>
        <div class="help"><i class="ri-customer-service-2-line"></i><div><strong>Butuh Bantuan?</strong><br>Hubungi petugas parkir</div></div>
    </footer>
</main>
</body>
</html>
