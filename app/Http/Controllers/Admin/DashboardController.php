<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Clients;
use App\Models\EmailCampaign;
use App\Models\Leads;
use App\Models\Messages;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Response;

class DashboardController extends Controller
{
    public function dashboard()
    {
        $user      = Auth::user();
        $companyId = $user->company_id;

        $leadsQuery = Leads::where('company_id', $companyId);

        $total          = (clone $leadsQuery)->count();
        $totalNew       = (clone $leadsQuery)->where('status', 'new')->count();
        $totalContacted = (clone $leadsQuery)->where('status', 'contacted')->count();
        $totalQualified = (clone $leadsQuery)->where('status', 'qualified')->count();
        $totalProposal  = (clone $leadsQuery)->where('status', 'proposal')->count();
        $totalNeg       = (clone $leadsQuery)->where('status', 'negotiation')->count();
        $totalWon       = (clone $leadsQuery)->where('status', 'won')->count();
        $totalLost      = (clone $leadsQuery)->where('status', 'lost')->count();
        $totalClients   = Clients::where('company_id', $companyId)->count();
        $totalCampaigns = EmailCampaign::where('company_id', $companyId)->count();
        $totalMessages  = Messages::whereHas('lead', fn ($q) => $q->where('company_id', $companyId))->count();

        $recentLeads = Leads::with('client')
            ->where('company_id', $companyId)
            ->latest()
            ->take(8)
            ->get();

        $pipeline = [
            'new'         => $totalNew,
            'contacted'   => $totalContacted,
            'qualified'   => $totalQualified,
            'proposal'    => $totalProposal,
            'negotiation' => $totalNeg,
            'won'         => $totalWon,
            'lost'        => $totalLost,
        ];

        $conversionRate = $total > 0 ? round(($totalWon / $total) * 100, 1) : 0;

        return view('dashboard', compact(
            'total', 'totalNew', 'totalWon', 'totalLost',
            'totalClients', 'totalCampaigns', 'totalMessages',
            'pipeline', 'recentLeads', 'conversionRate',
        ));
    }

    public function downloadReport()
    {
        $user      = Auth::user();
        $companyId = $user->company_id;

        $leads = Leads::with('client')
            ->where('company_id', $companyId)
            ->orderByDesc('created_at')
            ->get();

        $rows   = [];
        $rows[] = implode(',', ['ID', 'Referência', 'Título', 'Cliente', 'Estado', 'Valor', 'Fonte', 'Data']);

        foreach ($leads as $lead) {
            $rows[] = implode(',', [
                $lead->id,
                $lead->reference,
                '"' . str_replace('"', '""', $lead->title) . '"',
                '"' . str_replace('"', '""', optional($lead->client)->name ?? '') . '"',
                $lead->status,
                $lead->value ?? 0,
                $lead->source ?? '',
                $lead->created_at->format('d/m/Y'),
            ]);
        }

        $csv      = implode("\n", $rows);
        $filename = 'leads-report-' . now()->format('Y-m-d') . '.csv';

        return Response::make($csv, 200, [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename={$filename}",
        ]);
    }
}
