<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Client extends Model
{
    protected $fillable = [
        'name',
        'logo',
        'website',
        'order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'order'     => 'integer',
    ];

    // ── Scopes ──────────────────────────────────────────────
    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('order');
    }

    // ── Accessors ───────────────────────────────────────────
    public function getLogoUrlAttribute(): string
    {
        return asset('storage/' . $this->logo);
    }
}