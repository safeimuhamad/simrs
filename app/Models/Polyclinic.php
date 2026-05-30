<?php

class Polyclinic extends SimrsModel
{
    protected $table = 'polyclinics';
    protected $fillable = ['clinic_code','name','queue_prefix','location','fhir_location_id','status'];

    protected function searchClause($search)
    {
        if ($search === '') {
            return ['', []];
        }

        $like = '%' . $search . '%';
        return ['WHERE name LIKE ? OR clinic_code LIKE ? OR location LIKE ?', [$like, $like, $like]];
    }
}
