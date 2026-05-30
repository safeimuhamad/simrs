<?php
$entryTime = !empty($ticket['entry_time']) ? strtotime($ticket['entry_time']) : time();
$plateText = strtoupper($ticket['plate_number'] ?? $plate ?? 'B 1234 ABC');
$vehicleName = strtoupper($ticket['vehicle_type_name'] ?? ($vehicleType['name'] ?? 'Mobil'));
$areaName = $ticket['area_name'] ?? ($area['name'] ?? 'Area Parkir Utama');
$gateName = $ticket['entry_gate_name'] ?? ($gate['name'] ?? 'Gate Masuk');
$ticketNo = $ticket['ticket_no'] ?? 'TIKET BARU';
$qrToken = $ticket['qr_token'] ?? '';
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($title ?? 'Kiosk Parkir Masuk') ?></title>
    <link rel="stylesheet" href="<?= asset('css/remixicon.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/style.css') ?>?v=20260526-2">
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; min-height: 100vh; background: #eef6fd; color: #071d49; font-family: "Nunito", Arial, sans-serif; }
        .parking-kiosk { min-height: 100vh; display: flex; flex-direction: column; padding: 16px; gap: 14px; background: radial-gradient(circle at 10% 0%, rgba(14, 124, 255, .14), transparent 32%), #eef6fd; }
        .kiosk-frame { flex: 1; display: grid; grid-template-rows: auto auto 1fr auto; overflow: hidden; border: 1px solid #cfe0f1; border-radius: 14px; background: rgba(255,255,255,.96); box-shadow: 0 22px 70px rgba(13, 42, 90, .14); }
        .kiosk-bar { min-height: 54px; display: grid; grid-template-columns: 1fr auto 1fr; align-items: center; padding: 0 24px; color: #fff; background: linear-gradient(90deg, #04359b, #0d63d6); font-weight: 950; letter-spacing: .2px; }
        .kiosk-bar h1 { margin: 0; font-size: clamp(22px, 2vw, 34px); text-align: center; color: #fff; }
        .kiosk-clock { justify-self: end; display: flex; gap: 18px; font-size: clamp(14px, 1.2vw, 18px); }
        .brand-row { display: flex; justify-content: space-between; align-items: center; gap: 24px; padding: 24px 38px; border-bottom: 1px solid #dce8f4; }
        .brand { display: flex; align-items: center; gap: 16px; }
        .brand-mark { width: 72px; height: 72px; border-radius: 18px; display: grid; place-items: center; color: #fff; background: linear-gradient(135deg, #13b8a6, #2563eb); box-shadow: 0 16px 34px rgba(37,99,235,.2); }
        .brand-mark i { font-size: 40px; }
        .brand h2, .hero-copy h2 { margin: 0; color: #06245a; font-weight: 950; letter-spacing: 0; }
        .brand p, .hero-copy p { margin: 4px 0 0; color: #0d4fc2; font-weight: 700; }
        .hero-copy { text-align: right; }
        .content { padding: 26px 34px 0; display: grid; gap: 18px; align-content: start; }
        .welcome { display: flex; align-items: center; justify-content: center; gap: 30px; text-align: left; }
        .hero-icon { width: 108px; height: 108px; border-radius: 999px; display: grid; place-items: center; color: #fff; background: linear-gradient(135deg, #04359b, #0d63d6); box-shadow: 0 20px 42px rgba(13,99,214,.22); }
        .hero-icon i { font-size: 62px; }
        .welcome h3 { margin: 0; color: #06245a; font-size: clamp(34px, 3vw, 52px); font-weight: 950; }
        .welcome p { margin: 6px 0 0; color: #10295a; font-size: clamp(18px, 1.4vw, 24px); font-weight: 800; line-height: 1.35; }
        .vehicle-card { border: 1px solid #d4e4f4; border-radius: 16px; padding: 18px; background: #fff; }
        .section-title { margin: 0 0 12px; color: #0759c9; font-size: clamp(18px, 1.4vw, 25px); font-weight: 950; text-transform: uppercase; }
        .camera-preview { position: relative; height: clamp(230px, 28vh, 360px); overflow: hidden; border-radius: 10px; background: linear-gradient(180deg, #dbeafe, #f8fafc 48%, #7c8a9b 49%, #3f4d5f 100%); }
        .camera-preview::before { content: ""; position: absolute; inset: 0; background: linear-gradient(90deg, rgba(20,95,40,.32), transparent 18%, transparent 82%, rgba(20,95,40,.28)), repeating-linear-gradient(90deg, transparent 0 94px, rgba(255,255,255,.18) 94px 98px); opacity: .8; }
        .gate-arm { position: absolute; right: 18px; top: 34%; width: 260px; height: 14px; border-radius: 999px; background: repeating-linear-gradient(90deg, #ef4444 0 38px, #fff 38px 76px); transform: rotate(-8deg); transform-origin: right center; box-shadow: 0 5px 18px rgba(15,23,42,.18); }
        .gate-box { position: absolute; right: 38px; bottom: 18%; width: 54px; height: 118px; border-radius: 10px; background: #f59e0b; box-shadow: inset 0 -12px rgba(0,0,0,.08); }
        .camera-box { position: absolute; left: 34px; top: 28%; width: 74px; height: 78px; border-radius: 12px; background: #111827; border: 8px solid #e5e7eb; }
        .camera-box::after { content: ""; position: absolute; width: 28px; height: 28px; left: 15px; top: 17px; border-radius: 999px; background: radial-gradient(circle, #38bdf8 0 18%, #0f172a 20% 100%); border: 4px solid #64748b; }
        .car { position: absolute; left: 50%; bottom: 10%; width: min(480px, 58%); height: 168px; transform: translateX(-50%); border-radius: 48px 48px 26px 26px; background: linear-gradient(180deg, #ffffff, #dbe3ec); box-shadow: 0 28px 50px rgba(15,23,42,.32); }
        .car::before { content: ""; position: absolute; left: 18%; right: 18%; top: -54px; height: 82px; border-radius: 80px 80px 18px 18px; background: linear-gradient(180deg, #f8fafc, #9fc3df); border: 8px solid #e5eef8; }
        .car::after { content: ""; position: absolute; left: 50%; bottom: 16px; width: 134px; height: 38px; transform: translateX(-50%); border-radius: 8px; background: #0f172a; box-shadow: -178px 0 0 #111827, 178px 0 0 #111827; }
        .plate { position: absolute; left: 50%; bottom: 36px; transform: translateX(-50%); min-width: 128px; padding: 7px 14px; border-radius: 6px; color: #fff; background: #111827; font-weight: 900; text-align: center; letter-spacing: 2px; border: 2px solid #fff; }
        .plate-card { display: grid; grid-template-columns: auto 1fr; align-items: center; gap: 22px; margin-top: 16px; border: 1px solid #d7e6f6; border-radius: 14px; padding: 20px; background: #fcfdff; }
        .plate-icon { width: 78px; height: 70px; border-radius: 14px; display: grid; place-items: center; border: 4px solid #0f47a8; color: #0f47a8; font-weight: 950; line-height: 1; box-shadow: 0 0 0 10px #eef6ff; }
        .plate-label { color: #0e3f92; font-size: 15px; font-weight: 950; text-transform: uppercase; }
        .plate-number { color: #061f54; font-size: clamp(36px, 4vw, 62px); font-weight: 950; letter-spacing: 3px; line-height: 1; }
        .plate-sub { color: #10295a; font-size: clamp(18px, 1.4vw, 25px); font-weight: 800; margin-top: 5px; }
        .info-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
        .info-box { display: flex; align-items: center; gap: 16px; border: 1px solid #d7e6f6; border-radius: 14px; padding: 16px 20px; background: #fff; }
        .info-box i { width: 54px; height: 54px; border-radius: 999px; display: grid; place-items: center; color: #095bd5; background: #eef6ff; font-size: 30px; }
        .info-box strong { display: block; color: #0f47a8; font-size: 14px; text-transform: uppercase; }
        .info-box span { color: #061f54; font-size: clamp(17px, 1.4vw, 23px); font-weight: 850; line-height: 1.25; }
        .notice { display: flex; align-items: center; gap: 16px; border-radius: 14px; padding: 18px 22px; background: linear-gradient(90deg, #f4f8ff, #eaf4ff); color: #08255c; }
        .notice i { width: 58px; height: 58px; border-radius: 999px; display: grid; place-items: center; color: #fff; background: #095bd5; font-size: 30px; }
        .action-bar { display: grid; grid-template-columns: 1fr auto; align-items: center; gap: 20px; margin-top: auto; padding: 22px 34px; color: #fff; background: linear-gradient(90deg, #04359b, #0d63d6); border-radius: 14px; box-shadow: 0 -10px 36px rgba(13,99,214,.24); }
        .action-bar button, .action-bar a { border: 0; background: transparent; color: #fff; text-decoration: none; }
        .action-main { display: flex; align-items: center; gap: 18px; text-align: left; }
        .action-main i { width: 72px; height: 72px; border-radius: 999px; display: grid; place-items: center; background: rgba(255,255,255,.18); font-size: 42px; }
        .action-main strong { display: block; font-size: clamp(24px, 2vw, 36px); font-weight: 950; text-transform: uppercase; }
        .action-main span { font-size: clamp(16px, 1.3vw, 22px); opacity: .95; }
        .action-arrow { width: 72px; height: 72px; display: grid; place-items: center; border-radius: 999px; background: #fff !important; color: #04359b !important; font-size: 42px; }
        .footer-info { display: grid; grid-template-columns: 1fr auto; gap: 24px; align-items: center; padding: 18px 32px; border: 1px solid #d6e6f6; border-radius: 16px; background: rgba(255,255,255,.9); color: #0b2b66; }
        .footer-info .help { display: flex; align-items: center; gap: 14px; padding-left: 34px; border-left: 1px solid #d6e6f6; }
        .footer-info i { font-size: 36px; color: #0d63d6; }
        .print-ticket { display: none; }
        .print-status { position: fixed; left: 50%; bottom: 26px; z-index: 20; transform: translateX(-50%); display: none; align-items: center; gap: 10px; padding: 12px 18px; border-radius: 999px; color: #fff; background: rgba(6, 31, 84, .88); font-weight: 900; box-shadow: 0 16px 34px rgba(15,23,42,.22); }
        .print-status.show { display: inline-flex; }
        @media (max-width: 900px) {
            .kiosk-bar, .brand-row, .welcome, .info-grid, .footer-info { grid-template-columns: 1fr; flex-direction: column; text-align: center; }
            .hero-copy { text-align: center; }
            .plate-card { grid-template-columns: 1fr; text-align: center; }
            .plate-icon { margin: auto; }
        }
        @media print {
            @page { size: 80mm 110mm; margin: 0; }
            * { box-shadow: none !important; text-shadow: none !important; }
            html, body { width: 80mm; min-height: 110mm; margin: 0; padding: 0; background: #fff; color: #111827; }
            body * { visibility: hidden !important; }
            .print-ticket, .print-ticket * { visibility: visible !important; }
            .parking-kiosk, .kiosk-frame, .footer-info, .print-status { display: none !important; }
            .print-ticket { display: block !important; width: 66mm; min-height: 92mm; margin: 4mm auto; padding: 4mm; border: 1px solid #111827; border-radius: 4px; color: #111827; background: #fff; text-align: center; font-family: Arial, sans-serif; }
            .print-brand { display: flex; align-items: center; justify-content: center; gap: 7px; margin-bottom: 4mm; }
            .print-logo { width: 12mm; height: 12mm; border: 2px solid #111827; border-radius: 3px; display: grid; place-items: center; font-size: 8mm; font-weight: 900; line-height: 1; }
            .print-brand-name { text-align: left; font-weight: 900; font-size: 12px; line-height: 1.1; }
            .print-brand-name small { display: block; color: #475569; font-size: 8px; margin-top: 1px; }
            .print-line { border-top: 1px dashed #94a3b8; margin: 3mm 0; }
            .print-title { font-size: 11px; font-weight: 900; text-transform: uppercase; }
            .print-ticket-no { font-size: 22px; font-weight: 950; margin: 2mm 0; }
            .print-plate { font-size: 24px; font-weight: 950; letter-spacing: 1px; margin: 2mm 0; }
            .print-meta { font-size: 10px; line-height: 1.45; }
            .print-qr { width: 28mm; height: 28mm; margin: 3mm auto; display: grid; place-items: center; border: 1px solid #cbd5e1; font-size: 6px; word-break: break-all; padding: 2mm; }
            .print-note { font-size: 9px; line-height: 1.35; margin-top: 3mm; }
        }
    </style>
</head>
<body>
<main class="parking-kiosk">
    <section class="kiosk-frame">
        <div class="kiosk-bar">
            <div></div>
            <h1>KIOSK PARKIR - MASUK</h1>
            <div class="kiosk-clock"><span><?= strtoupper(date('d M Y', $entryTime)) ?></span><span><?= date('H:i', $entryTime) ?> WIB</span></div>
        </div>

        <header class="brand-row">
            <div class="brand">
                <div class="brand-mark"><i class="ri-hospital-line"></i></div>
                <div><h2>SIM Rumah Sakit</h2><p>Sistem Informasi Manajemen</p></div>
            </div>
            <div class="hero-copy"><h2>Selamat Datang<br>di SIM Rumah Sakit</h2></div>
        </header>

        <div class="content">
            <section class="welcome">
                <div class="hero-icon"><i class="ri-car-fill"></i></div>
                <div><h3>Selamat Datang!</h3><p>Silakan masuk<br>Palang akan terbuka otomatis</p></div>
            </section>

            <section class="vehicle-card">
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
                <div class="info-grid mt-3">
                    <div class="info-box"><i class="ri-calendar-line"></i><div><strong>Tanggal / Jam Masuk</strong><span><?= date('d M Y', $entryTime) ?><br><?= date('H:i:s', $entryTime) ?> WIB</span></div></div>
                    <div class="info-box"><i class="ri-map-pin-line"></i><div><strong>Lokasi Parkir</strong><span><?= htmlspecialchars($areaName) ?><br><?= htmlspecialchars($gateName) ?></span></div></div>
                </div>
                <div class="notice mt-3"><i class="ri-information-line"></i><div><strong>Simpan struk / tiket parkir Anda</strong><br>Struk diperlukan saat keluar dari area parkir</div></div>
            </section>
        </div>

        <form method="post" action="<?= url('parking-kiosk-entry-store') ?>" class="action-bar">
            <input type="hidden" name="plate_number" value="<?= htmlspecialchars($plateText) ?>">
            <input type="hidden" name="vehicle_type_id" value="<?= htmlspecialchars($vehicleType['id'] ?? '') ?>">
            <input type="hidden" name="area_id" value="<?= htmlspecialchars($area['id'] ?? '') ?>">
            <input type="hidden" name="entry_gate_id" value="<?= htmlspecialchars($gate['id'] ?? '') ?>">
            <button class="action-main" type="submit">
                <i class="ri-printer-line"></i>
                <span><strong><?= $ticket ? 'Struk Parkir Siap' : 'Ambil Struk Parkir' ?></strong><span><?= $ticket ? 'Gate masuk terbuka otomatis' : 'Tekan tombol untuk cetak' ?></span></span>
            </button>
            <button class="action-arrow" type="submit"><i class="ri-arrow-down-s-line"></i></button>
        </form>
    </section>

    <footer class="footer-info">
        <div class="d-flex align-items-center gap-3"><i class="ri-information-line"></i><div><strong>Informasi</strong><br>Kehilangan struk akan dikenakan biaya tambahan sesuai ketentuan rumah sakit.</div></div>
        <div class="help"><i class="ri-customer-service-2-line"></i><div><strong>Butuh Bantuan?</strong><br>Hubungi petugas parkir</div></div>
    </footer>
</main>
<?php if ($ticket): ?>
<section class="print-ticket" id="parkingPrintTicket">
    <div class="print-brand">
        <div class="print-logo">+</div>
        <div class="print-brand-name">SIM Rumah Sakit<small>Sistem Informasi Manajemen</small></div>
    </div>
    <div class="print-line"></div>
    <div class="print-title">Struk Parkir Masuk</div>
    <div class="print-ticket-no"><?= htmlspecialchars($ticketNo) ?></div>
    <div class="print-line"></div>
    <div class="print-plate"><?= htmlspecialchars($plateText) ?></div>
    <div class="print-meta">Jenis: <?= htmlspecialchars($vehicleName) ?></div>
    <div class="print-meta">Area: <?= htmlspecialchars($areaName) ?></div>
    <div class="print-meta">Gate: <?= htmlspecialchars($gateName) ?></div>
    <div class="print-meta">Masuk: <?= date('d/m/Y H:i:s', $entryTime) ?> WIB</div>
    <div class="print-qr"><?= htmlspecialchars($qrToken ?: $ticketNo) ?></div>
    <div class="print-note">Simpan struk ini. Struk diperlukan saat keluar area parkir.</div>
</section>
<div class="print-status" id="printStatus"><i class="ri-printer-line"></i> Mencetak struk parkir...</div>
<script>
    (function () {
        const status = document.getElementById('printStatus');
        setTimeout(function () {
            status?.classList.add('show');
            window.print();
        }, 550);
        setTimeout(function () {
            window.location.href = '<?= url('parking-kiosk-entry') ?>';
        }, 6500);
    })();
</script>
<?php endif; ?>
</body>
</html>
