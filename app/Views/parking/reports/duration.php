<div class="card bg-white rounded-10 border border-white mb-4">
    <div class="p-20 border-bottom">
        <h3 class="mb-0">Laporan Durasi Parkir</h3>
        <p class="text-body fs-14 mb-0">Rata-rata durasi parkir per kategori kendaraan.</p>
    </div>
    <div class="default-table-area mx-minus-1">
        <div class="table-responsive">
            <table class="table align-middle">
                <thead><tr><th>Kategori Kendaraan</th><th class="text-end">Total Tiket</th><th class="text-end">Rata-rata</th><th class="text-end">Tercepat</th><th class="text-end">Terlama</th></tr></thead>
                <tbody>
                    <?php foreach ($rows as $row): ?>
                        <?php
                            $avg = (int) round((float) $row['avg_duration']);
                            $min = (int) $row['min_duration'];
                            $max = (int) $row['max_duration'];
                        ?>
                        <tr>
                            <td><?= htmlspecialchars($row['vehicle_type_name']) ?></td>
                            <td class="text-end"><?= number_format((int) $row['total_tickets'], 0, ',', '.') ?></td>
                            <td class="text-end"><?= floor($avg / 60) ?>j <?= $avg % 60 ?>m</td>
                            <td class="text-end"><?= floor($min / 60) ?>j <?= $min % 60 ?>m</td>
                            <td class="text-end"><?= floor($max / 60) ?>j <?= $max % 60 ?>m</td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($rows)): ?><tr><td colspan="5" class="text-center py-4">Belum ada data durasi parkir.</td></tr><?php endif; ?>
                </tbody>
            </table>
        </div>
        <?= adminListFooter($baseRoute ?? 'parking-reports-duration', $search ?? '', $currentPage ?? 1, $totalPages ?? 1, $totalData ?? count($rows ?? []), $limit ?? 10) ?>
    </div>
</div>
