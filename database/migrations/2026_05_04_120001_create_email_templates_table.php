<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('email_templates', function (Blueprint $table) {
            $table->id();
            $table->foreignId('company_id')->nullable()->constrained()->onDelete('cascade');
            $table->string('slug')->unique();
            $table->string('name');
            $table->string('category')->default('general');
            $table->string('thumbnail')->nullable();
            $table->string('subject');
            $table->longText('body_html');
            $table->json('placeholders')->nullable();
            $table->boolean('is_global')->default(false);
            $table->timestamps();

            $table->index(['company_id', 'category']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('email_templates');
    }
};
