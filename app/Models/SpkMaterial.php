<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SpkMaterial extends Model
{
    protected $fillable = ['spk_id', 'material_name', 'target_weight_per_batch', 'total_batches_required', 'total_material_kg'];

    public function spk() { return $this->belongsTo(Spk::class); }

    // Computed: total kg needed = kg_per_batch × total_batches
    public function getTotalKgAttribute(): float
    {
        return (float) $this->total_material_kg ?: ($this->target_weight_per_batch * $this->total_batches_required);
    }
}
