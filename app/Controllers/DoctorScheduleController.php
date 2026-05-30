<?php

class DoctorScheduleController extends SimrsCrudController
{
    protected $modelClass = DoctorSchedule::class;
    protected $permission = 'simrs_schedule.manage';
    protected $baseRoute = 'simrs-doctor-schedules';
    protected $viewPath = 'simrs/doctor-schedules';
    protected $title = 'Jadwal Dokter';
    protected $moduleName = 'SIMRS - Jadwal Dokter';

    protected function formData($data)
    {
        $data['doctors'] = (new Doctor())->active();
        $data['polyclinics'] = (new Polyclinic())->active();
        return $data;
    }

    protected function payload()
    {
        return [
            'doctor_id' => $_POST['doctor_id'],
            'polyclinic_id' => $_POST['polyclinic_id'],
            'day_of_week' => (int) ($_POST['day_of_week'] ?? 1),
            'start_time' => $_POST['start_time'] ?? '08:00',
            'end_time' => $_POST['end_time'] ?? '12:00',
            'quota' => (int) ($_POST['quota'] ?? 0),
            'status' => $_POST['status'] ?? 'active',
        ];
    }
}
