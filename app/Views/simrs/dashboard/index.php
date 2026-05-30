<?php
$money = fn($value) => 'Rp ' . number_format((float) $value, 0, ',', '.');
$trend = $stats['visit_trend'] ?? [];
$incomeTrend = $stats['income_trend'] ?? [];
$polyRows = $stats['visits_by_poly'] ?? [];
$polyTotal = max(1, array_sum(array_map(fn($row) => (int) ($row['total'] ?? 0), $polyRows)));
$maxVisit = max(1, ...array_map(fn($row) => max((int) $row['outpatient'], (int) $row['inpatient'], (int) $row['lab']), $trend ?: [['outpatient' => 0, 'inpatient' => 0, 'lab' => 0]]));
$linePoints = function ($key, $height = 190) use ($trend, $maxVisit) {
    $points = [];
    $count = max(1, count($trend) - 1);
    foreach ($trend as $index => $row) {
        $x = ($index / $count) * 700;
        $y = $height - (((int) $row[$key]) / $maxVisit * ($height - 20));
        $points[] = round($x, 1) . ',' . round($y, 1);
    }
    return implode(' ', $points);
};
$maxIncome = max(1, ...array_map(fn($row) => (float) $row['amount'], $incomeTrend ?: [['amount' => 0]]));
$statusLabel = [
    'registered' => 'Terdaftar',
    'waiting' => 'Menunggu',
    'in_consultation' => 'Dalam Pemeriksaan',
    'pharmacy' => 'Farmasi',
    'billing' => 'Billing',
    'paid' => 'Sudah Bayar',
    'completed' => 'Selesai',
    'cancelled' => 'Batal',
];
?>

