<?php

use App\Jobs\DispatchTenantInboundFetches;
use App\Jobs\DispatchScheduledEmailCampaigns;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Schedule::job(new DispatchTenantInboundFetches())
    ->everyMinute()
    ->withoutOverlapping()
    ->name('tenant-inbound-fetches');

Schedule::job(new DispatchScheduledEmailCampaigns())
    ->everyMinute()
    ->withoutOverlapping()
    ->name('scheduled-email-campaigns');
