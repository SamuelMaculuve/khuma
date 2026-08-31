<?php

use App\Models\Subscription;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->foreignId('company_id')
                ->nullable()
                ->after('user_id')
                ->constrained('companies')
                ->nullOnDelete();

            $table->index(['company_id', 'status']);
        });

        Subscription::query()
            ->whereNull('company_id')
            ->with('user:id,company_id')
            ->each(function (Subscription $subscription) {
                if ($subscription->user?->company_id) {
                    $subscription->forceFill([
                        'company_id' => $subscription->user->company_id,
                    ])->save();
                }
            });
    }

    public function down(): void
    {
        Schema::table('subscriptions', function (Blueprint $table) {
            $table->dropIndex(['company_id', 'status']);
            $table->dropConstrainedForeignId('company_id');
        });
    }
};
