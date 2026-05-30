<?php $isEdit = !empty($item['id']); ?>
<div class="card bg-white rounded-10 border border-white p-20 mb-4">
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
        <div><h3 class="mb-1"><?= htmlspecialchars($title) ?></h3><p class="mb-0 text-body">Kelola data master parking management.</p></div>
        <a href="<?= url($config['route']) ?>" class="btn btn-light erp-btn"><i class="ri-arrow-left-line me-1"></i>Kembali</a>
    </div>
</div>
<form method="post" action="<?= url($config['route'] . ($isEdit ? '-update' : '-store')) ?>">
    <?php if ($isEdit): ?><input type="hidden" name="id" value="<?= htmlspecialchars($item['id']) ?>"><?php endif; ?>
    <div class="card bg-white rounded-10 border border-white mb-4">
        <div class="p-20 border-bottom"><h4 class="erp-detail-section-title mb-0">Informasi Data</h4></div>
        <div class="p-20 row g-4">
            <?php foreach ($config['fields'] as $field => $label): ?>
                <div class="col-md-4">
                    <label class="erp-detail-label"><?= htmlspecialchars($label) ?></label>
                    <?php if ($field === 'status'): ?>
                        <select class="form-control" name="status"><option value="active" <?= ($item['status'] ?? 'active') === 'active' ? 'selected' : '' ?>>active</option><option value="inactive" <?= ($item['status'] ?? '') === 'inactive' ? 'selected' : '' ?>>inactive</option><option value="maintenance" <?= ($item['status'] ?? '') === 'maintenance' ? 'selected' : '' ?>>maintenance</option><option value="expired" <?= ($item['status'] ?? '') === 'expired' ? 'selected' : '' ?>>expired</option></select>
                    <?php elseif ($field === 'gate_type'): ?>
                        <select class="form-control" name="gate_type"><option value="entry">entry</option><option value="exit" <?= ($item[$field] ?? '') === 'exit' ? 'selected' : '' ?>>exit</option><option value="both" <?= ($item[$field] ?? '') === 'both' ? 'selected' : '' ?>>both</option></select>
                    <?php elseif ($field === 'device_type'): ?>
                        <select class="form-control" name="device_type"><?php foreach (['manual','http_api','relay','tcp_ip','serial','qr_scanner','lpr_camera'] as $v): ?><option value="<?= $v ?>" <?= ($item[$field] ?? 'manual') === $v ? 'selected' : '' ?>><?= $v ?></option><?php endforeach; ?></select>
                    <?php elseif ($field === 'category'): ?>
                        <select class="form-control" name="category"><?php foreach (['motor','mobil','ambulance','doctor_employee','vendor','vip','operational','other'] as $v): ?><option value="<?= $v ?>" <?= ($item[$field] ?? '') === $v ? 'selected' : '' ?>><?= $v ?></option><?php endforeach; ?></select>
                    <?php elseif ($field === 'rate_type'): ?>
                        <select class="form-control" name="rate_type"><?php foreach (['flat','hourly','progressive','daily_max'] as $v): ?><option value="<?= $v ?>" <?= ($item[$field] ?? '') === $v ? 'selected' : '' ?>><?= $v ?></option><?php endforeach; ?></select>
                    <?php elseif ($field === 'member_type'): ?>
                        <select class="form-control" name="member_type"><?php foreach (['doctor','employee','vendor','vip','operational'] as $v): ?><option value="<?= $v ?>" <?= ($item[$field] ?? '') === $v ? 'selected' : '' ?>><?= $v ?></option><?php endforeach; ?></select>
                    <?php elseif ($field === 'area_id'): ?>
                        <select class="form-control" name="area_id"><option value="">-</option><?php foreach ($areas as $area): ?><option value="<?= $area['id'] ?>" <?= (string)($item[$field] ?? '') === (string)$area['id'] ? 'selected' : '' ?>><?= htmlspecialchars($area['name']) ?></option><?php endforeach; ?></select>
                    <?php elseif ($field === 'vehicle_type_id'): ?>
                        <select class="form-control" name="vehicle_type_id"><option value="">-</option><?php foreach ($vehicleTypes as $type): ?><option value="<?= $type['id'] ?>" <?= (string)($item[$field] ?? '') === (string)$type['id'] ? 'selected' : '' ?>><?= htmlspecialchars($type['name']) ?></option><?php endforeach; ?></select>
                    <?php elseif ($field === 'is_free'): ?>
                        <div class="form-check mt-2"><input class="form-check-input" type="checkbox" name="is_free" value="1" <?= !empty($item[$field]) ? 'checked' : '' ?>><label class="form-check-label">Gratis parkir</label></div>
                    <?php elseif (str_contains($field, 'date') || in_array($field, ['valid_from','valid_to'], true)): ?>
                        <input type="date" class="form-control" name="<?= htmlspecialchars($field) ?>" value="<?= htmlspecialchars($item[$field] ?? '') ?>">
                    <?php elseif ($field === 'notes'): ?>
                        <textarea class="form-control" name="notes"><?= htmlspecialchars($item[$field] ?? '') ?></textarea>
                    <?php else: ?>
                        <input class="form-control" name="<?= htmlspecialchars($field) ?>" value="<?= htmlspecialchars($item[$field] ?? '') ?>">
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    <div class="card bg-white rounded-10 border border-white p-20"><div class="d-flex justify-content-end flex-wrap gap-3"><a href="<?= url($config['route']) ?>" class="btn btn-light erp-btn"><i class="ri-close-line me-1"></i>Batal</a><button class="btn btn-primary text-white erp-btn"><i class="ri-save-line me-1"></i>Simpan</button></div></div>
</form>
