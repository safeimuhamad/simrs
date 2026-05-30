<?php
$stats = $stats ?? [];
$occupancy = $stats['occupancy'] ?? [];
$activeVehicles = (int) ($stats['active_vehicles'] ?? 0);
$totalCapacity = max(0, (int) ($stats['total_capacity'] ?? 0));
$occupancyPercent = $totalCapacity > 0 ? min(100, round(($activeVehicles / $totalCapacity) * 100)) : 0;
$incomeSeries = $stats['income_7_days'] ?? [];
$maxIncome = max(1, ...array_map(fn($row) => (float) ($row['total'] ?? 0), $incomeSeries ?: [['total' => 1]]));
$totalIncome7 = array_sum(array_map(fn($row) => (float) ($row['total'] ?? 0), $incomeSeries));
$avgIncome7 = count($incomeSeries) ? $totalIncome7 / count($incomeSeries) : 0;
$vehicleTypes = $stats['vehicle_types'] ?? [];
$vehicleTotal = max(1, array_sum(array_map(fn($row) => (int) ($row['total'] ?? 0), $vehicleTypes)));
$colors = ['#31c48d', '#3b82f6', '#f59e0b', '#ef4444', '#94a3b8', '#8b5cf6'];
$donutStops = [];
$cursor = 0;
foreach ($vehicleTypes as $index => $row) {
    $share = ((int) ($row['total'] ?? 0) / $vehicleTotal) * 100;
    $next = min(100, $cursor + $share);
    $color = $colors[$index % count($colors)];
    $donutStops[] = "{$color} {$cursor}% {$next}%";
    $cursor = $next;
}
$donutBackground = $donutStops ? implode(', ', $donutStops) : '#e8eef6 0 100%';

function parkingStatusBadge($ticket)
{
    $payment = $ticket['payment_status'] ?? 'unpaid';
    $status = $ticket['status'] ?? 'active';
    if ($payment === 'paid') {
        return ['Sudah Bayar', 'bg-success bg-opacity-10 text-success'];
    }
    if ($status === 'active') {
        return ['Aktif', 'bg-primary bg-opacity-10 text-primary'];
    }
    if ($status === 'lost_ticket') {
        return ['Tiket Lost', 'bg-danger bg-opacity-10 text-danger'];
    }
    return ['Belum Bayar', 'bg-warning bg-opacity-10 text-warning'];
}

function parkingDurationLabel($minutes)
{
    $minutes = (int) $minutes;
    if ($minutes <= 0) {
        return '-';
    }
    $hours = intdiv($minutes, 60);
    $mins = $minutes % 60;
    return ($hours > 0 ? $hours . 'j ' : '') . $mins . 'm';
}
?>

