<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class Subscription extends Model
{
    protected $fillable = [
        'user_id',
        'plan_id',
        'price',
        'status',
        'start_date',
        'end_date',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date'   => 'datetime',
        'price'      => 'float',
    ];

    // ──────────────────────────────────────────────
    // Relações
    // ──────────────────────────────────────────────

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function plan(): BelongsTo
    {
        return $this->belongsTo(Plan::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    // ──────────────────────────────────────────────
    // Scopes
    // ──────────────────────────────────────────────

    /** Subscrições activas e não expiradas */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 'active')->where('end_date', '>', now());
    }

    /** Subscrições que expiram num determinado número de dias */
    public function scopeExpiringIn(Builder $query, int $days): Builder
    {
        return $query->where('status', 'active')
            ->whereDate('end_date', now()->addDays($days)->toDateString());
    }

    // ──────────────────────────────────────────────
    // Accessors / Helpers
    // ──────────────────────────────────────────────

    public function isActive(): bool
    {
        return $this->status === 'active' && $this->end_date > now();
    }

    public function daysLeft(): int
    {
        return max(0, (int) now()->diffInDays($this->end_date, false));
    }
}
