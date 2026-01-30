<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Instance;
use App\Models\Leads;
use App\Models\Messages;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

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

        // 3. Encontrar lead pelo telefone do cliente
//        $lead = Leads::whereHas('client', function ($q) use ($phone) {
//            $q->where('phone', 'like', "%{$phone}%");
//        })->first();

//        $instance = Instance::where('token', $data['instance_token'])->get();

        Log::info("instance instance_token",['instance instance_token'=> $data]);

        $instance = Instance::where('token', $data['instance_token'])->first();

        Log::info("instance Mensagem recebida",['instance Recevida'=> $instance]);

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

        Log::info("Mensagem lastMessage",['Recevida lastMessage'=> $lastMessage]);

        // Criar nova mensagem no banco de dados
        $createdMessages = Messages::create([
            'message_id' => $data['messageId'] ?? '',
            'message_to' => $phone,
            'lead_id' => $lastMessage->lead_id,
            'sender_id' => $lastMessage->sender_id,
            'channel' => 'whatsapp',
            'direction' => 'inbound',
            'content' => $data['message'] ?? '',
        ]);

        Log::info("Mensagem createdMessages",['Recevida createdMessages'=> $createdMessages]);
        //php artisan make:migration message_to --table=messages
        // 1. gravar from
        // pegar o token
        // pegar ultima mensagem do numero X com token Y

//        tenho token e $phone, retonar a ultima mensagem tenho em conta essas 2 variaveis
//
//        Instance(user_id,name,systemName)
//        Messages(message_id,lead_id,sender_id (tem relacao com com Instance))
    }

    /**
     * Display the specified resource.
     */
    public function show(Messages $messages)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Messages $messages)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Messages $messages)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Messages $messages)
    {
        //
    }
}
