<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Spk extends Model
{
    protected $fillable = [
        'spk_number', 'product_name', 'status', 'current_step',
        'due_date', 'man_allocation', 'notes',
        'customer', 'ship_date', 'target_op', 'working_days',
        'working_minutes', 'machine', 'delay_hour', 'keterangan', 'remarks'
    ];

    public function materials()       { return $this->hasMany(SpkMaterial::class); }
    public function machineSettings() { return $this->hasOne(SpkMachineSettings::class); }
    public function batches()         { return $this->hasMany(SpkBatch::class); }
    public function extrudingLogs()   { return $this->hasMany(SpkExtrudingLog::class); }
    public function cleaningLogs()    { return $this->hasMany(SpkCleaningLog::class); }
    public function transferLogs()    { return $this->hasMany(SpkTransferLog::class); }
    public function assignments()     { return $this->hasMany(SpkAssignment::class); }
    public function approvals()       { return $this->hasMany(Approval::class); }
    public function baggingLogs()     { return $this->hasMany(SpkBaggingLog::class); }
    public function sisaMaterials()   { return $this->hasMany(SpkSisaMaterial::class); }
}
