<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Clients;
use App\Models\Instance;
use App\Models\Leads;
use App\Models\Messages;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class MessagesController extends Controller
{
    /**
     * Inbound webhook from n8n — uazapi message received.
     * POST /api/save-message
     * Body: { from, messageId, message, instance_token }
     */
    public function saveMessage(Request $request)
    {
        $data = $request->validate([
            'from'           => 'required|string',
            'messageId'      => 'required|string',
            'message'        => 'required|string',
            'instance_token' => 'required|string',
        ]);

        $phone = preg_replace('/[^0-9]/', '', str_replace('@s.whatsapp.net', '', $data['from']));

        $instance = Instance::with('user')->where('token', $data['instance_token'])->first();

        if (! $instance || ! $instance->user) {
            return response()->json(['error' => 'Instance not found'], 404);
        }

        $companyId = $instance->user->company_id;

        $client = Clients::where('company_id', $companyId)
            ->where('phone', 'like', "%{$phone}%")
            ->first();

        if (! $client) {
            $client = Clients::create([
                'company_id' => $companyId,
                'name'       => 'WhatsApp ' . substr($phone, -4),
                'phone'      => $phone,
            ]);
        }

        $lead = Leads::where('company_id', $companyId)
            ->where('client_id', $client->id)
            ->whereNotIn('status', ['won', 'lost'])
            ->latest()
            ->first();

        if (! $lead) {
            $lead = Leads::create([
                'company_id'  => $companyId,
                'client_id'   => $client->id,
                'reference'   => 'WA-' . $companyId . '-' . strtoupper(Str::random(8)),
                'title'       => 'Contacto WhatsApp',
                'description' => 'Lead criada automaticamente via WhatsApp.',
                'status'      => 'new',
                'source'      => 'whatsapp',
            ]);
        }

        if (Messages::where('message_id', $data['messageId'])->exists()) {
            return response()->json(['status' => 'duplicate']);
        }

        Messages::create([
            'message_id' => $data['messageId'],
            'message_to' => $phone,
            'lead_id'    => $lead->id,
            'client_id'  => $client->id,
            'sender_id'  => null,
            'channel'    => 'whatsapp',
            'direction'  => 'inbound',
            'content'    => $data['message'],
        ]);

        return response()->json(['status' => 'ok', 'lead_id' => $lead->id]);
    }
}
