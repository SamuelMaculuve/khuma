<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->string('mail_subdomain')->nullable()->unique()->after('email');
            $table->string('mail_inbox_local_part')->nullable()->after('mail_subdomain');
            $table->text('mail_inbox_password')->nullable()->after('mail_inbox_local_part');
            $table->text('mail_dkim_public_key')->nullable()->after('mail_inbox_password');
            $table->string('mail_dkim_selector')->nullable()->after('mail_dkim_public_key');
            $table->json('mail_cloudflare_records')->nullable()->after('mail_dkim_selector');
            $table->enum('mail_provision_status', ['pending', 'provisioning', 'ready', 'failed'])
                ->default('pending')
                ->after('mail_cloudflare_records');
            $table->text('mail_provision_error')->nullable()->after('mail_provision_status');
            $table->timestamp('mail_provisioned_at')->nullable()->after('mail_provision_error');
            $table->timestamp('mail_last_inbound_fetch_at')->nullable()->after('mail_provisioned_at');
        });
    }

    public function down(): void
    {
        Schema::table('companies', function (Blueprint $table) {
            $table->dropColumn([
                'mail_subdomain',
                'mail_inbox_local_part',
                'mail_inbox_password',
                'mail_dkim_public_key',
                'mail_dkim_selector',
                'mail_cloudflare_records',
                'mail_provision_status',
                'mail_provision_error',
                'mail_provisioned_at',
                'mail_last_inbound_fetch_at',
            ]);
        });
    }
};
