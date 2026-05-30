<?php
$stats = $stats ?? [];
$occupancy = $stats['occupancy'] ?? [];
$activeVehicles = (int) ($stats['active_vehicles'] ?? 0);
$vehicleTypes = $stats['vehicle_types'] ?? [];
$vehicleTotal = max(1, array_sum(array_map(fn($row) => (int) ($row['total'] ?? 0), $vehicleTypes)));
$colors = ['#31c48d', '#2563eb', '#f97316', '#ef4444', '#94a3b8', '#8b5cf6'];
$donutStops = [];
$cursor = 0;
foreach ($vehicleTypes as $index => $row) {
    $share = ((int) ($row['total'] ?? 0) / $vehicleTotal) * 100;
    $next = min(100, $cursor + $share);
    $donutStops[] = $colors[$index % count($colors)] . " {$cursor}% {$next}%";
    $cursor = $next;
}
$donutBackground = $donutStops ? implode(', ', $donutStops) : '#e8eef6 0 100%';
$incomeSplit = $stats['income_today_split'] ?? ['cash' => 0, 'non_cash' => 0];
$incomeHours = $stats['income_today_hours'] ?? [];
$maxIncome = max(1, ...array_map(fn($row) => (float) ($row['total'] ?? 0), $incomeHours ?: [['total' => 1]]));
$points = [];
foreach ($incomeHours as $index => $row) {
    $x = 20 + ($index * 52);
    $y = 150 - (((float) ($row['total'] ?? 0) / $maxIncome) * 122);
    $points[] = round($x, 1) . ',' . round($y, 1);
}

function operatorDurationLabel($minutes)
{
    $minutes = (int) $minutes;
    if ($minutes <= 0) {
        return '-';
    }
    $hours = intdiv($minutes, 60);
    $mins = $minutes % 60;
    return ($hours > 0 ? $hours . 'j ' : '') . $mins . 'm';
}

function operatorTicketBadge($ticket)
{
    if (($ticket['payment_status'] ?? '') === 'paid') {
        return ['Sudah Bayar', 'bg-success bg-opacity-10 text-success'];
    }

    if (($ticket['status'] ?? '') === 'active') {
        return ['Belum Bayar', 'bg-warning bg-opacity-10 text-warning'];
    }

    return ['Belum Bayar', 'bg-warning bg-opacity-10 text-warning'];
}
?>

<div class="simrs-dashboard-title mb-4">
    <h1>Dashboard Operator</h1>
    <p class="mb-0 text-body fs-16">Pantau aktivitas operasional parkir secara real-time</p>
</div>

