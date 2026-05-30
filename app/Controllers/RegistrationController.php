<?php

class RegistrationController extends Controller
{
    public function index()
    {
        $this->guard();
        $visits = (new PatientVisit())->list('', $_GET['date'] ?? date('Y-m-d'));
        $pagination = paginateRows($visits, 'simrs-registration', trim($_GET['search'] ?? ''), 10);
        $this->view('simrs/registration/index', array_merge(['title' => 'Pendaftaran Pasien', 'visits' => $pagination['rows']], $pagination));
    }

    public function create()
    {
        $this->guard();
        $this->view('simrs/registration/create', [
            'title' => 'Daftar Kunjungan',
            'patients' => (new Patient())->all('', 200, 0),
            'doctors' => (new Doctor())->active(),
            'polyclinics' => (new Polyclinic())->active(),
            'schedules' => (new DoctorSchedule())->activeByDay((int) date('N')),
        ]);
    }

    public function store()
    {
        $this->guard();
        $visitModel = new PatientVisit();
        $queueModel = new VisitQueue();
        $db = Database::connect();
        $db->beginTransaction();

        try {
            $visitId = $visitModel->create([
                'visit_no' => SimrsNumber::make('patient_visits', 'visit_no', 'VIS'),
                'patient_id' => $_POST['patient_id'],
                'doctor_id' => $_POST['doctor_id'] ?: null,
                'polyclinic_id' => $_POST['polyclinic_id'],
                'schedule_id' => $_POST['schedule_id'] ?: null,
                'visit_date' => $_POST['visit_date'] ?: date('Y-m-d'),
                'visit_time' => date('H:i:s'),
                'payment_type' => $_POST['payment_type'] ?? 'umum',
                'chief_complaint' => trim($_POST['chief_complaint'] ?? ''),
                'status' => 'waiting',
                'created_by' => $_SESSION['user_id'] ?? null,
            ]);

            $queueNo = SimrsNumber::queueNo($_POST['polyclinic_id'], $_POST['visit_date'] ?: date('Y-m-d'));
            $queueModel->create([
                'visit_id' => $visitId,
                'polyclinic_id' => $_POST['polyclinic_id'],
                'doctor_id' => $_POST['doctor_id'] ?: null,
                'queue_no' => $queueNo,
                'queue_date' => $_POST['visit_date'] ?: date('Y-m-d'),
                'status' => 'waiting',
            ]);

            $billingId = (new PatientBilling())->ensureForVisit($visitId);
            (new PatientBilling())->addItem($billingId, [
                'reference_type' => 'registration',
                'reference_id' => $visitId,
                'item_name' => 'Administrasi pendaftaran rawat jalan',
                'quantity' => 1,
                'unit_price' => (float) ($_POST['registration_fee'] ?? 0),
            ]);
            (new PatientBilling())->recalculate($billingId, 'draft');

            $db->commit();
            activity_log('SIMRS - Pendaftaran', 'create', 'Kunjungan pasien dibuat', $visitId);
            $_SESSION['success'] = 'Kunjungan berhasil didaftarkan. Nomor antrean: ' . $queueNo;
        } catch (Throwable $e) {
            if ($db->inTransaction()) {
                $db->rollBack();
            }
            $_SESSION['error'] = $e->getMessage();
        }

        $this->redirect('simrs-registration');
    }

    private function guard()
    {
        if (empty($_SESSION['user_id'])) {
            $this->redirect('login');
        }
        requirePermission('simrs_registration.manage');
    }
}
