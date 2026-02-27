<?php

namespace App\Traits;

/**
 * Trait para o modelo User.
 * Fornece atalhos para verificar features do plano activo.
 *
 * Requisito: o User deve ter um relacionamento `subscription`
 * que pertence a um `Plan` com `features`.
 */
trait HasPlanFeatures
{
    /**
     * Retorna o plano activo do utilizador ou null.
     */
    public function activePlan(): ?\App\Models\Plan
    {
        return $this->subscription?->plan;
    }

    /**
     * Verifica se o utilizador tem acesso a uma feature.
     */
    public function hasFeature(string $key): bool
    {
        return $this->activePlan()?->hasFeature($key) ?? false;
    }

    /**
     * Retorna o valor (limite) de uma feature.
     * Devolve null se o plano não tiver a feature.
     */
    public function featureValue(string $key): string|int|null
    {
        return $this->activePlan()?->getFeatureValue($key);
    }

    /**
     * Verifica se a feature é ilimitada no plano activo.
     */
    public function featureIsUnlimited(string $key): bool
    {
        return $this->activePlan()?->isFeatureUnlimited($key) ?? false;
    }

    /**
     * Verifica se o utilizador ainda pode usar uma feature
     * com base no uso actual vs. o limite do plano.
     *
     * Exemplo: $user->canUseFeature('members', $team->members()->count())
     */
    public function canUseFeature(string $key, int $currentUsage = 0): bool
    {
        return $this->activePlan()?->canUse($key, $currentUsage) ?? false;
    }
}
