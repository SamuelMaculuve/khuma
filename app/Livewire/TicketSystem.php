<?php

namespace App\Livewire;

use App\Jobs\SendLeadEmail;
use App\Models\Messages;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class TicketSystem extends Component
{
    use WithFileUploads;
    public $lead;

    public $currentInstance;

    public $hasChatAccess = true; // Simulação de acesso, deve ser baseado no plano do usuário

    public $ticket = [];

    public $statuses = [
        ['id' => 'open', 'name' => 'Open', 'color' => 'gray'],
        ['id' => 'pending', 'name' => 'Pending', 'color' => 'yellow'],
        ['id' => 'escalated', 'name' => 'Escalated', 'color' => 'orange'],
        ['id' => 'resolved', 'name' => 'Resolved', 'color' => 'green'],
    ];

    public $newMessage = '';
    public $attachment = null;
    public $showSubtickets = false;

    public $leadId;

    public $messages = [];
    public $channel = 'whatsapp'; // default
    public $direction = 'outbound'; // default direction

    public function sendMessage1()
    {
        if(!$this->hasChatAccess){
            return session()->flash('error', 'Acesso negado. Atualize seu plano para acessar esta funcionalidade.');
        }
        if (!empty($this->newMessage)) {
            $this->messages[] = [
                'id' => count($this->messages) + 1,
                'date' => now()->format('d de F de Y'),
                'author' => 'Usuário Atual',
                'time_ago' => 'agora',
                'content' => $this->newMessage,
                'type' => 'message'
            ];
            $this->newMessage = '';
        }
    }

    public function changeStatus($status)
    {
        $this->ticket['status'] = $status;

        // Adiciona uma mensagem de mudança de status
        $this->messages[] = [
            'id' => count($this->messages) + 1,
            'date' => now()->format('d de F de Y'),
            'author' => 'Sistema',
            'time_ago' => 'agora',
            'content' => "Estado alterado para " . ucfirst($status),
            'type' => 'status_change'
        ];
    }

    public function mount($lead = null)
    {
        $this->lead = $lead->load('client', 'company');

        $this->ticket = [
            'id' => $lead->id,
            'title' => $lead->title,

            // status do ticket
            'status' => match ($lead->status) {
                'new', 'contacted' => 'pending',
                'qualified', 'proposal', 'negotiation' => 'pending',
                'won' => 'resolved',
                'lost' => 'escalated',
                default => 'pending',
            },

            // prioridade simples (podes melhorar)
            'priority' => $lead->value > 50000 ? 'high' : 'medium',

            // campos fixos / sistema
            'assigned_to' => auth()->user()->name ?? 'Não atribuído',
            'team' => 'Equipa Comercial',
            'helpdesk' => 'CRM',
            'type' => 'Lead',

            'tags' => [$lead->source],

            'client' => optional($lead->client)->name,
            'phone' => optional($lead->client)->phone,

            'document' => '',
            'provider_ticket' => $lead->reference,
            'path' => 'CRM > Leads',
            'reason' => $lead->description,

            'max_open_time' => '72h',
            'max_resolved_time' => '7 dias',

            'resolved_date' => $lead->close_date
                ? Carbon::parse($lead->close_date)->format('d-m-Y')
                : null,
        ];

        $this->leadId = 1;

        $this->currentInstance = Auth::user()->instance;

        $this->loadMessages();
    }

    //TODO: FILTRAR POR LEAD, MAKE WORK COMO DEVE SER...
    public function loadMessages()
    {
        // Carregar mensagens do banco de dados
        $this->messages = Messages::where('lead_id', $this->lead->id)
            ->with(['sender', 'client'])
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($message) {
                $isOutbound = $message->direction === 'outbound';
                $author = $isOutbound
                    ? (optional($message->sender)->name ?? 'Agente')
                    : (optional($message->client)->name ?? optional($message->sender)->name ?? 'Cliente');

                return [
                    'id'        => $message->id,
                    'date'      => $message->created_at->format('d \d\e F \d\e Y'),
                    'created_at'=> $message->created_at->timestamp,
                    'author'    => $author,
                    'time_ago'  => $message->created_at->diffForHumans(),
                    'content'   => $message->content,
                    'type'       => $this->determineMessageType($message->channel, $message->metadata ?? null),
                    'channel'   => $message->channel,
                    'direction' => $message->direction,
                    'attachment' => isset($message->metadata['attachment']) ? $message->metadata['attachment'] : null,
                ];
            })
            ->sortByDesc('created_at')
            ->values()
            ->toArray();
    }

    private function determineMessageType($channel, $metadata)
    {
        // Lógica para determinar o tipo baseado no canal ou metadata
        if (isset($metadata['type'])) {
            return $metadata['type'];
        }

        // Canal específico pode determinar o tipo
        if ($channel === 'in_person') {
            return 'message';
        }

        return 'message'; // default
    }

    private function getStaticMessages()
    {
        // Aqui você pode adicionar notes e status changes
        // Estes podem vir de uma tabela separada ou ser mantidos como estático
        return [
            [
                'id' => 2,
                'date' => '23 de julho de 2024',
                'author' => 'Zenildo Nhabomba',
                'time_ago' => 'há 1 ano',
                'content' => 'Anotado.',
                'type' => 'note'
            ],
            [
                'id' => 5,
                'date' => '24 de junho de 2024',
                'author' => 'Zenildo Nhabomba',
                'time_ago' => 'há 1 ano',
                'content' => 'Etapa Alterada • Pending → Resolved (Etapa)',
                'type' => 'status_change'
            ],
        ];
    }

    public function sendMessage(): void
    {
        $this->validate([
            'newMessage' => 'required_without:attachment|nullable|string',
            'attachment' => 'nullable|file|max:10240',
            'channel'    => 'required|in:sms,whatsapp,email,phone,in_person',
        ]);

        match ($this->channel) {
            'email'    => $this->sendViaEmail(),
            'whatsapp' => $this->sendViaWhatsApp(),
            default    => $this->saveMessageOnly(),
        };

        $this->newMessage = '';
        $this->attachment = null;
        $this->loadMessages();
    }

    private function sendViaEmail(): void
    {
        $client = $this->lead->client;

        if (empty($client->email)) {
            session()->flash('error', 'Este cliente não tem endereço de email registado. Adicione um email ao perfil do cliente.');
            return;
        }

        $smtpMid = Str::uuid() . '@' . ($this->lead->company->mail_subdomain ?? 'khuma') . '.' . config('services.mail_tenant.parent_domain', 'khuma.store');

        $attachmentPath = null;
        $attachmentMeta = null;
        if ($this->attachment) {
            $dir  = 'lead-attachments/' . $this->lead->company_id;
            $name = $this->attachment->getClientOriginalName();
            $attachmentPath = $this->attachment->storeAs($dir, Str::uuid() . '-' . $name, 'public');
            $attachmentMeta = [
                'path' => $attachmentPath,
                'name' => $name,
                'mime' => $this->attachment->getMimeType(),
                'size' => $this->attachment->getSize(),
            ];
        }

        $message = Messages::create([
            'message_id' => $smtpMid,
            'message_to' => $client->email,
            'lead_id'    => $this->lead->id,
            'sender_id'  => Auth::id(),
            'client_id'  => $client->id,
            'channel'    => 'email',
            'direction'  => 'outbound',
            'content'    => $this->newMessage ?? '',
            'metadata'   => $attachmentMeta ? json_encode(['attachment' => $attachmentMeta]) : null,
        ]);

        SendLeadEmail::dispatch(
            messageId:      $message->id,
            toEmail:        $client->email,
            clientName:     $client->name,
            messageContent: $this->newMessage ?? '',
            leadReference:  $this->lead->reference,
            agentName:      Auth::user()->name,
            companyId:      $this->lead->company_id,
            leadId:         $this->lead->id,
            smtpMessageId:  $smtpMid,
            attachmentPath: $attachmentPath,
            attachmentName: $attachmentMeta['name'] ?? null,
        );
    }

    private function sendViaWhatsApp(): void
    {
        try {
            $response = Http::withHeaders([
                'Accept'       => 'application/json',
                'Content-Type' => 'application/json',
                'token'        => $this->currentInstance->token,
            ])->post('https://free.uazapi.com/send/text', [
                'number' => $this->lead->client->phone,
                'text'   => $this->newMessage,
            ]);

            if ($response->successful()) {
                $data = $response->json();
                Messages::create([
                    'message_id' => $data['messageid'] ?? '',
                    'message_to' => $this->lead->client->phone,
                    'lead_id'    => $this->lead->id,
                    'sender_id'  => Auth::id(),
                    'channel'    => $this->channel,
                    'direction'  => $this->direction,
                    'content'    => $this->newMessage,
                ]);
            } else {
                Log::error('WhatsApp send failed', ['body' => $response->body()]);
            }
        } catch (\Exception $e) {
            Log::error('WhatsApp send exception', ['error' => $e->getMessage()]);
        }
    }

    private function saveMessageOnly(): void
    {
        Messages::create([
            'message_id' => 'manual-' . Str::uuid(),
            'message_to' => $this->lead->client->phone ?? '',
            'lead_id'    => $this->lead->id,
            'sender_id'  => Auth::id(),
            'client_id'  => optional($this->lead->client)->id,
            'channel'    => $this->channel,
            'direction'  => $this->direction,
            'content'    => $this->newMessage,
        ]);
    }

    public function markAsRead($messageId)
    {
        $message = Messages::find($messageId);
        if ($message && !$message->read_at) {
            $message->update(['read_at' => now()]);
            $this->loadMessages();
        }
    }
    public function render()
    {
        return view('livewire.ticket-system');
    }
}
