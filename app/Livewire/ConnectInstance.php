<?php

namespace App\Livewire;

use Livewire\Component;

class ConnectInstance extends Component
{
    public $instanceData = null;
    public $loading = false;
    public $disconnecting = false;
    public $error = null;
    public $success = null;
    public $phone = '258848293580';
    public $token = 'd6ea651f-edeb-4660-88fc-16735e4d4475';

    protected $rules = [
        'phone' => 'required|string|min:9',
        'token' => 'required|string',
    ];

    // Inicializar com status
    public function mount()
    {
        // Opcional: buscar status inicial
        // $this->checkStatus();
    }

    public function connect()
    {
        $this->validate();

        $this->loading = true;
        $this->error = null;
        $this->success = null;
        $this->instanceData = null;

        try {
            $curl = curl_init();

            curl_setopt_array($curl, [
                CURLOPT_URL => "https://free.uazapi.com/instance/connect",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => json_encode([
                    'phone' => $this->phone
                ]),
                CURLOPT_HTTPHEADER => [
                    "Accept: application/json",
                    "Content-Type: application/json",
                    "token: " . $this->token
                ],
            ]);

            $response = curl_exec($curl);
            $err = curl_error($curl);

            curl_close($curl);

            if ($err) {
                $this->error = "Erro cURL: " . $err;
            } else {
                $this->instanceData = json_decode($response, true);

                if (isset($this->instanceData['error'])) {
                    $this->error = $this->instanceData['error'] ?? 'Erro desconhecido na API';
                } else {
                    $this->success = "Instância conectada com sucesso!";
                }
            }
        } catch (\Exception $e) {
            $this->error = "Erro: " . $e->getMessage();
        } finally {
            $this->loading = false;
        }
    }

    public function disconnect()
    {
        $this->disconnecting = true;
        $this->error = null;
        $this->success = null;

        try {
            $curl = curl_init();

            curl_setopt_array($curl, [
                CURLOPT_URL => "https://free.uazapi.com/instance/disconnect",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_HTTPHEADER => [
                    "Accept: application/json",
                    "token: " . $this->token
                ],
            ]);

            $response = curl_exec($curl);
            $err = curl_error($curl);

            curl_close($curl);

            if ($err) {
                $this->error = "Erro cURL: " . $err;
            } else {
                $responseData = json_decode($response, true);

                if (isset($responseData['success']) && $responseData['success']) {
                    $this->success = "Instância desconectada com sucesso!";

                    // Atualizar status local
                    if ($this->instanceData) {
                        $this->instanceData['connected'] = false;
                        $this->instanceData['response'] = 'Desconectado';
                    }
                } else {
                    $this->error = $responseData['error'] ?? 'Erro ao desconectar a instância';
                }
            }
        } catch (\Exception $e) {
            $this->error = "Erro: " . $e->getMessage();
        } finally {
            $this->disconnecting = false;
        }
    }

    public function checkStatus()
    {
        $this->loading = true;
        $this->error = null;

        try {
            $curl = curl_init();

            curl_setopt_array($curl, [
                CURLOPT_URL => "https://free.uazapi.com/instance/status?token=" . $this->token,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "GET",
                CURLOPT_HTTPHEADER => [
                    "Accept: application/json",
                ],
            ]);

            $response = curl_exec($curl);
            $err = curl_error($curl);

            curl_close($curl);

            if ($err) {
                $this->error = "Erro cURL: " . $err;
            } else {
                $statusData = json_decode($response, true);
                if (isset($statusData['error'])) {
                    $this->error = $statusData['error'];
                } else {
                    $this->instanceData = $statusData;
                }
            }
        } catch (\Exception $e) {
            $this->error = "Erro: " . $e->getMessage();
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
}
