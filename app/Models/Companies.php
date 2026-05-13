<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Companies extends Model
{

//    protected $fillable = ['name','tax_number','address','phone','email'];
    protected $guarded = [];
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function subscription(): HasOne
    {
        return $this->hasOne(Subscription::class, 'company_id');
    }

    public function activeSubscription(): HasOne
    {
        return $this->hasOne(Subscription::class, 'company_id')
            ->where('status', 'active')
            ->where('renews_at', '>', now());
    }
}
