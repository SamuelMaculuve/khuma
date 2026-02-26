<?php

namespace App\Jobs;

use App\Models\Subscription;
use App\Notifications\RenewalReminder;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendRenewalReminders implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Envia lembretes de renovação para subscrições que expiram em:
     *  - 7 dias
     *  - 3 dias
     *  - 1 dia
     */
    public function handle(): void
    {
        $reminderDays = [7, 3, 1];

        foreach ($reminderDays as $days) {
            $subscriptions = Subscription::query()
                ->with(['user', 'plan'])
                ->where('status', 'active')
                ->whereDate('end_date', now()->addDays($days)->toDateString())
                ->get();

            foreach ($subscriptions as $subscription) {
                try {
                    $subscription->user->notify(new RenewalReminder($subscription, $days));
                    Log::info("Renewal reminder sent to user {$subscription->user_id} - {$days} day(s) left");
                } catch (\Throwable $e) {
                    Log::error("Failed to send renewal reminder for subscription {$subscription->id}: {$e->getMessage()}");
                }
            }
        }
    }
}
