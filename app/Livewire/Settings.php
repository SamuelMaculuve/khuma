<?php

namespace App\Livewire;

use App\Models\Companies;
use App\Models\Team;
use App\Models\User;
use App\Services\MailcowService;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('layouts.app')]
class Settings extends Component
{
    #[Url]
    public string $tab = 'general';

    // General
    public string $companyName = '';
    public string $companyEmail = '';
    public string $companyPhone = '';
    public string $companyAddress = '';

    // Mail
    public string $mailStatus = 'pending';
    public string $mailSubdomain = '';
    public string $mailcowUrl = '';
    public string $mailcowKey = '';
    public bool   $testingMail = false;
    public ?string $mailTestResult = null;

    // Teams
    public bool   $showTeamForm = false;
    public ?int   $editingTeamId = null;
    public string $teamName = '';
    public string $teamEmailAlias = '';
    public string $teamDescription = '';
    public array  $teamMembers = [];
    public array  $availableUsers = [];
    public bool   $showDeleteConfirm = false;
    public ?int   $deletingTeamId = null;

    public function mount(): void
    {
        $company = auth()->user()->company;
        if ($company) {
            $this->companyName    = $company->name ?? '';
            $this->companyEmail   = $company->email ?? '';
            $this->companyPhone   = $company->phone ?? '';
            $this->companyAddress = $company->address ?? '';
            $this->mailStatus     = $company->mail_provision_status ?? 'pending';
            $this->mailSubdomain  = $company->mail_subdomain ?? '';
        }

        $this->mailcowUrl = (string) config('services.mailcow.url', '');
        $this->mailcowKey = '';

        $this->loadAvailableUsers();
    }

    // ── GENERAL ──────────────────────────────────────────────────
    public function saveGeneral(): void
    {
        $this->validate([
            'companyName'    => 'required|string|max:255',
            'companyEmail'   => 'nullable|email|max:255',
            'companyPhone'   => 'nullable|string|max:50',
            'companyAddress' => 'nullable|string|max:500',
        ]);

        auth()->user()->company?->update([
            'name'    => $this->companyName,
            'email'   => $this->companyEmail ?: null,
            'phone'   => $this->companyPhone ?: null,
            'address' => $this->companyAddress ?: null,
        ]);

        session()->flash('success_general', 'Informações atualizadas.');
    }

    // ── MAIL ─────────────────────────────────────────────────────
    public function testMailConnection(): void
    {
        $this->testingMail  = true;
        $this->mailTestResult = null;

        $url = $this->mailcowUrl ?: config('services.mailcow.url');
        $key = $this->mailcowKey ?: config('services.mailcow.api_key');

        try {
            $response = Http::withHeaders(['X-API-Key' => $key, 'Accept' => 'application/json'])
                ->withOptions(['verify' => false, 'timeout' => 8])
                ->get(rtrim($url, '/') . '/api/v1/get/status/containers');

            $this->mailTestResult = $response->successful()
                ? 'success:Ligação ao Mailcow bem-sucedida.'
                : 'error:Falha — HTTP ' . $response->status();
        } catch (\Throwable $e) {
            $this->mailTestResult = 'error:' . $e->getMessage();
        } finally {
            $this->testingMail = false;
        }
    }

    public function reprovisionMail(): void
    {
        $company = auth()->user()->company;
        if (! $company) return;

        $company->update(['mail_provision_status' => 'provisioning', 'mail_provision_error' => null]);
        \App\Jobs\ProvisionTenantMailDomain::dispatch($company->id);

        session()->flash('success_mail', 'Provisionamento de email iniciado. Esta página será atualizada automaticamente.');
        $this->mailStatus = 'provisioning';
    }

    public function refreshMailStatus(): void
    {
        $company = auth()->user()->company?->fresh();

        $this->mailStatus = $company?->mail_provision_status ?? 'pending';
        $this->mailSubdomain = $company?->mail_subdomain ?? '';
    }

    // ── TEAMS ────────────────────────────────────────────────────
    public function openCreateTeam(): void
    {
        $this->resetTeamForm();
        $this->loadAvailableUsers();
        $this->showTeamForm = true;
    }

