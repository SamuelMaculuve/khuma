<?php
namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\CallLog;
use App\Models\PhoneCall;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rule;

class PhoneCallController extends Controller
{

    public function store(Request $request, $id)
    {
        Log::info($request->all());

        $validated = $request->validate([
            'device_id' => 'required|string',
            'calls' => 'required|array',
            'calls.*.number' => 'nullable|string',
            'calls.*.type' => 'required|string|in:INCOMING,OUTGOING,MISSED',
            'calls.*.started_at' => 'nullable|date',
            'calls.*.duration_seconds' => 'nullable|integer',
            'calls.*.raw' => 'nullable|array',
        ]);

        $inserted = [];

        foreach ($validated['calls'] as $call) {
            $inserted[] = CallLog::create([
                'user_id' => $id,
                'device_id' => $validated['device_id'],
                'number' => $call['number'] ?? null,
                'type' => $call['type'],
                'started_at' => $call['started_at'] ?? null,
                'duration_seconds' => $call['duration_seconds'] ?? 0,
                'raw' => $call['raw'] ?? [],
            ]);
        }

        return response()->json([
            'status' => 'success',
            'inserted' => count($inserted),
        ]);
    }

    public function index(Request $request){

        return "yes";
    }

}
