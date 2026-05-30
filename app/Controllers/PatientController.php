<?php

class PatientController extends SimrsCrudController
{
    protected $modelClass = Patient::class;
    protected $permission = 'simrs_patient.view';
    protected $baseRoute = 'simrs-patients';
    protected $viewPath = 'simrs/patients';
    protected $title = 'Master Pasien';
    protected $moduleName = 'SIMRS - Pasien';

    public function show()
    {
        $this->guard('simrs_patient.view');
        $patient = (new Patient())->find($_GET['id'] ?? null);
        if (!$patient) {
            $this->redirect('simrs-patients');
        }
        $this->view('simrs/patients/show', ['title' => 'Detail Pasien', 'patient' => $patient]);
    }

    protected function payload()
    {
        return [
            'medical_record_no' => trim($_POST['medical_record_no'] ?? '') ?: SimrsNumber::make('patients', 'medical_record_no', 'RM'),
            'nik' => trim($_POST['nik'] ?? ''),
            'name' => trim($_POST['name'] ?? ''),
            'gender' => $_POST['gender'] ?? null,
            'birth_date' => $_POST['birth_date'] ?: null,
            'birth_place' => trim($_POST['birth_place'] ?? ''),
            'phone' => trim($_POST['phone'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'address' => trim($_POST['address'] ?? ''),
            'blood_type' => trim($_POST['blood_type'] ?? ''),
            'allergy_notes' => trim($_POST['allergy_notes'] ?? ''),
            'emergency_contact_name' => trim($_POST['emergency_contact_name'] ?? ''),
            'emergency_contact_phone' => trim($_POST['emergency_contact_phone'] ?? ''),
            'insurance_type' => $_POST['insurance_type'] ?? 'umum',
            'insurance_no' => trim($_POST['insurance_no'] ?? ''),
            'status' => $_POST['status'] ?? 'active',
            'created_by' => $_SESSION['user_id'] ?? null,
            'updated_by' => $_SESSION['user_id'] ?? null,
        ];
    }
}
