<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('attraction_metadata', function (Blueprint $table) {
            $table->id();
            $table->foreignId('park_id')->constrained()->cascadeOnDelete();
            $table->string('themeparks_api_id')->nullable();
            $table->string('arendz_id');
            $table->string('name')->nullable();
            $table->string('arendz_image_url', 2048)->nullable();
            $table->text('arendz_description_nl')->nullable();
            $table->timestamp('arendz_synced_at')->nullable();
            $table->timestamps();

            $table->unique(['park_id', 'arendz_id']);
            $table->index(['park_id', 'themeparks_api_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('attraction_metadata');
    }
};
