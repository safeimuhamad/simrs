<div class="card bg-white rounded-10 border border-white mb-4">
    <div class="p-20 border-bottom"><h3 class="mb-0">Laporan Okupansi Parkir</h3></div>
    <div class="default-table-area mx-minus-1"><div class="table-responsive"><table class="table align-middle"><thead><tr><th>Area</th><th>Lokasi</th><th class="text-end">Kapasitas</th><th class="text-end">Terisi</th><th class="text-end">Okupansi</th></tr></thead><tbody>
        <?php foreach ($rows as $row): $cap=max(1,(int)$row['capacity']); $pct=round(((int)$row['active_count']/$cap)*100); ?><tr><td><?= htmlspecialchars($row['name']) ?></td><td><?= htmlspecialchars($row['location'] ?: '-') ?></td><td class="text-end"><?= (int)$row['capacity'] ?></td><td class="text-end"><?= (int)$row['active_count'] ?></td><td class="text-end"><?= $pct ?>%</td></tr><?php endforeach; ?>
    </tbody></table></div><?= adminListFooter($baseRoute ?? 'parking-reports-occupancy', $search ?? '', $currentPage ?? 1, $totalPages ?? 1, $totalData ?? count($rows ?? []), $limit ?? 10) ?></div>
</div>
