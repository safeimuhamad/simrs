<?php
$cards = $dashboard['cards'] ?? [];
$sections = $dashboard['sections'] ?? [];
$quick = $dashboard['quick'] ?? [];
$toneColor = [
    'blue' => ['#eef5ff', '#2563eb'],
    'green' => ['#e7f9f0', '#18a968'],
    'orange' => ['#fff3e8', '#f97316'],
    'purple' => ['#f3edff', '#7c3aed'],
    'teal' => ['#e6fbf7', '#0f9f96'],
    'red' => ['#fee2e2', '#ef4444'],
];
?>

<div class="d-flex justify-content-between align-items-start gap-3 flex-wrap mb-4">
    <div class="simrs-dashboard-title">
        <h1><?= htmlspecialchars($title ?? 'Dashboard') ?> <span class="material-symbols-outlined align-middle text-success">verified</span></h1>
        <p class="mb-0 text-body fs-16">Selamat datang, <?= htmlspecialchars($_SESSION['name'] ?? 'User SIMRS') ?></p>
        <p class="mb-0 text-body"><?= htmlspecialchars($subtitle ?? 'Data operasional hari ini.') ?></p>
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
    <?php foreach ($cards as $card): ?>
        <?php [$bg, $fg] = $toneColor[$card['tone'] ?? 'blue'] ?? $toneColor['blue']; ?>
        <div class="col-md-6 col-xl-3">
            <div class="simrs-dashboard-card p-20 h-100">
                <div class="d-flex justify-content-between align-items-center gap-3">
                    <div>
                        <p class="mb-2 fw-semibold" style="color: <?= htmlspecialchars($fg) ?>;"><?= htmlspecialchars($card['label'] ?? '-') ?></p>
                        <h3 class="mb-2"><?= htmlspecialchars((string) ($card['value'] ?? 0)) ?></h3>
                        <small class="text-body"><?= htmlspecialchars($card['hint'] ?? '') ?></small>
                    </div>
                    <span class="material-symbols-outlined simrs-stat-icon" style="background: <?= htmlspecialchars($bg) ?>; color: <?= htmlspecialchars($fg) ?>;"><?= htmlspecialchars($card['icon'] ?? 'dashboard') ?></span>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<div class="row g-4 mb-4">
    <?php foreach ($sections as $index => $section): ?>
        <div class="<?= count($sections) === 1 ? 'col-12' : ($index === 0 ? 'col-xl-7' : 'col-xl-5') ?>">
            <div class="simrs-dashboard-card p-0 h-100 overflow-hidden">
                <div class="p-20 border-bottom d-flex justify-content-between align-items-center gap-3">
                    <h4 class="mb-0"><?= htmlspecialchars($section['title'] ?? 'Data') ?></h4>
                    <span class="badge bg-light text-primary">Realtime</span>
                </div>
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead>
                            <tr>
                                <?php foreach (($section['columns'] ?? []) as $column): ?>
                                    <th><?= htmlspecialchars($column) ?></th>
                                <?php endforeach; ?>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($section['rows'])): ?>
                                <?php foreach ($section['rows'] as $row): ?>
                                    <tr>
                                        <?php foreach ($row as $cell): ?>
                                            <td><?= $cell ?></td>
                                        <?php endforeach; ?>
                                    </tr>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <tr><td colspan="<?= max(1, count($section['columns'] ?? [])) ?>" class="text-center text-body py-4">Belum ada data.</td></tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</div>

<?php if (!empty($quick)): ?>
    <div class="simrs-dashboard-card p-20 mb-4">
        <h4 class="mb-3">Akses Cepat</h4>
        <div class="row g-3">
            <?php foreach ($quick as $item): ?>
                <?php if (!isset($item[1]) || $item[1] === '#' || can('simrs_dashboard.view') || role_name() === 'super_admin'): ?>
                    <div class="col-sm-6 col-lg-3">
                        <a class="simrs-quick-button text-decoration-none" href="<?= url($item[1] ?? '#') ?>">
                            <span class="material-symbols-outlined"><?= htmlspecialchars($item[2] ?? 'open_in_new') ?></span>
                            <?= htmlspecialchars($item[0] ?? '-') ?>
                        </a>
                    </div>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
    </div>
<?php endif; ?>
