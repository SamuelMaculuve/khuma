<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->foreignId('team_id')
                ->nullable()
                ->after('company_id')
                ->constrained('teams')
                ->nullOnDelete();

            $table->index(['company_id', 'team_id']);
        });
    }

    public function down(): void
    {
        Schema::table('leads', function (Blueprint $table) {
            $table->dropForeign(['team_id']);
            $table->dropIndex(['company_id', 'team_id']);
            $table->dropColumn('team_id');
        });
    }
};
