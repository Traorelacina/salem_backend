<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Social extends Model
{
    protected $fillable = [
        'name',
        'icon',
        'url',
        'color',
        'order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'order'     => 'integer',
    ];

    // Scope pour le footer public (actifs triés)
    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('order');
    }
}