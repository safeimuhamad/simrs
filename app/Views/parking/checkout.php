<div class="card bg-white rounded-10 border border-white mb-4">
    <div class="p-20 border-bottom"><h3 class="mb-0">Check-out Kendaraan</h3></div>
    <div class="p-20 border-bottom">
        <form class="row g-2"><input type="hidden" name="page" value="parking-checkout"><div class="col-md-10"><input class="form-control" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Cari tiket/plat"></div><div class="col-md-2"><button class="btn btn-outline-primary w-100">Cari</button></div></form>
    </div>
    <div class="default-table-area mx-minus-1"><div class="table-responsive"><table class="table align-middle"><thead><tr><th>Tiket</th><th>Plat</th><th>Jenis</th><th>Masuk</th><th>Status</th><th></th></tr></thead><tbody>
        <?php foreach ($tickets as $ticket): ?><tr><td><?= htmlspecialchars($ticket['ticket_no']) ?></td><td class="fw-semibold"><?= htmlspecialchars($ticket['plate_number']) ?></td><td><?= htmlspecialchars($ticket['vehicle_type_name']) ?></td><td><?= htmlspecialchars($ticket['entry_time']) ?></td><td><?= simrsStatusBadge($ticket['status'] ?? '', 'parking') ?></td><td class="text-end"><a class="btn btn-sm btn-primary text-white" href="<?= url('parking-tickets-show', ['id' => $ticket['id']]) ?>">Proses</a></td></tr><?php endforeach; ?>
        <?php if (empty($tickets)): ?><tr><td colspan="6" class="text-center py-4">Tidak ada kendaraan aktif.</td></tr><?php endif; ?>
    </tbody></table></div><?= adminListFooter($baseRoute ?? 'parking-checkout', $search ?? '', $currentPage ?? 1, $totalPages ?? 1, $totalData ?? count($tickets ?? []), $limit ?? 10) ?></div>
</div>
