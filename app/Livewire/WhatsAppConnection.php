<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppConnection extends Component
{
    public $phone = '258848293580';
    public $token = 'ca750e72-935e-4723-923a-5fccd3742b39';
    public $baseUrl = 'https://free.uazapi.com';

    public $connected = false;
    public $loggedIn = false;
    public $qrcode = null;
    public $instanceData = null;
    public $statusData = null;
    public $loading = false;
    public $error = null;

    public $manualQrcode = null;

    public function mount()
    {
        $this->checkStatus();
    }

    public function connect()
    {
        $this->loading = true;
        $this->error = null;
        $this->manualQrcode = null; // Resetamos o QR code manual

        try {
            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'token' => $this->token,
            ])->post($this->baseUrl . '/instance/connect', [
                'phone' => $this->phone
            ]);

            if ($response->successful()) {
                $data = $response->json();
                $this->instanceData = $data['instance'] ?? null;
                $this->connected = $data['connected'] ?? false;
                $this->loggedIn = $data['loggedIn'] ?? false;
                $this->manualQrcode = $data['instance']['qrcode'] ?? null; // Guardamos o QR code manual
                $this->qrcode = $this->manualQrcode; // Exibimos o QR code manual
            } else {
                $this->error = 'Erro ao conectar: ' . $response->body();
            }
        } catch (\Exception $e) {
            $this->error = 'Erro: ' . $e->getMessage();
        }

        $this->loading = false;
    }

    public function disconnect()
    {
        $this->loading = true;
        $this->error = null;

        try {
            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'token' => $this->token,
            ])->post($this->baseUrl . '/instance/disconnect');

            if ($response->successful()) {
                $data = $response->json();
                $this->instanceData = $data['instance'] ?? null;
                $this->connected = false;
                $this->loggedIn = false;
                $this->qrcode = null;
                $this->manualQrcode = null; // Limpa o QR code manual
            } else {
                $this->error = 'Erro ao desconectar: ' . $response->body();
            }
        } catch (\Exception $e) {
            $this->error = 'Erro: ' . $e->getMessage();
        }

        $this->loading = false;
    }

    public function checkStatus()
    {
        try {
            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'token' => $this->token,
            ])->get($this->baseUrl . '/instance/status');

            if ($response->successful()) {
                $data = $response->json();
                $this->statusData = $data;

                $this->connected = $data['status']['connected'] ?? false;
                $this->loggedIn = $data['status']['loggedIn'] ?? false;
                $this->instanceData = $data['instance'] ?? null;

                if ($this->connected && $this->loggedIn) {
                    $this->manualQrcode = null;
                    $this->qrcode = null;
                } else {
                    // Se temos um QR code manual
                    if ($this->manualQrcode) {
                        // Verifica se a API retornou um QR code diferente do manual
                        $apiQrcode = $data['instance']['qrcode'] ?? null;
                        if ($apiQrcode && $apiQrcode !== $this->manualQrcode) {
                            // O QR code manual expirou, atualizamos com o novo da API
                            $this->manualQrcode = $apiQrcode;
                            $this->qrcode = $apiQrcode;
                        } else {
                            // Mantemos o QR code manual
                            $this->qrcode = $this->manualQrcode;
                        }
                    } else {
                        // Não temos QR code manual, usamos o da API
                        $this->qrcode = $data['instance']['qrcode'] ?? null;
                    }
                }
            }
        } catch (\Exception $e) {
            Log::error('Erro ao verificar status: ' . $e->getMessage());
        }
    }



    public function render()
    {
        return view('livewire.whats-app-connection');
    }
}
