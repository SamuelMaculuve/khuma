<?php

namespace App\Livewire;

use Livewire\Component;

class KanbanBoard extends Component
{
    public $states = [
        'Pendentes' => [
            [
                'id' => 4876,
                'title' => 'Error no portal Estudante > Inscrever > Renovação',
                'number' => 4876,
                'requester' => 'Talia Mário Nhanthumbo',
                'link' => 'https://www.srv.fenix.es',
                'time' => 'Em 2 dia(s)',
                'service' => 'SRV_FENIX',
                'icon' => 'fenix',
                'priority' => 'alta',
                'status' => 'Pendentes'
            ],
            [
                'id' => 4873,
                'title' => 'Link suspeito do Fenix',
                'number' => 4873,
                'requester' => '',
                'link' => '',
                'time' => 'Em 2 dia(s)',
                'service' => '',
                'icon' => '',
                'priority' => 'média',
                'status' => 'Pendentes'
            ],
            [
                'id' => 4871,
                'title' => 'Error no portal Administração Acadêmica > Operações de Alunos > Visualizar Alunos',
                'number' => 4871,
                'requester' => 'Cilda Chongola',
                'link' => 'https://www.srv.fenix.es',
                'time' => 'Ontem',
                'service' => 'SRV_FENIX',
                'icon' => 'fenix',
                'priority' => 'alta',
                'status' => 'Pendentes'
            ],
            [
                'id' => 4869,
                'title' => 'Error no portal Comunicação > Novidades',
                'number' => 4869,
                'requester' => 'Ilidio Francisco Matola',
                'link' => 'https://www.srv.fenix.es',
                'time' => 'Ontem',
                'service' => 'SRV_FENIX',
                'icon' => 'fenix',
                'priority' => 'baixa',
                'status' => 'Pendentes'
            ],
            [
                'id' => 4868,
                'title' => 'Error no portal Estudante > Inscrever > Renovação',
                'number' => 4868,
                'requester' => 'Lourenço Maclau Vilanculo Junior',
                'link' => 'https://www.srv.fenix.es',
                'time' => 'Ontem',
                'service' => 'SRV_FENIX',
                'icon' => 'fenix',
                'priority' => 'alta',
                'status' => 'Pendentes'
            ],
        ],
        'Em Progresso' => [
            [
                'id' => 4848,
                'title' => 'Actualizações no website ISUTC',
                'number' => 4848,
                'requester' => 'Francisca Chacha',
                'link' => 'https://www.wb-esite.com.br',
                'time' => 'Há 9 dia(s)',
                'service' => 'SRV_WEBSITE',
                'icon' => 'website',
                'priority' => 'média',
                'status' => 'Em Progresso'
            ],
            [
                'id' => 4793,
                'title' => 'Problemas de lançamento de notas da segunda época',
                'number' => 4793,
                'requester' => 'Rabecca Fênix Cuna',
                'link' => 'https://www.srv.fenix.es',
                'time' => 'Em 3 dia(s)',
                'service' => 'SRV_FENIX',
                'icon' => 'fenix',
                'priority' => 'alta',
                'status' => 'Em Progresso'
            ],
        ],
        'Concluídos' => [
            [
                'id' => 4842,
                'title' => 'Error ao definir assiduidade mínima no fenix-intensivo',
                'number' => 4842,
                'requester' => 'Daniela Alexandra Augusto',
                'link' => 'https://www.srv.fenix.es',
                'time' => 'Há 9 dia(s)',
                'service' => 'SRV_FENIX',
                'icon' => 'fenix',
                'priority' => 'baixa',
                'status' => 'Concluídos'
            ],
        ]
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
        $this->states[$stateName][] = [
            'id' => $newId,
            'title' => 'Novo Item #' . $newId,
            'number' => $newId,
            'requester' => 'Novo Solicitante',
            'link' => 'https://www.example.com',
            'time' => 'Agora',
            'service' => 'SRV_NOVO',
            'icon' => 'novo',
            'priority' => 'média',
            'status' => $stateName
        ];
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

            if ($field === 'number') {
                return ($a[$field] - $b[$field]) * $direction;
            }

            return strcmp($a[$field] ?? '', $b[$field] ?? '') * $direction;
        });

        return $allItems;
    }

    public function render()
    {
        return view('livewire.kanban-board', [
            'flattenedItems' => $this->getFlattenedItemsProperty(),
        ]);
    }
}
