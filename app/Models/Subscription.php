<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    protected $fillable = [
        'user_id',
        'company_id',
        'plan_id',
        'status',
        'started_at',
        'renews_at'
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'renews_at' => 'datetime',
    ];

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function company()
    {
        return $this->belongsTo(Companies::class);
    }

    public function cycles()
    {
        return $this->hasMany(SubscriptionCycle::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    public function currentCycle()
    {
        return $this->cycles()->latest()->first();
    }

    public function isActive(): bool
    {
        return $this->status === 'active' && $this->renews_at?->isFuture();
    }
}
