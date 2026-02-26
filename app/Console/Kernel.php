<?php

namespace App\Console;

use App\Jobs\ExpireSubscriptions;
use App\Jobs\SendRenewalReminders;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    protected function schedule(Schedule $schedule): void
    {
        // Expira subscrições vencidas — corre todos os dias à meia-noite
        $schedule->job(new ExpireSubscriptions)->dailyAt('00:00');

        // Envia lembretes de renovação — corre todos os dias às 09:00
        $schedule->job(new SendRenewalReminders)->dailyAt('09:00');
    }

    protected function commands(): void
    {
        $this->load(__DIR__ . '/Commands');
        require base_path('routes/console.php');
    }
}
