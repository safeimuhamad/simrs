<?php

class MedicalRecord extends SimrsModel
{
    protected $table = 'medical_records';
    protected $fillable = ['visit_id','patient_id','doctor_id','subjective','objective','assessment','plan','vital_bp','vital_pulse','vital_temperature','vital_respiration','vital_weight','notes','fhir_observation_payload','created_by'];

    public function findByVisit($visitId)
    {
        $stmt = $this->db->prepare("SELECT * FROM medical_records WHERE visit_id = ? LIMIT 1");
        $stmt->execute([$visitId]);

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function saveForVisit($data)
    {
        $existing = $this->findByVisit($data['visit_id']);

        if ($existing) {
            $this->update($existing['id'], $data);
            return (int) $existing['id'];
        }

        return $this->create($data);
    }
}
