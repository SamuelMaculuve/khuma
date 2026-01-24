<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LeadHistories extends Model
{
    protected $casts = [
        'old_data' => 'array',
        'new_data' => 'array',
    ];

    public function lead(): BelongsTo
    {
        return $this->belongsTo(Leads::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
