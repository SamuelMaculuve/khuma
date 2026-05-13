<?php

namespace App\Livewire;

use App\Jobs\SendCampaignEmails;
use App\Models\Clients;
use App\Models\EmailCampaign;
use App\Models\EmailTemplate;
use App\Models\Team;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Layout;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

#[Layout('layouts.app')]
class EmailCampaigns extends Component
{
    use WithPagination, WithFileUploads;

    public string $search = '';

    public bool   $showWizard = false;
    public int    $step       = 1;
    public ?int   $editingId  = null;

    public ?int   $templateId = null;
    public string $name       = '';
    public string $subject    = '';
    public string $preheader  = '';
    public string $senderName = '';
    public string $senderEmail = '';
    public ?int $replyTeamId = null;
    public string $replyAction = EmailCampaign::REPLY_CREATE_IF_NONE;
    public string $sendMode = 'now';
    public string $scheduledAt = '';
    public string $timezone = '';
    public string $bodyHtml   = '';
    public string $bodyText   = '';
    public array  $images     = [];
    public array  $filters    = ['lead_status' => []];

    public $imageUpload = null;
    public ?string $uploadingPlaceholder = null;

    public bool $showStats = false;
    public ?int $statsId   = null;

    protected function rules(): array
    {
        return [
            'name'                  => 'required|string|max:255',
            'subject'               => 'required|string|max:255',
            'preheader'             => 'nullable|string|max:180',
            'senderName'            => 'nullable|string|max:255',
            'senderEmail'           => 'nullable|email|max:255',
            'replyTeamId'           => 'nullable|integer|exists:teams,id',
            'replyAction'           => 'required|in:record_only,create_lead_if_none,always_create_lead',
            'sendMode'              => 'required|in:now,scheduled',
            'scheduledAt'           => 'required_if:sendMode,scheduled|nullable|date',
            'timezone'              => 'required|string|max:80',
            'bodyHtml'              => 'required|string|min:10',
            'bodyText'              => 'nullable|string',
            'filters.lead_status'   => 'nullable|array',
            'filters.lead_status.*' => 'in:new,contacted,qualified,proposal,negotiation,won,lost',
        ];
    }

    public function openCreate(): void
    {
        $this->resetForm();
        $this->showWizard = true;
        $this->step       = 1;
    }

    public function openEdit(int $id): void
    {
        $campaign = EmailCampaign::where('company_id', auth()->user()->company_id)
            ->where('status', 'draft')
            ->findOrFail($id);

        $this->editingId  = $id;
        $this->templateId = $campaign->email_template_id;
        $this->name       = $campaign->name;
        $this->subject    = $campaign->subject;
        $this->preheader  = $campaign->preheader ?? '';
        $this->senderName = $campaign->sender_name ?? '';
        $this->senderEmail = $campaign->sender_email ?? '';
        $this->replyTeamId = $campaign->reply_team_id;
        $this->replyAction = $campaign->reply_action ?? EmailCampaign::REPLY_CREATE_IF_NONE;
        $this->timezone = $campaign->timezone ?: config('app.timezone', 'UTC');
        $this->scheduledAt = $campaign->scheduled_at?->copy()->timezone($this->timezone)->format('Y-m-d\TH:i') ?? '';
        $this->sendMode = $campaign->scheduled_at ? 'scheduled' : 'now';
        $this->bodyHtml   = $campaign->body_html;
        $this->bodyText   = $campaign->body_text ?? '';
        $this->images     = $campaign->images ?? [];
        $this->filters    = $campaign->filters ?? ['lead_status' => []];
        $this->showWizard = true;
        $this->step       = 2;
        $this->dispatch('quill-set-content', html: $campaign->body_html);
    }

