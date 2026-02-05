<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Instance;
use App\Models\Leads;
use App\Models\Messages;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Companies;
use App\Models\Clients;
use Illuminate\Support\Str;
class MessagesController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function saveMessage(Request $request)
    {
        // 1. Validar payload
        $data = $request->validate([
            'from' => 'required|string',
            'messageId' => 'required|string',
            'message' => 'required|string',
            'instance_token' => 'nullable|string',
        ]);

        // 2. Normalizar número (remover @s.whatsapp.net)
        $phone = str_replace('@s.whatsapp.net', '', $data['from']);

        $instance = Instance::where('token', $data['instance_token'])->first();

        if (! $instance) {
            return null; // instance inválida
        }

        $phoneNew = preg_replace('/[^0-9]/', '', $phone); // normalizar

        $lastMessage = Messages::where('sender_id', $instance->user_id)
            ->whereHas('lead.client', function ($q) use ($phoneNew) {
                $q->where('message_to', 'like', "%{$phoneNew}%");
            })
            ->orderBy('created_at', 'desc')
            ->first();

        if ($lastMessage){
            // Criar nova mensagem no banco de dados
           Messages::create([
                'message_id' => $data['messageId'] ?? '',
                'message_to' => $phone,
                'lead_id' => $lastMessage->lead_id,
                'client_id' => $lastMessage->lead->client->id,
                'sender_id' => $lastMessage->sender_id,
                'channel' => 'whatsapp',
                'direction' => 'inbound',
                'content' => $data['message'] ?? '',
            ]);
        }

        $this->saveMessageWithAutoClientLead($data,$phone,$data['instance_token']);

    }

    public function saveMessageWithAutoClientLead(array $data, string $phone, string $token)
    {
        // 1. Normalizar telefone
        $phone = preg_replace('/[^0-9]/', '', $phone);

        // 2. Encontrar instance pelo token
        $instance = Instance::where('token', $token)->firstOrFail();

        // 3. Encontrar company associada à instance
        $company = Companies::findOrFail($instance->user->company_id);

        // 4. Verificar se client já existe
        $client = Clients::where('phone', 'like', "%{$phone}%")->where('company_id', $instance->user->company_id)->first();


        if (!$client) {
            $client = Clients::create([
                'company_id' => $instance->user->company_id,
                'name' => 'Cliente WhatsApp ' . substr($phone, -4),
                'phone' => $phone,
                'email' => null,
                'address' => null,
                'additional_info' => json_encode(`{'info': 'Criado automaticamente via WhatsApp'}`),
            ]);
            Log::info("Mensagem lastMessage Lead",['msg' =>'Criado automaticamente via WhatsApp']);
        }

        // 5. Criar lead (ou reutilizar aberto)
        $lead = Leads::where('client_id', $client->id)
            ->whereIn('status', ['new', 'contacted', 'qualified', 'proposal', 'negotiation'])
            ->first();

        Log::info("saveMessageWithAutoClientLead::::::",['msg' =>'===========saveMessage[][][][][][]WithAutoClientLead============='.$lead]);

        if (!$lead) {
            $lead = Leads::create([
                'client_id' => $client->id,
                'reference' => 'LEAD-' . strtoupper(Str::random(8)),
                'title' => 'Contacto via WhatsApp',
                'description' => 'Lead criada automaticamente a partir de mensagem WhatsApp',
                'status' => 'new',
                'value' => 0,
                'source' => 'whatsapp',
            ]);
            Log::info("Mensagem lastMessage Lead",['msg' =>'Lead criada automaticamente a partir de mensagem WhatsApp']);
        }

        // 6. Criar mensagem
        $createdMessage = Messages::create([
            'message_id' => $data['messageId'] ?? Str::uuid(),
            'message_to' => $phone,
            'lead_id' => $lead->id,
            'client_id' => $client->id,
            'sender_id' => $instance->user->id, // relação com Instance
            'channel' => 'whatsapp',
            'direction' => 'inbound',
            'content' => $data['message'] ?? '',
        ]);
//        Log::info("Mensagem lastMessage Lead",['msg' =>'keep trying'.$createdMessage]);
        Log::info("Mensagem lastMessage Lead",['msg' =>'keep trying------'.$data['messageId']]);

        return $createdMessage;
    }
//
//    /**
//     * Display the specified resource.
//     */
//    public function show(Messages $messages)
//    {
//        //
//    }
//
//    /**
//     * Show the form for editing the specified resource.
//     */
//    public function edit(Messages $messages)
//    {
//        //
//    }
//
//    /**
//     * Update the specified resource in storage.
//     */
//    public function update(Request $request, Messages $messages)
//    {
//        //
//    }
//
//    /**
//     * Remove the specified resource from storage.
//     */
//    public function destroy(Messages $messages)
//    {
//        //
//    }
}
