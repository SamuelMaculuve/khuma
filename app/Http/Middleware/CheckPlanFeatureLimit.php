<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Middleware para verificar limites numéricos de features (ex: ao criar um recurso).
 *
 * Uso nas rotas:
 *   ->middleware('plan.limit:members,App\Models\TeamMember,team_id')
 *
 * Parâmetros:
 *   $featureKey   - Chave da feature (ex: 'members')
 *   $modelClass   - Classe do Model a contar (ex: 'App\Models\TeamMember')
 *   $scopeColumn  - Coluna de scope ligada ao utilizador (ex: 'team_id') [opcional]
 */
class CheckPlanFeatureLimit
{
    public function handle(
        Request $request,
        Closure $next,
        string $featureKey,
        string $modelClass = '',
        string $scopeColumn = ''
    ): Response {
        $user = auth()->user();

        if (!$user || !$user->subscription) {
            abort(403, 'Sem subscrição activa');
        }

        $plan = $user->subscription->plan;

        if (!$plan || !$plan->hasFeature($featureKey)) {
            abort(403, 'Funcionalidade indisponível no seu plano');
        }

        // Se é unlimited, passa sempre
        if ($plan->isFeatureUnlimited($featureKey)) {
            return $next($request);
        }

        $limit = (int) $plan->getFeatureValue($featureKey);

        // Calcula o uso actual se foi passado um Model
        $currentUsage = 0;
        if ($modelClass && class_exists($modelClass)) {
            $query = $modelClass::query();

            if ($scopeColumn) {
                // Tenta obter o ID do scope a partir da route ou do utilizador
                $scopeId = $request->route($scopeColumn)
                    ?? $request->input($scopeColumn)
                    ?? $user->{$scopeColumn};

                if ($scopeId) {
                    $query->where($scopeColumn, $scopeId);
                }
            } else {
                // Fallback: conta por user_id se a coluna existir
                $query->where('user_id', $user->id);
            }

            $currentUsage = $query->count();
        }

        if ($currentUsage >= $limit) {
            $message = "Limite de {$limit} {$featureKey} atingido no seu plano";

            if ($request->expectsJson()) {
                return response()->json([
                    'message' => $message,
                    'feature' => $featureKey,
                    'limit'   => $limit,
                    'current' => $currentUsage,
                ], Response::HTTP_FORBIDDEN);
            }

            abort(403, $message);
        }

        return $next($request);
    }
}
