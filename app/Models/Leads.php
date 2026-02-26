<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Leads extends Model
{
    use SoftDeletes;

    protected $table = 'leads';
    protected $guarded = [];

    public function client(): BelongsTo
    {
        return $this->belongsTo(Clients::class);
    }

    public function messages(): HasMany
    {
        return $this->hasMany(Messages::class);
    }

    public function notes(): HasMany
    {
        return $this->hasMany(Notes::class);
    }

    public function histories(): HasMany
    {
        return $this->hasMany(LeadHistories::class);
    }

    public function user(): BelongsTo
    {
        return $this->client->user();
    }
}
