<div class="card bg-white rounded-10 border border-white mb-4">
    <div class="p-20"><h3 class="mb-0">Antrean Poli</h3><p class="text-body fs-14 mb-0">Panggil pasien dan ubah status antrean.</p></div>
    <div class="p-20 border-top"><form class="row g-2"><input type="hidden" name="page" value="simrs-queue"><div class="col-md-10"><input class="form-control" name="search" value="<?= htmlspecialchars($search ?? '') ?>" placeholder="Cari antrean/pasien"></div><div class="col-md-2"><button class="btn btn-outline-primary w-100">Cari</button></div></form></div>
    <div class="default-table-area mx-minus-1"><div class="table-responsive"><table class="table align-middle"><thead><tr><th>No.</th><th>Pasien</th><th>Poli / Dokter</th><th>Status</th><th>Aksi</th></tr></thead><tbody>
    <?php foreach ($queues as $q): ?>
        <?php
        $source = $q['queue_source'] ?? 'visit';
        $serveStatus = $source === 'self_service' ? 'serving' : 'in_service';
        $actionParams = ['id' => $q['id'], 'source' => $source];
        ?>
        <tr>
            <td class="fs-4 fw-bold text-primary"><?= htmlspecialchars($q['queue_no']) ?></td>
            <td><?= htmlspecialchars($q['patient_name'] ?: 'Pasien/Pengunjung') ?><br><small><?= htmlspecialchars($q['medical_record_no'] ?? '-') ?></small></td>
            <td><?= htmlspecialchars($q['polyclinic_name'] ?? '-') ?><br><small><?= htmlspecialchars($q['doctor_name'] ?: '-') ?></small></td>
            <td><?= simrsStatusBadge($q['status'] ?? '', 'queue') ?></td>
            <td>
                <div class="d-flex flex-wrap gap-1">
                    <form method="POST" action="<?= url('simrs-queue-status', array_merge($actionParams, ['status' => 'called'])) ?>"><button class="btn btn-sm btn-outline-primary">Panggil</button></form>
                    <form method="POST" action="<?= url('simrs-queue-status', array_merge($actionParams, ['status' => $serveStatus])) ?>"><button class="btn btn-sm btn-outline-warning">Layani</button></form>
                    <?php if ($source !== 'self_service' && !empty($q['visit_id'])): ?><a class="btn btn-sm btn-outline-success" href="<?= url('simrs-outpatient-examine', ['visit_id'=>$q['visit_id']]) ?>">Buka EMR</a><?php endif; ?>
                    <form method="POST" action="<?= url('simrs-queue-status', array_merge($actionParams, ['status' => 'done'])) ?>"><button class="btn btn-sm btn-outline-secondary">Selesai</button></form>
                </div>
            </td>
        </tr>
    <?php endforeach; ?>
    <?php if (empty($queues)): ?><tr><td colspan="5" class="text-center py-4">Antrean kosong.</td></tr><?php endif; ?>
    </tbody></table></div><?= adminListFooter($baseRoute ?? 'simrs-queue', $search ?? '', $currentPage ?? 1, $totalPages ?? 1, $totalData ?? count($queues ?? []), $limit ?? 10) ?></div>
</div>
