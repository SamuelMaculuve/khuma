<?php

namespace App\Livewire;

use App\Models\Leads;
use Carbon\Carbon;
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
    public $sortField = 'number';
    public $sortDirection = 'desc';

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

    public function addNewItem($stateName)
    {
        if (!isset($this->states[$stateName])) {
            return;
        }

        $newId = rand(5000, 9999);
//        $this->states[$stateName][] = [
//            'id' => $newId,
//            'title' => 'Novo Item #' . $newId,
//            'number' => $newId,
//            'requester' => 'Novo Solicitante',
//            'link' => 'https://www.example.com',
//            'time' => 'Agora',
//            'service' => 'SRV_NOVO',
//            'icon' => 'novo',
//            'priority' => 'média',
//            'status' => $stateName
//        ];
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
        $leads = Leads::query()->get();

        foreach ($leads as $lead) {

            // fallback de segurança
            if (! array_key_exists($lead->status, $this->states)) {
                continue;
            }

            $this->states[$lead->status][] = [
                'id' => $lead->id,
                'client_id' => $lead->client_id,
                'reference' => $lead->reference,
                'title' => $lead->title,
                'description' => $lead->description,
                'status' => $lead->status,

                'value' => $lead->value,
                'expected_' => $lead->expected_,
                'close_date' => $lead->close_date,
                'source' => $lead->source,

                // opcional p/ UI
                'time' => $lead->close_date
                    ? Carbon::parse($lead->close_date)->diffForHumans()
                    : null,
            ];
        }
    }
    public function render()
    {
        return view('livewire.kanban-board', [
            'flattenedItems' => $this->getFlattenedItemsProperty(),
        ]);
    }
}
