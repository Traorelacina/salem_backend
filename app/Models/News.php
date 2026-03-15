<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class News extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'cover_image',
        'category',
        'excerpt',
        'content',
        'author',
        'is_published',
        'published_at',
        'views',
    ];

    protected $casts = [
        'is_published' => 'boolean',
        'published_at' => 'datetime',
        'views'        => 'integer',
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
    public function scopePublished($query)
    {
        return $query
            ->where('is_published', true)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->orderByDesc('published_at');
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

    public function getFormattedDateAttribute(): ?string
    {
        return $this->published_at?->format('d/m/Y');
    }

    // ── Methods ─────────────────────────────────────────────
    public function incrementViews(): void
    {
        $this->increment('views');
    }
}