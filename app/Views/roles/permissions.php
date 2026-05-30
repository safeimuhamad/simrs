<?php
$moduleLabels = [
    'user' => 'Master Data - Users',
    'role' => 'Master Data - Role & Hak Akses',
    'activity_logs' => 'Audit Trail',
    'simrs_dashboard' => 'SIMRS - Dashboard',
    'simrs_patient' => 'SIMRS - Master Pasien',
    'simrs_doctor' => 'SIMRS - Master Dokter',
    'simrs_polyclinic' => 'SIMRS - Master Poli',
    'simrs_schedule' => 'SIMRS - Jadwal Dokter',
    'simrs_registration' => 'SIMRS - Pendaftaran',
    'simrs_queue' => 'SIMRS - Antrean Poli',
    'simrs_outpatient' => 'SIMRS - Rawat Jalan',
    'simrs_inpatient' => 'SIMRS - Rawat Inap',
    'simrs_medical_record' => 'SIMRS - EMR',
    'simrs_prescription' => 'SIMRS - Resep',
    'simrs_lab' => 'SIMRS - Laboratorium',
    'simrs_radiology' => 'SIMRS - Radiologi',
    'simrs_pharmacy' => 'SIMRS - Farmasi',
    'simrs_billing' => 'SIMRS - Billing Pasien',
    'simrs_cashier' => 'SIMRS - Kasir',
    'simrs_report' => 'SIMRS - Laporan',
    'simrs_emergency' => 'SIMRS - Emergency / IGD',
    'simrs_bed' => 'SIMRS - Bed Management Detail',
    'simrs_nurse_station' => 'SIMRS - Nurse Station',
    'simrs_vital_sign' => 'SIMRS - Vital Sign',
    'simrs_operating_room' => 'SIMRS - Operating Room / OK',
    'simrs_icu' => 'SIMRS - ICU / NICU',
    'simrs_bpjs' => 'SIMRS - BPJS Bridging',
    'simrs_satusehat' => 'SIMRS - SATUSEHAT',
    'simrs_insurance_claim' => 'SIMRS - Insurance Claim',
    'simrs_payment_gateway' => 'SIMRS - Payment Gateway Log',
    'simrs_asset' => 'SIMRS - Asset Management RS',
    'simrs_ambulance' => 'SIMRS - Ambulance Management',
    'simrs_hris' => 'SIMRS - HRIS / Shift Pegawai',
    'simrs_stock_opname' => 'SIMRS - Stock Opname Obat',
    'simrs_pharmacy_pr' => 'SIMRS - Purchase Request Obat / Alkes',
    'simrs_pharmacy_vendor' => 'SIMRS - Supplier / Vendor Farmasi',
    'simrs_lab_result' => 'SIMRS - Lab Result Detail',
    'simrs_radiology_result' => 'SIMRS - Radiology Result Detail',
    'simrs_medical_document' => 'SIMRS - Medical Document / Consent',
    'simrs_notification' => 'SIMRS - Notification / Reminder',
    'parking_dashboard' => 'Parking - Dashboard',
    'parking_master' => 'Parking - Master Data',
    'parking_ticket' => 'Parking - Tiket',
    'parking_checkin' => 'Parking - Check-in',
    'parking_checkout' => 'Parking - Check-out',
    'parking_payment' => 'Parking - Pembayaran',
    'parking_validation' => 'Parking - Validasi Pasien',
    'parking_report' => 'Parking - Laporan',
];

$actionLabels = [
    'view' => 'Lihat',
    'create' => 'Tambah',
    'edit' => 'Ubah',
    'delete' => 'Hapus',
    'permission' => 'Atur Hak Akses',
    'manage' => 'Kelola',
];
?>

<div class="card bg-white rounded-10 border border-white p-20 mb-4">

    <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">

        <div>
            <h3 class="mb-1">
                Hak Akses Role
            </h3>

            <p class="mb-0 text-body">
                Role:
                <strong>
                    <?= htmlspecialchars($role['name'] ?? '-') ?>
                </strong>
            </p>
        </div>

        <a
            href="<?= url('roles') ?>"
            class="btn btn-light erp-btn"
        >
            <i class="ri-arrow-left-line me-1"></i>
            Kembali
        </a>

    </div>

