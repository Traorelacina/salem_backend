<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Service extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'icon',
        'image',
        'short_description',
        'content',
        'order',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'order'     => 'integer',
    ];

    // ── Auto-generate slug ───────────────────────────────────
    protected static function boot(): void
    {
        parent::boot();
        static::creating(function ($model) {
            if (empty($model->slug)) {
                $model->slug = Str::slug($model->title);
            }
        });
    }

    // ── Scopes ──────────────────────────────────────────────
    public function scopeActive($query)
    {
        return $query->where('is_active', true)->orderBy('order');
    }

    // ── Accessors ───────────────────────────────────────────
    public function getImageUrlAttribute(): ?string
    {
        return $this->image ? asset('storage/' . $this->image) : null;
    }
}