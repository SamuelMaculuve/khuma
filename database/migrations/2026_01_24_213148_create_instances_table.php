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
        Schema::create('instances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string("name");
            $table->string("systemName")->default("khuma");
            $table->string("status")->nullable();
            $table->string("token")->nullable();
            $table->string("profileName")->nullable();
            $table->string("profilePic")->nullable();
            $table->string("info")->nullable();
            $table->boolean("isBusiness")->default(false);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('instances');
    }
};
