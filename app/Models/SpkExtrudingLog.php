<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SpkExtrudingLog extends Model
{
    protected $fillable = [
        'spk_id',
        'worker_id',
        'batch_number',
        'temperature',
        'pressure',
        'duration_seconds',
        'monitoring_data',
        'notes',
        'temp_zone1', 'temp_zone2', 'temp_zone3', 'temp_zone4',
        'rpm', 'ampere', 'qc_status', 'qc_note'
    ];

    protected $casts = [
        'monitoring_data' => 'array',
    ];

    public function spk() { return $this->belongsTo(Spk::class); }
    public function worker() { return $this->belongsTo(User::class, 'worker_id'); }
}
