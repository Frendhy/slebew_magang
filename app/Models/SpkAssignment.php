<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SpkAssignment extends Model
{
    protected $fillable = [
        'spk_id',
        'worker_id',
        'step',
        'shift',
        'day_number',
        'shift_number',
        'is_cadangan',
        'assigned_by',
    ];

    public function spk() {
        return $this->belongsTo(Spk::class);
    }

    public function worker() {
        return $this->belongsTo(User::class, 'worker_id');
    }

    public function assignedBy() {
        return $this->belongsTo(User::class, 'assigned_by');
    }
}