<div class="row g-4 mb-4">
    <?php
    $cards = [
        ['Kendaraan Masuk', number_format((int) ($stats['entries_today'] ?? 0), 0, ',', '.'), 'Hari Ini', 'directions_car', '#e7f9f0', '#159b62', '12% dari kemarin'],
        ['Kendaraan Keluar', number_format((int) ($stats['exits_today'] ?? 0), 0, ',', '.'), 'Hari Ini', 'airport_shuttle', '#eef5ff', '#2563eb', '8% dari kemarin'],
        ['Kendaraan Aktif', number_format($activeVehicles, 0, ',', '.'), 'Sedang Parkir', 'local_parking', '#fff7ed', '#f97316', ''],
        ['Tiket Belum Dibayar', number_format((int) ($stats['unpaid'] ?? 0), 0, ',', '.'), 'Transaksi', 'receipt_long', '#fee2e2', '#dc2626', ''],
        ['Pendapatan Hari Ini', 'Rp ' . number_format((float) ($stats['income_today'] ?? 0), 0, ',', '.'), '', 'account_balance_wallet', '#e7f9f0', '#159b62', '15% dari kemarin'],
    ];
    ?>
    <?php foreach ($cards as $card): ?>
        <div class="col-md-6 col-xl">
            <div class="simrs-dashboard-card p-20 h-100">
                <div class="d-flex justify-content-between gap-3">
                    <div>
                        <p class="mb-2 fw-semibold" style="color: <?= $card[5] ?>;"><?= htmlspecialchars($card[0]) ?></p>
                        <h3 class="mb-1"><?= htmlspecialchars($card[1]) ?></h3>
                        <?php if ($card[2] !== ''): ?><p class="text-body fw-semibold mb-2"><?= htmlspecialchars($card[2]) ?></p><?php endif; ?>
                        <?php if ($card[6] !== ''): ?><small class="text-success">↑ <?= htmlspecialchars($card[6]) ?></small><?php endif; ?>
                    </div>
                    <span class="material-symbols-outlined simrs-stat-icon flex-shrink-0" style="background: <?= $card[4] ?>; color: <?= $card[5] ?>;"><?= htmlspecialchars($card[3]) ?></span>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<div class="row g-4 mb-4">
    <div class="col-xl-4">
        <div class="simrs-dashboard-card p-20 h-100">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="mb-0">Kendaraan Aktif di Area</h4>
                <a href="<?= url('parking-areas') ?>" class="fw-semibold text-decoration-none">Lihat Detail</a>
            </div>
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead><tr><th>Area Parkir</th><th class="text-end">Terpakai</th><th class="text-end">Kapasitas</th><th>Okupansi</th></tr></thead>
                    <tbody>
                    <?php foreach ($occupancy as $area): ?>
                        <?php $capacity = max(1, (int) ($area['capacity'] ?? 0)); $used = (int) ($area['active_count'] ?? 0); $percent = min(100, round(($used / $capacity) * 100)); ?>
                        <tr>
                            <td><?= htmlspecialchars(str_replace(['Area ', ' (Gedung Utama)'], ['', ' (Utama)'], $area['name'] ?? '-')) ?></td>
                            <td class="text-end"><?= $used ?></td>
                            <td class="text-end"><?= (int) ($area['capacity'] ?? 0) ?></td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <div class="parking-mini-progress"><span style="width: <?= $percent ?>%; background: <?= $percent < 65 ? '#f97316' : 'linear-gradient(90deg, #37b778, #65c99c)' ?>;"></span></div>
                                    <strong><?= $percent ?>%</strong>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-xl-4">
        <div class="simrs-dashboard-card p-20 h-100">
            <h4 class="mb-4">Kendaraan Aktif per Jenis</h4>
            <div class="d-flex align-items-center gap-4">
                <div class="parking-donut flex-shrink-0" style="background: conic-gradient(<?= htmlspecialchars($donutBackground) ?>);">
                    <div><span>Total</span><strong><?= $activeVehicles ?></strong><small>Kendaraan</small></div>
                </div>
                <div class="flex-grow-1">
                    <?php foreach ($vehicleTypes as $index => $row): ?>
                        <?php $count = (int) ($row['total'] ?? 0); $percent = round(($count / $vehicleTotal) * 100, 1); ?>
                        <div class="d-flex justify-content-between gap-2 mb-3">
                            <span><i class="ri-square-fill" style="color: <?= $colors[$index % count($colors)] ?>;"></i> <?= htmlspecialchars($row['name'] ?? '-') ?></span>
                            <strong><?= $count ?> (<?= $percent ?>%)</strong>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-4">
        <div class="simrs-dashboard-card p-20 h-100">
            <h4 class="mb-4">Pendapatan <span class="text-body">(Hari Ini)</span></h4>
            <div class="operator-income-chart">
                <svg viewBox="0 0 340 165" preserveAspectRatio="none">
                    <defs>
                        <linearGradient id="operatorIncomeFill" x1="0" x2="0" y1="0" y2="1">
                            <stop offset="0%" stop-color="#20bf73" stop-opacity=".28"/>
                            <stop offset="100%" stop-color="#20bf73" stop-opacity="0"/>
                        </linearGradient>
                    </defs>
                    <polyline points="<?= htmlspecialchars(implode(' ', $points)) ?>" fill="none" stroke="#10b981" stroke-width="4"/>
                    <?php if ($points): ?>
                        <polygon points="<?= htmlspecialchars($points[0] . ' ' . implode(' ', $points) . ' ' . end($points) . ' 332,154 20,154') ?>" fill="url(#operatorIncomeFill)"/>
                    <?php endif; ?>
                    <?php foreach ($points as $point): [$x, $y] = explode(',', $point); ?><circle cx="<?= $x ?>" cy="<?= $y ?>" r="5" fill="#10b981"/><?php endforeach; ?>
                </svg>
                <div class="operator-income-axis">
                    <?php foreach ($incomeHours as $row): ?><span><?= htmlspecialchars($row['label']) ?></span><?php endforeach; ?>
                </div>
            </div>
            <div class="row g-3 mt-3">
                <div class="col-6"><div class="operator-income-box"><span>Pendapatan Tunai</span><strong>Rp <?= number_format((float) ($incomeSplit['cash'] ?? 0), 0, ',', '.') ?></strong></div></div>
                <div class="col-6"><div class="operator-income-box"><span>Pendapatan Non Tunai</span><strong>Rp <?= number_format((float) ($incomeSplit['non_cash'] ?? 0), 0, ',', '.') ?></strong></div></div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-xl-8">
        <div class="simrs-dashboard-card p-20 h-100">
            <h4 class="mb-3">Tiket Parkir Aktif Terbaru</h4>
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead><tr><th>No.</th><th>No. Tiket</th><th>Waktu Masuk</th><th>Plat Nomor</th><th>Jenis</th><th>Area</th><th>Durasi</th><th>Tarif</th><th>Status</th></tr></thead>
                    <tbody>
                        <?php foreach (($stats['active_recent'] ?? []) as $index => $ticket): ?>
                            <?php [$badgeLabel, $badgeClass] = operatorTicketBadge($ticket); ?>
                            <tr>
                                <td><?= $index + 1 ?></td>
                                <td><?= htmlspecialchars($ticket['ticket_no'] ?? '-') ?></td>
                                <td><?= !empty($ticket['entry_time']) ? date('d M Y H:i', strtotime($ticket['entry_time'])) : '-' ?></td>
                                <td><?= htmlspecialchars($ticket['plate_number'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($ticket['vehicle_type_name'] ?? '-') ?></td>
                                <td><?= htmlspecialchars($ticket['area_name'] ?? '-') ?></td>
                                <td><?= operatorDurationLabel($ticket['live_duration_minutes'] ?? $ticket['duration_minutes'] ?? 0) ?></td>
                                <td><?= ((float) ($ticket['payable_amount'] ?? 0)) > 0 ? 'Rp ' . number_format((float) $ticket['payable_amount'], 0, ',', '.') : '-' ?></td>
                                <td><span class="default-badge <?= $badgeClass ?>"><?= $badgeLabel ?></span></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-xl-4">
        <div class="simrs-dashboard-card p-20 h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4 class="mb-0">Antrean Gate</h4>
                <a href="<?= url('parking-gate-queue') ?>" class="fw-semibold text-decoration-none">Lihat Semua</a>
            </div>
            <?php foreach (($stats['gate_queue_summary'] ?? []) as $gate): ?>
                <?php $isOut = ($gate['gate_type'] ?? '') === 'exit'; ?>
                <div class="operator-gate-row">
                    <span class="material-symbols-outlined operator-gate-icon <?= $isOut ? 'out' : 'in' ?>"><?= $isOut ? 'logout' : 'move_to_inbox' ?></span>
                    <strong><?= htmlspecialchars($gate['name'] ?? '-') ?></strong>
                    <span class="operator-gate-count ms-auto"><?= (int) ($gate['total'] ?? 0) ?></span>
                    <span class="fw-semibold text-success">Kendaraan</span>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<div class="simrs-dashboard-card p-20 mb-4">
    <h4 class="mb-4">Aksi Cepat</h4>
    <div class="operator-quick-grid">
        <a href="<?= url('parking-checkin') ?>" class="operator-quick-card" style="--quick-bg:#e7f9f0;--quick-color:#159b62;"><span class="material-symbols-outlined">directions_car</span><strong>Check-In</strong><small>Kendaraan</small></a>
        <a href="<?= url('parking-checkout') ?>" class="operator-quick-card" style="--quick-bg:#eef5ff;--quick-color:#2563eb;"><span class="material-symbols-outlined">airport_shuttle</span><strong>Check-Out</strong><small>Kendaraan</small></a>
        <a href="<?= url('parking-tickets') ?>" class="operator-quick-card" style="--quick-bg:#fff7ed;--quick-color:#f97316;"><span class="material-symbols-outlined">receipt_long</span><strong>Tiket Parkir</strong><small>Aktif</small></a>
        <a href="<?= url('parking-validations') ?>" class="operator-quick-card" style="--quick-bg:#f4edff;--quick-color:#7c3aed;"><span class="material-symbols-outlined">verified_user</span><strong>Validasi Parkir</strong><small>Pasien</small></a>
        <a href="<?= url('parking-gate-queue') ?>" class="operator-quick-card" style="--quick-bg:#effafa;--quick-color:#0ea5e9;"><span class="material-symbols-outlined">groups</span><strong>Antrean Gate</strong><small>&nbsp;</small></a>
        <a href="<?= url('parking-reports-transactions') ?>" class="operator-quick-card" style="--quick-bg:#f8fafc;--quick-color:#334155;"><span class="material-symbols-outlined">request_quote</span><strong>Transaksi</strong><small>Hari Ini</small></a>
    </div>
</div>
