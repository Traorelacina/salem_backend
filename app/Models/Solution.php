<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Solution extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'logo',
        'cover_image',
        'short_description',
        'content',
        'external_link',
        'category',
        'order',
        'is_active',
        'is_featured',
    ];

    protected $casts = [
        'is_active'   => 'boolean',
        'is_featured' => 'boolean',
        'order'       => 'integer',
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

    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    // ── Accessors ───────────────────────────────────────────
    public function getLogoUrlAttribute(): ?string
    {
        return $this->logo ? asset('storage/' . $this->logo) : null;
    }

    public function getCoverUrlAttribute(): ?string
    {
        return $this->cover_image ? asset('storage/' . $this->cover_image) : null;
    }
}