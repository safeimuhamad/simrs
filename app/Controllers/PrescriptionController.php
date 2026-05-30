<?php

class PrescriptionController extends Controller
{
    public function store()
    {
        if (empty($_SESSION['user_id'])) {
            $this->redirect('login');
        }
        requirePermission('simrs_prescription.manage');

        $visit = (new PatientVisit())->withRelations($_POST['visit_id'] ?? null);
        if (!$visit) {
            $this->redirect('simrs-outpatient');
        }

        $items = [];
        foreach (($_POST['items'] ?? []) as $item) {
            $items[] = [
                'product_id' => $item['product_id'] ?? null,
                'item_name' => trim($item['item_name'] ?? ''),
                'dosage' => trim($item['dosage'] ?? ''),
                'frequency' => trim($item['frequency'] ?? ''),
                'duration' => trim($item['duration'] ?? ''),
                'quantity' => (float) ($item['quantity'] ?? 1),
                'unit_name' => trim($item['unit_name'] ?? 'unit'),
                'price' => (float) str_replace('.', '', $item['price'] ?? 0),
                'notes' => trim($item['notes'] ?? ''),
            ];
        }

        $id = (new Prescription())->createWithItems([
            'prescription_no' => SimrsNumber::make('prescriptions', 'prescription_no', 'RX'),
            'visit_id' => $visit['id'],
            'patient_id' => $visit['patient_id'],
            'doctor_id' => $visit['doctor_id'],
            'status' => 'pending',
            'notes' => trim($_POST['notes'] ?? ''),
        ], $items);

        (new PatientVisit())->changeStatus($visit['id'], 'pharmacy');
        activity_log('SIMRS - Resep', 'create', 'Resep dokter dibuat', $id, $visit['visit_no']);
        $_SESSION['success'] = 'Resep berhasil dibuat dan masuk farmasi.';
        $this->redirect('simrs-outpatient-examine', ['visit_id' => $visit['id']]);
    }
}
