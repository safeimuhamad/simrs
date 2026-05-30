<?php

class Patient extends SimrsModel
{
    protected $table = 'patients';
    protected $fillable = ['medical_record_no','nik','name','gender','birth_date','birth_place','phone','email','address','blood_type','allergy_notes','emergency_contact_name','emergency_contact_phone','insurance_type','insurance_no','fhir_patient_id','status','created_by','updated_by'];

    protected function searchClause($search)
    {
        if ($search === '') {
            return ['', []];
        }

        $like = '%' . $search . '%';
        return ['WHERE name LIKE ? OR medical_record_no LIKE ? OR nik LIKE ? OR phone LIKE ?', [$like, $like, $like, $like]];
    }
}
