<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Cria a tabela subscriptions completa.
     * Se já existe, verifica colunas em falta.
     */
    public function up(): void
    {
        if (!Schema::hasTable('subscriptions')) {
            Schema::create('subscriptions', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->foreignId('plan_id')->constrained()->cascadeOnDelete();
                $table->decimal('price', 10, 2);
                $table->enum('status', ['pending', 'active', 'expired', 'failed', 'cancelled'])
                    ->default('pending');
                $table->timestamp('start_date')->nullable();
                $table->timestamp('end_date')->nullable();
                $table->timestamps();
            });
        } else {
            // Adiciona colunas se não existirem (caso a tabela já exista)
            Schema::table('subscriptions', function (Blueprint $table) {
                if (!Schema::hasColumn('subscriptions', 'price')) {
                    $table->decimal('price', 10, 2)->after('plan_id');
                }
                if (!Schema::hasColumn('subscriptions', 'start_date')) {
                    $table->timestamp('start_date')->nullable()->after('status');
                }
                if (!Schema::hasColumn('subscriptions', 'end_date')) {
                    $table->timestamp('end_date')->nullable()->after('start_date');
                }
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('subscriptions');
    }
};
