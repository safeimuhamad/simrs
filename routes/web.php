<?php

$page = trim((string) ($_GET['page'] ?? 'home'), '/');

$aliases = [
    '' => 'home',
    'dashboard' => 'simrs-dashboard',
];

$page = $aliases[$page] ?? $page;
$_GET['page'] = $page;

$protectedRoutePermissions = [
    'simrs-dashboard' => 'simrs_dashboard.view',
    'dashboard-pendaftaran' => 'simrs_registration.manage',
    'dashboard-dokter' => 'simrs_outpatient.manage',
    'dashboard-perawat' => 'simrs_queue.manage',
    'dashboard-farmasi' => 'simrs_pharmacy.manage',
    'dashboard-kasir' => 'simrs_cashier.manage',
    'dashboard-finance' => 'simrs_report.view',
    'dashboard-manajemen' => 'simrs_report.view',
    'simrs-patients' => 'simrs_patient.view',
    'simrs-doctors' => 'simrs_doctor.manage',
    'simrs-polyclinics' => 'simrs_polyclinic.manage',
    'simrs-doctor-schedules' => 'simrs_schedule.manage',
    'simrs-registration' => 'simrs_registration.manage',
    'simrs-queue' => 'simrs_queue.manage',
    'simrs-outpatient' => 'simrs_outpatient.manage',
    'simrs-inpatient' => 'simrs_inpatient.manage',
    'simrs-laboratory' => 'simrs_lab.manage',
    'simrs-radiology' => 'simrs_radiology.manage',
    'simrs-medical-records' => 'simrs_medical_record.manage',
    'simrs-diagnoses' => 'simrs_medical_record.manage',
    'simrs-treatments' => 'simrs_medical_record.manage',
    'simrs-prescriptions' => 'simrs_prescription.manage',
    'simrs-pharmacy' => 'simrs_pharmacy.manage',
    'simrs-medical-items' => 'simrs_pharmacy.manage',
    'simrs-billing' => 'simrs_billing.manage',
    'simrs-cashier' => 'simrs_cashier.manage',
    'simrs-reports' => 'simrs_report.view',
    'parking-dashboard' => 'parking_dashboard.view',
    'parking-areas' => 'parking_master.manage',
    'parking-gates' => 'parking_master.manage',
    'parking-vehicle-types' => 'parking_master.manage',
    'parking-rates' => 'parking_master.manage',
    'parking-rate-settings' => 'parking_master.manage',
    'parking-members' => 'parking_master.manage',
    'parking-tickets' => 'parking_ticket.manage',
    'parking-gate-queue' => 'parking_ticket.manage',
    'parking-checkin' => 'parking_checkin.manage',
    'parking-checkout' => 'parking_checkout.manage',
    'parking-payments' => 'parking_payment.manage',
    'parking-validations' => 'parking_validation.manage',
    'parking-reports' => 'parking_report.view',
    'users' => 'user.view',
    'roles' => 'role.view',
    'activity-logs' => 'activity_logs.view',
];

foreach (SimrsExtension::modules() as $route => $module) {
    $protectedRoutePermissions[$route] = $module['permission'];
}

foreach ($protectedRoutePermissions as $prefix => $permission) {
    if ($page === $prefix || str_starts_with($page, $prefix . '-')) {
        requirePermission($permission);
        break;
    }
}

$protectedActionPermissions = [
    'users-create' => 'user.create',
    'users-store' => 'user.create',
    'users-edit' => 'user.edit',
    'users-update' => 'user.edit',
    'users-delete' => 'user.delete',
    'roles-create' => 'role.create',
    'roles-store' => 'role.create',
    'roles-edit' => 'role.edit',
    'roles-update' => 'role.edit',
    'roles-permissions' => 'role.permission',
    'roles-permissions-update' => 'role.permission',
];

if (isset($protectedActionPermissions[$page])) {
    requirePermission($protectedActionPermissions[$page]);
}

