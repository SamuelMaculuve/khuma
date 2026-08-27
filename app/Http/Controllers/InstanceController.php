<?php

namespace App\Http\Controllers;

use App\Models\Instance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class InstanceController extends Controller
{
    public function index()
    {
        //
    }

    public function create()
    {
        return view('admin.instance.create');
    }

    public function connectShow()
    {
        return view('admin.instance.connect.show');
    }

    public function connect(Request $request)
    {
        $request->validate([
            'phone' => ['required', 'string', 'min:9'],
        ]);

        $token = $this->adminToken();

        if ($token === null) {
            return back()->with('error', 'A integração WhatsApp não está configurada. Contacte o administrador.');
        }

        try {
            $response = $this->uazapi()->withToken($token)->post('/instance/connect', [
                'phone' => $request->string('phone')->toString(),
            ]);

            if ($response->successful()) {
                return back()->with('success', 'Pedido de conexão enviado com sucesso.');
            }

            Log::warning('Uazapi connection request failed.', [
                'status' => $response->status(),
                'user_id' => $request->user()?->id,
            ]);
        } catch (\Throwable $exception) {
            Log::warning('Uazapi connection request could not be completed.', [
                'user_id' => $request->user()?->id,
                'exception' => $exception->getMessage(),
            ]);
        }

        return back()->with('error', 'Não foi possível iniciar a conexão WhatsApp. Tente novamente mais tarde.');
    }

    public function store(Request $request)
    {
        $user = $request->user();
        $company = $user?->company;

        if ($company === null) {
            return back()->with('error', 'Não foi possível identificar a empresa da conta.');
        }

        $limit = $user->featureLimit('whatsapp_instances', null);

        if ($limit !== null && $limit !== 'unlimited') {
            $currentInstances = Instance::whereHas('user', fn ($query) => $query->where('company_id', $user->company_id))->count();

            if ($currentInstances >= (int) $limit) {
                return redirect()
                    ->route('subscription.plans')
                    ->with('warning', 'O limite de instâncias WhatsApp do seu plano foi atingido.');
            }
        }

        $token = $this->adminToken();

        if ($token === null) {
            return back()->with('error', 'A integração WhatsApp não está configurada. Contacte o administrador.');
        }

        try {
            $response = $this->uazapi()->withToken($token)->post('/instance/init', [
                'name' => 'khuma-'.$company->name,
                'systemName' => 'khuma',
                'fingerprintProfile' => 'chrome',
                'browser' => 'chrome',
            ]);

            if (! $response->successful()) {
                Log::warning('Uazapi instance initialisation failed.', [
                    'status' => $response->status(),
                    'user_id' => $user->id,
                ]);

                return back()->with('error', 'Não foi possível criar a instância WhatsApp.');
            }

            $data = $response->json();
            $instanceData = data_get($data, 'instance', []);
            $instanceToken = data_get($data, 'instance.token') ?? data_get($data, 'token');

            if (blank($instanceToken)) {
                Log::warning('Uazapi instance initialisation returned no instance token.', [
                    'user_id' => $user->id,
                ]);

                return back()->with('error', 'A instância foi recusada pela integração. Tente novamente mais tarde.');
            }

            $instance = new Instance();
            $instance->user_id = $user->id;
            $instance->token = $instanceToken;
            $instance->status = data_get($instanceData, 'status');
            $instance->profilePic = data_get($instanceData, 'profilePicUrl');
            $instance->isBusiness = data_get($instanceData, 'isBusiness');
            $instance->profileName = data_get($instanceData, 'profileName');
            $instance->name = data_get($instanceData, 'name');
            $instance->info = data_get($data, 'info');
            $instance->save();

            return back()->with('success', 'Instância WhatsApp criada com sucesso.');
        } catch (\Throwable $exception) {
            Log::warning('Uazapi instance initialisation could not be completed.', [
                'user_id' => $user->id,
                'exception' => $exception->getMessage(),
            ]);

            return back()->with('error', 'Não foi possível criar a instância WhatsApp. Tente novamente mais tarde.');
        }
    }

    public function show(Instance $instance)
    {
        //
    }

    public function edit(Instance $instance)
    {
        //
    }

    public function update(Request $request, Instance $instance)
    {
        //
    }

    public function destroy(Instance $instance)
    {
        //
    }

    private function uazapi()
    {
        return Http::baseUrl(rtrim((string) config('services.uazapi.base_url'), '/'))
            ->acceptJson()
            ->timeout((int) config('services.uazapi.timeout', 30));
    }

    private function adminToken(): ?string
    {
        $token = config('services.uazapi.admin_token');

        return filled($token) ? (string) $token : null;
    }
}
