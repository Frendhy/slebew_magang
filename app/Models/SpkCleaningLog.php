<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SpkCleaningLog extends Model
{
    protected $fillable = [
        'spk_id', 'worker_id', 'cleaning_material', 'residual_material_kg',
        'start_time', 'end_time', 'qc_status', 'qc_note', 'qc_by', 'qc_at',
        'material_pendukung', 'material_hasil'
    ];

    protected $casts = [
        'material_pendukung' => 'array',
        'material_hasil' => 'array',
        'qc_at' => 'datetime',
    ];

    public function spk() { return $this->belongsTo(Spk::class); }
    public function worker() { return $this->belongsTo(User::class, 'worker_id'); }
}
