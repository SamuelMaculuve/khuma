<?php

namespace App\Http\Controllers;

use App\Models\Leads;
use Illuminate\Http\Request;

class LeadsController extends Controller
{
    public function index()
    {
        $companyId = (int) auth()->user()->company_id;

        return view('admin.whatsapp.index', [
            'leads' => Leads::forCompany($companyId)
                ->with(['client', 'team'])
                ->latest()
                ->get(),
        ]);
    }

    public function create()
    {
        abort_unless(auth()->user()->company_id, 403);

        return view('admin.whatsapp.create');
    }

    public function store(Request $request)
    {
        abort_unless(auth()->user()->company_id, 403);

        // Lead creation is currently handled by the Kanban Livewire flow.
        abort(405);
    }

    public function show(Leads $lead)
    {
        // Route model binding resolves by global ID. Re-scope it to the
        // authenticated tenant before rendering anything from the record.
        $lead = Leads::forCompany((int) auth()->user()->company_id)
            ->with(['client', 'team'])
            ->findOrFail($lead->getKey());

        $this->authorize('view', $lead);

        return view('admin.whatsapp.show', compact('lead'));
    }

    public function edit(Leads $lead)
    {
        $lead = Leads::forCompany((int) auth()->user()->company_id)
            ->findOrFail($lead->getKey());

        $this->authorize('update', $lead);

        abort(405);
    }

    public function update(Request $request, Leads $lead)
    {
        $lead = Leads::forCompany((int) auth()->user()->company_id)
            ->findOrFail($lead->getKey());

        $this->authorize('update', $lead);

        abort(405);
    }

    public function destroy(Leads $lead)
    {
        $lead = Leads::forCompany((int) auth()->user()->company_id)
            ->findOrFail($lead->getKey());

        $this->authorize('delete', $lead);

        abort(405);
    }
}
