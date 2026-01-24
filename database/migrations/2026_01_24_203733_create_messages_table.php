<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->string('message_id');
            $table->foreignId('lead_id')->constrained()->onDelete('cascade');
            $table->foreignId('sender_id')->constrained('users')->onDelete('cascade');
            $table->enum('channel', ['sms', 'whatsapp', 'email', 'phone', 'in_person']);
            $table->enum('direction', ['inbound', 'outbound']);
            $table->text('content');
            $table->json('metadata')->nullable(); // Para armazenar dados específicos do canal
            $table->timestamp('read_at')->nullable();
            $table->index('lead_id');
            $table->index(['lead_id', 'channel']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
