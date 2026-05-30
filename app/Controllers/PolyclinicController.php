<?php

class PolyclinicController extends SimrsCrudController
{
    protected $modelClass = Polyclinic::class;
    protected $permission = 'simrs_polyclinic.manage';
    protected $baseRoute = 'simrs-polyclinics';
    protected $viewPath = 'simrs/polyclinics';
    protected $title = 'Master Poli';
    protected $moduleName = 'SIMRS - Poli';

    protected function payload()
    {
        return [
            'clinic_code' => strtoupper(trim($_POST['clinic_code'] ?? '')),
            'name' => trim($_POST['name'] ?? ''),
            'queue_prefix' => strtoupper(trim($_POST['queue_prefix'] ?? 'A')),
            'location' => trim($_POST['location'] ?? ''),
            'status' => $_POST['status'] ?? 'active',
        ];
    }
}
