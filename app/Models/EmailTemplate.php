<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class EmailTemplate extends Model
{
    protected $guarded = [];

    protected $casts = [
        'placeholders' => 'array',
        'is_global'    => 'boolean',
    ];

    public function company(): BelongsTo
    {
        return $this->belongsTo(Companies::class);
    }

    public function scopeAvailableTo(Builder $query, ?int $companyId): Builder
    {
        return $query->where(function ($q) use ($companyId) {
            $q->where('is_global', true)
              ->orWhere('company_id', $companyId);
        });
    }
}
