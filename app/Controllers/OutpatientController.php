<?php

class OutpatientController extends Controller
{
    public function index()
    {
        $this->guard();
        [$doctorId, $polyclinicIds] = $this->doctorScope();
        $visits = (new PatientVisit())->list('', $_GET['date'] ?? date('Y-m-d'), $doctorId, $polyclinicIds);
        $pagination = paginateRows($visits, 'simrs-outpatient', trim($_GET['search'] ?? ''), 10);
        $this->view('simrs/outpatient/index', array_merge([
            'title' => 'Rawat Jalan',
            'visits' => $pagination['rows'],
        ], $pagination));
    }

    public function examine()
    {
        $this->guard();
        $visitId = $_GET['visit_id'] ?? null;
        $visit = (new PatientVisit())->withRelations($visitId);

        if (!$visit) {
            $this->redirect('simrs-outpatient');
        }

        [$doctorId, $polyclinicIds] = $this->doctorScope();
        if ($doctorId && (int) ($visit['doctor_id'] ?? 0) !== (int) $doctorId) {
            $this->redirect('simrs-outpatient');
        }
        if (!$doctorId && !empty($polyclinicIds) && !in_array((int) ($visit['polyclinic_id'] ?? 0), $polyclinicIds, true)) {
            $this->redirect('simrs-outpatient');
        }

        $this->view('simrs/outpatient/examine', [
            'title' => 'Pemeriksaan Dokter',
            'visit' => $visit,
            'record' => (new MedicalRecord())->findByVisit($visitId) ?: [],
            'diagnoses' => (new Diagnosis())->byVisit($visitId),
            'prescriptions' => (new Prescription())->byVisit($visitId),
            'medicines' => (new Pharmacy())->medicines(),
        ]);
    }

    private function guard()
    {
        if (empty($_SESSION['user_id'])) {
            $this->redirect('login');
        }
        requirePermission('simrs_outpatient.manage');
    }

    private function doctorScope()
    {
        if (role_name() !== 'dokter') {
            return [null, []];
        }

        $doctorModel = new Doctor();
        $doctor = $doctorModel->currentForUser();
        $doctorId = $doctor['id'] ?? null;

        return [$doctorId, $doctorModel->assignedPolyclinicIds($doctorId)];
    }
}
