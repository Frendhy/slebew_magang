<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SpkTransferLog extends Model
{
    protected $fillable = [
        'spk_id', 'worker_id', 'material_name', 'pallet_id', 'weight_kg',
        'slip_number', 'location', 'status', 'received_by'
    ];

    public function spk()    { return $this->belongsTo(Spk::class); }
    public function worker() { return $this->belongsTo(User::class, 'worker_id'); }
}
