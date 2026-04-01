<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Social extends Model
{
    protected $fillable = [
        'name',
        'icon_path',
        'url',
        'color',
        'order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'order'     => 'integer',
    ];

    protected $appends = ['icon_url'];

    // ── Accesseur : URL publique de l'icône ──────────────────
    public function getIconUrlAttribute(): ?string
    {
        if (!$this->icon_path) return null;
        return Storage::disk('public')->url($this->icon_path);
    }

    // ── Scope pour le footer public (actifs triés) ───────────
    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('order');
    }
}