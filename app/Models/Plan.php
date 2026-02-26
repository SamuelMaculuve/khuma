<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Plan extends Model
{
    protected $fillable = ['name', 'code', 'description'];

    public function features()
    {
        return $this->hasMany(PlanFeature::class);
    }

    public function feature($key)
    {
        return $this->features
            ->where('feature_key', $key)
            ->first()?->feature_value;
    }

    public function hasFeature(string $key): bool
    {
        return (bool) $this->feature($key);
    }

    public function featureLimit(string $key, $default = null)
    {
        return $this->feature($key) ?? $default;
    }


    public function prices()
    {
        return $this->hasMany(PlanPrice::class);
    }
    public function activePrice(): HasOne
    {
        return $this->hasOne(PlanPrice::class)->where('is_active', true)->latest();
    }
    public function currentPrice()
    {
        return $this->prices()
            ->where('is_active', true)
            ->latest()
            ->first();
    }
}
