<?php
$shouldAutoPrint = !empty($ticket) && (($_GET['print'] ?? '') === '1');
$ticketTimestamp = !empty($ticket['created_at']) ? strtotime($ticket['created_at']) : time();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($title ?? 'Self Service Queue') ?></title>
    <link rel="stylesheet" href="<?= asset('css/remixicon.css') ?>">
    <link rel="stylesheet" href="<?= asset('css/style.css') ?>?v=20260526-2">
    <link rel="stylesheet" href="<?= asset('css/simrs-theme.css') ?>?v=20260529-3">
    <style>
        body { background: #f4f9fd; color: #14213d; min-height: 100vh; }
        body.kiosk-print-direct { background: #f4f9fd; }
        .kiosk-shell { min-height: 100vh; padding: 32px; background: radial-gradient(circle at top left, rgba(13,184,198,.16), transparent 34%), #f4f9fd; }
        body.kiosk-print-direct .kiosk-shell { display: grid; place-items: center; }
        body.kiosk-print-direct .kiosk-header,
        body.kiosk-print-direct .kiosk-main,
        body.kiosk-print-direct .kiosk-info { display: none; }
        .direct-print-cover { display: none; width: min(520px, 92vw); text-align: center; background: #fff; border: 1px solid #dbe8f5; border-radius: 28px; padding: 42px; box-shadow: 0 24px 70px rgba(15,23,42,.12); }
        body.kiosk-print-direct .direct-print-cover { display: block; }
        .direct-print-icon { width: 78px; height: 78px; margin: 0 auto 22px; border-radius: 24px; display: grid; place-items: center; background: linear-gradient(135deg, #13b8a6, #2563eb); color: #fff; font-size: 38px; box-shadow: 0 18px 42px rgba(37,99,235,.22); }
        .direct-print-cover h1 { margin: 0 0 10px; color: #092456; font-size: 30px; font-weight: 950; }
        .direct-print-cover p { margin: 0; color: #65758b; font-size: 17px; }
        .kiosk-header { display: flex; align-items: center; justify-content: space-between; gap: 24px; margin-bottom: 28px; }
        .kiosk-brand { display: flex; align-items: center; gap: 16px; }
        .kiosk-logo { width: 68px; height: 68px; border-radius: 18px; display: grid; place-items: center; color: #fff; font-size: 34px; background: linear-gradient(135deg, #13b8a6, #2563eb); box-shadow: 0 18px 38px rgba(19,184,166,.22); }
        .kiosk-logo .material-symbols-outlined, .ticket-brand-mark .material-symbols-outlined { font-size: 40px; color: #fff; }
        .kiosk-title { font-size: 34px; font-weight: 800; margin: 0; letter-spacing: 0; }
        .kiosk-subtitle { color: #65758b; font-size: 17px; margin: 4px 0 0; }
        .kiosk-time { background: #fff; border: 1px solid #dce8f5; border-radius: 18px; padding: 14px 20px; font-weight: 700; color: #334155; }
        .kiosk-main { display: grid; grid-template-columns: minmax(0, 2fr) 420px; gap: 22px; align-items: start; }
        .kiosk-left, .kiosk-right, .kiosk-info { background: rgba(255,255,255,.95); border: 1px solid #dbe8f5; border-radius: 22px; box-shadow: 0 18px 48px rgba(15,23,42,.07); }
        .kiosk-left { padding: 26px; }
        .kiosk-right { padding: 22px; }
        .kiosk-step { font-size: 15px; font-weight: 900; color: #2563eb; margin: 0 0 20px; text-transform: uppercase; }
        .kiosk-grid { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 18px; }
        .service-card { position: relative; border: 1px solid #dbe8f5; border-radius: 20px; background: #fff; padding: 24px; text-align: left; min-height: 150px; box-shadow: 0 16px 42px rgba(15,23,42,.06); transition: transform .18s ease, border-color .18s ease, box-shadow .18s ease; cursor: pointer; }
        .service-card:hover { transform: translateY(-3px); border-color: #20b8b0; box-shadow: 0 18px 48px rgba(15,23,42,.1); }
        .service-icon { width: 56px; height: 56px; border-radius: 18px; display: grid; place-items: center; background: #e8fbf7; color: #0f9f96; margin-bottom: 18px; }
        .service-icon .material-symbols-outlined { font-size: 32px; }
        .service-card h3 { font-size: 19px; font-weight: 900; margin: 0 0 8px; text-transform: uppercase; color: #0f3b82; }
        .service-card p { color: #6b7a90; margin: 0; min-height: 44px; }
        .service-counter { display: inline-flex; align-items: center; gap: 8px; margin-top: 18px; padding: 8px 12px; border-radius: 999px; background: #eef6ff; color: #2563eb; font-weight: 700; }
        .kiosk-action { min-height: 56px; border-radius: 14px; font-size: 18px; font-weight: 800; background: linear-gradient(90deg, #13b8a6, #2563eb); border: 0; }
        .kiosk-modal-backdrop { position: fixed; inset: 0; z-index: 1000; display: none; align-items: center; justify-content: center; padding: 24px; background: rgba(8, 29, 60, .48); backdrop-filter: blur(8px); }
        .kiosk-modal-backdrop.show { display: flex; }
        .kiosk-modal { width: min(760px, 100%); background: #fff; border: 1px solid #dbe8f5; border-radius: 26px; box-shadow: 0 28px 80px rgba(15,23,42,.22); overflow: hidden; }
        .kiosk-modal-head { padding: 22px 26px; display: flex; justify-content: space-between; align-items: flex-start; gap: 18px; border-bottom: 1px solid #e1ebf5; background: linear-gradient(135deg, #f8fdff, #eef9ff); }
        .kiosk-modal-title { margin: 0; font-weight: 950; color: #092456; font-size: 26px; }
        .kiosk-modal-subtitle { margin: 4px 0 0; color: #64748b; font-size: 15px; }
        .kiosk-modal-close { border: 0; background: #eef6ff; color: #2563eb; border-radius: 14px; width: 44px; height: 44px; display: grid; place-items: center; }
        .kiosk-modal-body { padding: 26px; }
        .modal-service-pill { display: inline-flex; align-items: center; gap: 10px; padding: 12px 16px; border-radius: 999px; background: #e8fbf7; color: #0f766e; font-weight: 900; margin-bottom: 18px; }
        .ticket-panel { max-width: 320px; margin: 0 auto; background: #fff; border: 1px solid #dbe8f5; border-radius: 18px; text-align: center; overflow: hidden; }
        .ticket-brand { display: none; align-items: center; justify-content: center; gap: 10px; margin-bottom: 14px; }
        .ticket-brand-mark { width: 42px; height: 42px; border-radius: 12px; display: grid; place-items: center; color: #fff; background: linear-gradient(135deg, #13b8a6, #2563eb); }
        .ticket-brand-name { font-weight: 900; color: #092456; line-height: 1.1; text-align: left; }
        .ticket-brand-name small { display: block; font-weight: 700; color: #64748b; font-size: 11px; margin-top: 2px; }
        .ticket-service { background: linear-gradient(90deg, #1d6bf2, #0e8cd4); color: #fff; padding: 16px; font-size: 20px; font-weight: 900; text-transform: uppercase; }
        .ticket-number { font-size: 72px; line-height: 1; font-weight: 950; color: #092456; margin: 26px 0 10px; letter-spacing: 0; }
        .ticket-counter { display: inline-flex; align-items: center; gap: 10px; background: #e8fbf7; color: #0f766e; border-radius: 16px; padding: 12px 18px; font-size: 20px; font-weight: 900; }
        .ticket-actions { display: flex; justify-content: center; flex-wrap: wrap; gap: 12px; margin-top: 26px; }
        .queue-print-ticket { display: none; }
        .recent-row { display: grid; grid-template-columns: 48px 1fr auto; gap: 10px; align-items: center; padding: 10px; border: 1px solid #dbe8f5; border-radius: 14px; margin-top: 10px; }
        .recent-icon { width: 42px; height: 42px; border-radius: 12px; display: grid; place-items: center; background: #eef6ff; color: #2563eb; }
        .kiosk-info { margin-top: 22px; padding: 20px; display: grid; grid-template-columns: repeat(5, 1fr); gap: 14px; }
        .info-item { display: flex; align-items: center; gap: 12px; border-right: 1px solid #dbe8f5; padding-right: 12px; }
        .info-item:last-child { border-right: 0; }
        .info-icon { width: 46px; height: 46px; border-radius: 999px; display: grid; place-items: center; background: #eef6ff; color: #2563eb; font-size: 22px; flex: 0 0 auto; }
        @media (max-width: 1100px) {
            .kiosk-main { grid-template-columns: 1fr; }
            .kiosk-grid { grid-template-columns: repeat(2, minmax(0, 1fr)); }
            .kiosk-info { grid-template-columns: repeat(2, 1fr); }
            .info-item { border-right: 0; }
        }
        @media print {
            @page { size: 80mm 92mm; margin: 0; }
            * { box-shadow: none !important; text-shadow: none !important; }
            html, body { width: 80mm; min-height: 92mm; margin: 0; padding: 0; background: #fff; color: #111827; }
            body * { visibility: hidden !important; }
            .queue-print-ticket, .queue-print-ticket * { visibility: visible !important; }
            .kiosk-shell, .kiosk-modal-backdrop, script { display: none !important; }
            .queue-print-ticket {
                display: block !important;
                position: absolute;
                left: 0;
                top: 0;
                width: 60mm;
                min-height: 74mm;
                margin: 3mm 10mm;
                padding: 4mm;
                border: 1px solid #111827 !important;
                border-radius: 4px;
                background: #fff;
                overflow: hidden;
                text-align: center;
                box-sizing: border-box;
            }
            .queue-print-brand { display: flex; align-items: center; justify-content: center; gap: 8px; margin-bottom: 9px; }
            .queue-print-logo { width: 28px; height: 28px; border: 2px solid #111827; border-radius: 4px; display: grid; place-items: center; font-size: 20px; font-weight: 950; line-height: 1; }
            .queue-print-brand strong { display: block; font-size: 12px; line-height: 1.1; text-align: left; color: #092456; }
            .queue-print-brand small { display: block; font-size: 8px; line-height: 1.1; text-align: left; color: #111827; }
            .queue-print-service { border-top: 1px dashed #94a3b8; border-bottom: 1px dashed #94a3b8; padding: 5px 0; font-size: 12px; font-weight: 900; text-transform: uppercase; color: #111827; }
            .queue-print-number { margin: 9px 0 4px; color: #000; font-size: 42px; line-height: 1; font-weight: 950; letter-spacing: 0; }
            .queue-print-text { margin: 5px 0; color: #111827; font-size: 10px; line-height: 1.25; }
            .queue-print-counter { display: inline-flex; align-items: center; gap: 4px; margin: 6px 0; color: #111827; font-size: 14px; font-weight: 900; }
            .queue-print-meta { margin-top: 6px; color: #111827; font-size: 10px; line-height: 1.45; }
            .queue-print-footer { margin-top: 9px; padding-top: 6px; border-top: 1px dashed #94a3b8; font-size: 8px; line-height: 1.35; color: #111827; }
        }
    </style>
</head>
<body class="<?= $shouldAutoPrint ? 'kiosk-print-direct' : '' ?>">
<div class="kiosk-shell">
    <?php if ($shouldAutoPrint): ?>
        <section class="direct-print-cover">
            <div class="direct-print-icon"><i class="ri-printer-line"></i></div>
            <h1>Mencetak Nomor Antrean</h1>
            <p>Mohon tunggu sebentar. Halaman akan kembali otomatis setelah tiket dikirim ke printer.</p>
        </section>
    <?php endif; ?>

    <header class="kiosk-header">
        <div class="kiosk-brand">
            <div class="kiosk-logo"><span class="material-symbols-outlined">local_hospital</span></div>
            <div>
                <h1 class="kiosk-title">SIM Rumah Sakit</h1>
                <p class="kiosk-subtitle">Self Service Antrean - pilih layanan dan cetak tiket.</p>
            </div>
        </div>
        <div class="kiosk-time"><?= date('d M Y') ?> &bull; <?= date('H:i') ?> WIB</div>
    </header>

    <?php if (!empty($_SESSION['error'])): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($_SESSION['error']); unset($_SESSION['error']); ?></div>
    <?php endif; ?>

    <div class="kiosk-main">
        <section class="kiosk-left">
            <p class="kiosk-step">1. Pilih Jenis Layanan</p>
            <div class="kiosk-grid">
                <?php foreach ($services as $service): ?>
                    <button
                        type="button"
                        class="service-card"
                        data-type="<?= htmlspecialchars($service['type']) ?>"
                        data-polyclinic="<?= htmlspecialchars((string) ($service['polyclinic_id'] ?? '')) ?>"
                        data-name="<?= htmlspecialchars($service['name']) ?>"
                        data-counter="<?= htmlspecialchars($service['counter_no']) ?>"
                    >
                        <div class="service-icon"><span class="material-symbols-outlined"><?= htmlspecialchars($service['icon']) ?></span></div>
                        <h3><?= htmlspecialchars($service['name']) ?></h3>
                        <p><?= htmlspecialchars($service['subtitle']) ?></p>
                        <span class="service-counter">Counter <?= htmlspecialchars($service['counter_no']) ?></span>
                    </button>
                <?php endforeach; ?>
            </div>
        </section>

        <aside class="kiosk-right">
            <h3 class="mb-3">Nomor Antrian Anda</h3>
            <section class="ticket-panel" id="ticket-panel">
                <div class="ticket-brand">
                    <div class="ticket-brand-mark"><span class="material-symbols-outlined">local_hospital</span></div>
                    <div class="ticket-brand-name">SIM Rumah Sakit<small>Sistem Informasi Manajemen</small></div>
                </div>
                <div class="ticket-service"><?= htmlspecialchars($ticket['service_name'] ?? 'Pilih Layanan') ?></div>
                <div class="ticket-number"><?= htmlspecialchars($ticket['queue_no'] ?? '---') ?></div>
                <p class="text-body mb-3"><?= !empty($ticket) ? 'Terima kasih, selamat menunggu' : 'Nomor akan muncul setelah tombol Ambil Nomor ditekan' ?></p>
                <div class="ticket-counter"><i class="ri-map-pin-line"></i> Counter <?= htmlspecialchars($ticket['counter_no'] ?? '-') ?></div>
                <?php if (!empty($ticket)): ?>
                    <p class="text-body mt-3 mb-0"><?= date('d/m/Y H:i', strtotime($ticket['created_at'])) ?> WIB</p>
                    <?php if (!empty($ticket['visitor_name'])): ?><p class="mb-0 mt-2">Nama: <?= htmlspecialchars($ticket['visitor_name']) ?></p><?php endif; ?>
                <?php endif; ?>
            </section>

            <div class="ticket-actions">
                <?php if (!empty($ticket)): ?>
                    <button class="btn btn-primary text-white kiosk-action px-4" onclick="window.print()"><i class="ri-printer-line me-2"></i>Print</button>
                    <a class="btn btn-light kiosk-action px-4 d-inline-flex align-items-center" href="<?= url('self-service-queue') ?>">Ambil Lagi</a>
                <?php endif; ?>
                <a class="btn btn-outline-primary kiosk-action px-4 d-inline-flex align-items-center" href="<?= url('queue-display') ?>" target="_blank"><i class="ri-tv-line me-2"></i>Display</a>
            </div>

            <h4 class="mt-4 mb-2">Antrean Terakhir</h4>
            <?php foreach (array_slice($recent ?? [], 0, 5) as $row): ?>
                <div class="recent-row">
                    <div class="recent-icon"><i class="ri-ticket-2-line"></i></div>
                    <div><strong><?= htmlspecialchars($row['service_name']) ?></strong><br><span><?= htmlspecialchars($row['queue_no']) ?></span></div>
                    <span class="text-body"><?= date('H:i', strtotime($row['created_at'])) ?></span>
                </div>
            <?php endforeach; ?>
        </aside>
    </div>

    <section class="kiosk-info">
        <div class="info-item"><div class="info-icon"><i class="ri-time-line"></i></div><div><strong>Jam Operasional</strong><br><span>07.00 - 21.00 WIB</span></div></div>
        <div class="info-item"><div class="info-icon"><i class="ri-team-line"></i></div><div><strong>Utamakan Pasien</strong><br><span>Lansia, Hamil & Disabilitas</span></div></div>
        <div class="info-item"><div class="info-icon"><i class="ri-volume-mute-line"></i></div><div><strong>Jaga Ketertiban</strong><br><span>Suara di area layanan</span></div></div>
        <div class="info-item"><div class="info-icon"><i class="ri-file-list-3-line"></i></div><div><strong>Siapkan Identitas</strong><br><span>KTP / kartu pasien</span></div></div>
        <div class="info-item"><div class="info-icon"><i class="ri-question-line"></i></div><div><strong>Butuh Bantuan?</strong><br><span>Hubungi petugas</span></div></div>
    </section>

</div>

<?php if (!empty($ticket)): ?>
    <section class="queue-print-ticket" id="queuePrintTicket">
        <div class="queue-print-brand">
            <div class="queue-print-logo">+</div>
            <div>
                <strong>SIM Rumah Sakit</strong>
                <small>Sistem Informasi Manajemen</small>
            </div>
        </div>
        <div class="queue-print-service"><?= htmlspecialchars($ticket['service_name'] ?? 'Layanan') ?></div>
        <div class="queue-print-number"><?= htmlspecialchars($ticket['queue_no'] ?? '---') ?></div>
        <p class="queue-print-text">Terima kasih, selamat menunggu</p>
        <div class="queue-print-counter"><i class="ri-map-pin-line"></i> Counter <?= htmlspecialchars($ticket['counter_no'] ?? '-') ?></div>
        <div class="queue-print-meta">
            <?= date('d/m/Y H:i', $ticketTimestamp) ?> WIB
            <?php if (!empty($ticket['visitor_name'])): ?>
                <br>Nama: <?= htmlspecialchars($ticket['visitor_name']) ?>
            <?php endif; ?>
        </div>
        <div class="queue-print-footer">
            Simpan tiket ini dan perhatikan layar pemanggilan antrean.
        </div>
    </section>
<?php endif; ?>

<div class="kiosk-modal-backdrop" id="queueModal" aria-hidden="true">
    <div class="kiosk-modal" role="dialog" aria-modal="true" aria-labelledby="queueModalTitle">
        <div class="kiosk-modal-head">
            <div>
                <h2 class="kiosk-modal-title" id="queueModalTitle">Ambil Nomor Antrean</h2>
                <p class="kiosk-modal-subtitle">Lengkapi data opsional, lalu tekan Ambil Nomor.</p>
            </div>
            <button type="button" class="kiosk-modal-close" id="closeModal" aria-label="Tutup modal">
                <span class="material-symbols-outlined">close</span>
            </button>
        </div>

        <form method="post" action="<?= url('self-service-queue-store') ?>" id="kioskForm">
            <div class="kiosk-modal-body">
                <input type="hidden" name="service_type" id="serviceType">
                <input type="hidden" name="polyclinic_id" id="polyclinicId">
                <input type="hidden" name="selected_service" id="selectedService">

                <div class="modal-service-pill">
                    <span class="material-symbols-outlined">confirmation_number</span>
                    <span id="selectedServiceLabel">Pilih layanan</span>
                    <span id="selectedCounterLabel" class="ms-2">Counter -</span>
                </div>

                <div class="row g-3">
                    <div class="col-md-12">
                        <label class="erp-detail-label">Dokter (Opsional)</label>
                        <select class="form-control" name="doctor_id">
                            <option value="">Pilih dokter jika ada pilihan</option>
                            <?php foreach ($doctors as $doctor): ?>
                                <option value="<?= (int) $doctor['id'] ?>"><?= htmlspecialchars($doctor['name'] . ' - ' . ($doctor['specialist'] ?: 'Dokter')) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label class="erp-detail-label">Nama Pasien/Pengunjung</label>
                        <input class="form-control" name="visitor_name" placeholder="Opsional">
                    </div>
                    <div class="col-md-6">
                        <label class="erp-detail-label">No. HP</label>
                        <input class="form-control" name="phone" placeholder="Opsional">
                    </div>
                </div>
            </div>

            <div class="p-20 border-top d-flex justify-content-end gap-3">
                <button type="button" class="btn btn-light erp-btn" id="cancelModal">Batal</button>
                <button class="btn btn-primary text-white kiosk-action px-4" id="submitBtn" disabled>
                    <span class="material-symbols-outlined align-middle me-1">confirmation_number</span>
                    Ambil Nomor
                </button>
            </div>
        </form>
    </div>
</div>

<script>
const queueModal = document.getElementById('queueModal');
const openQueueModal = () => {
    queueModal.classList.add('show');
    queueModal.setAttribute('aria-hidden', 'false');
};
const closeQueueModal = () => {
    queueModal.classList.remove('show');
    queueModal.setAttribute('aria-hidden', 'true');
};

document.querySelectorAll('.service-card').forEach((button) => {
    button.addEventListener('click', () => {
        document.querySelectorAll('.service-card').forEach((item) => item.style.borderColor = '#dbe8f5');
        button.style.borderColor = '#13b8a6';
        document.getElementById('serviceType').value = button.dataset.type;
        document.getElementById('polyclinicId').value = button.dataset.polyclinic || '';
        document.getElementById('selectedService').value = button.dataset.name;
        document.getElementById('selectedServiceLabel').textContent = button.dataset.name;
        document.getElementById('selectedCounterLabel').textContent = 'Counter ' + (button.dataset.counter || '-');
        document.getElementById('submitBtn').disabled = false;
        openQueueModal();
    });
});

document.getElementById('closeModal').addEventListener('click', closeQueueModal);
document.getElementById('cancelModal').addEventListener('click', closeQueueModal);
queueModal.addEventListener('click', (event) => {
    if (event.target === queueModal) closeQueueModal();
});
document.addEventListener('keydown', (event) => {
    if (event.key === 'Escape') closeQueueModal();
});
</script>
<?php if ($shouldAutoPrint): ?>
<script>
setTimeout(() => window.print(), 450);
setTimeout(() => {
    window.location.href = '<?= url('self-service-queue') ?>';
}, 4500);
</script>
<?php endif; ?>
</body>
</html>
