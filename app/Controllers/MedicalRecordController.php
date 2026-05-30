<?php

class MedicalRecordController extends Controller
{
    public function store()
    {
        if (empty($_SESSION['user_id'])) {
            $this->redirect('login');
        }
        requirePermission('simrs_medical_record.manage');

        $visit = (new PatientVisit())->withRelations($_POST['visit_id'] ?? null);
        if (!$visit) {
            $this->redirect('simrs-outpatient');
        }

        $recordId = (new MedicalRecord())->saveForVisit([
            'visit_id' => $visit['id'],
            'patient_id' => $visit['patient_id'],
            'doctor_id' => $visit['doctor_id'],
            'subjective' => trim($_POST['subjective'] ?? ''),
            'objective' => trim($_POST['objective'] ?? ''),
            'assessment' => trim($_POST['assessment'] ?? ''),
            'plan' => trim($_POST['plan'] ?? ''),
            'vital_bp' => trim($_POST['vital_bp'] ?? ''),
            'vital_pulse' => trim($_POST['vital_pulse'] ?? ''),
            'vital_temperature' => trim($_POST['vital_temperature'] ?? ''),
            'vital_respiration' => trim($_POST['vital_respiration'] ?? ''),
            'vital_weight' => trim($_POST['vital_weight'] ?? ''),
            'notes' => trim($_POST['notes'] ?? ''),
            'created_by' => $_SESSION['user_id'] ?? null,
        ]);

        (new PatientVisit())->changeStatus($visit['id'], 'in_consultation');
        activity_log('SIMRS - EMR', 'save', 'Rekam medis disimpan', $recordId, $visit['visit_no']);
        $_SESSION['success'] = 'Rekam medis berhasil disimpan.';
        $this->redirect('simrs-outpatient-examine', ['visit_id' => $visit['id']]);
    }
}