</div>

<div class="alert alert-info mb-4">
    Centang <strong>Lihat</strong> agar menu bisa tampil. Centang <strong>Kelola</strong> untuk memberi akses operasional modul SIMRS.
</div>

<form action="<?= url('roles-permissions-update') ?>" method="POST">

    <input type="hidden" name="role_id" value="<?= $role['id'] ?>">

    <div class="card bg-white rounded-10 border border-white mb-4">

        <div class="p-20 border-bottom">
            <h4 class="erp-detail-section-title mb-0">
                Daftar Hak Akses
            </h4>
        </div>

        <div class="default-table-area mx-minus-1">

            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead>
                        <tr>
                            <th width="30%">Menu / Modul</th>
                            <th>Hak Akses</th>
                        </tr>
                    </thead>

                    <tbody>
                        <?php if (!empty($groupedPermissions)): ?>
                            <?php foreach ($groupedPermissions as $module => $permissions): ?>
                                <tr>
                                    <td>
                                        <strong>
                                            <?= htmlspecialchars($moduleLabels[$module] ?? ucwords(str_replace('_', ' ', $module))) ?>
                                        </strong>
                                    </td>

                                    <td>
                                        <div class="d-flex flex-wrap gap-3">
                                            <?php foreach ($permissions as $permission): ?>
                                                <div class="form-check">
                                                    <input
                                                        class="form-check-input"
                                                        type="checkbox"
                                                        name="permissions[]"
                                                        value="<?= $permission['id'] ?>"
                                                        id="permission-<?= $permission['id'] ?>"
                                                        <?= in_array($permission['id'], $selectedPermissions) ? 'checked' : '' ?>
                                                    >

                                                    <label
                                                        class="form-check-label"
                                                        for="permission-<?= $permission['id'] ?>"
                                                    >
                                                        <?= htmlspecialchars($actionLabels[$permission['action_name']] ?? ucwords(str_replace('_', ' ', $permission['action_name']))) ?>
                                                    </label>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="2" class="text-center text-body">
                                    Belum ada data permission.
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

        </div>

    </div>

    <div class="row g-4 mb-4">

        <div class="col-lg-8">
            <div class="card bg-white rounded-10 border border-white h-100">

                <div class="p-20 border-bottom">
                    <h4 class="erp-detail-section-title mb-0">
                        Catatan Hak Akses
                    </h4>
                </div>

                <div class="p-20">
                    <div class="text-body">
                        Hak akses menentukan menu yang terlihat dan aksi yang dapat dilakukan oleh role ini.
                        Minimal centang <strong>Lihat</strong> agar menu tampil untuk user dengan role tersebut.
                    </div>
                </div>

            </div>
        </div>

        <div class="col-lg-4">
            <div class="card bg-white rounded-10 border border-white h-100">

                <div class="p-20 border-bottom">
                    <h4 class="erp-detail-section-title mb-0">
                        Ringkasan
                    </h4>
                </div>

                <div class="p-20">

                    <div class="d-flex justify-content-between mb-2">
                        <span>Role</span>
                        <strong>
                            <?= htmlspecialchars($role['name'] ?? '-') ?>
                        </strong>
                    </div>

                    <div class="d-flex justify-content-between mb-2">
                        <span>Modul</span>
                        <strong>
                            <?= !empty($groupedPermissions) ? count($groupedPermissions) : 0 ?>
                        </strong>
                    </div>

                    <hr>

                    <div class="text-body">
                        Simpan perubahan untuk memperbarui permission role ini di seluruh sistem.
                    </div>

                </div>

            </div>
        </div>

    </div>

    <div class="card bg-white rounded-10 border border-white p-20">

        <div class="d-flex justify-content-end flex-wrap gap-3">

            <a
                href="<?= url('roles') ?>"
                class="btn btn-light erp-btn"
            >
                <i class="ri-close-line me-1"></i>
                Batal
            </a>

            <?php if (can('role.permission')): ?>
                <button
                    type="submit"
                    class="btn btn-primary text-white erp-btn"
                >
                    <i class="ri-save-line me-1"></i>
                    Simpan Hak Akses
                </button>
            <?php endif; ?>

        </div>

    </div>

</form>
