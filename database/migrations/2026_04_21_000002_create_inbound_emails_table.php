<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('inbound_emails', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->constrained()->cascadeOnDelete();
            $table->string('alias')->index();
            $table->string('to_address');
            $table->string('from_address')->index();
            $table->string('from_name')->nullable();
            $table->string('subject')->nullable();
            $table->string('message_id')->nullable()->unique();
            $table->string('in_reply_to')->nullable()->index();
            $table->longText('body_text')->nullable();
            $table->longText('body_html')->nullable();
            $table->json('headers')->nullable();
            $table->enum('status', ['received', 'processed', 'failed'])->default('received');
            $table->text('error_message')->nullable();
            $table->timestamp('received_at');
            $table->timestamps();

            $table->index(['company_id', 'alias']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('inbound_emails');
    }
};
