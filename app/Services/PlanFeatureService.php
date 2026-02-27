<?php

namespace App\Services;

use App\Models\User;

class PlanFeatureService
{
    /**
     * Verifica se o utilizador tem acesso a uma feature.
     */
    public function hasFeature(User $user, string $key): bool
    {
        return $user->hasFeature($key);
    }

    /**
     * Verifica se o utilizador ainda pode criar/adicionar recursos
     * com base no limite da feature e no uso actual.
     */
    public function canUse(User $user, string $key, int $currentUsage): bool
    {
        return $user->canUseFeature($key, $currentUsage);
    }

    /**
     * Devolve o limite da feature formatado para exibição.
     * Ex: 5 → "5", 'unlimited' → "Ilimitado"
     */
    public function displayLimit(User $user, string $key): string
    {
        if (!$user->hasFeature($key)) {
            return 'Indisponível';
        }

        if ($user->featureIsUnlimited($key)) {
            return 'Ilimitado';
        }

        return (string) $user->featureValue($key);
    }

    /**
     * Devolve um array com o resumo das features do plano activo.
     * Útil para passar ao frontend (ex: Inertia/Blade).
     */
    public function summary(User $user): array
    {
        $plan = $user->activePlan();

        if (!$plan) {
            return [];
        }

        return $plan->features->mapWithKeys(function ($feature) {
            return [
                $feature->feature_key => [
                    'value'     => $feature->feature_value,
                    'unlimited' => $feature->feature_value === 'unlimited',
                    'limit'     => $feature->feature_value === 'unlimited'
                        ? null
                        : (int) $feature->feature_value,
                ],
            ];
        })->toArray();
    }
}
