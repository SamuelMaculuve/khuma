<?php

namespace App\Jobs;

use App\Models\Companies;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class DispatchTenantInboundFetches implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function handle(): void
    {
        Companies::query()
            ->where('mail_provision_status', 'ready')
            ->whereNotNull('mail_inbox_password')
            ->pluck('id')
            ->each(fn ($id) => FetchTenantInboundMail::dispatch((int) $id));
    }
}
