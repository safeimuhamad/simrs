<?php

class ParkingGate extends SimrsModel
{
    protected $table = 'parking_gates';
    protected $fillable = ['gate_code','name','gate_type','area_id','device_type','device_endpoint','status'];
}
