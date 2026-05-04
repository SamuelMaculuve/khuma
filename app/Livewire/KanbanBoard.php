<?php

namespace App\Livewire;

use App\Models\Clients;
use App\Models\Leads;
use App\Models\Messages;
use Carbon\Carbon;
use Illuminate\Support\Str;
use Livewire\Component;

class KanbanBoard extends Component
{
    public  $states = [
        'new' => [],
        'contacted' => [],
        'qualified' => [],
        'proposal' => [],
        'negotiation' => [],
        'won' => [],
        'lost' => [],
    ];

    public $newStateName = '';
    public $viewMode = 'kanban'; // 'kanban' ou 'list'
    public $search = '';
    public $filterStatus = 'todos';
    public $filterPriority = 'todos';

    // Propriedades para ordenação
    public $sortField = 'id';
    public $sortDirection = 'desc';

    public bool $showLeadForm = false;
    public string $leadStatus = 'new';
    public ?int $lead_client_id = null;
    public string $lead_title = '';
    public string $lead_description = '';
    public ?string $lead_value = null;
    public ?string $lead_expected_close_date = null;
    public string $lead_source = '';

    public $availableClients = [];

    public bool $creatingNewClient = false;
    public string $new_client_name  = '';
    public string $new_client_email = '';
    public string $new_client_phone = '';

    protected function rules(): array
    {
        $rules = [
            'lead_title'               => ['required', 'string', 'max:255'],
            'lead_description'         => ['nullable', 'string'],
            'lead_value'               => ['nullable', 'numeric', 'min:0'],
            'lead_expected_close_date' => ['nullable', 'date'],
            'lead_source'              => ['nullable', 'string', 'max:255'],
            'leadStatus'               => ['required', 'string'],
        ];

        if ($this->creatingNewClient) {
            $rules['new_client_name']  = ['required', 'string', 'max:255'];
            $rules['new_client_email'] = ['nullable', 'email', 'max:255'];
            $rules['new_client_phone'] = ['nullable', 'string', 'max:50'];
        } else {
            $rules['lead_client_id']   = ['required', 'exists:clients,id'];
        }

        return $rules;
    }

    public function addState()
    {
        if (!empty($this->newStateName) && !isset($this->states[$this->newStateName])) {
            $this->states[$this->newStateName] = [];
            $this->newStateName = '';
        }
    }

    public function removeState($stateName)
    {
        if ($stateName !== 'Pendentes' && isset($this->states[$stateName])) {
            // Mover todos os itens para "Pendentes" antes de remover o estado
            foreach ($this->states[$stateName] as $item) {
                $item['status'] = 'Pendentes';
                $this->states['Pendentes'][] = $item;
            }
            unset($this->states[$stateName]);
        }
    }

    public function moveItem($itemId, $fromState, $toState)
    {
        if (!isset($this->states[$fromState]) || !isset($this->states[$toState]) || $fromState === $toState) {
            return;
        }

        // Encontrar o item no estado de origem
        $itemIndex = null;
        $itemToMove = null;

        foreach ($this->states[$fromState] as $index => $item) {
            if ($item['id'] == $itemId) {
                $itemIndex = $index;
                $itemToMove = $item;
                $itemToMove['status'] = $toState;
                break;
            }
        }

        if ($itemToMove !== null) {
            // Remover do estado de origem
            array_splice($this->states[$fromState], $itemIndex, 1);

            // Adicionar ao estado de destino
            $this->states[$toState][] = $itemToMove;
        }
    }

    public function openLeadForm(string $stateName): void
    {
        if (! array_key_exists($stateName, $this->states)) {
            return;
        }

        $companyId = auth()->user()->company_id;

        $this->resetLeadForm();
        $this->leadStatus = $stateName;
        $this->availableClients = Clients::where('company_id', $companyId)
            ->orderBy('name')
            ->get(['id', 'name'])
            ->toArray();
        $this->showLeadForm = true;
    }

    public function closeLeadForm(): void
    {
        $this->showLeadForm = false;
        $this->resetLeadForm();
    }

    public function toggleNewClient(): void
    {
        $this->creatingNewClient = ! $this->creatingNewClient;
        $this->resetValidation(['lead_client_id', 'new_client_name', 'new_client_email', 'new_client_phone']);

        if ($this->creatingNewClient) {
            $this->lead_client_id = null;
        } else {
            $this->reset(['new_client_name', 'new_client_email', 'new_client_phone']);
        }
    }

