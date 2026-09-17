<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SpkSisaMaterial extends Model
{
    use HasFactory;

    protected $fillable = [
        'spk_id',
        'material_name',
        'target_kg',
        'actual_kg',
        'sisa_kg',
        'reason',
        'location',
        'status'
    ];

    public function spk()
    {
        return $this->belongsTo(Spk::class);
    }
}
