<?php

namespace App\Livewire;

use App\Models\Instance;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class WhatsAppConnection extends Component
{
    public $country_code = '+258';
    public $phone = '';
    public $currentInstance = null;
    public $baseUrl = '';
    public $isEvolution = true;

    public $connected = false;
    public $loggedIn = false;
    public $qrcode = null;
    public $instanceData = null;

    public $prompt = '';

    public $statusData = null;
    public $loading = false;
    public $error = null;

    public $manualQrcode = null;

    public function mount()
    {
        $this->currentInstance = Auth::user()->instance;
        $this->baseUrl = config('app.use_uazapi') ? 'https://free.uazapi.com' : config('app.evolution_api_url');
        $this->isEvolution = config('app.use_evolution');
        $this->checkStatus();
    }

    public function connect()
    {
        $this->validate([
            'country_code' => 'required',
            'phone' => 'required|numeric|min:7',
        ]);

        $fullPhone = str_replace('+', '', $this->country_code) . $this->phone;

        $this->loading = true;
        $this->error = null;
        $this->manualQrcode = null; // Resetamos o QR code manual

        try {

            if ($this->isEvolution) {
                $url = $this->baseUrl . '/instance/create';
                $token = config('app.evolution_api_key');
                Log::info('Connecting to Evolution API', ['url' => $url, 'phone' => $fullPhone, 'token' => $token]);
                $user = Auth::user();
                $instance_name = "Instance-{$user->id}-" . Str::random(5);

                $response = Http::withHeaders([
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    "apikey" => $token,
                ])->post($url, [
                    'instanceName' => $instance_name,
                    'integration' => 'WHATSAPP-BAILEYS',
                    'qrcode' => true,
                    'webhook' => [
                        'url' => 'https://workflow.mazedeve.com/webhook-test/dabb4939-e474-4296-b301-74d62b8462fc',
                        'byEvents' => true,
                        'base64' => true,
                        'events' => [
                            'MESSAGES_UPSERT',
                            'MESSAGE_DELETED',
                            'SEND_MESSAGE',
                            'PRESENCE_UPDATE',
                            'MESSAGES_UPDATE',
                        ]
                    ]
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    Log::info('Instance Created', ['response' => $data]);
                    $instance = new Instance();
                    $instance->name = $instance_name ?? null;
                    $instance->status = $data['instance']['status'] ?? null;
                    $instance->token = $data['instance']['instanceId'] ?? null;
                    $instance->user_id = Auth::id();
                    $instance->save();

                    $this->instanceData = $data['instance'] ?? null;
                    $this->currentInstance = $instance;
                    $this->manualQrcode = $data['qrcode']['base64'] ?? null; // Guardamos o QR code manual
                    $this->qrcode = $this->manualQrcode;

                    // $this->connected = $data['connected'] ?? false;
                    // $this->loggedIn = $data['loggedIn'] ?? false;


                } else {
                    Log::error('Erro ao conectar: ' . $response->body());
                    $this->error = 'Erro ao conectar: ' . $response->body();
                }

                // curl_setopt_array($curl, [
                //     CURLOPT_URL => $url,
                //     CURLOPT_RETURNTRANSFER => true,
                //     CURLOPT_ENCODING => "",
                //     CURLOPT_MAXREDIRS => 10,
                //     CURLOPT_TIMEOUT => 30,
                //     CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                //     CURLOPT_CUSTOMREQUEST => "POST",
                //     CURLOPT_POSTFIELDS => json_encode([
                //         'instanceName' => $instance_name,
                //         'integration' => 'WHATSAPP-BAILEYS',
                //         'qrcode' => true,
                //         'webhook' => [
                //             'url' => 'https://workflow.mazedeve.com/webhook-test/dabb4939-e474-4296-b301-74d62b8462fc',
                //             'byEvents' => true,
                //             'base64' => true,
                //             'events' => [
                //                 'MESSAGES_UPSERT',
                //                 'MESSAGE_DELETED',
                //                 'SEND_MESSAGE',
                //                 'PRESENCE_UPDATE',
                //                 'MESSAGES_UPDATE',
                //             ]
                //         ]
                //     ]),
                //     CURLOPT_HTTPHEADER => [
                //         "Content-Type: application/json",
                //         "apikey: $token"
                //     ],
                // ]);

            } else {

                $response = Http::withHeaders([
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    'token' => $this->currentInstance->token,
                ])->post($this->baseUrl . '/instance/connect', [
                    'phone' => $fullPhone
                ]);

                if ($response->successful()) {
                    $data = $response->json();
                    $this->instanceData = $data['instance'] ?? null;
                    $this->connected = $data['connected'] ?? false;
                    $this->loggedIn = $data['loggedIn'] ?? false;
                    $this->manualQrcode = $data['instance']['qrcode'] ?? null; // Guardamos o QR code manual
                    $this->qrcode = $this->manualQrcode; // Exibimos o QR code manual
                    $this->enableWebhook();
                } else {
                    $this->error = 'Erro ao conectar: ' . $response->body();
                }
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
                'token' => $this->currentInstance->token,
            ])->post($this->baseUrl . '/instance/disconnect');

            if ($response->successful()) {
                $data = $response->json();
                $this->cleanVariables($data);
            } else {
                $this->error = 'Erro ao desconectar: ' . $response->body();
            }
        } catch (\Exception $e) {
            $this->error = 'Erro: ' . $e->getMessage();
        }

        $this->loading = false;
    }

    public function deleteInstance()
    {
        $this->loading = true;
        $this->error = null;

        try {

            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'token' => $this->currentInstance->token,
            ])->delete($this->baseUrl . '/instance');

            $this->currentInstance->delete();
            $this->cleanVariables();
            $this->currentInstance = null;

            Log::info('ERROR DELETE INSTANCE:', ['response' => $response]);
        } catch (\Exception $e) {
            $this->error = 'Erro: ' . $e->getMessage();
        }

        $this->loading = false;
    }

    public function checkStatus()
    {
        try {
            if ($this->isEvolution) {
                $instance_name = $this->currentInstance->name;
                $url = $this->baseUrl . "/instance/connectionState/$instance_name";
                $token = config('app.evolution_api_key');
                $response = Http::withHeaders([
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    "apikey" => $token,
                ])->get($url, []);

                if ($response->successful()) {
                    $data = $response->json();
                    // Log::info('Instance Status', ['response' => $data]);

                    $this->connected = $data['instance']['state'] ?? 'connecting';
                    // $this->loggedIn = $data['loggedIn'] ?? false;


                } else {
                    Log::error('Erro ao conectar: ' . $response->body());
                    $this->error = 'Erro ao conectar: ' . $response->body();
                }

                return;
            } else {
                $response = Http::withHeaders([
                    'Accept' => 'application/json',
                    'token' => $this->currentInstance->token,
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

                        $this->currentInstance->profilePic = $data['instance']['profilePicUrl'] ?? null;
                        $this->currentInstance->status = $data['instance']['status'];
                        $this->currentInstance->isBusiness = $data['instance']['isBusiness'];
                        $this->currentInstance->profileName = $data['instance']['profileName'];
                        $this->currentInstance->update();
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
            }
        } catch (\Exception $e) {
            Log::error('Erro ao verificar status: ' . $e->getMessage());
        }
    }

    public function cleanVariables($data = null)
    {
        $this->instanceData = $data['instance'] ?? null;
        $this->connected = false;
        $this->loggedIn = false;
        $this->qrcode = null;
        $this->manualQrcode = null; // Limpa o QR code manual
    }

    public function render()
    {
        return view('livewire.whats-app-connection');
    }

    public function enableWebhook()
    {
        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            'token' => $this->currentInstance->token,
        ])->post($this->baseUrl . '/webhook', [
            "enabled" => true,
            'url' => "https://workflow.mazedeve.com/webhook/50984dd5-358c-473f-a9fe-98d682878db8",
            "events" => [
                "messages"
            ]
        ]);

        if ($response->successful()) {
            Log::info("Webhook Created", ['response' => $response->json()]);
        } else {
            Log::error("ERROR Creating Webhook", ['response' => $response->body()]);
        }
    }

    public function generateQrCode($name)
    {
        $url = $this->baseUrl . "/instance/connect/$name";
        $token = config('app.evolution_api_key');
        $response = Http::withHeaders([
            'Accept' => 'application/json',
            'Content-Type' => 'application/json',
            "apikey" => $token,
        ])->get($url, []);

        if ($response->successful()) {
            $data = $response->json();
            Log::info('Instance QR code', ['response' => $data]);
            $this->manualQrcode = $data['base64'] ?? null; // Guardamos o QR code manual
            $this->qrcode = $this->manualQrcode;
        } else {
            $this->error = 'Erro ao gerar qr code: ' . $response->body();
        }
    }

    public function newqrcode()
    {
        $this->generateQrCode($this->currentInstance->name);
    }
}
