<?php
namespace App\Livewire;

use Livewire\Component;

class WhatsappInterface extends Component
{
    public $contacts = [];
    public $selectedContact = null;
    public $message = '';
    public $messages = [];
    public $callStatus = null;
    public $showMethodModal = false;
    public $selectedMethod = 'whatsapp'; // 'whatsapp' ou 'sms'
    public $tempMessage = '';

    protected $listeners = ['endCall', 'sendMessageConfirmed'];

    public function mount()
    {
        $this->contacts = [
            [
                'id' => 1,
                'name' => 'João Silva',
                'phone' => '+258 84 455 2968',
                'avatar' => 'https://ui-avatars.com/api/?name=João+Silva&background=random',
                'last_seen' => 'Hoje 14:30',
                'status' => 'online',
                'whatsapp_available' => true,
                'sms_available' => true
            ],
            [
                'id' => 2,
                'name' => 'Maria Santos',
                'phone' => '+258 87 307 7105',
                'avatar' => 'https://ui-avatars.com/api/?name=Maria+Santos&background=random',
                'last_seen' => 'Ontem 21:15',
                'status' => 'offline',
                'whatsapp_available' => true,
                'sms_available' => true
            ],
            [
                'id' => 3,
                'name' => 'Carlos Alberto',
                'phone' => '+258 86 509 8838',
                'avatar' => 'https://ui-avatars.com/api/?name=Carlos+Alberto&background=random',
                'last_seen' => 'Hoje 10:22',
                'status' => 'online',
                'whatsapp_available' => false, // Não tem WhatsApp
                'sms_available' => true
            ],
            [
                'id' => 4,
                'name' => 'Ana Pereira',
                'phone' => '+258 82 123 4567',
                'avatar' => 'https://ui-avatars.com/api/?name=Ana+Pereira&background=random',
                'last_seen' => '2 minutos atrás',
                'status' => 'online',
                'whatsapp_available' => true,
                'sms_available' => true
            ]
        ];

        // Mensagens com método de envio
        $this->messages = [
            [
                'id' => 1,
                'contact_id' => 1,
                'type' => 'received',
                'text' => 'Olá, como vai?',
                'time' => '10:30',
                'method' => null
            ],
            [
                'id' => 2,
                'contact_id' => 1,
                'type' => 'sent',
                'text' => 'Tudo bem! E contigo?',
                'time' => '10:32',
                'method' => 'whatsapp' // Indicando que foi por WhatsApp
            ],
            [
                'id' => 3,
                'contact_id' => 1,
                'type' => 'sent',
                'text' => 'Vou te enviar os documentos por email',
                'time' => '10:33',
                'method' => 'sms' // Indicando que foi por SMS
            ],
        ];
    }

    public function selectContact($contactId)
    {
        $this->selectedContact = collect($this->contacts)->firstWhere('id', $contactId);
        $this->callStatus = null;
        $this->showMethodModal = false;

        // Definir método padrão baseado na disponibilidade
        if ($this->selectedContact['whatsapp_available']) {
            $this->selectedMethod = 'whatsapp';
        } elseif ($this->selectedContact['sms_available']) {
            $this->selectedMethod = 'sms';
        }
    }

    public function startCall($contactId)
    {
        $contact = collect($this->contacts)->firstWhere('id', $contactId);
        $this->selectedContact = $contact;
        $this->callStatus = 'calling';

        $this->dispatchBrowserEvent('callStarted', ['contact' => $contact['name']]);
    }

    public function endCall()
    {
        $this->callStatus = 'ended';
        $this->dispatchBrowserEvent('callEnded');
    }

    public function sendMessage()
    {
        if ($this->message && $this->selectedContact) {
            $this->tempMessage = $this->message;
            $this->showMethodModal = true;
        }
    }

    public function sendMessageConfirmed()
    {
        if ($this->tempMessage && $this->selectedContact) {
            // Verificar se o método está disponível para o contato
            $methodAvailable = false;

            if ($this->selectedMethod === 'whatsapp' && $this->selectedContact['whatsapp_available']) {
                $methodAvailable = true;
            } elseif ($this->selectedMethod === 'sms' && $this->selectedContact['sms_available']) {
                $methodAvailable = true;
            }

            if (!$methodAvailable) {
                $this->dispatchBrowserEvent('showError', [
                    'message' => 'Este método não está disponível para este contato'
                ]);
                $this->showMethodModal = false;
                $this->tempMessage = '';
                return;
            }

            // Adicionar mensagem
            $this->messages[] = [
                'id' => count($this->messages) + 1,
                'contact_id' => $this->selectedContact['id'],
                'type' => 'sent',
                'text' => $this->tempMessage,
                'time' => now()->format('H:i'),
                'method' => $this->selectedMethod
            ];

            // Simular envio real
            $this->simulateMessageSending($this->selectedMethod);

            $this->dispatchBrowserEvent('scrollToBottom');

            // Resetar
            $this->message = '';
            $this->tempMessage = '';
            $this->showMethodModal = false;
        }
    }

    private function simulateMessageSending($method)
    {
        // Em produção, aqui integraria com APIs reais
        if ($method === 'whatsapp') {
            // Simular envio WhatsApp
            $this->dispatchBrowserEvent('showNotification', [
                'type' => 'success',
                'message' => 'Mensagem enviada via WhatsApp com sucesso!'
            ]);
        } elseif ($method === 'sms') {
            // Simular envio SMS
            $this->dispatchBrowserEvent('showNotification', [
                'type' => 'success',
                'message' => 'SMS enviado com sucesso!'
            ]);
        }
    }

    public function selectMethod($method)
    {
        $this->selectedMethod = $method;
        $this->sendMessageConfirmed();
    }

    public function cancelMessage()
    {
        $this->showMethodModal = false;
        $this->tempMessage = '';
    }

    public function getMethodIcon($method)
    {
        switch ($method) {
            case 'whatsapp': return 'fab fa-whatsapp text-green-500';
            case 'sms': return 'fas fa-sms text-blue-500';
            default: return 'fas fa-question text-gray-400';
        }
    }

    public function getMethodText($method)
    {
        switch ($method) {
            case 'whatsapp': return 'WhatsApp';
            case 'sms': return 'SMS';
            default: return 'Desconhecido';
        }
    }

    public function render()
    {
        return view('livewire.whatsapp-interface');
    }
}
