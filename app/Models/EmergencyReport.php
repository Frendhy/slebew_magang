<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmergencyReport extends Model
{
    protected $fillable = [
        'spk_id', 'worker_id', 'step', 'recipient_roles',
        'description', 'media_path', 'media_type', 'media_original_name', 'media_files',
        'status', 'resolved_by', 'resolved_at', 'resolve_note',
    ];

    protected $casts = [
        'recipient_roles' => 'array',
        'media_files'     => 'array',
        'resolved_at'     => 'datetime',
    ];

    // ── Relationships ──────────────────────────────────────────────────────────
    public function spk()        { return $this->belongsTo(Spk::class); }
    public function worker()     { return $this->belongsTo(User::class, 'worker_id'); }
    public function resolver()   { return $this->belongsTo(User::class, 'resolved_by'); }

    // ── Scopes ─────────────────────────────────────────────────────────────────
    public function scopeOpen($q)         { return $q->where('status', 'open'); }
    public function scopeForRole($q, $role) {
        return $q->whereJsonContains('recipient_roles', $role);
    }

    // ── Helpers ────────────────────────────────────────────────────────────────
    public function isOpen()     { return $this->status === 'open'; }
    public function isResolved() { return $this->status === 'resolved'; }

    public function getMediaUrlAttribute()
    {
        return $this->media_path ? asset('storage/' . $this->media_path) : null;
    }
}
