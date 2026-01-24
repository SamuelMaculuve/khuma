<?php

namespace App\Livewire;

use Livewire\Component;

class TicketSystem extends Component
{
    public $ticket = [
        'id' => 20129,
        'title' => 'Timeouts no servidor de Primavera',
        'status' => 'pending', // pending, resolved, escalated
        'priority' => 'high', // low, medium, high
        'assigned_to' => 'Zenildo Nhabomba',
        'team' => 'Equipa de Apoio ao Cliente',
        'helpdesk' => 'HelpDesk IT',
        'type' => 'Problema',
        'tags' => ['SRV_PRIMAVERA'],
        'client' => 'Transcom S.A., Zenildo Nhabomba',
        'phone' => '844642763',
        'document' => '',
        'provider_ticket' => 'https://zibi.desk2u.com/workspace/tickets/20129',
        'path' => 'Property 3',
        'reason' => 'Aguarda feedback da Cegid',
        'max_open_time' => 'Tempo Maximo em Aberto',
        'max_resolved_time' => 'Tempo Maximo Em Resolvido',
        'resolved_date' => '13-06-2024',
    ];

    public $statuses = [
        ['id' => 'open', 'name' => 'Open', 'color' => 'gray'],
        ['id' => 'pending', 'name' => 'Pending', 'color' => 'yellow'],
        ['id' => 'escalated', 'name' => 'Escalated', 'color' => 'orange'],
        ['id' => 'resolved', 'name' => 'Resolved', 'color' => 'green'],
    ];

    public $newMessage = '';
    public $showSubtickets = false;

    public $messages = [
        [
            'id' => 1,
            'date' => '21 de agosto de 2024',
            'author' => 'Zenildo Nhabomba',
            'time_ago' => 'há 1 ano',
            'content' => 'Email enviado ao João da Cegid a solicitar apoio, aguardamos feedback.',
            'type' => 'message'
        ],
        [
            'id' => 2,
            'date' => '23 de julho de 2024',
            'author' => 'Zenildo Nhabomba',
            'time_ago' => 'há 1 ano',
            'content' => 'Anotado.',
            'type' => 'note'
        ],
        [
            'id' => 3,
            'date' => '24 de junho de 2024',
            'author' => 'csmith@transcom.co.mz',
            'time_ago' => 'há 1 ano',
            'content' => '@Zenildo Nhabomba pfv tenha muita atenção para anotar a data e hora exacta da aplicação destas medidas, no ticket de Desk2U. Já falamos sobre isto em outros momentos.',
            'type' => 'message'
        ],
        [
            'id' => 4,
            'date' => '24 de junho de 2024',
            'author' => 'Zenildo Nhabomba',
            'time_ago' => 'há 1 ano',
            'content' => 'Vamos também aplicar a redução dos logs',
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
        [
            'id' => 6,
            'date' => '24 de junho de 2024',
            'author' => 'Zenildo Nhabomba',
            'time_ago' => 'há 1 ano',
            'content' => 'Foi feita a reindexação da Base de dados, vou passar para resolved enquanto validamos se esse problema fica resolvido.',
            'type' => 'message'
        ],
        [
            'id' => 7,
            'date' => '17 de junho de 2024',
            'author' => 'Timilde Malbaze',
            'time_ago' => 'há 1 ano',
            'content' => 'Mudei o estado por engano.',
            'type' => 'note'
        ],
        [
            'id' => 8,
            'date' => '17 de junho de 2024',
            'author' => 'Timilde Malbaze',
            'time_ago' => 'há 1 ano',
            'content' => 'Etapa Alterada • Escalated → Pending (Etapa)',
            'type' => 'status_change'
        ],
    ];

    public function sendMessage()
    {
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

    public function render()
    {
        return view('livewire.ticket-system');
    }
}
