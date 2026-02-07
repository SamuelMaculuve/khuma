<?php

namespace App\Livewire;

use App\Models\Messages;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Livewire\Component;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class TicketSystem extends Component
{
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
        $this->lead = $lead;

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
        $dbMessages = Messages::where('lead_id',$this->lead->id)->
            where('sender_id', auth()->user()->id)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($message) {
                return [
                    'id' => $message->id,
                    'date' => $message->created_at->format('d \d\e F \d\e Y'),
                    'author' => $message->client == null ? $message->sender->name : $message->client->name ?? "N/A",
                    'time_ago' => $message->created_at->diffForHumans(),
                    'content' => $message->content,
                    'type' => $this->determineMessageType($message->channel, $message->metadata),
                    'channel' => $message->channel,
                    'direction' => $message->direction
                ];
            })
            ->toArray();

        // Combinar com mensagens estáticas (notes e status changes)
        $this->messages = array_merge($dbMessages, $this->getStaticMessages());

        // Ordenar por data
        usort($this->messages, function ($a, $b) {
            return strtotime($b['date']) - strtotime($a['date']);
        });
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

    public function sendMessage()
    {
        $this->validate([
            'newMessage' => 'required|string|min:1',
            'channel' => 'required|in:sms,whatsapp,email,phone,in_person',
        ]);

        try {

            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'token' => $this->currentInstance->token,
            ])->post('https://free.uazapi.com/send/text', [
                'number' => $this->lead->client->phone,
                'text' => $this->newMessage
            ]);
            Log::info('Erro ao conectar: ',['Vamos ver Sucesso ENVIO']);
            if ($response->successful()) {
                $data = $response->json();
                Log::info('Erro ao conectar: ',['DATA' =>  $data]);
                // Criar nova mensagem no banco de dados
                 Messages::create([
                    'message_id' => $data['messageid'] ?? '',
                    'message_to' => $this->lead->client->phone,
                    'lead_id' => $this->lead->id,
                    'sender_id' => Auth::id(),
                    'channel' => $this->channel,
                    'direction' => $this->direction,
                    'content' => $this->newMessage,
                ]);

                Log::info('Erro ao conectar: ',['Sucesso ENVIO' =>  $response->body()]);

            } else {
                Log::info('Erro ao conectar: ',['ERRO ENVIO' =>  $response->body()]);
            }
        } catch (\Exception $e) {
            Log::info('Erro ao conectar: ',['ERRO ENVIO'=>$e->getMessage()]);
        }

        // Resetar campo
        $this->newMessage = '';

        // Recarregar mensagens
        $this->loadMessages();

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
