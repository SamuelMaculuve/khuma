<?php

namespace App\Livewire;

use App\Jobs\SendCampaignEmails;
use App\Models\Clients;
use App\Models\EmailCampaign;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class EmailCampaigns extends Component
{
    use WithPagination;

    public string $search = '';

    public bool   $showForm   = false;
    public ?int   $editingId  = null;
    public string $name       = '';
    public string $subject    = '';
    public string $bodyHtml   = '';
    public string $bodyText   = '';
    public array  $filters    = ['lead_status' => []];

    public bool $showStats = false;
    public ?int $statsId   = null;

    protected function rules(): array
    {
        return [
            'name'                  => 'required|string|max:255',
            'subject'               => 'required|string|max:255',
            'bodyHtml'              => 'required|string|min:10',
            'bodyText'              => 'nullable|string',
            'filters.lead_status'   => 'nullable|array',
            'filters.lead_status.*' => 'in:new,contacted,qualified,proposal,negotiation,won,lost',
        ];
    }

    public function openCreate(): void
    {
        $this->resetForm();
        $this->showForm = true;
    }

    public function openEdit(int $id): void
    {
        $campaign = EmailCampaign::where('company_id', auth()->user()->company_id)
            ->where('status', 'draft')
            ->findOrFail($id);

        $this->editingId = $id;
        $this->name      = $campaign->name;
        $this->subject   = $campaign->subject;
        $this->bodyHtml  = $campaign->body_html;
        $this->bodyText  = $campaign->body_text ?? '';
        $this->filters   = $campaign->filters ?? ['lead_status' => []];
        $this->showForm  = true;
        $this->dispatch('quill-set-content', html: $campaign->body_html);
    }

    public function save(): void
    {
        $this->validate();

        $payload = [
            'company_id' => auth()->user()->company_id,
            'created_by' => auth()->id(),
            'name'       => $this->name,
            'subject'    => $this->subject,
            'body_html'  => $this->bodyHtml,
            'body_text'  => $this->bodyText ?: null,
            'filters'    => $this->filters,
        ];

        if ($this->editingId) {
            EmailCampaign::where('company_id', auth()->user()->company_id)
                ->where('status', 'draft')
                ->findOrFail($this->editingId)
                ->update($payload);
        } else {
            $payload['status'] = 'draft';
            EmailCampaign::create($payload);
        }

        $this->resetForm();
        session()->flash('success', 'Campanha guardada com sucesso.');
    }

    public function send(int $id): void
    {
        $campaign = EmailCampaign::where('company_id', auth()->user()->company_id)
            ->where('status', 'draft')
            ->findOrFail($id);

        $count = $this->countRecipients($campaign);
        $campaign->update(['status' => 'scheduled', 'recipients_count' => $count]);

        SendCampaignEmails::dispatch($id);

        session()->flash('success', "Campanha enviada para {$count} destinatário(s).");
    }

    public function delete(int $id): void
    {
        EmailCampaign::where('company_id', auth()->user()->company_id)
            ->where('status', 'draft')
            ->findOrFail($id)
            ->delete();

        session()->flash('success', 'Campanha eliminada.');
    }

    public function openStats(int $id): void
    {
        $this->statsId   = $id;
        $this->showStats = true;
    }

    public function closeStats(): void
    {
        $this->showStats = false;
        $this->statsId   = null;
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    private function countRecipients(EmailCampaign $campaign): int
    {
        $query = Clients::query()
            ->where('company_id', $campaign->company_id)
            ->whereNotNull('email')
            ->where('email', '!=', '');

        if (!empty($campaign->filters['lead_status'])) {
            $query->whereHas('leads', function ($q) use ($campaign) {
                $q->whereIn('status', (array) $campaign->filters['lead_status']);
            });
        }

        return $query->count();
    }

    private function resetForm(): void
    {
        $this->editingId = null;
        $this->name      = '';
        $this->subject   = '';
        $this->bodyHtml  = '';
        $this->bodyText  = '';
        $this->filters   = ['lead_status' => []];
        $this->showForm  = false;
        $this->resetErrorBag();
    }

    public function render()
    {
        $campaigns = EmailCampaign::query()
            ->where('company_id', auth()->user()->company_id)
            ->when($this->search, fn($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->withCount('logs')
            ->orderByDesc('created_at')
            ->paginate(10);

        $statsData = null;
        if ($this->showStats && $this->statsId) {
            $statsData = EmailCampaign::with([
                'logs' => fn($q) => $q->latest()->limit(50),
            ])->where('company_id', auth()->user()->company_id)->find($this->statsId);
        }

        return view('livewire.email-campaigns', compact('campaigns', 'statsData'));
    }
}
