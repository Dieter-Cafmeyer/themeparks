<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('parks', function (Blueprint $table) {
            $table->string('arendz_id')->nullable()->after('api_id');
            $table->string('arendz_image_url', 2048)->nullable()->after('longitude');
            $table->text('arendz_description_nl')->nullable()->after('arendz_image_url');
            $table->timestamp('arendz_synced_at')->nullable()->after('arendz_description_nl');

            $table->index('arendz_id');
        });
    }

    public function down(): void
    {
        Schema::table('parks', function (Blueprint $table) {
            $table->dropIndex(['arendz_id']);
            $table->dropColumn([
                'arendz_id',
                'arendz_image_url',
                'arendz_description_nl',
                'arendz_synced_at',
            ]);
        });
    }
};
