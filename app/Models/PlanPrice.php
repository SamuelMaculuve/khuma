<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlanPrice extends Model
{
    protected $fillable = [
        'plan_id',
        'amount',
        'currency',
        'is_active',
        'starts_at',
        'ends_at'
    ];

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }
}