    public function openEditTeam(int $id): void
    {
        $team = Team::where('company_id', auth()->user()->company_id)->findOrFail($id);
        $this->editingTeamId    = $id;
        $this->teamName         = $team->name;
        $this->teamEmailAlias   = $team->email_alias ?? '';
        $this->teamDescription  = $team->description ?? '';
        $this->teamMembers      = $team->members->pluck('id')->map(fn($v) => (string)$v)->toArray();
        $this->loadAvailableUsers();
        $this->showTeamForm = true;
    }

    public function saveTeam(): void
    {
        $this->validate([
            'teamName'       => 'required|string|max:255',
            'teamEmailAlias' => 'nullable|string|max:50|alpha_dash',
        ]);

        $companyId = auth()->user()->company_id;

        if ($this->editingTeamId) {
            $team = Team::where('company_id', $companyId)->findOrFail($this->editingTeamId);
            $team->update([
                'name'        => $this->teamName,
                'email_alias' => $this->teamEmailAlias ?: null,
                'description' => $this->teamDescription ?: null,
            ]);
        } else {
            $team = Team::create([
                'company_id'  => $companyId,
                'name'        => $this->teamName,
                'email_alias' => $this->teamEmailAlias ?: null,
                'description' => $this->teamDescription ?: null,
            ]);

            // Create Mailcow alias for this team if mail is provisioned
            $company = auth()->user()->company;
            if ($company?->mail_provision_status === 'ready' && $this->teamEmailAlias) {
                try {
                    MailcowService::fromConfig()->addAlias(
                        "{$this->teamEmailAlias}@{$company->mail_subdomain}." . config('services.mail_tenant.parent_domain'),
                        "{$company->mail_inbox_local_part}@{$company->mail_subdomain}." . config('services.mail_tenant.parent_domain'),
                    );
                } catch (\Throwable) {}
            }
        }

        $team->members()->sync(array_map('intval', $this->teamMembers));

        $this->resetTeamForm();
        session()->flash('success_teams', 'Equipa guardada com sucesso.');
    }

    public function confirmDeleteTeam(int $id): void
    {
        $this->deletingTeamId    = $id;
        $this->showDeleteConfirm = true;
    }

    public function deleteTeam(): void
    {
        if ($this->deletingTeamId) {
            Team::where('company_id', auth()->user()->company_id)
                ->findOrFail($this->deletingTeamId)
                ->delete();
        }
        $this->showDeleteConfirm = false;
        $this->deletingTeamId    = null;
        session()->flash('success_teams', 'Equipa eliminada.');
    }

    public function render()
    {
        $company = auth()->user()->company;
        $this->mailStatus = $company?->mail_provision_status ?? 'pending';

        $teams = Team::with('members')
            ->where('company_id', auth()->user()->company_id)
            ->orderBy('name')
            ->get();

        return view('livewire.settings', [
            'company' => $company,
            'teams' => $teams,
            'mailDiagnostics' => $this->mailDiagnostics(),
        ]);
    }

    private function resetTeamForm(): void
    {
        $this->editingTeamId   = null;
        $this->teamName        = '';
        $this->teamEmailAlias  = '';
        $this->teamDescription = '';
        $this->teamMembers     = [];
        $this->showTeamForm    = false;
        $this->resetErrorBag();
    }

    private function loadAvailableUsers(): void
    {
        $this->availableUsers = User::where('company_id', auth()->user()->company_id)
            ->orderBy('name')
            ->get(['id', 'name', 'email'])
            ->toArray();
    }

    private function mailDiagnostics(): array
    {
        return [
            ['label' => 'Mailcow API URL', 'ok' => filled(config('services.mailcow.url'))],
            ['label' => 'Mailcow API Key', 'ok' => filled(config('services.mailcow.api_key'))],
            ['label' => 'Cloudflare API Token', 'ok' => filled(config('services.cloudflare.api_token'))],
            ['label' => 'Cloudflare Zone ID', 'ok' => filled(config('services.cloudflare.zone_id'))],
            ['label' => 'Domínio principal', 'ok' => filled(config('services.mail_tenant.parent_domain'))],
            ['label' => 'Servidor SMTP', 'ok' => filled(config('services.mail_tenant.mail_host'))],
        ];
    }
}
