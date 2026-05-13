<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('email_campaigns', function (Blueprint $table) {
            $table->string('preheader')->nullable()->after('subject');
            $table->string('sender_name')->nullable()->after('preheader');
            $table->string('sender_email')->nullable()->after('sender_name');
            $table->string('reply_to_email')->nullable()->after('sender_email');
            $table->string('timezone')->default(config('app.timezone', 'UTC'))->after('scheduled_at');
            $table->string('reply_action')->default('create_lead_if_none')->after('timezone');
        });

        Schema::table('email_campaign_logs', function (Blueprint $table) {
            $table->string('tracking_token')->nullable()->unique()->after('email_address');
            $table->string('message_id')->nullable()->unique()->after('tracking_token');
            $table->timestamp('opened_at')->nullable()->after('sent_at');
            $table->timestamp('clicked_at')->nullable()->after('opened_at');
            $table->timestamp('replied_at')->nullable()->after('clicked_at');
            $table->unsignedInteger('open_count')->default(0)->after('replied_at');
            $table->unsignedInteger('click_count')->default(0)->after('open_count');
            $table->unsignedInteger('reply_count')->default(0)->after('click_count');
            $table->string('last_clicked_url')->nullable()->after('reply_count');
            $table->foreignId('inbound_email_id')->nullable()->after('last_clicked_url')->constrained('inbound_emails')->nullOnDelete();
            $table->foreignId('lead_id')->nullable()->after('inbound_email_id')->constrained('leads')->nullOnDelete();
            $table->json('metadata')->nullable()->after('lead_id');

            $table->index(['email_campaign_id', 'opened_at']);
            $table->index(['email_campaign_id', 'clicked_at']);
            $table->index(['email_campaign_id', 'replied_at']);
        });
    }

    public function down(): void
    {
        Schema::table('email_campaign_logs', function (Blueprint $table) {
            $table->dropForeign(['inbound_email_id']);
            $table->dropForeign(['lead_id']);
            $table->dropIndex(['email_campaign_id', 'opened_at']);
            $table->dropIndex(['email_campaign_id', 'clicked_at']);
            $table->dropIndex(['email_campaign_id', 'replied_at']);
            $table->dropUnique(['tracking_token']);
            $table->dropUnique(['message_id']);
            $table->dropColumn([
                'tracking_token',
                'message_id',
                'opened_at',
                'clicked_at',
                'replied_at',
                'open_count',
                'click_count',
                'reply_count',
                'last_clicked_url',
                'inbound_email_id',
                'lead_id',
                'metadata',
            ]);
        });

        Schema::table('email_campaigns', function (Blueprint $table) {
            $table->dropColumn([
                'preheader',
                'sender_name',
                'sender_email',
                'reply_to_email',
                'timezone',
                'reply_action',
            ]);
        });
    }
};
