<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubscriptionCycle extends Model
{
    protected $fillable = [
        'subscription_id',
        'price',
        'currency',
        'period_start',
        'period_end',
        'status'
    ];

    public function subscription()
    {
        return $this->belongsTo(Subscription::class);
    }

    public function invoice()
    {
        return $this->hasOne(Invoice::class);
    }
}

