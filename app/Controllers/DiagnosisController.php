<?php

class DiagnosisController extends Controller
{
    public function store()
    {
        if (empty($_SESSION['user_id'])) {
            $this->redirect('login');
        }

        requirePermission('simrs_medical_record.manage');

        $visitId = $_POST['visit_id'] ?? null;
        $record = (new MedicalRecord())->findByVisit($visitId);

        (new Diagnosis())->create([
            'visit_id' => $visitId,
            'medical_record_id' => $record['id'] ?? null,
            'diagnosis_code' => strtoupper(trim($_POST['diagnosis_code'] ?? '')),
            'diagnosis_name' => trim($_POST['diagnosis_name'] ?? ''),
            'diagnosis_type' => $_POST['diagnosis_type'] ?? 'primary',
            'notes' => trim($_POST['notes'] ?? ''),
        ]);

        activity_log('SIMRS - Diagnosa', 'create', 'Diagnosa ICD manual disimpan', $visitId);
        $_SESSION['success'] = 'Diagnosa berhasil disimpan.';

        $this->redirect('simrs-outpatient-examine', ['visit_id' => $visitId]);
    }
}