$postOnlyRoutes = [
    'process-login',
    'simrs-queue-status',
    'simrs-medical-records-store',
    'simrs-diagnoses-store',
    'simrs-treatments-store',
    'simrs-prescriptions-store',
    'simrs-pharmacy-status',
    'simrs-pharmacy-dispense',
    'simrs-billing-add-item',
    'simrs-cashier-pay',
    'parking-checkin-store',
    'parking-checkout-process',
    'parking-payments-store',
    'parking-validations-store',
    'parking-kiosk-entry-store',
    'parking-kiosk-exit-pay',
    'parking-api/lpr-entry',
    'parking-api/lpr-exit',
    'self-service-queue-store',
];

if (in_array($page, $postOnlyRoutes, true)
    || preg_match('/-(?:store|update|delete|approve|reject|save|send)$/', $page)
) {
    requirePost();
}

$simrsExtensionModules = SimrsExtension::modules();
foreach ($simrsExtensionModules as $route => $module) {
    if ($page === $route) {
        (new SimrsExtensionController())->index($route);
        return;
    }

    foreach (['create', 'store', 'show', 'edit', 'update'] as $action) {
        if ($page === $route . '-' . $action) {
            (new SimrsExtensionController())->{$action}($route);
            return;
        }
    }
}

switch ($page) {
    case 'home':
        if (empty($_SESSION['user_id'])) {
            header('Location: ' . url('login'));
            exit;
        }

        $landingPage = roleDashboardRoute();
        header('Location: ' . url($landingPage));
        exit;

    case 'login':
        (new AuthController())->login();
        break;
    case 'process-login':
        (new AuthController())->processLogin();
        break;
    case 'logout':
        (new AuthController())->logout();
        break;

    case 'self-service-queue':
        (new SelfServiceQueueController())->kiosk();
        break;
    case 'self-service-queue-store':
        (new SelfServiceQueueController())->store();
        break;
    case 'queue-display':
        (new SelfServiceQueueController())->display();
        break;
    case 'parking-kiosk-entry':
        (new ParkingKioskController())->entry();
        break;
    case 'parking-kiosk-entry-store':
        (new ParkingKioskController())->storeEntry();
        break;
    case 'parking-kiosk-exit':
        (new ParkingKioskController())->exit();
        break;
    case 'parking-kiosk-exit-pay':
        (new ParkingKioskController())->payExit();
        break;
    case 'parking-api/lpr-entry':
        (new ParkingApiController())->lprEntry();
        break;
    case 'parking-api/lpr-exit':
        (new ParkingApiController())->lprExit();
        break;
    case 'parking-api/ticket':
        (new ParkingApiController())->ticket();
        break;

    case 'simrs-dashboard':
        (new SimrsReportController())->dashboard();
        break;
    case 'dashboard-pendaftaran':
        (new RoleDashboardController())->registration();
        break;
    case 'dashboard-dokter':
        (new RoleDashboardController())->doctor();
        break;
    case 'dashboard-perawat':
        (new RoleDashboardController())->nurse();
        break;
    case 'dashboard-farmasi':
        (new RoleDashboardController())->pharmacy();
        break;
    case 'dashboard-kasir':
        (new RoleDashboardController())->cashier();
        break;
    case 'dashboard-finance':
        (new RoleDashboardController())->finance();
        break;
    case 'dashboard-manajemen':
        (new RoleDashboardController())->management();
        break;

    case 'simrs-patients':
        (new PatientController())->index();
        break;
    case 'simrs-patients-create':
        (new PatientController())->create();
        break;
    case 'simrs-patients-store':
        (new PatientController())->store();
        break;
    case 'simrs-patients-edit':
        (new PatientController())->edit();
        break;
    case 'simrs-patients-update':
        (new PatientController())->update();
        break;
    case 'simrs-patients-show':
        (new PatientController())->show();
        break;

    case 'simrs-doctors':
        (new DoctorController())->index();
        break;
    case 'simrs-doctors-create':
        (new DoctorController())->create();
        break;
    case 'simrs-doctors-store':
        (new DoctorController())->store();
        break;
    case 'simrs-doctors-edit':
        (new DoctorController())->edit();
        break;
    case 'simrs-doctors-update':
        (new DoctorController())->update();
        break;

    case 'simrs-polyclinics':
        (new PolyclinicController())->index();
        break;
    case 'simrs-polyclinics-create':
        (new PolyclinicController())->create();
        break;
    case 'simrs-polyclinics-store':
        (new PolyclinicController())->store();
        break;
    case 'simrs-polyclinics-edit':
        (new PolyclinicController())->edit();
        break;
    case 'simrs-polyclinics-update':
        (new PolyclinicController())->update();
        break;

    case 'simrs-doctor-schedules':
        (new DoctorScheduleController())->index();
        break;
    case 'simrs-doctor-schedules-create':
        (new DoctorScheduleController())->create();
        break;
    case 'simrs-doctor-schedules-store':
        (new DoctorScheduleController())->store();
        break;
    case 'simrs-doctor-schedules-edit':
        (new DoctorScheduleController())->edit();
        break;
    case 'simrs-doctor-schedules-update':
        (new DoctorScheduleController())->update();
        break;

    case 'simrs-registration':
        (new RegistrationController())->index();
        break;
    case 'simrs-registration-create':
        (new RegistrationController())->create();
        break;
    case 'simrs-registration-store':
        (new RegistrationController())->store();
        break;

    case 'simrs-queue':
        (new QueueController())->index();
        break;
    case 'simrs-queue-status':
        (new QueueController())->updateStatus();
        break;

    case 'simrs-outpatient':
        (new OutpatientController())->index();
        break;
    case 'simrs-outpatient-examine':
        (new OutpatientController())->examine();
        break;
    case 'simrs-inpatient':
        (new InpatientController())->index();
        break;
    case 'simrs-laboratory':
        (new LaboratoryController())->index();
        break;
    case 'simrs-radiology':
        (new RadiologyController())->index();
        break;
    case 'simrs-medical-records-store':
        (new MedicalRecordController())->store();
        break;
    case 'simrs-diagnoses-store':
        (new DiagnosisController())->store();
        break;
    case 'simrs-treatments-store':
        (new TreatmentController())->store();
        break;
    case 'simrs-prescriptions-store':
        (new PrescriptionController())->store();
        break;

    case 'simrs-pharmacy':
        (new PharmacyController())->index();
        break;
    case 'simrs-pharmacy-show':
        (new PharmacyController())->show();
        break;
    case 'simrs-pharmacy-status':
        (new PharmacyController())->status();
        break;
    case 'simrs-pharmacy-dispense':
        (new PharmacyController())->dispense();
        break;

    case 'simrs-medical-items':
        (new MedicalItemController())->index();
        break;
    case 'simrs-medical-items-create':
        (new MedicalItemController())->create();
        break;
    case 'simrs-medical-items-store':
        (new MedicalItemController())->store();
        break;
    case 'simrs-medical-items-edit':
        (new MedicalItemController())->edit();
        break;
    case 'simrs-medical-items-update':
        (new MedicalItemController())->update();
        break;
    case 'simrs-medical-items-delete':
        (new MedicalItemController())->delete();
        break;

    case 'simrs-billing':
        (new BillingController())->index();
        break;
    case 'simrs-billing-show':
        (new BillingController())->show();
        break;
    case 'simrs-billing-add-item':
        (new BillingController())->addItem();
        break;

    case 'simrs-cashier':
        (new CashierController())->index();
        break;
    case 'simrs-cashier-pay':
        (new CashierController())->pay();
        break;

    case 'simrs-reports-visits':
        (new SimrsReportController())->visits();
        break;
    case 'simrs-reports-income':
        (new SimrsReportController())->income();
        break;
    case 'simrs-reports-pharmacy':
        (new SimrsReportController())->pharmacy();
        break;

    case 'parking-dashboard':
        (new ParkingDashboardController())->index();
        break;
    case 'parking-areas':
        (new ParkingMasterController())->index('areas');
        break;
    case 'parking-areas-create':
        (new ParkingMasterController())->create('areas');
        break;
    case 'parking-areas-store':
        (new ParkingMasterController())->store('areas');
        break;
    case 'parking-areas-edit':
        (new ParkingMasterController())->edit('areas');
        break;
    case 'parking-areas-update':
        (new ParkingMasterController())->update('areas');
        break;
    case 'parking-gates':
        (new ParkingMasterController())->index('gates');
        break;
    case 'parking-gates-create':
        (new ParkingMasterController())->create('gates');
        break;
    case 'parking-gates-store':
        (new ParkingMasterController())->store('gates');
        break;
    case 'parking-gates-edit':
        (new ParkingMasterController())->edit('gates');
        break;
    case 'parking-gates-update':
        (new ParkingMasterController())->update('gates');
        break;
    case 'parking-vehicle-types':
        (new ParkingMasterController())->index('vehicle-types');
        break;
    case 'parking-vehicle-types-create':
        (new ParkingMasterController())->create('vehicle-types');
        break;
    case 'parking-vehicle-types-store':
        (new ParkingMasterController())->store('vehicle-types');
        break;
    case 'parking-vehicle-types-edit':
        (new ParkingMasterController())->edit('vehicle-types');
        break;
    case 'parking-vehicle-types-update':
        (new ParkingMasterController())->update('vehicle-types');
        break;
    case 'parking-rates':
        (new ParkingMasterController())->index('rates');
        break;
    case 'parking-rates-create':
        (new ParkingMasterController())->create('rates');
        break;
    case 'parking-rates-store':
        (new ParkingMasterController())->store('rates');
        break;
    case 'parking-rates-edit':
        (new ParkingMasterController())->edit('rates');
        break;
    case 'parking-rates-update':
        (new ParkingMasterController())->update('rates');
        break;
    case 'parking-rate-settings':
        (new ParkingMasterController())->index('rates');
        break;
    case 'parking-members':
        (new ParkingMasterController())->index('members');
        break;
    case 'parking-members-create':
        (new ParkingMasterController())->create('members');
        break;
    case 'parking-members-store':
        (new ParkingMasterController())->store('members');
        break;
    case 'parking-members-edit':
        (new ParkingMasterController())->edit('members');
        break;
    case 'parking-members-update':
        (new ParkingMasterController())->update('members');
        break;
    case 'parking-tickets':
        (new ParkingTicketController())->index();
        break;
    case 'parking-tickets-show':
        (new ParkingTicketController())->show();
        break;
    case 'parking-checkin':
        (new ParkingTicketController())->checkin();
        break;
    case 'parking-checkin-store':
        (new ParkingTicketController())->storeCheckin();
        break;
    case 'parking-checkout':
        (new ParkingTicketController())->checkout();
        break;
    case 'parking-checkout-process':
        (new ParkingTicketController())->processCheckout();
        break;
    case 'parking-payments-store':
        (new ParkingTicketController())->pay();
        break;
    case 'parking-validations':
        (new ParkingTicketController())->validations();
        break;
    case 'parking-validations-store':
        (new ParkingTicketController())->storeValidation();
        break;
    case 'parking-gate-open':
        (new ParkingTicketController())->openGate();
        break;
    case 'parking-gate-queue':
        (new ParkingDashboardController())->gateQueue();
        break;
    case 'parking-reports-transactions':
        (new ParkingDashboardController())->transactions();
        break;
    case 'parking-reports-income':
        (new ParkingDashboardController())->income();
        break;
    case 'parking-reports-occupancy':
        (new ParkingDashboardController())->occupancy();
        break;
    case 'parking-reports-duration':
        (new ParkingDashboardController())->duration();
        break;
    case 'parking-reports-lost-ticket':
        (new ParkingDashboardController())->lostTicket();
        break;

    case 'users':
        (new UserController())->index();
        break;
    case 'users-create':
        (new UserController())->create();
        break;
    case 'users-store':
        (new UserController())->store();
        break;
    case 'users-edit':
        (new UserController())->edit();
        break;
    case 'users-update':
        (new UserController())->update();
        break;
    case 'users-delete':
        (new UserController())->delete();
        break;

    case 'roles':
        (new RoleController())->index();
        break;
    case 'roles-create':
        (new RoleController())->create();
        break;
    case 'roles-store':
        (new RoleController())->store();
        break;
    case 'roles-edit':
        (new RoleController())->edit();
        break;
    case 'roles-update':
        (new RoleController())->update();
        break;
    case 'roles-permissions':
        (new RoleController())->permissions();
        break;
    case 'roles-permissions-update':
        (new RoleController())->updatePermissions();
        break;

    case 'activity-logs':
        (new ActivityLogController())->index();
        break;

    case 'robots.txt':
        header('Content-Type: text/plain; charset=utf-8');
        echo "User-agent: *\nDisallow: /\n";
        break;

    default:
        http_response_code(404);
        echo '404 - Page not found';
        break;
}