<div class="d-flex justify-content-between align-items-start gap-3 flex-wrap mb-4 parking-dashboard-head">
    <div class="simrs-dashboard-title">
        <h1>Dashboard Parkir <span class="material-symbols-outlined align-middle text-success">directions_car</span></h1>
        <p class="mb-0 text-body fs-16">Pantau aktivitas parkir rumah sakit secara real-time</p>
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
    $cards = [
        ['Kendaraan Masuk', number_format((int) ($stats['entries_today'] ?? 0), 0, ',', '.'), 'Hari Ini', 'directions_car', '#e7f9f0', '#159b62', '12% dari kemarin'],
        ['Kendaraan Keluar', number_format((int) ($stats['exits_today'] ?? 0), 0, ',', '.'), 'Hari Ini', 'airport_shuttle', '#eef5ff', '#2563eb', '8% dari kemarin'],
        ['Kendaraan Aktif', number_format($activeVehicles, 0, ',', '.'), 'Sedang Parkir', 'local_parking', '#fff7ed', '#d97706', 'Total di Area Parkir'],
        ['Pendapatan Parkir', 'Rp ' . number_format((float) ($stats['income_today'] ?? 0), 0, ',', '.'), 'Hari Ini', 'account_balance_wallet', '#f4edff', '#7c3aed', '15% dari kemarin'],
        ['Tiket Belum Dibayar', number_format((int) ($stats['unpaid'] ?? 0), 0, ',', '.'), 'Transaksi', 'receipt_long', '#fee2e2', '#dc2626', 'Total Tagihan'],
        ['Tiket Lost', number_format((int) ($stats['lost_ticket'] ?? 0), 0, ',', '.'), 'Transaksi', 'confirmation_number', '#e6fbf7', '#0f766e', 'Hari Ini'],
    ];
    ?>
    <?php foreach ($cards as $card): ?>
        <div class="col-md-6 col-xl-2">
            <div class="simrs-dashboard-card p-20 h-100">
                <div class="d-flex justify-content-between gap-3">
                    <div>
                        <p class="mb-2 fw-semibold" style="color: <?= $card[5] ?>;"><?= htmlspecialchars($card[0]) ?></p>
                        <h3 class="mb-1"><?= htmlspecialchars($card[1]) ?></h3>
                        <p class="text-body fw-semibold mb-2"><?= htmlspecialchars($card[2]) ?></p>
                        <small class="<?= str_contains($card[6], '%') ? 'text-success' : 'text-body' ?>"><?= str_contains($card[6], '%') ? '↑ ' : '' ?><?= htmlspecialchars($card[6]) ?></small>
                    </div>
                    <span class="material-symbols-outlined simrs-stat-icon flex-shrink-0" style="background: <?= $card[4] ?>; color: <?= $card[5] ?>;"><?= htmlspecialchars($card[3]) ?></span>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<div class="row g-4 mb-4">
    <div class="col-xl-5">
        <div class="simrs-dashboard-card p-20 h-100">
            <h4 class="mb-4">Okupansi Area Parkir</h4>
            <div class="row g-4 align-items-center">
                <div class="col-md-4">
                    <div class="parking-occupancy-ring mx-auto" style="--parking-occupancy: <?= $occupancyPercent ?>%;">
                        <div>
                            <small>Total Okupansi</small>
                            <strong><?= $occupancyPercent ?>%</strong>
                            <span><?= $activeVehicles ?> / <?= $totalCapacity ?> Kendaraan</span>
                        </div>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead><tr><th>Area Parkir</th><th>Okupansi</th><th>Kapasitas</th><th>Terpakai</th></tr></thead>
                            <tbody>
                            <?php foreach ($occupancy as $area): ?>
                                <?php $capacity = max(1, (int) ($area['capacity'] ?? 0)); $used = (int) ($area['active_count'] ?? 0); $percent = min(100, round(($used / $capacity) * 100)); ?>
                                <tr>
                                    <td><?= htmlspecialchars($area['name'] ?? '-') ?></td>
                                    <td>
                                        <div class="d-flex align-items-center gap-2">
                                            <span class="fw-semibold"><?= $percent ?>%</span>
                                            <div class="parking-mini-progress"><span style="width: <?= $percent ?>%;"></span></div>
                                        </div>
                                    </td>
                                    <td><?= (int) ($area['capacity'] ?? 0) ?></td>
                                    <td><?= $used ?></td>
                                </tr>
                            <?php endforeach; ?>
                            <?php if (empty($occupancy)): ?><tr><td colspan="4" class="text-center text-body py-4">Belum ada area parkir.</td></tr><?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            <div class="border-top pt-3 mt-3"><a href="<?= url('parking-areas') ?>" class="fw-semibold text-decoration-none">Lihat Detail Area <span class="material-symbols-outlined align-middle fs-18">east</span></a></div>
        </div>
    </div>

    <div class="col-xl-3">
        <div class="simrs-dashboard-card p-20 h-100">
            <h4 class="mb-4">Kendaraan Aktif Per Jenis</h4>
            <div class="d-flex align-items-center gap-4">
                <div class="parking-donut flex-shrink-0" style="background: conic-gradient(<?= htmlspecialchars($donutBackground) ?>);">
                    <div><span>Total</span><strong><?= $activeVehicles ?></strong><small>Kendaraan</small></div>
                </div>
                <div class="flex-grow-1">
                    <?php foreach ($vehicleTypes as $index => $row): ?>
                        <?php $count = (int) ($row['total'] ?? 0); $percent = $vehicleTotal > 0 ? round(($count / $vehicleTotal) * 100, 1) : 0; ?>
                        <div class="d-flex justify-content-between gap-2 mb-3">
                            <span><i class="ri-square-fill" style="color: <?= $colors[$index % count($colors)] ?>;"></i> <?= htmlspecialchars($row['name'] ?? '-') ?></span>
                            <strong><?= $count ?> (<?= $percent ?>%)</strong>
                        </div>
                    <?php endforeach; ?>
                    <?php if (empty($vehicleTypes)): ?><p class="text-body mb-0">Belum ada kendaraan aktif.</p><?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-4">
        <div class="simrs-dashboard-card p-20 h-100">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4 class="mb-0">Pendapatan Parkir <span class="text-body">(7 Hari Terakhir)</span></h4>
                <select class="form-control w-auto"><option>7 Hari Terakhir</option></select>
            </div>
            <div class="parking-bar-chart">
                <?php foreach ($incomeSeries as $row): ?>
                    <?php $height = max(10, round(((float) ($row['total'] ?? 0) / $maxIncome) * 150)); ?>
                    <div class="parking-bar-item">
                        <span class="parking-bar" style="height: <?= $height ?>px;"></span>
                        <small><?= htmlspecialchars($row['label']) ?></small>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="parking-income-summary mt-4">
                <div><span>Total 7 Hari</span><strong>Rp <?= number_format($totalIncome7, 0, ',', '.') ?></strong></div>
                <div><span>Rata-rata / Hari</span><strong>Rp <?= number_format($avgIncome7, 0, ',', '.') ?></strong></div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-xl-7">
        <div class="simrs-dashboard-card p-20 h-100">
            <h4 class="mb-3">Transaksi Terbaru</h4>
            <div class="table-responsive">
                <table class="table align-middle">
                    <thead><tr><th>No. Tiket</th><th>Waktu Masuk</th><th>Plat Nomor</th><th>Jenis</th><th>Area</th><th>Waktu Keluar</th><th>Durasi</th><th>Tarif</th><th>Status</th></tr></thead>
                    <tbody>
                    <?php foreach (($stats['recent_transactions'] ?? []) as $ticket): ?>
                        <?php [$label, $class] = parkingStatusBadge($ticket); ?>
                        <tr>
                            <td><?= htmlspecialchars($ticket['ticket_no'] ?? '-') ?></td>
                            <td><?= !empty($ticket['entry_time']) ? date('d M Y H:i', strtotime($ticket['entry_time'])) : '-' ?></td>
                            <td><?= htmlspecialchars($ticket['plate_number'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($ticket['vehicle_type_name'] ?? '-') ?></td>
                            <td><?= htmlspecialchars($ticket['area_name'] ?? '-') ?></td>
                            <td><?= !empty($ticket['exit_time']) ? date('d M Y H:i', strtotime($ticket['exit_time'])) : '-' ?></td>
                            <td><?= parkingDurationLabel($ticket['duration_minutes'] ?? 0) ?></td>
                            <td><?= ((float) ($ticket['payable_amount'] ?? 0)) > 0 ? 'Rp ' . number_format((float) $ticket['payable_amount'], 0, ',', '.') : '-' ?></td>
                            <td><span class="default-badge <?= $class ?>"><?= $label ?></span></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($stats['recent_transactions'])): ?><tr><td colspan="9" class="text-center text-body py-4">Belum ada transaksi parkir.</td></tr><?php endif; ?>
                    </tbody>
                </table>
            </div>
            <div class="border-top pt-3"><a href="<?= url('parking-tickets') ?>" class="fw-semibold text-decoration-none">Lihat Semua Transaksi <span class="material-symbols-outlined align-middle fs-18">east</span></a></div>
        </div>
    </div>

    <div class="col-xl-5">
        <div class="simrs-dashboard-card p-20 h-100">
            <div class="d-flex justify-content-between mb-3"><h4 class="mb-0">Gate Aktivitas <span class="text-body">(Real-time)</span></h4><a href="<?= url('parking-gates') ?>">Lihat Semua Gate</a></div>
            <?php foreach (($stats['gate_logs'] ?? []) as $log): ?>
                <?php $isOut = str_contains((string) ($log['action'] ?? ''), 'exit') || ($log['gate_type'] ?? '') === 'exit'; ?>
                <div class="parking-gate-row">
                    <span class="material-symbols-outlined parking-gate-icon <?= $isOut ? 'out' : 'in' ?>"><?= $isOut ? 'logout' : 'login' ?></span>
                    <strong><?= htmlspecialchars($log['gate_name'] ?? '-') ?></strong>
                    <span class="default-badge <?= $isOut ? 'bg-danger bg-opacity-10 text-danger' : 'bg-success bg-opacity-10 text-success' ?>"><?= $isOut ? 'OUT' : 'IN' ?></span>
                    <span class="text-body"><?= !empty($log['created_at']) ? date('H:i:s', strtotime($log['created_at'])) : '-' ?></span>
                    <span><?= htmlspecialchars(trim(($log['plate_number'] ?? '-') . ' - ' . ($log['vehicle_type_name'] ?? ''))) ?></span>
                    <span class="default-badge bg-success bg-opacity-10 text-success ms-auto">Terbuka</span>
                </div>
            <?php endforeach; ?>
            <?php if (empty($stats['gate_logs'])): ?><p class="text-center text-body py-4 mb-0">Belum ada aktivitas gate.</p><?php endif; ?>
            <div class="border-top pt-3 mt-2"><a href="<?= url('parking-gates') ?>" class="fw-semibold text-decoration-none">Lihat Semua Gate <span class="material-symbols-outlined align-middle fs-18">east</span></a></div>
        </div>
    </div>
</div>

<div class="simrs-dashboard-card p-20 mb-4">
    <h4 class="mb-3">Ringkasan Tiket</h4>
    <div class="row g-4">
        <?php
        $summaryCards = [
            ['Aktif', $activeVehicles, 'Kendaraan', 'directions_car', '#eef5ff', '#2563eb'],
            ['Belum Dibayar', (int) ($stats['unpaid'] ?? 0), 'Kendaraan', 'receipt_long', '#fff7ed', '#d97706'],
            ['Tiket Lost', (int) ($stats['lost_ticket'] ?? 0), 'Kendaraan', 'confirmation_number', '#fee2e2', '#dc2626'],
            ['Sudah Bayar Hari Ini', (int) ($stats['paid_today'] ?? 0), 'Transaksi', 'check_circle', '#e7f9f0', '#159b62'],
            ['Rata-rata Durasi', parkingDurationLabel($stats['avg_duration_today'] ?? 0), 'Hari Ini', 'schedule', '#f4edff', '#7c3aed'],
            ['Kapasitas Total', $totalCapacity, 'Slot Parkir', 'local_parking', '#e6fbf7', '#0f766e'],
        ];
        ?>
        <?php foreach ($summaryCards as $card): ?>
            <div class="col-md-6 col-xl-2">
                <div class="parking-summary-card" style="background: <?= $card[4] ?>;">
                    <div>
                        <span style="color: <?= $card[5] ?>;"><?= htmlspecialchars($card[0]) ?></span>
                        <strong><?= htmlspecialchars((string) $card[1]) ?></strong>
                        <small><?= htmlspecialchars($card[2]) ?></small>
                    </div>
                    <span class="material-symbols-outlined simrs-stat-icon" style="background: rgba(255,255,255,.55); color: <?= $card[5] ?>;"><?= htmlspecialchars($card[3]) ?></span>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
