<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware para verificar se o plano do utilizador suporta uma feature.
 *
 * Uso nas rotas:
 *   ->middleware('plan.feature:members')
 *   ->middleware('plan.feature:chatbot_lines')
 *   ->middleware('plan.feature:whatsapp_instances')
 */
class CheckPlanFeature
{
    public function handle(Request $request, Closure $next, string $featureKey): Response
    {
        $user = auth()->user();

        if (!$user || !$user->subscription) {
            abort(403, 'Sem subscrição activa');
        }

        $plan = $user->subscription->plan;

        if (!$plan || !$plan->hasFeature($featureKey)) {
            abort(403, 'Funcionalidade indisponível no seu plano');
        }

        // Partilha o valor da feature com os controllers via request
        $request->attributes->set('plan_feature_key', $featureKey);
        $request->attributes->set('plan_feature_value', $plan->getFeatureValue($featureKey));
        $request->attributes->set('plan_feature_unlimited', $plan->isFeatureUnlimited($featureKey));

        return $next($request);
    }
}
