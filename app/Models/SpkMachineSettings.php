<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SpkMachineSettings extends Model
{
    protected $table = 'spk_machine_settings';
    protected $fillable = ['spk_id', 'temp_zone1', 'temp_zone2', 'temp_zone3', 'pressure_bar', 'rpm_speed', 'notes'];

    public function spk() { return $this->belongsTo(Spk::class); }
}
