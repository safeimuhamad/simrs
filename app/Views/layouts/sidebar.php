<div class="sidebar-area" id="sidebar-area">
    <div class="simrs-sidebar-logo">
        <div class="simrs-brand-mark">
            <span class="material-symbols-outlined">local_hospital</span>
        </div>
        <div>
            <div class="simrs-sidebar-title">SIM Rumah Sakit</div>
            <div class="simrs-sidebar-subtitle">Sistem Informasi Manajemen</div>
        </div>
        <button class="sidebar-burger-menu-close bg-transparent py-3 border-0 opacity-0 z-n1 position-absolute top-50 end-0 translate-middle-y" id="sidebar-burger-menu-close">
            <span class="border-1 d-block for-dark-burger" style="border-bottom: 1px solid #475569; height: 1px; width: 25px; transform: rotate(45deg);"></span>
            <span class="border-1 d-block for-dark-burger" style="border-bottom: 1px solid #475569; height: 1px; width: 25px; transform: rotate(-45deg);"></span>
        </button>
    </div>

    <aside id="layout-menu" class="layout-menu menu-vertical menu active" data-simplebar>
        <ul class="menu-inner">
            <?php
            $parkingOnly = can('parking_dashboard.view') && !can('simrs_dashboard.view');
            $canParkingOperationalGroup = can('parking_checkin.manage') || can('parking_checkout.manage') || can('parking_ticket.manage') || can('parking_validation.manage');
            $canParkingMasterGroup = can('parking_master.manage');
            $canParkingReportGroup = can('parking_report.view');
            $canParkingSettingGroup = can('parking_master.manage');
            $canPatientGroup = can('simrs_registration.manage') || can('simrs_queue.manage') || can('simrs_patient.view');
            $canAdvancedClinicalGroup = can('simrs_emergency.manage') || can('simrs_bed.manage') || can('simrs_nurse_station.manage') || can('simrs_vital_sign.manage') || can('simrs_operating_room.manage') || can('simrs_icu.manage');
            $canDiagnosticExtensionGroup = can('simrs_lab_result.manage') || can('simrs_radiology_result.manage') || can('simrs_medical_document.manage') || can('simrs_notification.manage');
            $canServiceGroup = can('simrs_outpatient.manage') || can('simrs_inpatient.manage') || can('simrs_lab.manage') || can('simrs_radiology.manage') || can('simrs_pharmacy.manage') || $canAdvancedClinicalGroup || $canDiagnosticExtensionGroup;
            $canFinanceExtensionGroup = can('simrs_bpjs.manage') || can('simrs_satusehat.manage') || can('simrs_insurance_claim.manage') || can('simrs_payment_gateway.manage');
            $canFinanceGroup = can('simrs_billing.manage') || can('simrs_cashier.manage') || can('simrs_report.view') || $canFinanceExtensionGroup;
            $canPharmacyExtensionGroup = can('simrs_stock_opname.manage') || can('simrs_pharmacy_pr.manage') || can('simrs_pharmacy_vendor.manage');
            $canOperationGroup = can('simrs_asset.manage') || can('simrs_ambulance.manage') || can('simrs_hris.manage');
            $canManagementGroup = can('simrs_doctor.manage') || can('simrs_polyclinic.manage') || can('simrs_schedule.manage') || can('simrs_pharmacy.manage') || can('parking_dashboard.view') || can('simrs_report.view') || $canPharmacyExtensionGroup || $canOperationGroup;
            $canSettingGroup = can('user.view') || can('role.view') || can('activity_logs.view');
            $dashboardRoute = roleDashboardRoute();
            $dashboardPages = ['simrs-dashboard', 'dashboard-pendaftaran', 'dashboard-dokter', 'dashboard-perawat', 'dashboard-farmasi', 'dashboard-kasir', 'dashboard-finance', 'dashboard-manajemen', 'dashboard'];
            ?>
            <?php if ($parkingOnly): ?>
                <li class="menu-item <?= isActiveMenu(['parking-dashboard']) ?>">
                    <a href="<?= url('parking-dashboard') ?>" class="menu-link">
                        <span class="material-symbols-outlined menu-icon">home</span>
                        <span class="title">Dashboard</span>
                    </a>
                </li>

                <?php if ($canParkingOperationalGroup): ?>
                    <li class="menu-title">OPERASIONAL</li>
                    <?php if (can('parking_checkin.manage')): ?><li class="menu-item <?= isActiveMenu(['parking-checkin']) ?>"><a href="<?= url('parking-checkin') ?>" class="menu-link"><span class="material-symbols-outlined menu-icon">directions_car</span><span class="title">Check-In Kendaraan</span></a></li><?php endif; ?>
                    <?php if (can('parking_checkout.manage')): ?><li class="menu-item <?= isActiveMenu(['parking-checkout']) ?>"><a href="<?= url('parking-checkout') ?>" class="menu-link"><span class="material-symbols-outlined menu-icon">garage</span><span class="title">Check-Out Kendaraan</span></a></li><?php endif; ?>
                    <?php if (can('parking_ticket.manage')): ?><li class="menu-item <?= isActiveMenu(['parking-tickets','parking-tickets-show']) ?>"><a href="<?= url('parking-tickets') ?>" class="menu-link"><span class="material-symbols-outlined menu-icon">confirmation_number</span><span class="title">Tiket Parkir</span></a></li><?php endif; ?>
                    <?php if (can('parking_validation.manage')): ?><li class="menu-item <?= isActiveMenu(['parking-validations']) ?>"><a href="<?= url('parking-validations') ?>" class="menu-link"><span class="material-symbols-outlined menu-icon">verified</span><span class="title">Validasi Parkir</span></a></li><?php endif; ?>
                    <?php if (can('parking_ticket.manage')): ?><li class="menu-item <?= isActiveMenu(['parking-gate-queue']) ?>"><a href="<?= url('parking-gate-queue') ?>" class="menu-link"><span class="material-symbols-outlined menu-icon">sensor_door</span><span class="title">Antrean Gate</span></a></li><?php endif; ?>
                <?php endif; ?>

                <?php if ($canParkingMasterGroup): ?>
                    <li class="menu-title">MASTER DATA</li>
                    <li class="menu-item <?= isActiveMenu(['parking-areas','parking-areas-create','parking-areas-edit']) ?>"><a href="<?= url('parking-areas') ?>" class="menu-link"><span class="material-symbols-outlined menu-icon">pin_drop</span><span class="title">Area Parkir</span></a></li>
                    <li class="menu-item <?= isActiveMenu(['parking-gates','parking-gates-create','parking-gates-edit']) ?>"><a href="<?= url('parking-gates') ?>" class="menu-link"><span class="material-symbols-outlined menu-icon">meeting_room</span><span class="title">Gate / Pintu</span></a></li>
                    <li class="menu-item <?= isActiveMenu(['parking-rates','parking-rates-create','parking-rates-edit']) ?>"><a href="<?= url('parking-rates') ?>" class="menu-link"><span class="material-symbols-outlined menu-icon">price_check</span><span class="title">Tarif Parkir</span></a></li>
                    <li class="menu-item <?= isActiveMenu(['parking-vehicle-types','parking-vehicle-types-create','parking-vehicle-types-edit']) ?>"><a href="<?= url('parking-vehicle-types') ?>" class="menu-link"><span class="material-symbols-outlined menu-icon">directions_car</span><span class="title">Kategori Kendaraan</span></a></li>
                    <li class="menu-item <?= isActiveMenu(['parking-members','parking-members-create','parking-members-edit']) ?>"><a href="<?= url('parking-members') ?>" class="menu-link"><span class="material-symbols-outlined menu-icon">groups</span><span class="title">Member Parkir</span></a></li>
                <?php endif; ?>

                <?php if ($canParkingReportGroup): ?>
                    <li class="menu-title">LAPORAN</li>
                    <li class="menu-item <?= isActiveMenu(['parking-reports-transactions']) ?>"><a href="<?= url('parking-reports-transactions') ?>" class="menu-link"><span class="material-symbols-outlined menu-icon">assignment</span><span class="title">Transaksi Parkir</span></a></li>
                    <li class="menu-item <?= isActiveMenu(['parking-reports-income']) ?>"><a href="<?= url('parking-reports-income') ?>" class="menu-link"><span class="material-symbols-outlined menu-icon">account_balance_wallet</span><span class="title">Pendapatan Parkir</span></a></li>
                    <li class="menu-item <?= isActiveMenu(['parking-reports-occupancy']) ?>"><a href="<?= url('parking-reports-occupancy') ?>" class="menu-link"><span class="material-symbols-outlined menu-icon">area_chart</span><span class="title">Okupansi Parkir</span></a></li>
                    <li class="menu-item <?= isActiveMenu(['parking-reports-duration']) ?>"><a href="<?= url('parking-reports-duration') ?>" class="menu-link"><span class="material-symbols-outlined menu-icon">schedule</span><span class="title">Durasi Parkir</span></a></li>
                    <li class="menu-item <?= isActiveMenu(['parking-reports-lost-ticket']) ?>"><a href="<?= url('parking-reports-lost-ticket') ?>" class="menu-link"><span class="material-symbols-outlined menu-icon">find_in_page</span><span class="title">Ticket Lost</span></a></li>
                <?php endif; ?>

                <?php if ($canParkingSettingGroup): ?>
                    <li class="menu-title">PENGATURAN</li>
                    <li class="menu-item <?= isActiveMenu(['parking-rate-settings']) ?>"><a href="<?= url('parking-rate-settings') ?>" class="menu-link"><span class="material-symbols-outlined menu-icon">settings</span><span class="title">Pengaturan Tarif</span></a></li>
                <?php endif; ?>
            <?php else: ?>
            <?php if (can('simrs_dashboard.view') || in_array($dashboardRoute, $dashboardPages, true)): ?>
                <li class="menu-item <?= isActiveMenu($dashboardPages) ?>">
                    <a href="<?= url($dashboardRoute) ?>" class="menu-link">
                        <span class="material-symbols-outlined menu-icon">dashboard</span>
                        <span class="title">Dashboard</span>
                    </a>
                </li>
            <?php endif; ?>

            <?php if ($canPatientGroup): ?>
                <li class="menu-title">PASIEN</li>
                <?php if (can('simrs_registration.manage')): ?><li class="menu-item <?= isActiveMenu(['simrs-registration', 'simrs-registration-create']) ?>"><a href="<?= url('simrs-registration') ?>" class="menu-link"><span class="material-symbols-outlined menu-icon">app_registration</span><span class="title">Pendaftaran</span></a></li><?php endif; ?>
                <?php if (can('simrs_queue.manage')): ?><li class="menu-item <?= isActiveMenu(['simrs-queue']) ?>"><a href="<?= url('simrs-queue') ?>" class="menu-link"><span class="material-symbols-outlined menu-icon">groups</span><span class="title">Antrean</span></a></li><?php endif; ?>
                <?php if (can('simrs_patient.view')): ?><li class="menu-item <?= isActiveMenu(['simrs-patients', 'simrs-patients-create', 'simrs-patients-edit', 'simrs-patients-show']) ?>"><a href="<?= url('simrs-patients') ?>" class="menu-link"><span class="material-symbols-outlined menu-icon">personal_injury</span><span class="title">Pasien</span></a></li><?php endif; ?>
            <?php endif; ?>

            <?php if ($canServiceGroup): ?>
                <li class="menu-title">LAYANAN</li>
                <?php if (can('simrs_outpatient.manage')): ?><li class="menu-item <?= isActiveMenu(['simrs-outpatient', 'simrs-outpatient-examine']) ?>"><a href="<?= url('simrs-outpatient') ?>" class="menu-link"><span class="material-symbols-outlined menu-icon">stethoscope</span><span class="title">Rawat Jalan</span></a></li><?php endif; ?>
                <?php if (can('simrs_inpatient.manage')): ?><li class="menu-item <?= isActiveMenu(['simrs-inpatient']) ?>"><a href="<?= url('simrs-inpatient') ?>" class="menu-link"><span class="material-symbols-outlined menu-icon">hotel</span><span class="title">Rawat Inap</span></a></li><?php endif; ?>
                <?php if (can('simrs_lab.manage')): ?><li class="menu-item <?= isActiveMenu(['simrs-laboratory']) ?>"><a href="<?= url('simrs-laboratory') ?>" class="menu-link"><span class="material-symbols-outlined menu-icon">science</span><span class="title">Laboratorium</span></a></li><?php endif; ?>
                <?php if (can('simrs_radiology.manage')): ?><li class="menu-item <?= isActiveMenu(['simrs-radiology']) ?>"><a href="<?= url('simrs-radiology') ?>" class="menu-link"><span class="material-symbols-outlined menu-icon">radiology</span><span class="title">Radiologi</span></a></li><?php endif; ?>
                <?php if (can('simrs_pharmacy.manage')): ?><li class="menu-item <?= isActiveMenu(['simrs-pharmacy', 'simrs-pharmacy-show']) ?>"><a href="<?= url('simrs-pharmacy') ?>" class="menu-link"><span class="material-symbols-outlined menu-icon">medication</span><span class="title">Farmasi</span></a></li><?php endif; ?>
                <?php if ($canAdvancedClinicalGroup): ?><li class="menu-item <?= isActiveMenu(['simrs-emergency','simrs-bed-management','simrs-nurse-station','simrs-vital-signs','simrs-operating-room','simrs-icu-nicu']) ?> <?= isOpenMenu(['simrs-emergency','simrs-bed-management','simrs-nurse-station','simrs-vital-signs','simrs-operating-room','simrs-icu-nicu']) ?>"><a href="javascript:void(0);" class="menu-link menu-toggle"><span class="material-symbols-outlined menu-icon">emergency</span><span class="title">Layanan Lanjutan</span></a><ul class="menu-sub"><?php if (can('simrs_emergency.manage')): ?><li class="menu-item <?= isActiveMenu(['simrs-emergency']) ?>"><a href="<?= url('simrs-emergency') ?>" class="menu-link">Emergency / IGD</a></li><?php endif; ?><?php if (can('simrs_bed.manage')): ?><li class="menu-item <?= isActiveMenu(['simrs-bed-management']) ?>"><a href="<?= url('simrs-bed-management') ?>" class="menu-link">Bed Management</a></li><?php endif; ?><?php if (can('simrs_nurse_station.manage')): ?><li class="menu-item <?= isActiveMenu(['simrs-nurse-station']) ?>"><a href="<?= url('simrs-nurse-station') ?>" class="menu-link">Nurse Station</a></li><?php endif; ?><?php if (can('simrs_vital_sign.manage')): ?><li class="menu-item <?= isActiveMenu(['simrs-vital-signs']) ?>"><a href="<?= url('simrs-vital-signs') ?>" class="menu-link">Vital Sign</a></li><?php endif; ?><?php if (can('simrs_operating_room.manage')): ?><li class="menu-item <?= isActiveMenu(['simrs-operating-room']) ?>"><a href="<?= url('simrs-operating-room') ?>" class="menu-link">Operating Room / OK</a></li><?php endif; ?><?php if (can('simrs_icu.manage')): ?><li class="menu-item <?= isActiveMenu(['simrs-icu-nicu']) ?>"><a href="<?= url('simrs-icu-nicu') ?>" class="menu-link">ICU / NICU</a></li><?php endif; ?></ul></li><?php endif; ?>
                <?php if ($canDiagnosticExtensionGroup): ?><li class="menu-item <?= isActiveMenu(['simrs-lab-results','simrs-radiology-results','simrs-medical-documents','simrs-notifications']) ?> <?= isOpenMenu(['simrs-lab-results','simrs-radiology-results','simrs-medical-documents','simrs-notifications']) ?>"><a href="javascript:void(0);" class="menu-link menu-toggle"><span class="material-symbols-outlined menu-icon">clinical_notes</span><span class="title">Hasil & Dokumen</span></a><ul class="menu-sub"><?php if (can('simrs_lab_result.manage')): ?><li class="menu-item <?= isActiveMenu(['simrs-lab-results']) ?>"><a href="<?= url('simrs-lab-results') ?>" class="menu-link">Lab Result Detail</a></li><?php endif; ?><?php if (can('simrs_radiology_result.manage')): ?><li class="menu-item <?= isActiveMenu(['simrs-radiology-results']) ?>"><a href="<?= url('simrs-radiology-results') ?>" class="menu-link">Radiology Result Detail</a></li><?php endif; ?><?php if (can('simrs_medical_document.manage')): ?><li class="menu-item <?= isActiveMenu(['simrs-medical-documents']) ?>"><a href="<?= url('simrs-medical-documents') ?>" class="menu-link">Medical Document</a></li><?php endif; ?><?php if (can('simrs_notification.manage')): ?><li class="menu-item <?= isActiveMenu(['simrs-notifications']) ?>"><a href="<?= url('simrs-notifications') ?>" class="menu-link">Notification / Reminder</a></li><?php endif; ?></ul></li><?php endif; ?>
            <?php endif; ?>

            <?php if ($canFinanceGroup): ?>
                <li class="menu-title">KEUANGAN</li>
                <?php if (can('simrs_billing.manage')): ?><li class="menu-item <?= isActiveMenu(['simrs-billing', 'simrs-billing-show']) ?>"><a href="<?= url('simrs-billing') ?>" class="menu-link"><span class="material-symbols-outlined menu-icon">receipt_long</span><span class="title">Billing</span></a></li><?php endif; ?>
                <?php if (can('simrs_cashier.manage')): ?><li class="menu-item <?= isActiveMenu(['simrs-cashier']) ?>"><a href="<?= url('simrs-cashier') ?>" class="menu-link"><span class="material-symbols-outlined menu-icon">point_of_sale</span><span class="title">Kasir</span></a></li><?php endif; ?>
                <?php if (can('simrs_report.view')): ?><li class="menu-item <?= isActiveMenu(['simrs-reports-income']) ?>"><a href="<?= url('simrs-reports-income') ?>" class="menu-link"><span class="material-symbols-outlined menu-icon">query_stats</span><span class="title">Laporan Keuangan</span></a></li><?php endif; ?>
                <?php if ($canFinanceExtensionGroup): ?><li class="menu-item <?= isActiveMenu(['simrs-bpjs-bridging','simrs-satusehat','simrs-insurance-claims','simrs-payment-gateway-logs']) ?> <?= isOpenMenu(['simrs-bpjs-bridging','simrs-satusehat','simrs-insurance-claims','simrs-payment-gateway-logs']) ?>"><a href="javascript:void(0);" class="menu-link menu-toggle"><span class="material-symbols-outlined menu-icon">account_tree</span><span class="title">Integrasi & Klaim</span></a><ul class="menu-sub"><?php if (can('simrs_bpjs.manage')): ?><li class="menu-item <?= isActiveMenu(['simrs-bpjs-bridging']) ?>"><a href="<?= url('simrs-bpjs-bridging') ?>" class="menu-link">BPJS Bridging</a></li><?php endif; ?><?php if (can('simrs_satusehat.manage')): ?><li class="menu-item <?= isActiveMenu(['simrs-satusehat']) ?>"><a href="<?= url('simrs-satusehat') ?>" class="menu-link">SATUSEHAT</a></li><?php endif; ?><?php if (can('simrs_insurance_claim.manage')): ?><li class="menu-item <?= isActiveMenu(['simrs-insurance-claims']) ?>"><a href="<?= url('simrs-insurance-claims') ?>" class="menu-link">Insurance Claim</a></li><?php endif; ?><?php if (can('simrs_payment_gateway.manage')): ?><li class="menu-item <?= isActiveMenu(['simrs-payment-gateway-logs']) ?>"><a href="<?= url('simrs-payment-gateway-logs') ?>" class="menu-link">Payment Gateway Log</a></li><?php endif; ?></ul></li><?php endif; ?>
            <?php endif; ?>

            <?php if ($canManagementGroup): ?>
                <li class="menu-title">MANAJEMEN</li>
                <?php if (can('simrs_doctor.manage') || can('simrs_polyclinic.manage') || can('simrs_schedule.manage')): ?><li class="menu-item <?= isActiveMenu(['simrs-doctors','simrs-polyclinics','simrs-doctor-schedules']) ?> <?= isOpenMenu(['simrs-doctors','simrs-polyclinics','simrs-doctor-schedules']) ?>"><a href="javascript:void(0);" class="menu-link menu-toggle"><span class="material-symbols-outlined menu-icon">database</span><span class="title">Master Data</span></a><ul class="menu-sub"><?php if (can('simrs_doctor.manage')): ?><li class="menu-item <?= isActiveMenu(['simrs-doctors']) ?>"><a href="<?= url('simrs-doctors') ?>" class="menu-link">Dokter</a></li><?php endif; ?><?php if (can('simrs_polyclinic.manage')): ?><li class="menu-item <?= isActiveMenu(['simrs-polyclinics']) ?>"><a href="<?= url('simrs-polyclinics') ?>" class="menu-link">Poli</a></li><?php endif; ?><?php if (can('simrs_schedule.manage')): ?><li class="menu-item <?= isActiveMenu(['simrs-doctor-schedules']) ?>"><a href="<?= url('simrs-doctor-schedules') ?>" class="menu-link">Jadwal Dokter</a></li><?php endif; ?></ul></li><?php endif; ?>
                <?php if (can('simrs_pharmacy.manage')): ?><li class="menu-item <?= isActiveMenu(['simrs-medical-items','simrs-medical-items-create','simrs-medical-items-edit']) ?>"><a href="<?= url('simrs-medical-items') ?>" class="menu-link"><span class="material-symbols-outlined menu-icon">inventory_2</span><span class="title">Inventori Farmasi</span></a></li><?php endif; ?>
                <?php if ($canPharmacyExtensionGroup): ?><li class="menu-item <?= isActiveMenu(['simrs-stock-opnames','simrs-pharmacy-purchase-requests','simrs-pharmacy-vendors']) ?> <?= isOpenMenu(['simrs-stock-opnames','simrs-pharmacy-purchase-requests','simrs-pharmacy-vendors']) ?>"><a href="javascript:void(0);" class="menu-link menu-toggle"><span class="material-symbols-outlined menu-icon">medication_liquid</span><span class="title">Pengadaan Farmasi</span></a><ul class="menu-sub"><?php if (can('simrs_stock_opname.manage')): ?><li class="menu-item <?= isActiveMenu(['simrs-stock-opnames']) ?>"><a href="<?= url('simrs-stock-opnames') ?>" class="menu-link">Stock Opname</a></li><?php endif; ?><?php if (can('simrs_pharmacy_pr.manage')): ?><li class="menu-item <?= isActiveMenu(['simrs-pharmacy-purchase-requests']) ?>"><a href="<?= url('simrs-pharmacy-purchase-requests') ?>" class="menu-link">Purchase Request</a></li><?php endif; ?><?php if (can('simrs_pharmacy_vendor.manage')): ?><li class="menu-item <?= isActiveMenu(['simrs-pharmacy-vendors']) ?>"><a href="<?= url('simrs-pharmacy-vendors') ?>" class="menu-link">Vendor Farmasi</a></li><?php endif; ?></ul></li><?php endif; ?>
                <?php if ($canOperationGroup): ?><li class="menu-item <?= isActiveMenu(['simrs-assets','simrs-ambulance','simrs-employee-shifts']) ?> <?= isOpenMenu(['simrs-assets','simrs-ambulance','simrs-employee-shifts']) ?>"><a href="javascript:void(0);" class="menu-link menu-toggle"><span class="material-symbols-outlined menu-icon">business_center</span><span class="title">Operasional RS</span></a><ul class="menu-sub"><?php if (can('simrs_asset.manage')): ?><li class="menu-item <?= isActiveMenu(['simrs-assets']) ?>"><a href="<?= url('simrs-assets') ?>" class="menu-link">Asset Management</a></li><?php endif; ?><?php if (can('simrs_ambulance.manage')): ?><li class="menu-item <?= isActiveMenu(['simrs-ambulance']) ?>"><a href="<?= url('simrs-ambulance') ?>" class="menu-link">Ambulance</a></li><?php endif; ?><?php if (can('simrs_hris.manage')): ?><li class="menu-item <?= isActiveMenu(['simrs-employee-shifts']) ?>"><a href="<?= url('simrs-employee-shifts') ?>" class="menu-link">HRIS / Shift</a></li><?php endif; ?></ul></li><?php endif; ?>
                <?php if (can('parking_dashboard.view')): ?><li class="menu-item <?= isActiveMenu(['parking-dashboard','parking-checkin','parking-checkout','parking-tickets','parking-validations']) ?> <?= isOpenMenu(['parking-dashboard','parking-checkin','parking-checkout','parking-tickets','parking-validations']) ?>"><a href="javascript:void(0);" class="menu-link menu-toggle"><span class="material-symbols-outlined menu-icon">local_parking</span><span class="title">Parking</span></a><ul class="menu-sub"><li class="menu-item <?= isActiveMenu(['parking-dashboard']) ?>"><a href="<?= url('parking-dashboard') ?>" class="menu-link">Dashboard Parking</a></li><li class="menu-item <?= isActiveMenu(['parking-checkin']) ?>"><a href="<?= url('parking-checkin') ?>" class="menu-link">Check-in</a></li><li class="menu-item <?= isActiveMenu(['parking-checkout']) ?>"><a href="<?= url('parking-checkout') ?>" class="menu-link">Check-out</a></li><li class="menu-item <?= isActiveMenu(['parking-tickets']) ?>"><a href="<?= url('parking-tickets') ?>" class="menu-link">Tiket</a></li><li class="menu-item <?= isActiveMenu(['parking-validations']) ?>"><a href="<?= url('parking-validations') ?>" class="menu-link">Validasi</a></li></ul></li><?php endif; ?>
                <?php if (can('simrs_report.view')): ?><li class="menu-item <?= isActiveMenu(['simrs-reports-visits','simrs-reports-pharmacy']) ?>"><a href="<?= url('simrs-reports-visits') ?>" class="menu-link"><span class="material-symbols-outlined menu-icon">bar_chart</span><span class="title">Laporan</span></a></li><?php endif; ?>
            <?php endif; ?>

            <?php if ($canSettingGroup): ?>
                <li class="menu-title">PENGATURAN</li>
                <?php if (can('user.view')): ?><li class="menu-item <?= isActiveMenu(['users','users-create','users-edit']) ?>"><a href="<?= url('users') ?>" class="menu-link"><span class="material-symbols-outlined menu-icon">manage_accounts</span><span class="title">User</span></a></li><?php endif; ?>
                <?php if (can('role.view')): ?><li class="menu-item <?= isActiveMenu(['roles','roles-create','roles-edit','roles-permissions']) ?>"><a href="<?= url('roles') ?>" class="menu-link"><span class="material-symbols-outlined menu-icon">admin_panel_settings</span><span class="title">Role & Hak Akses</span></a></li><?php endif; ?>
                <?php if (can('activity_logs.view')): ?><li class="menu-item <?= isActiveMenu(['activity-logs']) ?>"><a href="<?= url('activity-logs') ?>" class="menu-link"><span class="material-symbols-outlined menu-icon">history</span><span class="title">Audit Trail</span></a></li><?php endif; ?>
            <?php endif; ?>
            <?php endif; ?>
        </ul>

        <div class="p-20 border-top mt-3">
            <button class="sidebar-burger-menu btn btn-light w-100 d-flex align-items-center justify-content-center gap-2" id="sidebar-burger-menu">
                <span class="material-symbols-outlined">chevron_left</span>
                Sembunyikan Menu
            </button>
        </div>
    </aside>
</div>
