<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPlanFeature
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $featureKey): Response
    {
        $user = auth()->user();

        if (!$user || !$user->subscription) {
            abort(403);
        }

        if (!$user->subscription->plan->hasFeature($featureKey)) {
            abort(403, 'Funcionalidade indisponível no seu plano');
        }

        return $next($request);
    }
}
