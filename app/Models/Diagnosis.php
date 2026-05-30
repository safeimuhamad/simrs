<?php

class Diagnosis extends SimrsModel
{
    protected $table = 'diagnoses';
    protected $fillable = ['visit_id','medical_record_id','diagnosis_code','diagnosis_name','diagnosis_type','notes','fhir_condition_id'];

    public function byVisit($visitId)
    {
        $stmt = $this->db->prepare("SELECT * FROM diagnoses WHERE visit_id = ? ORDER BY diagnosis_type ASC, id ASC");
        $stmt->execute([$visitId]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
