<?php

namespace App\Http\Controllers\Webhook;

use App\Http\Controllers\Controller;
use App\Models\Instance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class EvoltionApiController extends Controller
{
    public function store(Request $request)
    {
        Log::info('Evoltion API Webhook received', ['payload' => $request->all()]);



    }

    public function handle(Request $request)
    {
        $payload = $request->all();
        if($payload['event'] != 'messages.upsert'){
            Log::info('Ignoring non-message event', ['event' => $payload['event']]);
            return response()->json(['status' => 'ignored', 'reason' => 'Not a messages.upsert event']);
        }
        Log::info('Webhook received', $payload);

        // 1. Get instance name from payload
        $instanceName = $payload['instance'] ?? null;



        if (!$instanceName) {
            return response()->json(['error' => 'Instance not found in payload'], 400);
        }

        // 2. Fetch instance from database by name
        $instance = Instance::where('name', $instanceName)->first();

        if (!$instance) {
            Log::error("Instance {$instanceName} not found in database");
            return response()->json(['error' => "Instance {$instanceName} not found in database"], 404);
        }

        // 4. Send to AI API with the instance prompt
        try {
            $response = Http::withHeaders([
                'Accept'       => 'application/json',
                'Content-Type' => 'application/json',
            ])->post(config('app.n8n_webhook_endpoint'), [
                'prompt'  => $instance->prompt,
                'payload' => $payload,
            ]);

            if ($response->successful()) {
                Log::info('AI API response', $response->json());
                return response()->json(['status' => 'ok', 'ai_response' => $response->json()]);
            } else {
                Log::error('AI API error', ['body' => $response->body()]);
                return response()->json(['error' => 'AI API failed'], 500);
            }

        } catch (\Exception $e) {
            Log::error('Webhook error: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
