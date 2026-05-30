<?php

class DoctorController extends SimrsCrudController
{
    protected $modelClass = Doctor::class;
    protected $permission = 'simrs_doctor.manage';
    protected $baseRoute = 'simrs-doctors';
    protected $viewPath = 'simrs/doctors';
    protected $title = 'Master Dokter';
    protected $moduleName = 'SIMRS - Dokter';

    protected function payload()
    {
        return [
            'user_id' => $_POST['user_id'] ?: null,
            'doctor_code' => trim($_POST['doctor_code'] ?? '') ?: 'DR-' . date('YmdHis'),
            'name' => trim($_POST['name'] ?? ''),
            'specialist' => trim($_POST['specialist'] ?? ''),
            'sip_no' => trim($_POST['sip_no'] ?? ''),
            'phone' => trim($_POST['phone'] ?? ''),
            'email' => trim($_POST['email'] ?? ''),
            'status' => $_POST['status'] ?? 'active',
        ];
    }
}