<div class="d-flex justify-content-between align-items-start gap-3 flex-wrap mb-4">
    <div class="simrs-dashboard-title">
        <h1>Dashboard <span class="material-symbols-outlined align-middle text-success">verified</span></h1>
        <p class="mb-0 text-body fs-16">Selamat pagi, <?= htmlspecialchars($_SESSION['name'] ?? 'User SIMRS') ?></p>
        <p class="mb-0 text-body">Data dashboard otomatis mengikuti transaksi SIMRS hari ini.</p>
    </div>
    <div class="simrs-dashboard-card px-4 py-3 d-flex align-items-center gap-3">
        <span class="material-symbols-outlined">calendar_month</span>
        <div>
            <strong><?= date('l, d M Y') ?></strong><br>
            <span class="text-body"><?= date('H:i:s') ?> WIB</span>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <?php
    $statCards = [
        ['Pasien Hari Ini', (int) $stats['patients_today'], 'Dari kunjungan hari ini', 'groups', 'success', '#e7f9f0'],
        ['Kunjungan', (int) $stats['visits_today'], 'Total visit hari ini', 'assignment_add', 'primary', '#eef5ff'],
        ['Pasien Rawat Inap', (int) $stats['inpatient_count'], 'Bed terisi ' . (int) $stats['bed_occupancy']['percent'] . '%', 'hotel', 'purple', '#f3edff'],
        ['Antrean Aktif', (int) $stats['active_queue'], 'Menunggu pelayanan', 'schedule', 'warning', '#fff3e8'],
        ['Pendapatan Hari Ini', $money($stats['income_today']), 'Pembayaran diterima', 'account_balance_wallet', 'success', '#e6fbf7'],
    ];
    ?>
    <?php foreach ($statCards as $card): ?>
        <div class="col-md-6 col-xl">
            <div class="simrs-dashboard-card p-20 h-100">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <p class="mb-2 fw-semibold" style="color: var(--simrs-<?= $card[4] === 'purple' ? 'purple' : ($card[4] === 'warning' ? 'orange' : ($card[4] === 'primary' ? 'blue' : 'green')) ?>);"><?= htmlspecialchars($card[0]) ?></p>
                        <h3 class="mb-2"><?= htmlspecialchars((string) $card[1]) ?></h3>
                        <small class="text-body"><?= htmlspecialchars($card[2]) ?></small>
                    </div>
                    <span class="material-symbols-outlined simrs-stat-icon" style="background: <?= $card[5] ?>; color: var(--simrs-<?= $card[4] === 'purple' ? 'purple' : ($card[4] === 'warning' ? 'orange' : ($card[4] === 'primary' ? 'blue' : 'green')) ?>);"><?= htmlspecialchars($card[3]) ?></span>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<div class="row g-4 mb-4">
    <div class="col-xl-6">
        <div class="simrs-dashboard-card p-20 h-100">
            <div class="d-flex justify-content-between mb-3">
                <h4 class="mb-0">Grafik Kunjungan</h4>
                <span class="badge bg-light text-primary">7 Hari Terakhir</span>
            </div>
            <div class="d-flex gap-4 fs-12 mb-2">
                <span><i class="ri-circle-fill text-success"></i> Rawat Jalan</span>
                <span><i class="ri-circle-fill text-primary"></i> Rawat Inap</span>
                <span><i class="ri-circle-fill text-danger"></i> Laboratorium</span>
            </div>
            <div class="simrs-chart-grid">
                <svg class="simrs-chart-line" viewBox="0 0 700 220" preserveAspectRatio="none">
                    <polyline fill="none" stroke="#20bf73" stroke-width="3" points="<?= $linePoints('outpatient') ?>"/>
                    <polyline fill="none" stroke="#3b82f6" stroke-width="3" points="<?= $linePoints('inpatient') ?>"/>
                    <polyline fill="none" stroke="#ef476f" stroke-width="3" points="<?= $linePoints('lab') ?>"/>
                </svg>
            </div>
            <div class="d-flex justify-content-between text-body fs-12 mt-2">
                <?php foreach ($trend as $row): ?><span><?= htmlspecialchars($row['label']) ?></span><?php endforeach; ?>
            </div>
        </div>
    </div>

    <div class="col-xl-4">
        <div class="simrs-dashboard-card p-20 h-100">
            <div class="d-flex justify-content-between mb-3"><h4 class="mb-0">Kunjungan per Poli</h4><span class="badge bg-light text-primary">Hari Ini</span></div>
            <div class="d-flex align-items-center gap-4">
                <div class="simrs-donut flex-shrink-0"><span>Total<br><?= (int) $polyTotal ?></span></div>
                <div class="flex-grow-1">
                    <?php foreach ($polyRows as $index => $poly): ?>
                        <?php $percent = round(((int) $poly['total'] / $polyTotal) * 100); ?>
                        <div class="d-flex justify-content-between mb-2">
                            <span><i class="ri-square-fill" style="color: <?= ['#31c48d','#3b82f6','#8b5cf6','#f59e0b','#14b8a6','#94a3b8'][$index % 6] ?>"></i> <?= htmlspecialchars($poly['name']) ?></span>
                            <strong><?= (int) $poly['total'] ?> (<?= $percent ?>%)</strong>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-2">
        <div class="simrs-dashboard-card p-20 h-100">
            <div class="d-flex justify-content-between mb-3"><h4 class="mb-0">Notifikasi</h4><a href="<?= url('activity-logs') ?>">Lihat Semua</a></div>
            <?php foreach (($stats['notifications'] ?? []) as $note): ?>
                <div class="d-flex gap-2 border-bottom py-2">
                    <span class="material-symbols-outlined p-2 rounded" style="background: <?= htmlspecialchars($note['bg']) ?>;"><?= htmlspecialchars($note['icon']) ?></span>
                    <div><strong class="fs-13"><?= htmlspecialchars($note['title']) ?></strong><p class="text-body fs-12 mb-0"><?= htmlspecialchars($note['text']) ?></p></div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-xl-5">
        <div class="simrs-dashboard-card p-20 h-100">
            <div class="d-flex justify-content-between mb-3"><h4 class="mb-0">Pasien Terbaru</h4><a href="<?= url('simrs-patients') ?>">Lihat Semua</a></div>
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead><tr><th>No. RM</th><th>Nama Pasien</th><th>Poli</th><th>Dokter</th><th>Status</th></tr></thead>
                    <tbody>
                    <?php foreach (($stats['recent_patients'] ?? []) as $row): ?>
                        <tr>
                            <td><?= htmlspecialchars($row['medical_record_no']) ?></td>
                            <td><?= htmlspecialchars($row['patient_name']) ?></td>
                            <td><?= htmlspecialchars($row['polyclinic_name'] ?: '-') ?></td>
                            <td><?= htmlspecialchars($row['doctor_name'] ?: '-') ?></td>
                            <td><?= simrsStatusBadge($row['status'] ?? '') ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-xl-3">
        <div class="simrs-dashboard-card p-20 h-100">
            <div class="d-flex justify-content-between mb-3"><h4 class="mb-0">Bed Occupancy (BOR)</h4><a href="<?= url('simrs-inpatient') ?>">Lihat Detail</a></div>
            <div class="d-flex align-items-center gap-4">
                <div class="simrs-bor-ring" style="--bor-percent: <?= (int) $stats['bed_occupancy']['percent'] ?>%;"><span><?= (int) $stats['bed_occupancy']['percent'] ?>%<br><small>Terisi</small></span></div>
                <div class="flex-grow-1">
                    <div class="d-flex justify-content-between mb-2"><span>Total Tempat Tidur</span><strong><?= (int) $stats['bed_occupancy']['total'] ?></strong></div>
                    <div class="d-flex justify-content-between mb-2"><span>Terisi</span><strong><?= (int) $stats['bed_occupancy']['occupied'] ?></strong></div>
                    <div class="d-flex justify-content-between mb-3"><span>Kosong</span><strong><?= (int) $stats['bed_occupancy']['available'] ?></strong></div>
                    <hr>
                    <?php foreach (($stats['bed_occupancy']['classes'] ?? []) as $class): ?>
                        <div class="d-flex justify-content-between mb-2"><span><?= htmlspecialchars($class['class_name']) ?></span><strong><?= (int) $class['occupied'] ?> / <?= (int) $class['total'] ?></strong></div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-4">
        <div class="simrs-dashboard-card p-20 h-100">
            <div class="d-flex justify-content-between"><span class="text-primary">Statistik</span><a href="<?= url('simrs-reports-income') ?>">Lihat Laporan</a></div>
            <h4 class="mt-3">Pendapatan <span class="text-body">(7 Hari Terakhir)</span></h4>
            <h3><?= $money(array_sum(array_map(fn($row) => (float) $row['amount'], $incomeTrend))) ?></h3>
            <p class="text-body">Total pembayaran pasien dalam 7 hari terakhir</p>
            <div class="d-flex align-items-end gap-3" style="height: 160px;">
                <?php foreach ($incomeTrend as $bar): ?>
                    <div class="flex-grow-1 rounded-top" title="<?= htmlspecialchars($bar['label']) ?>" style="height: <?= max(8, ((float) $bar['amount'] / $maxIncome) * 140) ?>px; background: linear-gradient(#13b8c6,#0f9f96);"></div>
                <?php endforeach; ?>
            </div>
            <div class="d-flex justify-content-between text-body fs-12 mt-2">
                <?php foreach ($incomeTrend as $bar): ?><span><?= htmlspecialchars($bar['label']) ?></span><?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<div class="simrs-dashboard-card p-20 mb-4">
    <h4 class="mb-3">Akses Cepat</h4>
    <div class="row g-3">
        <?php foreach ([['Pendaftaran','simrs-registration','person_add'],['Antrean','simrs-queue','groups'],['Rawat Jalan','simrs-outpatient','stethoscope'],['Rawat Inap','simrs-inpatient','hotel'],['Laboratorium','simrs-laboratory','science'],['Farmasi','simrs-pharmacy','medication'],['Billing','simrs-billing','receipt_long'],['Kasir','simrs-cashier','point_of_sale'],['Laporan','simrs-reports-visits','bar_chart']] as $quick): ?>
            <div class="col-sm-6 col-lg"><a class="simrs-quick-button text-decoration-none" href="<?= url($quick[1]) ?>"><span class="material-symbols-outlined"><?= $quick[2] ?></span><?= $quick[0] ?></a></div>
        <?php endforeach; ?>
    </div>
</div>
