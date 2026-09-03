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
        $user = $request->user();

        if (!$user) {
            abort(403);
        }

        if (method_exists($user, 'hasRole') && $user->hasRole('admin')) {
            return $next($request);
        }

        if (!$user->hasActiveSubscription()) {
            return redirect()
                ->route('subscription.plans')
                ->with('warning', 'Escolha uma subscrição para desbloquear os módulos do Khuma CRM.');
        }

        if (!$user->hasFeature($featureKey)) {
            abort(403, 'Funcionalidade indisponível no seu plano.');
        }

        return $next($request);
    }
}
