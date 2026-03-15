<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Vérifier si la table portfolios n'existe PAS avant de la créer
        if (!Schema::hasTable('portfolios')) {
            Schema::create('portfolios', function (Blueprint $table) {
                $table->id();
                $table->string('title');
                $table->string('slug')->unique();
                $table->string('client');
                $table->string('client_logo')->nullable();
                $table->string('category');
                $table->string('cover_image')->nullable();
                $table->text('short_description');
                $table->longText('content');
                $table->string('external_link')->nullable();
                $table->string('android_link')->nullable();
                $table->string('ios_link')->nullable();
                $table->boolean('is_confidential')->default(false);
                $table->integer('order')->default(0);
                $table->boolean('is_active')->default(true);
                $table->boolean('is_featured')->default(false);
                $table->timestamps();
            });
        }

        // Vérifier si la table portfolio_images n'existe PAS avant de la créer
        if (!Schema::hasTable('portfolio_images')) {
            Schema::create('portfolio_images', function (Blueprint $table) {
                $table->id();
                $table->foreignId('portfolio_id')
                      ->constrained()
                      ->onDelete('cascade');
                $table->string('path');
                $table->string('caption')->nullable();
                $table->integer('order')->default(0);
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('portfolio_images');
        Schema::dropIfExists('portfolios');
    }
};