    public function saveLead(): void
    {
        $data = $this->validate();

        $user      = auth()->user();
        $companyId = $user->company_id;

        if ($this->creatingNewClient) {
            $client = Clients::create([
                'company_id' => $companyId,
                'name'       => $data['new_client_name'],
                'email'      => $data['new_client_email'] ?: null,
                'phone'      => $data['new_client_phone'] ?: null,
            ]);
            $clientId = $client->id;

            $this->availableClients = Clients::where('company_id', $companyId)
                ->orderBy('name')
                ->get(['id', 'name'])
                ->toArray();
        } else {
            $clientId = $data['lead_client_id'];
        }

        $lead = Leads::create([
            'client_id'           => $clientId,
            'company_id'          => $companyId,
            'reference'           => 'LD-' . $companyId . '-' . strtoupper(Str::random(8)),
            'title'               => $data['lead_title'],
            'description'         => $data['lead_description'] ?: null,
            'status'              => $data['leadStatus'],
            'value'               => $data['lead_value'] !== null && $data['lead_value'] !== '' ? $data['lead_value'] : null,
            'expected_close_date' => $data['lead_expected_close_date'] ?: null,
            'source'              => $data['lead_source'] ?: null,
        ]);

        if (array_key_exists($lead->status, $this->states)) {
            $clientName = $this->creatingNewClient
                ? ($data['new_client_name'] ?? '')
                : (Clients::find($clientId)?->name ?? '');

            $this->states[$lead->status][] = [
                'id'          => $lead->id,
                'client_id'   => $lead->client_id,
                'client_name' => $clientName,
                'reference'   => $lead->reference,
                'title'       => $lead->title,
                'description' => $lead->description,
                'status'      => $lead->status,
                'value'       => $lead->value,
                'source'      => $lead->source,
                'time'        => $lead->expected_close_date
                    ? Carbon::parse($lead->expected_close_date)->diffForHumans()
                    : null,
            ];
        }

        $this->closeLeadForm();
        $this->dispatch('lead-created', leadId: $lead->id);
    }

    private function resetLeadForm(): void
    {
        $this->reset([
            'lead_client_id',
            'lead_title',
            'lead_description',
            'lead_value',
            'lead_expected_close_date',
            'lead_source',
            'creatingNewClient',
            'new_client_name',
            'new_client_email',
            'new_client_phone',
        ]);
        $this->resetValidation();
    }

    public function switchView($mode)
    {
        $this->viewMode = $mode;
    }

    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    // Método para obter todos os itens achatados para a visualização de lista
    public function getFlattenedItemsProperty()
    {
        $allItems = [];

        foreach ($this->states as $stateName => $items) {
            foreach ($items as $item) {
                $allItems[] = array_merge($item, ['status' => $stateName]);
            }
        }

        // Aplicar filtro de busca
        if (!empty($this->search)) {
            $allItems = array_filter($allItems, function($item) {
                $searchLower = strtolower($this->search);
                return str_contains(strtolower($item['title']), $searchLower) ||
                    str_contains(strtolower($item['requester']), $searchLower) ||
                    str_contains(strtolower($item['service']), $searchLower);
            });
        }

        // Aplicar filtro de status
        if ($this->filterStatus !== 'todos') {
            $allItems = array_filter($allItems, function($item) {
                return $item['status'] === $this->filterStatus;
            });
        }

        // Aplicar filtro de prioridade
        if ($this->filterPriority !== 'todos') {
            $allItems = array_filter($allItems, function($item) {
                return $item['priority'] === $this->filterPriority;
            });
        }

        // Ordenar
        usort($allItems, function($a, $b) {
            $field = $this->sortField;
            $direction = $this->sortDirection === 'asc' ? 1 : -1;

//            if ($field === 'number') {
//                return ($a[$field] - $b[$field]) * $direction;
//            }

            return strcmp($a[$field] ?? '', $b[$field] ?? '') * $direction;
        });

        return $allItems;
    }

    public function mount()
    {
        $leads = Leads::with('client')
            ->where('company_id', auth()->user()->company_id)
            ->get();

        foreach ($leads as $lead) {
            if (! array_key_exists($lead->status, $this->states)) {
                continue;
            }

            $this->states[$lead->status][] = [
                'id'          => $lead->id,
                'client_id'   => $lead->client_id,
                'client_name' => optional($lead->client)->name,
                'reference'   => $lead->reference,
                'title'       => $lead->title,
                'description' => $lead->description,
                'status'      => $lead->status,
                'value'       => $lead->value,
                'source'      => $lead->source,
                'time'        => $lead->expected_close_date
                    ? Carbon::parse($lead->expected_close_date)->diffForHumans()
                    : null,
            ];
        }
    }

    public function loadMessages()
    {
//        // Carregar mensagens do banco de dados
//        $dbMessages = Messages::where('sender_id', auth()->user()->id)
//            ->orderBy('created_at', 'desc')
//            ->get()
//            ->map(function ($message) {
//                return [
//                    'id' => $message->id,
//                    'date' => $message->created_at->format('d \d\e F \d\e Y'),
//                    'author' => $message->client == null ? $message->sender->name : $message->client->name ?? "N/A",
//                    'time_ago' => $message->created_at->diffForHumans(),
//                    'content' => $message->content,
//                    'type' => $this->determineMessageType($message->channel, $message->metadata),
//                    'channel' => $message->channel,
//                    'direction' => $message->direction
//                ];
//            })
//            ->toArray();
//
//        // Combinar com mensagens estáticas (notes e status changes)
//        $this->messages = array_merge($dbMessages, $this->getStaticMessages());
//
//        // Ordenar por data
//        usort($this->messages, function ($a, $b) {
//            return strtotime($b['date']) - strtotime($a['date']);
//        });
    }
    public function render()
    {
        return view('livewire.kanban-board', [
            'flattenedItems' => $this->getFlattenedItemsProperty(),
        ]);
    }
}
