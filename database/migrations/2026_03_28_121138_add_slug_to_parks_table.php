<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use App\Models\Park;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Only add column if it doesn't exist
        if (!Schema::hasColumn('parks', 'slug')) {
            Schema::table('parks', function (Blueprint $table) {
                $table->string('slug')->nullable()->after('name');
            });
        }

        // Generate slugs for existing parks
        $slugCounts = [];
        Park::query()->each(function ($park) use (&$slugCounts) {
            // Skip if slug already exists
            if ($park->slug) {
                return;
            }
            
            $baseSlug = Str::slug($park->name);
            $slug = $baseSlug;
            
            // If slug already exists, append a number
            if (isset($slugCounts[$baseSlug])) {
                $slugCounts[$baseSlug]++;
                $slug = $baseSlug . '-' . $slugCounts[$baseSlug];
            } else {
                $slugCounts[$baseSlug] = 0;
            }
            
            $park->update([
                'slug' => $slug
            ]);
        });

        // Make slug unique and not nullable after populating
        Schema::table('parks', function (Blueprint $table) {
            $table->string('slug')->unique()->nullable(false)->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('parks', function (Blueprint $table) {
            $table->dropColumn('slug');
        });
    }
};
