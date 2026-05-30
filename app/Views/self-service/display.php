<?php
$current = $called[0] ?? null;
$nextRows = array_slice($waiting ?? [], 0, 6);
$historyRows = array_slice($called ?? [], 1, 4);
$counterRows = array_slice(array_merge([$current ?: []], $nextRows), 0, 6);
$queueParts = static function ($queueNo) {
    $queueNo = trim((string) $queueNo);
    return [
        'prefix' => $queueNo !== '' ? strtoupper(substr($queueNo, 0, 1)) : '-',
        'number' => $queueNo !== '' ? $queueNo : '-',
    ];
};
$currentParts = $queueParts($current['queue_no'] ?? '-');
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="refresh" content="12">
    <title><?= htmlspecialchars($title ?? 'Display Antrean') ?></title>
    <link rel="stylesheet" href="<?= asset('css/remixicon.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/style.css') ?>?v=20260526-2">
    <style>
        :root {
            --ink: #082052;
            --muted: #64748b;
            --line: #dce8f5;
            --blue: #2563eb;
            --teal: #13b8a6;
            --green: #16a34a;
            --orange: #f97316;
            --purple: #7c3aed;
            --red: #e11d48;
        }
        * { box-sizing: border-box; }
        body { margin: 0; min-height: 100vh; overflow: hidden; color: var(--ink); background: #edf7fb; }
        .display-shell { min-height: 100vh; padding: 22px; display: grid; grid-template-columns: 250px minmax(0, 1fr) 395px; grid-template-rows: minmax(0, 1fr) auto; gap: 20px; background: radial-gradient(circle at 8% 8%, rgba(19,184,166,.2), transparent 26%), linear-gradient(135deg, #f8fdff, #e8f6fb); }
        .brand-card, .info-card, .health-card, .now-card, .queue-panel, .counter-card, .summary-card, .qr-card, .thanks-bar { border: 1px solid var(--line); background: rgba(255,255,255,.96); border-radius: 20px; box-shadow: 0 20px 46px rgba(15,23,42,.07); }
        .left-rail { display: grid; grid-template-rows: auto auto 1fr; gap: 16px; min-height: 0; }
        .brand-card { padding: 18px; display: flex; align-items: center; gap: 14px; }
        .brand-icon { width: 62px; height: 62px; flex: 0 0 auto; border-radius: 18px; display: grid; place-items: center; color: #fff; font-size: 34px; background: linear-gradient(135deg, var(--teal), var(--blue)); box-shadow: 0 16px 34px rgba(37,99,235,.2); }
        .brand-title { margin: 0; font-size: 24px; line-height: 1.05; font-weight: 950; letter-spacing: 0; }
        .brand-subtitle { margin: 4px 0 0; color: var(--muted); font-size: 13px; font-weight: 700; }
        .info-card { overflow: hidden; }
        .panel-title { margin: 0; padding: 16px 18px; font-size: 17px; font-weight: 950; border-bottom: 1px solid var(--line); text-transform: uppercase; }
        .info-row { display: grid; grid-template-columns: 52px 1fr; gap: 12px; align-items: center; padding: 16px 18px; border-bottom: 1px solid #eef4fb; }
        .info-row:last-child { border-bottom: 0; }
        .info-icon { width: 48px; height: 48px; display: grid; place-items: center; border-radius: 16px; color: var(--blue); background: #eef6ff; font-size: 28px; }
        .info-value { margin: 0; font-size: 22px; font-weight: 950; }
        .info-label { margin: 2px 0 0; color: var(--muted); font-weight: 700; }
        .health-card { padding: 18px; align-self: start; background: linear-gradient(135deg, #effdf7, #dff8f6); }
        .health-card h3 { margin: 0 0 12px; font-size: 18px; font-weight: 950; }
        .health-card p { margin: 0 0 8px; color: #245066; font-weight: 700; line-height: 1.45; }
        .center-stack { display: grid; grid-template-rows: minmax(0, 1fr) auto; gap: 18px; min-width: 0; min-height: 0; }
        .now-card { position: relative; min-height: 535px; overflow: hidden; padding: 34px 395px 34px 34px; display: flex; align-items: center; justify-content: center; background: linear-gradient(135deg, #0b3fb3, #0b72e5 58%, #0ba6c0); color: #fff; }
        .now-card::before { content: ""; position: absolute; inset: 0; background: radial-gradient(circle at 22% 12%, rgba(255,255,255,.26), transparent 27%), radial-gradient(circle at 78% 68%, rgba(255,255,255,.16), transparent 32%), linear-gradient(90deg, rgba(2,18,73,.32), transparent 58%); }
        .now-card::after { content: ""; position: absolute; left: 0; right: 0; bottom: 0; height: 58px; background: rgba(19,99,231,.72); }
        .now-content { position: relative; z-index: 3; width: min(640px, 100%); text-align: center; transform: translateX(-20px); }
        .now-label { display: inline-flex; align-items: center; gap: 12px; padding: 14px 28px; border-radius: 999px; background: linear-gradient(90deg, var(--teal), #15c9d4); color: #fff; font-size: 24px; font-weight: 950; text-transform: uppercase; box-shadow: 0 14px 32px rgba(3,105,161,.22); }
        .now-number { margin: 24px 0 14px; color: #fff; font-size: clamp(118px, 8vw, 172px); line-height: .9; font-weight: 950; letter-spacing: 0; text-shadow: 0 18px 34px rgba(4,18,59,.32); }
        .now-service { margin: 0 0 14px; font-size: 24px; font-weight: 850; }
        .now-counter { width: min(510px, 100%); margin: 0 auto; display: grid; grid-template-columns: 82px 1fr; align-items: center; gap: 20px; border-radius: 22px; background: rgba(255,255,255,.96); color: var(--ink); padding: 18px 28px; text-align: left; box-shadow: 0 16px 36px rgba(2,18,73,.18); }
        .now-counter-icon { width: 74px; height: 74px; border-radius: 20px; display: grid; place-items: center; color: #fff; background: linear-gradient(135deg, var(--teal), #0f9f96); font-size: 42px; }
        .now-counter strong { display: block; font-size: 44px; line-height: 1; font-weight: 950; }
        .now-counter span { display: block; color: #53627a; font-size: 24px; font-weight: 850; margin-top: 4px; }
        .now-note { display: inline-flex; align-items: center; gap: 10px; margin-top: 16px; padding: 12px 22px; border-radius: 999px; background: rgba(5,22,78,.5); color: #fff; font-size: 17px; font-weight: 850; }
        .ticker { position: absolute; z-index: 4; left: 34px; right: 34px; bottom: 15px; display: flex; align-items: center; gap: 12px; color: #fff; font-size: 16px; font-weight: 750; }
        .ticker i { width: 32px; height: 32px; display: grid; place-items: center; border-radius: 999px; background: rgba(255,255,255,.2); }
        .doctor-presenter { position: absolute; z-index: 2; right: -380px; bottom: -225px; width: min(920px, 66%); height: auto; max-width: none; transform: scale(1.58); transform-origin: bottom right; filter: drop-shadow(0 18px 26px rgba(5,22,78,.22)); }
        .right-stack { display: grid; grid-template-rows: minmax(0, auto) 1fr; gap: 18px; min-height: 0; }
        .queue-panel { overflow: hidden; }
        .next-title { display: flex; align-items: center; gap: 9px; padding: 16px 18px; margin: 0; border-bottom: 1px solid var(--line); font-size: 19px; font-weight: 950; text-transform: uppercase; }
        .next-list { padding: 14px 18px; display: grid; gap: 4px; }
        .next-row { display: grid; grid-template-columns: 44px 105px 1fr auto; align-items: center; gap: 10px; padding: 11px 0; border-bottom: 1px solid #eef4fb; }
        .next-row:last-child { border-bottom: 0; }
        .letter-badge { width: 34px; height: 34px; border-radius: 10px; display: grid; place-items: center; color: #fff; background: var(--blue); font-size: 20px; font-weight: 950; }
        .letter-badge.prefix-A { background: #16b834; }
        .letter-badge.prefix-B { background: #1d74f5; }
        .letter-badge.prefix-G, .letter-badge.prefix-D { background: var(--orange); }
        .letter-badge.prefix-L { background: var(--purple); }
        .letter-badge.prefix-R, .letter-badge.prefix-X { background: var(--red); }
        .next-number { color: var(--ink); font-size: 28px; font-weight: 950; letter-spacing: 0; }
        .next-meta { color: var(--muted); font-size: 13px; font-weight: 750; }
        .next-meta strong { display: block; color: var(--ink); font-size: 14px; margin-bottom: 2px; }
        .next-counter { color: var(--blue); font-weight: 950; white-space: nowrap; }
        .safety-card { border-radius: 20px; overflow: hidden; padding: 22px 24px; min-height: 190px; background: radial-gradient(circle at 88% 22%, rgba(255,255,255,.28), transparent 28%), linear-gradient(135deg, #0f8d84, #12c9b9); color: #fff; box-shadow: 0 20px 46px rgba(15,23,42,.08); }
        .safety-card h2 { margin: 0 0 18px; font-size: 28px; line-height: 1.08; font-weight: 950; }
        .safety-card p { margin: 9px 0; display: flex; align-items: center; gap: 9px; font-size: 18px; font-weight: 850; }
        .bottom-strip { display: grid; grid-template-columns: repeat(6, 1fr); gap: 12px; }
        .counter-card { padding: 14px; min-height: 120px; border-color: #bfe8de; background: linear-gradient(135deg, #f7fffc, #effcf6); }
        .counter-card:nth-child(3n+2) { border-color: #c9dcff; background: linear-gradient(135deg, #f7fbff, #eef5ff); }
        .counter-card:nth-child(3n) { border-color: #ffdcb8; background: linear-gradient(135deg, #fffdf8, #fff4e8); }
        .counter-label { display: inline-flex; align-items: center; gap: 6px; color: #0f766e; font-size: 13px; font-weight: 950; text-transform: uppercase; }
        .counter-number { margin: 8px 0 2px; font-size: 34px; font-weight: 950; letter-spacing: 0; color: var(--ink); }
        .counter-service { color: var(--muted); font-size: 14px; font-weight: 800; }
        .summary-strip { grid-column: 1 / -1; display: grid; grid-template-columns: repeat(4, 1fr) 1.6fr; gap: 14px; }
        .summary-card, .qr-card { min-height: 86px; padding: 16px; display: flex; align-items: center; gap: 14px; }
        .summary-icon { width: 54px; height: 54px; flex: 0 0 auto; display: grid; place-items: center; border-radius: 18px; color: #16a34a; background: #eafbf0; font-size: 30px; }
        .summary-card:nth-child(2) .summary-icon { color: var(--blue); background: #eef6ff; }
        .summary-card:nth-child(3) .summary-icon { color: var(--orange); background: #fff5e8; }
        .summary-card:nth-child(4) .summary-icon { color: var(--purple); background: #f4efff; }
        .summary-label { margin: 0; color: var(--ink); font-weight: 850; }
        .summary-value { margin: 2px 0 0; color: var(--ink); font-size: 28px; font-weight: 950; }
        .summary-value small { font-size: 14px; color: #b45309; }
        .qr-card { justify-content: space-between; }
        .qr-box { width: 64px; height: 64px; border-radius: 10px; display: grid; place-items: center; color: var(--ink); background: repeating-linear-gradient(45deg, #111827 0 4px, #fff 4px 8px); border: 4px solid #fff; box-shadow: inset 0 0 0 2px #111827; }
        .thanks-bar { grid-column: 1 / -1; padding: 14px; text-align: center; color: #53627a; font-size: 18px; font-weight: 750; }
        @media (max-width: 1280px) {
            body { overflow: auto; }
            .display-shell { grid-template-columns: 1fr; grid-template-rows: auto; }
            .left-rail, .right-stack { grid-template-columns: 1fr 1fr; grid-template-rows: auto; }
            .now-card { padding-right: 330px; }
            .doctor-presenter { right: -320px; width: 760px; transform: scale(1.28); }
            .bottom-strip, .summary-strip { grid-template-columns: repeat(2, 1fr); }
        }
    </style>
</head>
<body>
<div class="display-shell">
    <aside class="left-rail">
        <section class="brand-card">
            <div class="brand-icon"><span class="material-symbols-outlined">local_hospital</span></div>
            <div>
                <h1 class="brand-title">SIM Rumah Sakit</h1>
                <p class="brand-subtitle">Melayani dengan Hati</p>
            </div>
        </section>

        <section class="info-card">
            <h2 class="panel-title">Informasi</h2>
            <div class="info-row">
                <div class="info-icon"><i class="ri-time-line"></i></div>
                <div>
                    <p class="info-value"><?= date('H:i:s') ?></p>
                    <p class="info-label"><?= date('d M Y') ?></p>
                </div>
            </div>
            <div class="info-row">
                <div class="info-icon"><i class="ri-sun-cloudy-line"></i></div>
                <div>
                    <p class="info-value">28&deg;C</p>
                    <p class="info-label">Cerah Berawan</p>
                </div>
            </div>
        </section>

        <section class="health-card">
            <h3><i class="ri-shield-check-fill"></i> Jaga Kesehatan</h3>
            <p>Gunakan masker dan cuci tangan sebelum & sesudah berkunjung.</p>
            <p><i class="ri-checkbox-circle-fill"></i> Cuci tangan</p>
            <p><i class="ri-checkbox-circle-fill"></i> Gunakan masker</p>
            <p><i class="ri-checkbox-circle-fill"></i> Jaga jarak</p>
        </section>
    </aside>

    <main class="center-stack">
        <section class="now-card">
            <img class="doctor-presenter" src="<?= asset('images/queue/doctor-presenter.png') ?>" alt="">
            <div class="now-content">
                <div class="now-label"><i class="ri-volume-up-line"></i> Sedang Dipanggil</div>
                <div class="now-number"><?= htmlspecialchars($currentParts['number']) ?></div>
                <p class="now-service">Silakan menuju ke</p>
                <div class="now-counter">
                    <div class="now-counter-icon"><i class="ri-team-fill"></i></div>
                    <div>
                        <strong>COUNTER <?= htmlspecialchars($current['counter_no'] ?? '-') ?></strong>
                        <span><?= htmlspecialchars(($current['service_type'] ?? '') === 'registration' ? 'Customer Service' : ($current['service_name'] ?? 'Layanan')) ?></span>
                    </div>
                </div>
                <div class="now-note"><i class="ri-information-line"></i> Untuk layanan : <?= htmlspecialchars($current['service_name'] ?? 'Menunggu panggilan berikutnya') ?></div>
            </div>
            <div class="ticker"><i class="ri-volume-up-line"></i> Terima kasih atas kesabaran Anda. Kami akan memanggil nomor berikutnya.</div>
        </section>

        <section class="bottom-strip">
            <?php foreach ($counterRows as $index => $row): ?>
                <?php if (empty($row)) { continue; } ?>
                <?php $parts = $queueParts($row['queue_no'] ?? '-'); ?>
                <div class="counter-card">
                    <div class="counter-label"><i class="ri-team-fill"></i> Counter <?= htmlspecialchars($row['counter_no'] ?? ($index + 1)) ?></div>
                    <div class="counter-number"><?= htmlspecialchars($parts['number']) ?></div>
                    <div class="counter-service"><?= htmlspecialchars($row['service_name'] ?? 'Layanan') ?></div>
                </div>
            <?php endforeach; ?>
        </section>
    </main>

    <aside class="right-stack">
        <section class="queue-panel">
            <h2 class="next-title"><i class="ri-team-fill"></i> Antrean Berikutnya</h2>
            <div class="next-list">
                <?php foreach ($nextRows as $row): ?>
                    <?php $parts = $queueParts($row['queue_no'] ?? '-'); ?>
                    <div class="next-row">
                        <div class="letter-badge prefix-<?= htmlspecialchars($parts['prefix']) ?>"><?= htmlspecialchars($parts['prefix']) ?></div>
                        <div class="next-number"><?= htmlspecialchars($parts['number']) ?></div>
                        <div class="next-meta">
                            <strong><?= htmlspecialchars($row['service_name'] ?? '-') ?></strong>
                            <?= htmlspecialchars($row['visitor_name'] ?: 'Pasien/Pengunjung') ?>
                        </div>
                        <div class="next-counter">&rarr; Counter <?= htmlspecialchars($row['counter_no'] ?? '-') ?></div>
                    </div>
                <?php endforeach; ?>
                <?php if (empty($nextRows)): ?><p class="text-body mb-0">Belum ada antrean menunggu.</p><?php endif; ?>
            </div>
        </section>

        <section class="queue-panel">
            <h2 class="next-title">Panggilan Terakhir</h2>
            <div class="next-list">
                <?php foreach ($historyRows as $row): ?>
                    <?php $parts = $queueParts($row['queue_no'] ?? '-'); ?>
                    <div class="next-row">
                        <div class="letter-badge prefix-<?= htmlspecialchars($parts['prefix']) ?>"><?= htmlspecialchars($parts['prefix']) ?></div>
                        <div class="next-number"><?= htmlspecialchars($parts['number']) ?></div>
                        <div class="next-meta"><strong><?= htmlspecialchars($row['service_name'] ?? '-') ?></strong>Dipanggil</div>
                        <div class="next-counter">C<?= htmlspecialchars($row['counter_no'] ?? '-') ?></div>
                    </div>
                <?php endforeach; ?>
                <?php if (empty($historyRows)): ?><p class="text-body mb-0">Belum ada riwayat panggilan.</p><?php endif; ?>
            </div>
        </section>

        <section class="safety-card">
            <h2>Keselamatan Pasien<br>Prioritas Kami</h2>
            <p><i class="ri-checkbox-circle-fill"></i> Cuci tangan</p>
            <p><i class="ri-checkbox-circle-fill"></i> Gunakan masker</p>
            <p><i class="ri-checkbox-circle-fill"></i> Jaga jarak</p>
        </section>
    </aside>

    <section class="summary-strip">
        <div class="summary-card">
            <div class="summary-icon"><i class="ri-group-fill"></i></div>
            <div><p class="summary-label">Kunjungan Hari Ini</p><p class="summary-value"><?= (int) (($summary['waiting'] ?? 0) + ($summary['called'] ?? 0) + ($summary['serving'] ?? 0) + ($summary['done'] ?? 0)) ?></p></div>
        </div>
        <div class="summary-card">
            <div class="summary-icon"><i class="ri-ticket-2-fill"></i></div>
            <div><p class="summary-label">Total Antrean Aktif</p><p class="summary-value"><?= (int) (($summary['waiting'] ?? 0) + ($summary['called'] ?? 0) + ($summary['serving'] ?? 0)) ?></p></div>
        </div>
        <div class="summary-card">
            <div class="summary-icon"><i class="ri-team-fill"></i></div>
            <div><p class="summary-label">Rata-rata Waktu Tunggu</p><p class="summary-value">18 <small>menit</small></p></div>
        </div>
        <div class="summary-card">
            <div class="summary-icon"><i class="ri-time-line"></i></div>
            <div><p class="summary-label">Waktu Operasional</p><p class="summary-value">07:00 - 21:00</p></div>
        </div>
        <div class="qr-card">
            <div><p class="summary-label">Scan QR untuk ambil nomor antrean</p><p class="mb-0 text-body">atau kunjungi kiosk mandiri</p></div>
            <div class="qr-box" aria-hidden="true"></div>
        </div>
    </section>

    <div class="thanks-bar">Terima kasih telah mempercayakan kesehatan Anda kepada kami</div>
</div>
</body>
</html>
