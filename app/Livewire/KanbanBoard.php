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
                'icon' => 'fenix'
            ],
            [
                'id' => 4873,
                'title' => 'Link suspeito do Fenix',
                'number' => 4873,
                'requester' => '',
                'link' => '',
                'time' => 'Em 2 dia(s)',
                'service' => '',
                'icon' => ''
            ],
            [
                'id' => 4871,
                'title' => 'Error no portal Administração Acadêmica > Operações de Alunos > Visualizar Alunos',
                'number' => 4871,
                'requester' => 'Cilda Chongola',
                'link' => 'https://www.srv.fenix.es',
                'time' => 'Ontem',
                'service' => 'SRV_FENIX',
                'icon' => 'fenix'
            ],
            [
                'id' => 4869,
                'title' => 'Error no portal Comunicação > Novidades',
                'number' => 4869,
                'requester' => 'Ilidio Francisco Matola',
                'link' => 'https://www.srv.fenix.es',
                'time' => 'Ontem',
                'service' => 'SRV_FENIX',
                'icon' => 'fenix'
            ],
            [
                'id' => 4868,
                'title' => 'Error no portal Estudante > Inscrever > Renovação',
                'number' => 4868,
                'requester' => 'Lourenço Maclau Vilanculo Junior',
                'link' => 'https://www.srv.fenix.es',
                'time' => 'Ontem',
                'service' => 'SRV_FENIX',
                'icon' => 'fenix'
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
                'icon' => 'website'
            ],
            [
                'id' => 4793,
                'title' => 'Problemas de lançamento de notas da segunda época',
                'number' => 4793,
                'requester' => 'Rabecca Fênix Cuna',
                'link' => 'https://www.srv.fenix.es',
                'time' => 'Em 3 dia(s)',
                'service' => 'SRV_FENIX',
                'icon' => 'fenix'
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
                'icon' => 'fenix'
            ],
        ]
    ];

    public $newStateName = '';
    public $draggingItem = null;
    public $draggingFromState = null;

    public function render()
    {
        return view('livewire.kanban-board');
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
        if ($stateName !== 'Pendentes') { // Não permitir remover o estado padrão
            unset($this->states[$stateName]);
        }
    }

    public function startDrag($itemId, $stateName)
    {
        $this->draggingItem = $itemId;
        $this->draggingFromState = $stateName;
    }

    public function endDrag($targetState)
    {
        if (!$this->draggingItem || !$this->draggingFromState) {
            return;
        }

        // Encontrar o item no estado de origem
        $itemIndex = null;
        $itemToMove = null;

        foreach ($this->states[$this->draggingFromState] as $index => $item) {
            if ($item['id'] == $this->draggingItem) {
                $itemIndex = $index;
                $itemToMove = $item;
                break;
            }
        }

        if ($itemToMove && $this->draggingFromState !== $targetState) {
            // Remover do estado de origem
            array_splice($this->states[$this->draggingFromState], $itemIndex, 1);

            // Adicionar ao estado de destino
            $this->states[$targetState][] = $itemToMove;
        }

        $this->draggingItem = null;
        $this->draggingFromState = null;
    }

    public function addNewItem($stateName)
    {
        $newId = rand(5000, 9999);
        $this->states[$stateName][] = [
            'id' => $newId,
            'title' => 'Novo Item #' . $newId,
            'number' => $newId,
            'requester' => 'Novo Solicitante',
            'link' => 'https://www.example.com',
            'time' => 'Agora',
            'service' => 'SRV_NOVO',
            'icon' => 'novo'
        ];
    }
}