    public function pickTemplate(int $templateId): void
    {
        $template = EmailTemplate::availableTo(auth()->user()->company_id)->findOrFail($templateId);

        $this->templateId = $template->id;
        $this->subject    = $this->subject ?: $template->subject;
        $this->bodyHtml   = $template->body_html;
        $this->images     = [];
        foreach ($template->placeholders ?? [] as $ph) {
            if (($ph['type'] ?? null) === 'image') {
                $this->images[$ph['key']] = '';
            }
        }

        $this->step = 2;
        $this->dispatch('quill-set-content', html: $this->bodyHtml);
    }

    public function startBlank(): void
    {
        $this->templateId = null;
        $this->bodyHtml   = '<p>Olá {{name}},</p><p></p>';
        $this->images     = [];
        $this->step       = 2;
        $this->dispatch('quill-set-content', html: $this->bodyHtml);
    }

    public function nextStep(): void
    {
        if ($this->step === 2) {
            $this->validateOnly('name');
            $this->validateOnly('subject');
            $this->validateOnly('bodyHtml');
        }
        $this->step = min(3, $this->step + 1);
    }

    public function prevStep(): void
    {
        $this->step = max(1, $this->step - 1);
    }

    public function updatedImageUpload(): void
    {
        $this->validate(['imageUpload' => 'image|max:4096']);

        $companyId = auth()->user()->company_id;
        $path      = $this->imageUpload->store("campaigns/{$companyId}", 'public');
        $url       = asset(Storage::disk('public')->url($path));

        if ($this->uploadingPlaceholder) {
            $this->restoreMissingImagePlaceholder($this->uploadingPlaceholder);
            $this->images[$this->uploadingPlaceholder] = $url;
            $this->dispatch('quill-set-content', html: $this->bodyHtml);
            $this->uploadingPlaceholder = null;
        } else {
            $this->dispatch('quill-insert-image', url: $url);
        }

        $this->imageUpload = null;
    }

    public function selectPlaceholderForUpload(string $key): void
    {
        $this->uploadingPlaceholder = $key;
        $this->dispatch('trigger-image-upload');
    }

    public function clearPlaceholderImage(string $key): void
    {
        $this->images[$key] = '';
        $this->dispatch('quill-set-content', html: $this->bodyHtml);
    }

    public function save(): void
    {
        $this->validate();

        $this->persistCampaign();
        $this->resetForm();
        session()->flash('success', 'Campanha guardada com sucesso.');
    }

    public function saveAndQueue(): void
    {
        $this->validate();

        $campaign = $this->persistCampaign();
        $count = $this->countRecipients($campaign);

        if ($this->sendMode === 'scheduled') {
            $campaign->update([
                'status' => 'scheduled',
                'recipients_count' => $count,
            ]);

            session()->flash('success', "Campanha agendada para {$campaign->scheduled_at?->timezone($campaign->timezone)->format('d/m/Y H:i')} ({$count} destinatário(s)).");
        } else {
            $campaign->update([
                'status' => 'scheduled',
                'scheduled_at' => now(),
                'recipients_count' => $count,
            ]);
            SendCampaignEmails::dispatch($campaign->id);

            session()->flash('success', "Campanha enviada para a fila com {$count} destinatário(s).");
        }

        $this->resetForm();
    }

    private function persistCampaign(): EmailCampaign
    {
        $payload = [
            'company_id'        => auth()->user()->company_id,
            'created_by'        => auth()->id(),
            'email_template_id' => $this->templateId,
            'name'              => $this->name,
            'subject'           => $this->subject,
            'preheader'         => $this->preheader ?: null,
            'sender_name'       => $this->senderName ?: null,
            'sender_email'      => $this->senderEmail ?: null,
            'reply_team_id'     => $this->validatedReplyTeamId(),
            'reply_to_email'    => $this->replyToEmailForTeam($this->validatedReplyTeamId()),
            'reply_action'      => $this->replyAction,
            'scheduled_at'      => $this->scheduledDateTime(),
            'timezone'          => $this->timezone ?: config('app.timezone', 'UTC'),
            'body_html'         => $this->bodyHtml,
            'body_text'         => $this->bodyText ?: null,
            'images'            => $this->images,
            'filters'           => $this->filters,
        ];

        if ($this->editingId) {
            $campaign = EmailCampaign::where('company_id', auth()->user()->company_id)
                ->where('status', 'draft')
                ->findOrFail($this->editingId);
            $campaign->update($payload);
        } else {
            $payload['status'] = 'draft';
            $campaign = EmailCampaign::create($payload);
        }

        return $campaign->refresh();
    }

