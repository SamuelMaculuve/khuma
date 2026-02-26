<?php

namespace App\Jobs;

use App\Models\Subscription;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class ExpireSubscriptions implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        $expired = Subscription::query()
            ->where('status', 'active')
            ->where('end_date', '<', now())
            ->get();

        foreach ($expired as $subscription) {
            $subscription->update(['status' => 'expired']);
            Log::info("Subscription {$subscription->id} for user {$subscription->user_id} marked as expired.");
        }

        Log::info("ExpireSubscriptions: {$expired->count()} subscription(s) expired.");
    }
}
