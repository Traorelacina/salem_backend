<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone',
        'company',
        'subject',
        'message',
        'status',
        'admin_note',
        'read_at',
        'replied_at',
    ];

    protected $casts = [
        'read_at'    => 'datetime',
        'replied_at' => 'datetime',
    ];

    // ── Scopes ──────────────────────────────────────────────
    public function scopeNew($query)
    {
        return $query->where('status', 'new');
    }

    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    public function scopeByStatus($query, string $status)
    {
        return $query->where('status', $status);
    }

    // ── Methods ─────────────────────────────────────────────
    public function markAsRead(): void
    {
        if (!$this->read_at) {
            $this->update([
                'status'  => 'read',
                'read_at' => now(),
            ]);
        }
    }

    public function markAsReplied(): void
    {
        $this->update([
            'status'     => 'replied',
            'replied_at' => now(),
        ]);
    }
}