    public function send(int $id): void
    {
        $campaign = EmailCampaign::where('company_id', auth()->user()->company_id)
            ->whereIn('status', ['draft', 'scheduled'])
            ->findOrFail($id);

        $count = $this->countRecipients($campaign);
        $campaign->update(['status' => 'scheduled', 'scheduled_at' => now(), 'recipients_count' => $count]);

        SendCampaignEmails::dispatch($id);

        session()->flash('success', "Campanha enviada para {$count} destinatário(s).");
    }

    public function cancelSchedule(int $id): void
    {
        EmailCampaign::where('company_id', auth()->user()->company_id)
            ->where('status', 'scheduled')
            ->findOrFail($id)
            ->update([
                'status' => 'draft',
                'scheduled_at' => null,
            ]);

        session()->flash('success', 'Agendamento cancelado. A campanha voltou para rascunho.');
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

    public function getRecipientCountProperty(): int
    {
        return $this->countRecipientsForFilters($this->filters);
    }

    public function getPreviewHtmlProperty(): string
    {
        return $this->renderedHtml(sample: true);
    }

    public function getSelectedTemplateProperty(): ?EmailTemplate
    {
        return $this->templateId
            ? EmailTemplate::find($this->templateId)
            : null;
    }

    public function templatePreviewHtml(EmailTemplate $template): string
    {
        $html = $template->body_html;

        foreach ($template->placeholders ?? [] as $placeholder) {
            if (($placeholder['type'] ?? null) === 'image') {
                $html = str_replace('{{' . $placeholder['key'] . '}}', $this->placeholderImage(), $html);
            }
        }

        return strtr($html, [
            '{{name}}'         => 'Maria Silva',
            '{{email}}'        => 'maria@exemplo.pt',
            '{{company_name}}' => auth()->user()->company?->name ?? 'A sua empresa',
        ]);
    }

    private function renderedHtml(bool $sample = false): string
    {
        $html = $this->bodyHtml;
        foreach ($this->images as $key => $url) {
            $html = str_replace('{{' . $key . '}}', $url ?: $this->placeholderImage(), $html);
        }
        if ($sample) {
            $company = auth()->user()->company?->name ?? 'A sua empresa';
            $html = strtr($html, [
                '{{name}}'         => 'Maria Silva',
                '{{email}}'        => 'maria@exemplo.pt',
                '{{company_name}}' => $company,
            ]);
        }
        return $html;
    }

    private function scheduledDateTime(): ?Carbon
    {
        if ($this->sendMode !== 'scheduled' || ! $this->scheduledAt) {
            return null;
        }

        return Carbon::parse($this->scheduledAt, $this->timezone ?: config('app.timezone', 'UTC'))->utc();
    }

    public function getReplyActionOptionsProperty(): array
    {
        return [
            EmailCampaign::REPLY_RECORD_ONLY => 'Apenas registar resposta',
            EmailCampaign::REPLY_CREATE_IF_NONE => 'Criar lead se não existir lead activo',
            EmailCampaign::REPLY_ALWAYS_CREATE => 'Criar sempre novo lead',
        ];
    }

    public function getTimezoneOptionsProperty(): array
    {
        return [
            'Africa/Maputo',
            'Africa/Johannesburg',
            'Europe/Lisbon',
            'UTC',
        ];
    }

    public function getReplyTeamOptionsProperty()
    {
        return Team::query()
            ->where('company_id', auth()->user()->company_id)
            ->orderBy('name')
            ->get();
    }

    private function validatedReplyTeamId(): ?int
    {
        if (! $this->replyTeamId) {
            return null;
        }

        return Team::where('company_id', auth()->user()->company_id)
            ->whereKey($this->replyTeamId)
            ->exists()
            ? $this->replyTeamId
            : null;
    }

    private function replyToEmailForTeam(?int $teamId): ?string
    {
        if (! $teamId) {
            return null;
        }

        $team = Team::with('company')
            ->where('company_id', auth()->user()->company_id)
            ->find($teamId);

        return $team?->emailAddress() ?: null;
    }

    private function restoreMissingImagePlaceholder(string $key): void
    {
        $placeholder = '{{' . $key . '}}';

        if (str_contains($this->bodyHtml, $placeholder)) {
            return;
        }

        $fallbackImage = $this->placeholderImage();
        $position = strpos($this->bodyHtml, $fallbackImage);

        if ($position === false) {
            return;
        }

        $this->bodyHtml = substr_replace($this->bodyHtml, $placeholder, $position, strlen($fallbackImage));
    }

    private function placeholderImage(): string
    {
        return 'data:image/svg+xml;base64,' . base64_encode(
            '<svg xmlns="http://www.w3.org/2000/svg" width="600" height="240" viewBox="0 0 600 240">'
            . '<rect width="600" height="240" fill="#e5e7eb"/>'
            . '<text x="50%" y="50%" font-family="sans-serif" font-size="18" fill="#6b7280" text-anchor="middle" dominant-baseline="middle">Clique para adicionar imagem</text>'
            . '</svg>'
        );
    }

    private function countRecipients(EmailCampaign $campaign): int
    {
        return $this->countRecipientsForFilters($campaign->filters ?? [], $campaign->company_id);
    }

    private function countRecipientsForFilters(array $filters, ?int $companyId = null): int
    {
        $query = Clients::query()
            ->where('company_id', $companyId ?? auth()->user()->company_id)
            ->whereNotNull('email')
            ->where('email', '!=', '');

        if (!empty($filters['lead_status'])) {
            $query->whereHas('leads', function ($q) use ($filters) {
                $q->whereIn('status', (array) $filters['lead_status']);
            });
        }

        return $query->count();
    }

    private function resetForm(): void
    {
        $this->editingId  = null;
        $this->templateId = null;
        $this->name       = '';
        $this->subject    = '';
        $this->preheader  = '';
        $this->senderName = '';
        $this->senderEmail = '';
        $this->replyTeamId = null;
        $this->replyAction = EmailCampaign::REPLY_CREATE_IF_NONE;
        $this->sendMode = 'now';
        $this->scheduledAt = '';
        $this->timezone = config('app.timezone', 'UTC');
        $this->bodyHtml   = '';
        $this->bodyText   = '';
        $this->images     = [];
        $this->filters    = ['lead_status' => []];
        $this->showWizard = false;
        $this->step       = 1;
        $this->resetErrorBag();
    }

    public function render()
    {
        $campaigns = EmailCampaign::query()
            ->where('company_id', auth()->user()->company_id)
            ->when($this->search, fn($q) => $q->where('name', 'like', "%{$this->search}%"))
            ->withCount('logs')
            ->withCount([
                'logs as opened_count' => fn ($q) => $q->whereNotNull('opened_at'),
                'logs as clicked_count' => fn ($q) => $q->whereNotNull('clicked_at'),
                'logs as replied_count' => fn ($q) => $q->whereNotNull('replied_at'),
            ])
            ->orderByDesc('created_at')
            ->paginate(10);

        $templates = $this->showWizard
            ? EmailTemplate::availableTo(auth()->user()->company_id)->orderBy('category')->orderBy('name')->get()
            : collect();

        $statsData = null;
        if ($this->showStats && $this->statsId) {
            $statsData = EmailCampaign::with([
                'logs' => fn($q) => $q->with(['client', 'lead'])->latest()->limit(50),
            ])->where('company_id', auth()->user()->company_id)->find($this->statsId);
        }

        return view('livewire.email-campaigns', compact('campaigns', 'templates', 'statsData'));
    }
}
