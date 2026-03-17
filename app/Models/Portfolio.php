<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Portfolio extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'client',
        'client_logo',
        'category',
        'cover_image',
        'short_description',
        'content',
        'external_link',
        'android_link',
        'ios_link',
        'is_confidential',
        'order',
        'is_active',
        'is_featured',
    ];

    protected $casts = [
        'is_confidential' => 'boolean',
        'is_active'       => 'boolean',
        'is_featured'     => 'boolean',
        'order'           => 'integer',
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

    // ── Relations ───────────────────────────────────────────
    public function images(): HasMany
    {
        return $this->hasMany(PortfolioImage::class)->orderBy('order');
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

    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    // ── Accessors ───────────────────────────────────────────
    public function getCoverUrlAttribute(): ?string
    {
        return $this->cover_image ? asset('storage/' . $this->cover_image) : null;
    }

    public function getClientLogoUrlAttribute(): ?string
    {
        return $this->client_logo ? asset('storage/' . $this->client_logo) : null;
    }
}
