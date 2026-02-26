<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subscription extends Model
{
    protected $fillable = [
        'user_id',
        'plan_id',
        'status',
        'started_at',
        'renews_at'
    ];

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    public function cycles()
    {
        return $this->hasMany(SubscriptionCycle::class);
    }

    public function currentCycle()
    {
        return $this->cycles()->latest()->first();
    }
}

