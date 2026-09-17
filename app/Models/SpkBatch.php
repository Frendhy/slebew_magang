<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SpkBatch extends Model
{
    protected $fillable = [
        'spk_id', 'step', 'worker_id', 'batch_number', 'duration_seconds',
        'temp_hot', 'temp_cold'
    ];

    public function spk() { return $this->belongsTo(Spk::class); }
    public function worker() { return $this->belongsTo(User::class, 'worker_id'); }

    // Helper: format duration as MM:SS
    public function getDurationFormattedAttribute()
    {
        $m = str_pad(intdiv($this->duration_seconds, 60), 2, '0', STR_PAD_LEFT);
        $s = str_pad($this->duration_seconds % 60, 2, '0', STR_PAD_LEFT);
        return "{$m}:{$s}";
    }
}
