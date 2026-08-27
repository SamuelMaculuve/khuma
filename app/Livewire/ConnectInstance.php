<?php

namespace App\Livewire;

use Illuminate\Support\Facades\Http;
use Livewire\Component;

class ConnectInstance extends Component
{
    public $instanceData = null;
    public $loading = false;
    public $disconnecting = false;
    public $error = null;
    public $success = null;
    public $phone = '';
    public $token = '';

    protected $rules = [
        'phone' => 'required|string|min:9',
        'token' => 'required|string',
    ];

    public function connect()
    {
        $this->validate();
        $this->loading = true;
        $this->clearMessages();
        $this->instanceData = null;

        try {
            $response = $this->uazapi()->withToken($this->token)->post('/instance/connect', [
                'phone' => $this->phone,
            ]);

            if ($response->successful()) {
                $this->instanceData = $response->json();
                $this->success = 'Instância conectada com sucesso!';
            } else {
                $this->error = $this->responseError($response->json(), $response->status());
            }
        } catch (\Throwable) {
            $this->error = 'Não foi possível conectar à integração WhatsApp. Tente novamente mais tarde.';
        } finally {
            $this->loading = false;
        }
    }

    public function disconnect()
    {
        $this->validateOnly('token');
        $this->disconnecting = true;
        $this->clearMessages();

        try {
            $response = $this->uazapi()->withToken($this->token)->post('/instance/disconnect');
            $responseData = $response->json();

            if ($response->successful() && data_get($responseData, 'success', true)) {
                $this->success = 'Instância desconectada com sucesso!';

                if ($this->instanceData) {
                    $this->instanceData['connected'] = false;
                    $this->instanceData['response'] = 'Desconectado';
                }
            } else {
                $this->error = $this->responseError($responseData, $response->status());
            }
        } catch (\Throwable) {
            $this->error = 'Não foi possível desconectar a instância. Tente novamente mais tarde.';
        } finally {
            $this->disconnecting = false;
        }
    }

    public function checkStatus()
    {
        $this->validateOnly('token');
        $this->loading = true;
        $this->error = null;

        try {
            // The provider currently expects the instance token in this path.
            $response = $this->uazapi()->withToken($this->token)->get('/instance/status/'.rawurlencode($this->token));

            if ($response->successful()) {
                $this->instanceData = $response->json();
            } else {
                $this->error = $this->responseError($response->json(), $response->status());
            }
        } catch (\Throwable) {
            $this->error = 'Não foi possível consultar o estado da instância. Tente novamente mais tarde.';
        } finally {
            $this->loading = false;
        }
    }

    public function clearMessages()
    {
        $this->error = null;
        $this->success = null;
    }

    public function render()
    {
        return view('livewire.connect-instance');
    }

    private function uazapi()
    {
        return Http::baseUrl(rtrim((string) config('services.uazapi.base_url'), '/'))
            ->acceptJson()
            ->timeout((int) config('services.uazapi.timeout', 30));
    }

    private function responseError(mixed $response, int $status): string
    {
        return (string) data_get($response, 'error', "A integração WhatsApp devolveu o estado {$status}.");
    }
}
