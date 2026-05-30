<?php if (!empty($_SESSION['success'])): ?><div class="alert alert-success"><?= htmlspecialchars($_SESSION['success']); unset($_SESSION['success']); ?></div><?php endif; ?>
<div class="card bg-white rounded-10 border border-white mb-4" id="ticket-print">
    <div class="d-flex justify-content-between align-items-start p-20 border-bottom flex-wrap gap-3">
        <div><h3 class="mb-1"><?= htmlspecialchars($ticket['ticket_no']) ?></h3><p class="mb-0 text-body"><?= htmlspecialchars($ticket['plate_number']) ?> | <?= htmlspecialchars($ticket['vehicle_type_name'] ?: '-') ?> | <?= htmlspecialchars($ticket['area_name'] ?: '-') ?></p></div>
        <div class="text-center">
            <img alt="QR Tiket" src="https://api.qrserver.com/v1/create-qr-code/?size=96x96&data=<?= urlencode($ticket['qr_token']) ?>">
            <div class="fs-12 text-body"><?= htmlspecialchars(substr($ticket['qr_token'], 0, 12)) ?></div>
        </div>
    </div>
    <div class="p-20 row g-3">
        <div class="col-md-3"><span class="text-body">Masuk</span><h5><?= htmlspecialchars($ticket['entry_time']) ?></h5></div>
        <div class="col-md-3"><span class="text-body">Keluar</span><h5><?= htmlspecialchars($ticket['exit_time'] ?: '-') ?></h5></div>
        <div class="col-md-3"><span class="text-body">Durasi</span><h5><?= (int)$ticket['duration_minutes'] ?> menit</h5></div>
        <div class="col-md-3"><span class="text-body">Tagihan</span><h5>Rp <?= number_format((float)$ticket['payable_amount'], 0, ',', '.') ?></h5></div>
        <div class="col-md-3"><span class="text-body">Status</span><div class="mt-1"><?= simrsStatusBadge($ticket['status'] ?? '', 'parking') ?></div></div>
        <div class="col-md-3"><span class="text-body">Pembayaran</span><div class="mt-1"><?= simrsStatusBadge($ticket['payment_status'] ?? '', 'payment') ?></div></div>
        <div class="col-md-6"><span class="text-body">Pasien/Member</span><h5><?= htmlspecialchars($ticket['patient_name'] ?: ($ticket['member_name'] ?: '-')) ?></h5></div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-lg-6">
        <form method="post" action="<?= url('parking-checkout-process') ?>" class="card bg-white rounded-10 border border-white h-100">
            <input type="hidden" name="ticket_id" value="<?= htmlspecialchars($ticket['id']) ?>">
            <div class="p-20 border-bottom"><h4 class="mb-0">Check-out</h4></div>
            <div class="p-20 row g-2">
                <div class="col-md-6"><label>Gate Keluar</label><select class="form-control" name="exit_gate_id"><?php foreach ($exitGates as $gate): ?><option value="<?= $gate['id'] ?>"><?= htmlspecialchars($gate['name']) ?></option><?php endforeach; ?></select></div>
                <div class="col-md-6"><label>Waktu Keluar</label><input type="datetime-local" class="form-control" name="exit_time" value="<?= date('Y-m-d\TH:i') ?>"></div>
                <div class="col-md-12"><label class="d-flex gap-2"><input type="checkbox" name="lost_ticket" value="1"> Lost ticket</label></div>
            </div>
            <div class="p-20 border-top"><button class="btn btn-outline-primary">Hitung Tarif & Open Gate</button></div>
        </form>
    </div>
    <div class="col-lg-6">
        <form method="post" action="<?= url('parking-payments-store') ?>" class="card bg-white rounded-10 border border-white h-100">
            <input type="hidden" name="ticket_id" value="<?= htmlspecialchars($ticket['id']) ?>">
            <div class="p-20 border-bottom"><h4 class="mb-0">Pembayaran Parkir</h4></div>
            <div class="p-20 row g-2">
                <div class="col-md-6"><label>Tanggal</label><input type="date" class="form-control" name="payment_date" value="<?= date('Y-m-d') ?>"></div>
                <div class="col-md-6"><label>Metode</label><select class="form-control" name="payment_method"><option value="cash">Cash</option><option value="transfer">Transfer</option><option value="debit">Debit</option><option value="qris">QRIS</option><option value="patient_billing">Gabung Billing Pasien</option><option value="waived">Waived</option></select></div>
                <div class="col-md-6"><label>Jumlah</label><input class="form-control" name="amount" value="<?= number_format((float)$ticket['payable_amount'], 0, ',', '.') ?>"></div>
                <div class="col-md-6"><label>Rekening Bank</label><select class="form-control" name="bank_account_id"><option value="">Cash/tanpa bank</option><?php foreach ($bankAccounts as $bank): ?><option value="<?= $bank['id'] ?>"><?= htmlspecialchars($bank['account_name'] ?? '-') ?></option><?php endforeach; ?></select></div>
                <div class="col-md-12"><input class="form-control" name="reference_no" placeholder="No referensi"></div>
            </div>
            <div class="p-20 border-top"><button class="btn btn-primary text-white">Catat Pembayaran</button></div>
        </form>
    </div>
</div>

<div class="card bg-white rounded-10 border border-white mb-4">
    <div class="p-20 border-bottom d-flex gap-2 flex-wrap"><button onclick="window.print()" class="btn btn-light">Cetak Tiket</button><?php if (!empty($ticket['exit_gate_id'])): ?><a href="<?= url('parking-gate-open', ['ticket_id' => $ticket['id'], 'gate_id' => $ticket['exit_gate_id'], 'action' => 'open_exit']) ?>" class="btn btn-outline-primary">Open Gate</a><?php endif; ?></div>
    <div class="p-20 row g-4">
        <div class="col-lg-4"><h5>Pembayaran</h5><?php foreach ($ticket['payments'] as $p): ?><div class="border-bottom py-2"><?= htmlspecialchars($p['payment_no']) ?> - Rp <?= number_format((float)$p['amount'], 0, ',', '.') ?></div><?php endforeach; ?></div>
        <div class="col-lg-4"><h5>Validasi</h5><?php foreach ($ticket['validations'] as $v): ?><div class="border-bottom py-2"><?= htmlspecialchars($v['validation_type']) ?> / <?= htmlspecialchars($v['discount_type']) ?></div><?php endforeach; ?></div>
        <div class="col-lg-4"><h5>Gate Log</h5><?php foreach ($ticket['logs'] as $log): ?><div class="border-bottom py-2"><?= htmlspecialchars($log['created_at'] . ' - ' . $log['action']) ?></div><?php endforeach; ?></div>
    </div>
</div>
