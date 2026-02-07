<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CallLog;
use App\Models\Leads;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();

        // 🔹 chamadas recentes (5 últimas)
        $recentCalls = CallLog::query()
            ->when(!$user->hasRole('admin'), function ($q) use ($user) {
                $q->where('user_id', $user->id);
            })
            ->latest()
            ->take(5)
            ->get();

        if ($user->hasRole('admin')) {
            // Estatísticas globais
            $totalCalls = CallLog::count();
            $totalDuration = CallLog::sum('duration_seconds');
            $missedCalls = CallLog::where('type', 'MISSED')->count();
            $avgDuration = CallLog::avg('duration_seconds');

            return view('dashboard', [
                'role' => 'admin',
                'totalCalls' => $totalCalls,
                'totalDuration' => $totalDuration,
                'missedCalls' => $missedCalls,
                'avgDuration' => $avgDuration,
                'recentCalls' => $recentCalls,
            ]);
        }

        if ($user->hasRole('subscriber') || $user->hasRole('salesperson')) {
            $subscription = $user->subscription; // relação User->Subscription
            $plan = $subscription?->plan ?? 'kuma_essencial';

            $query = CallLog::where('user_id', $user->id);

            // Essencial = últimas 3 meses
            if ($plan === 'kuma_essencial') {
                $query->where('created_at', '>=', now()->subMonths(3));
            }

            $totalCalls = $query->count();
            $totalDuration = $query->sum('duration_seconds');
            $missedCalls = $query->where('type', 'MISSED')->count();
            $avgDuration = $query->avg('duration_seconds');

            $leads = Leads::where('company_id', $user->company_id);

            $total_leads = $leads->count();
            $total_new_leads = $leads->where('status','new')->count();
            $total_lost_leads = $leads->where('status','lost')->count();
            $total_won_leads = $leads->where('status','won')->count();

            return view('dashboard', [
                'role' => 'subscriber',
                'plan' => $plan,
                'totalCalls' => $totalCalls,
                'totalDuration' => $totalDuration,
                'missedCalls' => $missedCalls,
                'avgDuration' => $avgDuration,
                'recentCalls' => $recentCalls,
                'total_leads' => $total_leads,
                'total_new_leads' => $total_new_leads,
                'total_lost_leads' => $total_lost_leads,
                'total_won_leads' => $total_won_leads,
            ]);
        }
    }
}
