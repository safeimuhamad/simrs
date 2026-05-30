<?php

class ParkingVehicleType extends SimrsModel
{
    protected $table = 'parking_vehicle_types';
    protected $fillable = ['type_code','name','category','is_free','status'];
}
