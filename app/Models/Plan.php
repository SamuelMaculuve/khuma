<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Plan extends Model
{
    protected $fillable = ['code', 'name', 'description'];

    public function features(): HasMany
    {
        return $this->hasMany(PlanFeature::class);
    }

    public function prices(): HasMany
    {
        return $this->hasMany(PlanPrice::class);
    }

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    /**
     * Verifica se o plano tem uma feature activada.
     */
    public function hasFeature(string $key): bool
    {
        return $this->features()->where('feature_key', $key)->exists();
    }

    /**
     * Retorna o valor da feature ou null se não existir.
     */
    public function getFeatureValue(string $key): string|int|null
    {
        $feature = $this->features()->where('feature_key', $key)->first();
        return $feature?->feature_value;
    }

    /**
     * Verifica se a feature é "unlimited".
     */
    public function isFeatureUnlimited(string $key): bool
    {
        return $this->getFeatureValue($key) === 'unlimited';
    }

    /**
     * Verifica se o utilizador pode realizar uma acção tendo em conta
     * o limite numérico da feature e o uso actual.
     *
     * @param  string  $key          Feature key (ex: 'members')
     * @param  int     $currentUsage Quantidade actualmente em uso
     */
    public function canUse(string $key, int $currentUsage = 0): bool
    {
        if (!$this->hasFeature($key)) {
            return false;
        }

        if ($this->isFeatureUnlimited($key)) {
            return true;
        }

        $limit = (int) $this->getFeatureValue($key);
        return $currentUsage < $limit;
    }
}
