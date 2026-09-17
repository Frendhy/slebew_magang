<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SpkBaggingLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'spk_id',
        'worker_id',
        'sak_count',
        'weight_kg',
        'duration_minutes',
        'reject_count',
        'qc_status'
    ];

    public function spk()
    {
        return $this->belongsTo(Spk::class);
    }

    public function worker()
    {
        return $this->belongsTo(User::class, 'worker_id');
    }
}
