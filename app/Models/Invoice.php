<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $fillable = [
        'subscription_cycle_id',
        'amount',
        'status',
        'issued_at',
        'paid_at'
    ];

    public function cycle()
    {
        return $this->belongsTo(SubscriptionCycle::class);
    }
}

