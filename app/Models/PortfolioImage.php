<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PortfolioImage extends Model
{
    protected $fillable = [
        'portfolio_id',
        'path',
        'caption',
        'order',
    ];

    protected $casts = [
        'order' => 'integer',
    ];

    // ── Relations ───────────────────────────────────────────
    public function portfolio(): BelongsTo
    {
        return $this->belongsTo(Portfolio::class);
    }

    // ── Accessors ───────────────────────────────────────────
    public function getUrlAttribute(): string
    {
        return asset('storage/' . $this->path);
    }
}